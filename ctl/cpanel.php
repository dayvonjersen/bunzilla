<?php
/**
 * Control Panel Controller */
class cpanel extends Controller
{
    /**
     * all pages require auth user */
    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();
    }

    /**
     * breadcrumbs */
    public $breadcrumbs = [];
    protected function setBreadcrumbs($method)
    {
        switch($method)
        {
            case 'tagEdit':
                $this->breadcrumbs[] = ['href' => 'cpanel#tags',
                                        'title' => 'Tags',
                                        'icon' => 'icon-tags'];
                $this->breadcrumbs[] = ['href' => '',
                                        'title' => 'Edit Tag',
                                        'icon' => 'icon-pencil-alt'];
                break;
            case 'statusEdit':
                $this->breadcrumbs[] = ['href' => 'cpanel#statuses',
                                        'title' => 'Statuses',
                                        'icon' => 'icon-pinboard'];
                $this->breadcrumbs[] = ['href' => '',
                                        'title' => 'Edit Status',
                                        'icon' => 'icon-pencil-alt'];
                break;
            case 'priorityEdit':
                $this->breadcrumbs[] = ['href' => 'cpanel#priorities',
                                        'title' => 'Priorities',
                                        'icon' => 'icon-attention'];
                $this->breadcrumbs[] = ['href' => '',
                                        'title' => 'Edit Priority',
                                        'icon' => 'icon-pencil-alt'];
                break;
            case 'categoryEdit':
                $this->breadcrumbs[] = ['href' => 'cpanel#categories',
                                        'title' => 'Categories',
                                        'icon' => 'icon-list-dl'];
                $this->breadcrumbs[] = ['href' => '',
                                        'title' => 'Edit Category',
                                        'icon' => 'icon-pencil-alt'];
                break;
            default:
                $this->breadcrumbs[] = ['href' => 'cpanel/index', 
                                        'title' => 'cpanel',
                                        'icon'  => 'icon-cog'];
                break;
        }
    }

    /**
     * Every served from this */
    public function index()
    {
        $this->tpl .= '/index';

        $this->data['statistics'] = [
            'total_reports' => selectCount('reports'),
            'open_reports'  => selectCount('reports', 'closed = 0')
        ];

        $this->setBreadcrumbs(__FUNCTION__);
    }

    /**
     * public-facing INSERT INTO http routes */
    public function add( )
    {
        $args = func_get_args();
        $mode = empty($args) ? null : array_shift($args);
        switch($mode)
        {
            case 'category':
                $tbl = 'categories';
                break;
            case 'status':
                $tbl = 'statuses';
                break;
            case 'tag':
                $tbl = 'tags';
                break;
            case 'priority':
                $tbl = 'priorities';
                break;
            default:
                $this->abort('Unsupported action!');
        }

        call_user_func_array([$this,$mode.'Add'],$args);
        Statuslog::create($mode, db()->lastInsertId(), 'created');
        Cache::clear($tbl);
        $this->index();
    }

    /**
     * public-facing UPDATE http routes */
    public function edit()
    {
        $args = func_get_args();
        $mode = empty($args) ? null : array_shift($args);
        switch($mode)
        {
            case 'category':
                $tbl = 'categories';
                if(isset($_POST['move']))
                    $this->handleMove();
                break;
            case 'status':
                $tbl = 'statuses';
                $this->handleSetDefault($mode,$tbl);
                break;
            case 'tag':
                $tbl = 'tags';
                break;
            case 'priority':
                $tbl = 'priorities';
                $this->handleSetDefault($mode,$tbl);
                break;
            default:
                $this->abort('Unsupported action!');
        }
        call_user_func_array([$this,$mode.'Edit'],$args);
        if(isset($args[0]))
        {
            Statuslog::create(
                $mode, 
                (int)$args[0], 
                'modified '.$mode.' '.Cache::read($tbl)[$args[0]]
            );
        }
        Cache::clear($tbl);
        $this->index();
    }

    /**
     * helper function for above */
    private function handleSetDefault($field, $tbl)
    {
        if(isset($_POST['set_default']))
        {
            $id = (int)$_POST['set_default'];
            if(!selectCount($tbl,"id = $id"))
                $this->abort("invalid $field");
            db()->query("UPDATE $tbl SET `default` = 1 WHERE id = $id");
            db()->query("UPDATE $tbl SET `default` = 0 WHERE id != $id");
            Statuslog::create($field, $id, 'is now the default '.$field);
            Cache::clear($tbl);
            $this->flash[] = 'Default '.$field.' updated.';
            $this->index();
            exit;
        }
    }

    /**
     * public facing DELETE FROM http routes
     * I'm aware that doing this without POST is probably a bad idea
     */
    public function delete($mode,$id)
    {
        if(!http_referer_is_host())
            $this->abort('PANIC.');

        $id = (int)$id;

        /**
         * Each case requires some prep-work before deleting the row */
        switch($mode)
        {
            case 'category':
                $table = 'categories';
                $field = 'category';
                /**
                 * An ON DELETE CASCADE would make this redundant */
                $comments = db()->query(
                    'DELETE FROM comments 
                     WHERE report IN (
                        SELECT id FROM reports WHERE category = '.$id
                    .')'
                )->rowCount();
                db()->query(
                    'DELETE FROM tag_joins WHERE report IN (
                        SELECT id FROM reports WHERE category = '.$id
                    .')'
                );
                $reports = db()->query(
                    'DELETE FROM reports WHERE category = '.$id
                )->rowCount();

                /**
                 * Logging */
                if($reports + $comments)
                    Statuslog::globalMessage(
                        sprintf('%d report%s and %d comment%s were purged by %s',
                            $reports, $reports == 1 ? '' : 's', 
                            $comments, $comments == 1 ? '' : 's',
                            $_SERVER['PHP_AUTH_USER']
                        )
                    );

                /**
                 * Response */
                $this->flash[] = $reports .' report(s) and '.$comments
                    .' comment(s) were purged as a result of this action.';
                break;

            case 'status':
                $table = 'statuses';
                $field = 'status';

                /**
                 * Deleting a status requires looking at the default */
                if(selectCount('statuses') == 1)
                    $this->abort('Can\'t delete the only status! 
                        (Try editing it instead)');

                $default_status = db()->query(
                    'SELECT id FROM statuses WHERE `default` = 1'
                )->fetchColumn();

                $stat = Cache::read('statuses');
                if($default_status == $id)
                {
                    $default_status = db()->query(
                        'SELECT id FROM statuses 
                         WHERE id != '.$id.' ORDER BY RAND() LIMIT 1'
                    )->fetchColumn();
                    db()->query(
                        'UPDATE statuses SET `default` = 1 
                         WHERE id = '.$default_status
                    );
                    $this->flash[] = 'New default status is '
                        .$stat[$default_status]['title'];
                }
                db()->query(
                        'UPDATE reports SET status = '.$default_status
                       .' WHERE status = '.$id);

                /**
                 * Logging and Response*/
                $msg = 'All reports previously marked '.$stat[$id]['title']
                   .' are now marked as '.$stat[$default_status]['title'];

                Statuslog::create('status', $default_status, $msg);
                $this->flash[] = $msg;
                break;

            case 'tag':
                $table = 'tags';
                $field = 'tag';
                /**
                 * ON DELETE CASCADE would make this redundant */
                db()->query('DELETE FROM tag_joins WHERE tag = '.$id);
                break;

            case 'priority':
                $table = 'priorities';
                $field = 'priority';

                /**
                 * Deleting the default priority is similar 
                 * to deleting the default status,
                 *
                 * however there are some subtle differences 
                 */
                if(selectCount('priorities') == 1)
                    $this->abort('Can\'t delete the only priority!
                        (Try editing it instead)');

                $default_priority = db()->query(
                    'SELECT id FROM priorities WHERE `default` = 1'
                )->fetchColumn();

                $prior = Cache::read('priorities');
                if($default_priority == $id)
                {
                    /**
                     * Choose the most used priority as the new default */
                    if(selectCount('reports','priority != '.$id))
                    {
                        $default_priority = db()->query(
                            'SELECT priority, COUNT(*) AS number_reports
                             FROM reports 
                             WHERE priority != '.$id.'
                             GROUP BY priority
                             ORDER BY number_reports
                             LIMIT 1')->fetchColumn(0);
                    } else {
                    /**
                     * If there are no other used priorities,
                     * then just choose anything else */
                        $default_priority = db()->query(
                            'SELECT id FROM priority WHERE id != '.$id
                        );
                    }

                    db()->query(
                        'UPDATE priorities SET `default` = 1 
                         WHERE id = '.$default_priority);
                    $this->flash[] = 'New default priority set to '
                        .$prior[$default_priority]['title'];
                }

                db()->query(
                    'UPDATE reports SET priority = '.$default_priority
                   .' WHERE priority = '.$id);

                /**
                 * Logging and Response */
                $msg = "All reports previously marked {$prior[$id]['title']
                    } are now marked as {$prior[$default_priority]['title']}.";
                StatusLog::create('priority', $default_priority, $msg);
                $this->flash[] = $msg;
                break;

            default:
                $this->abort('Unsupported action!');
        }
        
        db()->query('DELETE FROM '.$table.' WHERE id = '.$id);
        Statuslog::globalMessage(sprintf('%s "%s" was permanently deleted by %s',
            $mode,Cache::read($table)[$id]['title'],$_SERVER['PHP_AUTH_USER']
        ));
        Cache::clear($table);
        $this->flash[] = $field .' was permanently deleted.';
        $this->index();        
    }

    /**
     * That's it for public-facing routes, the rest of this file consists of
     * helper functions and abstractions */

    /**
     * for to consolidating the reports ( called from edit() ) */
    private function handleMove()
    {   
        if(!isset($_POST['from'],$_POST['to']))
            return;

        $from = (int)$_POST['from'];
        $to   = (int)$_POST['to'];

        if(selectCount('categories',"id IN ($from,$to)") != 2)
        {
            $this->flash[] = 'Invalid parameter';
        } else {
            $reports = db()->query(
                'SELECT id, category, description, reproduce, expected, actual
                 FROM reports
                 WHERE category = '.$from
            )->fetchAll(PDO::FETCH_ASSOC);
            
            require_once BUNZ_CTL_DIR . 'report.php';

            if(report::moveBulk($reports,$from,$to))
            {
                $categories = Cache::read('categories');
                Statuslog::create('category',$to,sprintf(
                    'All reports in category "%s" were moved to "%s"',
                    $categories[$from]['title'],
                    $categories[$to]['title']
                ));
                $this->flash[] = count($reports) .' were moved to ' 
                    . $categories[$to]['title'];
            } else {
                $this->abort('Something bad happened.');
            }
        }
        $this->index();
        exit;
    }

    /**
     * Don't Repeat Yourself!
     */
    private function _exec($sql,$params)
    {
        if($params === false || empty($params))
            $this->abort('Bad form!');

        foreach($params as $k => $v)
            $params[$k] = (string)$v;

        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }

    private function categoryAdd()
    {
        $filt = new Filter();
        $filt->addString('title');
        $filt->addString('caption');
        $filt->addBool('description');
        $filt->addBool('reproduce');
        $filt->addBool('actual');
        $filt->addBool('expected');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');
        $params = $filt->input_array();
        $sql = 
            'INSERT INTO categories
                (id,title,caption,
                 description,reproduce,expected,actual,
                 color,icon)
             VALUES
                (\'\',:title,:caption,
                :description,:reproduce,:actual,:expected,
                :color,:icon)';

        if($this->_exec($sql,$params))
            $this->flash[] = 'Category added.';
    }

    private function statusAdd()
    {
        $filt = new Filter();
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');
        $params = $filt->input_array();
        $params['def'] = !selectCount('statuses') ? 1 : 0;
        $sql = 
            'INSERT INTO statuses
                (id,title,color,icon,`default`)
             VALUES
                (\'\',:title,:color,:icon,:def)';
        if($this->_exec($sql,$params))
            $this->flash[] = 'Status added';
    }

    private function priorityAdd()
    {
        $filt = new Filter();
        $filt->addInt('id');
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');
        $params = $filt->input_array();
        $params['def'] = !selectCount('priorities') ? 1 : 0;
        $sql = 
            'INSERT INTO priorities
                (id,title,color,icon,`default`)
             VALUES
                (:id,:title,:color,:icon,:def)';
        if($this->_exec($sql,$params))
            $this->flash[] = 'Priority added';
    }

    private function tagAdd()
    {
        $filt = new Filter();
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');
        $params = $filt->input_array();
        $sql = 
            'INSERT INTO tags
                (id,title,color,icon)
             VALUES
                (\'\',:title,:color,:icon)';
        if($this->_exec($sql,$params))
            $this->flash[] = 'Tag added';
    }

    private function categoryEdit($id)
    {
        if(!selectCount('categories','id = '.(int)$id))
            $this->abort('No such category!');
       
        $this->data['category'] = Cache::read('categories')[$id];

        if(empty($_POST))
        {
            $this->tpl .= '/categoryEdit';
            $this->setBreadcrumbs(__FUNCTION__);
            exit;
        }

        $filt = new Filter();
        $filt->addString('title');
        $filt->addString('caption');
        $filt->addBool('description');
        $filt->addBool('reproduce');
        $filt->addBool('actual');
        $filt->addBool('expected');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');
    
        $params = $filt->input_array();

        $set = [];
        foreach($params as $field => $value)
        {
            if($value === null)
                unset($params[$field]);
            else
                $set[] = $field .' = :'. $field;
        }

        if(empty($params))
        {
            $this->flash[] = 'No changes made.';
            $this->index();
            exit;
        }

        $sql = 'UPDATE categories SET '.implode(',',$set)
             .' WHERE id = '.(int)$id;

        if($this->_exec($sql,$params))
            $this->flash[] = 'Category updated.';
    }

    private function statusEdit($id)
    {
        if(!selectCount('statuses','id = '.(int)$id))
            $this->abort('No such status!');
       
        $this->data['status'] = Cache::read('statuses')[$id];

        if(empty($_POST))
        {
            $this->tpl .= '/statusEdit';
            $this->setBreadcrumbs(__FUNCTION__);
            exit;
        }

        $filt = new Filter();
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');

        $params = $filt->input_array();

        $set = [];
        foreach($params as $field => $value)
        {
            if($value === null)
                unset($params[$field]);
            else
                $set[] = $field .' = :'. $field;
        }

        if(empty($params))
        {
            $this->flash[] = 'No changes made.';
            $this->index();
            exit;
        }

        $sql = 'UPDATE statuses SET '.implode(',',$set).' WHERE id = '.(int)$id;

        if($this->_exec($sql,$params))
            $this->flash[] = 'Status updated.';
    }

    private function priorityEdit($id)
    {
        if(!selectCount('priorities','id = '.(int)$id))
            $this->abort('No such priority!');
       
        $this->data['priority'] = Cache::read('priorities')[$id];

        if(empty($_POST))
        {
            $this->tpl .= '/priorityEdit';
            $this->setBreadcrumbs(__FUNCTION__);
            exit;
        }

        $filt = new Filter();
        $filt->addInt('id');
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');

        $params = $filt->input_array();

        $set = [];
        foreach($params as $field => $value)
        {
            if($value === null)
                unset($params[$field]);
            else
                $set[] = $field .' = :'. $field;
        }

        if(empty($params))
        {
            $this->flash[] = 'No changes made.';
            $this->index();
            exit;
        }

        $sql = 'UPDATE priorities SET '.implode(',',$set).' WHERE id = '.(int)$id;

        if($this->_exec($sql,$params))
            $this->flash[] = 'Priority updated.';
    }

    private function tagEdit($id)
    {
        if(!selectCount('tags','id = '.(int)$id))
            $this->abort('No such status!');
       
        $this->data['tag'] = Cache::read('tags')[$id];

        if(empty($_POST))
        {
            $this->tpl .= '/tagEdit';
            $this->setBreadcrumbs(__FUNCTION__);
            exit;
        }

        $filt = new Filter();
        $filt->addString('title');
        $filt->addRegex('color','/^[0-9a-f]{6}/i');
        $filt->addString('icon');

        $params = $filt->input_array();

        $set = [];
        foreach($params as $field => $value)
        {
            if($value === null)
                unset($params[$field]);
            else
                $set[] = $field .' = :'. $field;
        }

        if(empty($params))
        {
            $this->flash[] = 'No changes made.';
            $this->index();
            exit;
        }

        $sql = 'UPDATE tags SET '.implode(',',$set).' WHERE id = '.(int)$id;

        if($this->_exec($sql,$params))
            $this->flash[] = 'Tag updated.';
    }

    /**
     * soon™ */
    private function __NOTWORKING_crontab()
    {
        /**
         * harmful */
        $user = system('whoami', $exit_code);
        if($exit_code != 0)
            echo 'whoami doesn\'t work';
        unset($exit_code);

        $cron = system('crontab -u '.$user.' -l',$exit_code);
        $test = '# This is a test';

        if($exit_code == 127)
            echo 'crontab not installed';

        if(preg_match('/^no crontab/', $cron))
        {
            $cron = $test;
        } else {
            $cron .= "\n$test";
        }

        unset($exit_code);

        $temp = tempnam('/tmp', 'cron-');
        file_put_contents($temp, $cron);

        echo system('crontab -u '.$user.' '.$temp, $exit_code);
        echo "\n$exit_code";
    }

    public function export($mode = 'full')
    {
        if(!in_array($mode,['full','data','structure'],1))
        {
            $this->flash[] = 'Invalid export mode!';
            $this->index();
            exit;
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="'.date('YmdHis').'-'.preg_replace('/[^a-z0-9-_]/i','_',BUNZ_PROJECT_TITLE).'--bunzilla-'.strtoupper($mode).'-dump.sql.gz"');
        flush();
        ob_start();
        echo '-- database dump for: ',BUNZ_PROJECT_TITLE,' ',BUNZ_PROJECT_VERSION,"\n",
             '-- ',BUNZ_SIGNATURE,' v',BUNZ_VERSION,"\n",
             '-- ',date(BUNZ_BUNZILLA_DATE_FORMAT),"\n\n";
        $db = db()->query('SELECT DATABASE()')->fetch(PDO::FETCH_COLUMN);
        echo $mode != 'data' ? "CREATE DATABASE IF NOT EXISTS `$db`;\n" : '',
             "USE `$db`\n\n";
        foreach(db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $tbl)
        {
            if($mode != 'data')
            {
                $create_table = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', db()->query('SHOW CREATE TABLE '.$tbl)->fetch(PDO::FETCH_NUM)[1]);
                if($mode == 'structure')
                    $create_table = preg_replace('/auto_increment=\d+\s+/i', '', $create_table);
                echo "$create_table;\n";
            }
            $columns = db()->query('SHOW COLUMNS FROM '.$tbl)->fetchAll(PDO::FETCH_COLUMN);
            if($mode != 'structure')
            {
                echo "\n -- data for $tbl\nREPLACE INTO `$tbl`\n  (`",implode('`,',$columns),"`)\nVALUES\n";
                $rows = db()->query('SELECT * FROM '.$tbl)->fetchAll(PDO::FETCH_ASSOC);
                foreach($rows as $row)
                {
                    echo "  (";
                    foreach($columns as $col)
                        echo db()->quote($row[$col]),$col === end($columns) ? '' : ',';
                    echo ')',$row === end($rows) ? ';' : ',',"\n";
                }
            }
            echo "\n";
        }
        echo '-- executed in ',microtime(1) - BUNZ_START_TIME,'s';
        $output = gzencode(ob_get_contents(),9);
        ob_end_clean();
        echo $output;
        exit;
    }
}
