#!/usr/bin/php
<?php
if(!defined('STDIN'))
    exit("Bunzilla Cronjob FAILED (not called from shell)\n\n");

require_once '../Bunzilla.php';

if(!defined('BUNZ_BUNZILLA_DO_THE_CRON') || !BUNZ_BUNZILLA_DO_THE_CRON)
    exit("Bunzilla Cronjob DID NOT RUN (enable in res/settings.ini)\n\n");

define('CRON_DEBUG_MODE', true);

if(CRON_DEBUG_MODE)
    echo "\033[0;33m
+----------------------------------+
|               NOTE               |
|                                  |
|Running in debug mode.            |
|                                  |
|Nothing is actually being deleted.|
|                                  |
|                =)                |
+----------------------------------+\033[0m\n\n";

require_once BUNZ_LIB_DIR . 'db.inc.php';
require_once BUNZ_LIB_DIR . 'cache.inc.php';

// TODO::// grab some stats

// grab outstanding issues
$outstanding_issues = db()->query('SELECT subject FROM reports WHERE closed = 0 ORDER BY time ASC LIMIT 5')->fetchAll(PDO::FETCH_COLUMN);

// TODO store the above in a useful place
// TODO send emails

// delete old closed reports
// get all reports to be deleted
echo "Getting old closed reports...\n\n";

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
         WHERE report IN ($reports_list)"
        )->fetchAll(PDO::FETCH_COLUMN) as $id)
        $old_comments[] = $id;

    $deleted = [
        'comments' => 'report',
        'status_log' => 'report',
        'tag_joins' => 'report',
        'reports' => 'id'
    ];

    if(CRON_DEBUG_MODE)
        echo "\nWould be deleting old reports...\n\n";

    foreach($deleted as $tbl => $field)
    {
        $sql = "DELETE FROM $tbl WHERE $field IN ($reports_list)";
        if(CRON_DEBUG_MODE)
            echo $sql,"\n";
        else
            db()->query($sql);

        $deleted[$tbl] = 
            CRON_DEBUG_MODE ? 
                selectCount($tbl, "$field IN ($reports_list)") 
                : db()->rowCount();
    }

    foreach($deleted as $tbl => $count)
        echo "$count rows from $tbl deleted\n";

    if(CRON_DEBUG_MODE)
        echo "\nEnd deletions. Would be deleting diffs... \n\n";

    // delete now nonexistent records from diff/
    if(!chdir(BUNZ_DIR . 'diff'))
        exit("chdir diff failed\n\n");
    foreach($old_reports as $id)
    {
        if(file_exists("reports/$id"))
        {
            echo "reports/$id deleted \n";
            if(!CRON_DEBUG_MODE)
                unlink("reports/$id");
        }
    }
    foreach($old_comments as $id)
    {
        if(file_exists("comments/$id"))
        {
            echo "comments/$id deleted\n";
            if(!CRON_DEBUG_MODE)
                unlink("comments/$id");
        }
    }

    // additional diff cleanup just in case
    for($reports = [], $diff = dir('reports/'); ($file = $diff->read()) !== false;)
    {
        if(is_dir($file) || strpos($file,'.') === 0)
            continue;
        if(!selectCount('reports', 'id = '.((int) $file)))
        {
            echo "reports/$file deleted\n";
            if(!CRON_DEBUG_MODE)
                unlink("reports/$file");
        }
    }
    $diff->close();
    for($comments = [], $diff = dir('comments/'); ($file = $diff->read()) !== false;)
    {
        if(is_dir($file) || strpos($file,'.') === 0)
            continue;
        if(!selectCount('comments', 'id = '.((int) $file)))
        {
            echo "comments/$file deleted\n";
            if(!CRON_DEBUG_MODE)
                unlink("comments/$file");
        }
    }
    $diff->close();
}

echo "\nEnd diff deletes ... Fixing and optimizing tables... Rebuilding caches...\n\n";

// fix (repair and optimize) all tables
// clear and rebuild all caches
if(!CRON_DEBUG_MODE)
{
    $fixStmt = db()->prepare('REPAIR TABLE :tbl; OPTIMIZE TABLE :tbl');
    foreach(db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $tbl)
    {
        $fixStmt->execute(['tbl' => $tbl]);
        Cache::clear($tbl);
        Cache::read($tbl);
    }
}

echo "End maintenance... Detecting missing metadata...\n\n";

// detect missing metadata (category/status/tag/priority)
$ids = $sql = [];
foreach(['category' => 'categories',
         'status' => 'statuses',
         'tag' => 'tags',
         'priority' => 'priorities'] as $field => $key)
{
    $ids[$key] = [];
    foreach(Cache::read($key) as $values)
        $ids[$key][] = $values['id'];
    $ids[$key] = implode(',',$ids[$key]);
    if(strlen($ids[$key]))
        $sql[$field] = " $field NOT IN ({$ids[$key]}) ";
}

if(isset($sql['tag']))
{
    $missing_tags = db()->query(
        "SELECT report 
         FROM tag_joins WHERE {$sql['tag']}"
        )->fetchAll(PDO::FETCH_COLUMN);

    if(count($missing_tags)) 
        $sql['tag'] = ' id IN ('.implode(',', $missing_tags).') ';
    else
        unset($sql['tag']);
}

if(CRON_DEBUG_MODE)
    echo 'WHERE clause(s) generated: ',count($sql),"\n\n",
        print_r($sql,1),"\n\n";

if(count($sql))
{
    $result = db()->query(
        'SELECT *
         FROM reports
         WHERE '.implode(' OR ', $sql));
    if($result->rowCount())
    {
        echo $result->rowCount()," report(s) have bad metadata: \n\n";
        print_r($result->fetchAll(PDO::FETCH_ASSOC));

        // TODO: rectify missing metadata
    }
} else {
    echo "\nNo missing metadata detected\n\n";
}

echo "\nCron complete.\n\nLongest-opened unresolved issues: \n\n",
    count($outstanding_issues) ? implode("\n",$outstanding_issues) : 'None! :D', "\n\n";

// TODO: send email(s)

exit(0);
