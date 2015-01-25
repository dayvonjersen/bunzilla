<?php
class changelog extends Controller
{
    public function index()
    {
        unset($this->tpl);

        header('Content-Type: text/plain; charset=utf-8');

        $versions = db()->query('SELECT DISTINCT(version) FROM change_log ORDER BY time ASC')->fetchAll(PDO::FETCH_NUM);
        echo BUNZ_PROJECT_TITLE," changelog";
        foreach($versions as $ver)
        {
            echo "\n\nversion {$ver[0]}:","\n\n";
            foreach(db()->query('SELECT message FROM change_log WHERE version = '.db()->quote($ver[0]).' ORDER BY time DESC')->fetchAll(PDO::FETCH_NUM) as $msg)
                echo " - {$msg[0]}\n";
        }

        exit;
    }
}
