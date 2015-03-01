#!/usr/bin/php
<?php
if(!defined('STDIN'))
    exit("Bunzilla Cronjob FAILED (not called from shell)");

require_once '../Bunzilla.php';

if(!defined('BUNZ_BUNZILLA_DO_THE_CRON') || !BUNZ_BUNZILLA_DO_THE_CRON)
    exit("Bunzilla Cronjob DID NOT RUN (enable in res/settings.ini)");

require_once BUNZ_LIB_DIR . 'db.inc.php';
require_once BUNZ_LIB_DIR . 'cache.inc.php';

// delete old closed reports
// get all reports to be deleted
$old_reports = [];
foreach(db()->query(
    'SELECT id 
     FROM reports 
     WHERE closed = 1 
        AND updated_at < UNIX_TIMESTAMP()-3600*24*30'
    )->fetchAll(PDO::FETCH_COLUMN) as $id)
    $old_reports[] = $id;

if(!empty($old_reports))
{
    // what is delete cascade
    $reports_list = implode(',', $old_reports);

    // grab these so we can remove the diffs if they exist
    $old_comments = [];
    foreach(db()->query(
        "SELECT id 
         FROM comments 
         WHERE report IN ($old_reports)"
        )->fetchAll(PDO::FETCH_COLUMN) as $id)
        $old_comments[] = $id;

    $deleted = [
        'comments' => 'report',
        'status_log' => 'report',
        'tag_joins' => 'report',
        'reports' => 'id'
    ];
    foreach($deleted as $tbl => $field)
    {
        //db()->query(
        echo 
            "DELETE FROM $tbl WHERE $field IN ($reports_list)"
        //);
        $deleted[$tbl] = db()->rowCount();
    }

    // delete now nonexistent records from diff/
    if(!chdir(BUNZ_DIR . 'diff'))
        exit("chdir diff failed");
    foreach($old_reports as $id)
        if(file_exists("reports/$id"))
            unlink("report/$id");
    foreach($old_comments as $id)
        if(file_exists("comments/$id"))
            unlink("comments/$id");

    // additional diff cleanup just in case
    for($reports = [], $diff = dir('reports/'); ($file = $diff->read()) !== false;)
    {
        if(is_dir($file) || strpos($file,'.') === 0)
            continue;
        if(!selectCount('reports', 'id = '.((int) $file)))
            unlink("reports/$file");
    }
    $diff->close();
    for($comments = [], $diff = dir('comments/'); ($file = $diff->read()) !== false;)
    {
        if(is_dir($file) || strpos($file,'.') === 0)
            continue;
        if(!selectCount('comments', 'id = '.((int) $file)))
            unlink("comments/$file");
    }
    $diff->close();
    
}

// detect missing metadata (category/status/tag/priority)

// grab some stats

// grab outstanding issues

// fix (repair and optimize) all tables
// clear and rebuild all caches
$fixStmt = db()->prepare('REPAIR TABLE :tbl; OPTIMIZE TABLE :tbl');
foreach(db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $tbl)
{
    $fixStmt->execute(['tbl' => $tbl]);
    Cache::clear($tbl);
    Cache::read($tbl);
}
