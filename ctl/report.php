<?php
/**
 * Another poorly named controller
 * 
 * it's actually the default route
 *
 * words are hard
 */

class report extends Controller
{
    protected $id = null;
    
    protected function getPreviewField( $catId )
    {
        static $cats = null;
        if($cats === null)
            $cats = Cache::read('categories');
        $cat = $cats[$catId];
        if($cat['description'])
            $field = 'description';
        elseif($cat['reproduce'])
            $field = 'reproduce';
        elseif($cat['expected'])
            $field = 'expected';
        elseif($cat['actual'])
            $field = 'actual';
        else
            return false;
        return "r.$field";
    }

    public $breadcrumbs = [];
    public function setBreadcrumbs($method)
    {
        
        $this->breadcrumbs[] = ['href' => 'report/index', 
                                'title' => 'Category Listing',
                                'icon'  => 'icon-ul'];
        if($method == 'index')
            return;

        $category = Cache::read('categories')[$this->data['category_id']];

        $this->breadcrumbs[] = ['href' => 'report/category/'.$category['id'],
                                'title' => $category['title'],
                                'icon' => $category['icon']
        ];
        if($method == 'category')
            return;

        $this->breadcrumbs[] = ['href' => 'report/view/'.$this->data['report']['id'],
                                'title' => $this->data['report']['subject'],
                                'icon' => $this->data['report']['closed'] ? 'icon-lock' : 'icon-doc-text-inv'];
        return;
    }

    public function index()
    {
        $this->tpl .= '/index';

        $this->data['recent_activity'] = db()->query(
            'SELECT *
             FROM status_log
             ORDER BY time DESC
             LIMIT 10'
        )->fetchAll(PDO::FETCH_ASSOC);

        $stats = [];
        $cats = Cache::read('categories');
        foreach($cats as $id => $cat)
        {
            $stats[$id] = current(db()->query(
            'SELECT
                COUNT(*) AS total_issues,
                COUNT(DISTINCT(email)) AS unique_posters,
                GREATEST(COALESCE(MAX(edit_time),0),MAX(time),COALESCE(MAX(updated_at),0))
                    AS last_activity
             FROM reports
             WHERE category = '.$id
            )->fetchAll(PDO::FETCH_ASSOC));

            $latest_comment = db()->query(
                'SELECT MAX(c.time) 
                 FROM comments AS c
                 LEFT JOIN reports AS r
                    ON c.report = r.id
                 WHERE r.category = '.$id.'
                    AND c.time >= '.(int)$stats[$id]['last_activity']
            )->fetch(PDO::FETCH_COLUMN);
            if($latest_comment)
                $stats[$id]['last_activity'] = $latest_comment;
            
            $stats[$id]['open_issues'] = selectCount('reports','closed = 0 AND category = '.$id);
        }
        $this->data['stats'] = $stats;
        $this->setBreadcrumbs(__FUNCTION__);
    }

    // should implement this kind of abstraction in more places
    protected function checkId($id)
    {
        if($this->id!==null)
            return true;

        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report.');
        $this->id = (int)$id;
    }

    // individual reports
    public function view($id)
    {
        $this->checkId($id);

        $this->tpl .= '/view';

        $this->data['report'] = 
            current(
                db()->query(
                    'SELECT * FROM reports WHERE id = '.$this->id
                )->fetchAll(PDO::FETCH_ASSOC)
        );

//todo: remove xxx
        $this->data += $this->data['report'];

        $this->data['comments'] = selectCount('comments','report = '.$this->id) ? db()->query('SELECT * FROM comments WHERE report = '.$this->id
                )->fetchAll(PDO::FETCH_ASSOC) 
            : [];

        // this is not great because the chance of 
        // a status_log and and a comment sharing the same ID is possible
        // but eh
        $this->data['timeline'] = db()->query(
            '(SELECT id, time FROM status_log WHERE report = '.$this->id.' OR category = '.$this->data['report']['category'].')
             UNION
             (SELECT id, time FROM comments WHERE report = '.$this->id.')
             ORDER BY time ASC'
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->data['report']['tags'] = db()->query(
            'SELECT tag
             FROM tag_joins 
             WHERE report = '.$this->id)->fetchAll(PDO::FETCH_COLUMN);

        $this->data['status_log'] = db()->query(
            'SELECT id,who,message,time FROM status_log WHERE report = '.$this->id
// too much spam.' OR category = '.$this->data['report']['category']
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->data['category_id'] = $this->data['report']['category'];
        $this->setBreadcrumbs(__FUNCTION__);
        if(!$this->auth())
            captcha::set();
        exit;
    }

    // reports by category
    public function category($id, $offset = 0)
    {
        if(!selectCount('categories','id = '.(int)$id))
            $this->abort('No such category.');

        $offset = ((int) abs($offset)) * 50;
        if($offset && $offset > selectCount('reports', 'category = '.(int) $id))
            $this->abort('Stop that.');

        $this->tpl .= '/category';

        $field = $this->getPreviewField((int)$id);
        $field .= $field ? ' AS preview_text,' : '';

        $this->data = [
// todo: remove xxx
            'category' => current(db()->query(
                'SELECT * FROM categories WHERE id = '.(int)$id
            )->fetchAll(PDO::FETCH_ASSOC)),


            'reports' => db()->query(
                'SELECT 
                    r.id, r.email, r.subject, '.$field.' 
                    r.priority, r.status, r.closed,
                    r.time, r.edit_time, r.updated_at,
                    COUNT(c.id) AS comment_count, MAX(c.time) AS last_comment
                 FROM reports AS r
                    LEFT JOIN comments AS c
                    ON r.id = c.report
                 WHERE r.category = '.(int)$id.'
                 GROUP BY r.id
                 ORDER BY r.closed ASC,
                    last_comment DESC,
                    r.priority DESC,
                    r.updated_at DESC,
                    r.edit_time DESC,
                    r.time ASC
                 LIMIT '.$offset.',50'
            )->fetchALL(PDO::FETCH_ASSOC)
        ];

        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id'])->fetchAll(PDO::FETCH_COLUMN);
            $this->data['reports'][$i]['comments'] = $report['comment_count'];
            $this->data['reports'][$i]['updated_at'] = max($report['updated_at'],$report['last_comment']);
        }

        $this->data['category_id'] = (int)$id;
        $this->data['page_offset'] = ceil($offset / 50);
        $this->setBreadcrumbs(__FUNCTION__);
    }

    // moderation actions
    public function action($id)
    {
        $this->requireLogin();
        $this->checkId($id);

        if(empty($_POST))
            $this->abort('What are you doing!? No GET access baka!');
        
        if(isset($_POST['delete']))
            $location = $this->delete();

        elseif(isset($_POST['status'],$_POST['priority']))
        {
            $location = $this->updateStatus((int)$_POST['status'],(int)$_POST['priority']);
        }

        $_SESSION['flash'] = serialize($this->flash);
        header('Location: '.BUNZ_HTTP_DIR.$location);
        exit;
    }

    public function move($id,$destination_category = -1)
    {
        $this->requireLogin();
        $this->checkId($id);

        if(isset($_POST['zig'], $_POST['category']))
            $destination_category = (int) $_POST['category'];

        if(!selectCount('categories','id = '.(int)$destination_category))
            $this->abort('No such category.');

        $report = db()->query(
            'SELECT id, category, description, reproduce, expected, actual
             FROM reports
             WHERE id = '.$this->id
        )->fetch(PDO::FETCH_ASSOC);

        if($report['category'] == $destination_category)
        {
            $this->flash[] = 'I\'m afraid I can\'t let you do that, Dave.';
        } else {
            $categories = Cache::read('categories');

            if(self::moveBulk([$report],$report['category'],$destination_category))
            {
                $this->flash[] = 'Report moved to category &quot;'
                    .$categories[(int)$destination_category]['title'].'&quot;';
                StatusLog::create('report',$report['id'],end($this->flash),null);
            }
            else
                $this->abort('Something terrible happened.');
        }

        $_SESSION['flash'] = serialize($this->flash);
        header('Location: '.BUNZ_HTTP_DIR.'report/view/'.$report['id']);
        exit;        
    }

    public static function moveBulk( $reports, $current_category, $destination_category )
    {
        $current_category = (int) $current_category;
        $destination_category = (int) $destination_category;

        if(selectCount('categories',"id IN ($current_category, $destination_category)") != 2)
            throw new InvalidArgumentException('Invalid parameter.');

        $categories = Cache::read('categories');

        $stmt = db()->prepare(
            'UPDATE reports 
             SET category = :category, 
                 description = :description, 
                 reproduce = :reproduce, 
                 expected = :expected, 
                 actual = :actual
             WHERE id = :id'
        );

        $destination_category = $categories[$destination_category];
        foreach($reports as $report)
        {
            $current_category = $categories[$report['category']];
            foreach(['description','reproduce','expected','actual'] as $field)
            {
                if($current_category[$field] == $destination_category[$field])
                    continue;
                if($destination_category[$field] && !$report[$field])
                    $report[$field] = '[Moved from '
                                      .$current_category['title']
                                      .' @ '.date(BUNZ_BUNZILLA_DATE_FORMAT)
                                      .']';
            }
            $report['category'] = $destination_category['id'];
            if(!$stmt->execute($report))
                return false;
        }
        return true;
    }


    public function merge($id)
    {
        $this->requireLogin();
        $destination_report = isset($_POST['zig'],$_POST['report'])
            ? (int) $_POST['report'] : -1;
        $this->checkId($destination_report);
        $dest = (int)$destination_report;
        $this->checkId($id);
        $curr = (int)$id;

        $results = [];
        foreach(db()->query("SELECT * FROM reports WHERE id IN($curr,$dest)")->fetchAll(PDO::FETCH_ASSOC) as $report)
        {
            $results[$report['id']] = $report;
        }

        $current_report = $results[$curr];
        $destination_report = $results[$dest];
        
        if($current_report['category'] != $destination_report['category'])
        {
            $this->flash[] = 'Merge is experimental; Please try again now that the report has moved.';
            $this->move($curr,$dest);
        }

        $current_report['tags'] = db()->query(
            'SELECT t.title
             FROM tags AS t
                LEFT JOIN tag_joins AS tj
                ON t.id = tj.tag
             WHERE tj.report = '.$curr
        )->fetchAll(PDO::FETCH_COLUMN);

        $stmt = db()->prepare(
            'INSERT INTO comments (report,email,epenis,time,ip,message,reply_to) VALUES (:report,:email,:epenis,:time,:ip,:message,:reply_to)');

        $makeInsert = function($report,$email,$epenis,$ip,$message,$time,$reply_to){
            return ['report'=>$report,'email'=>$email,'epenis'=>$epenis,'ip'=>$ip,'message'=>$message,'time'=>$time,'reply_to'=>$reply_to];
        };

        $stmt->execute($makeInsert($dest,__METHOD__,2,dtr_pton('127.0.0.1'),nl2br(
"<div class='col s12 section z-depth-5 shade-lighten-4 h6'><p><i class='icon-move'></i>Merge is an experimental feature.</p><p>If you have any suggestions, feedback, or concerns please leave a message on <a href='http://meta.bunzilla.ga/'>the Bunzilla meta-tracker</a>.</div>

submitted: ".date(BUNZ_BUNZILLA_DATE_FORMAT, $current_report['time'])."\n"
.($current_report['edit_time'] ? '<b>edit at</b> '.date(BUNZ_BUNZILLA_DATE_FORMAT, $current_report['edit_time']).' (diffs included this could get real ugly)'."\n" : '')."
subject: {$current_report['subject']}
priority: ".Cache::read('priorities')[$current_report['priority']]['title']."
status: ".Cache::read('statuses')[$current_report['status']]['title']."\n"
.(empty($current_report['tags']) ? '' : 'tagged: '.implode(', ',$current_report['tags'])."\n")),
time(),null));
        $reply_to = db()->lastInsertId();
        $inserts = [];
        foreach(['description','reproduce','expected','actual'] as $field)
        {
            if($current_report[$field])
            {
                $inserts[] = $makeInsert($dest,$current_report['email'],$current_report['epenis'],$current_report['ip'],"<h3>$field</h3>".$current_report[$field],$current_report['time'],$reply_to);
            }
        }
        if($current_report['edit_time'])
        {
            if(!file_exists(BUNZ_DIR.'diff/reports/'.$curr))
                throw new RuntimeException;

            $inserts[] = $makeInsert($dest,$current_report['email'],$current_report['epenis'],$current_report['ip'],"<h3>diff</h3>".nl2br(htmlentities(file_get_contents(BUNZ_DIR.'diff/reports/'.$curr))),$current_report['edit_time'],$reply_to);
        }

        foreach($inserts as $values)
            $stmt->execute($values);

        db()->query('UPDATE comments SET time = UNIX_TIMESTAMP(), report = '.$dest.', reply_to = '.$reply_to.' WHERE report = '.$curr) or die(print_r(db()->errorInfo(),1));

        $this->flash[] = 'well, THAT happened.';

        $this->id = $curr;
        $this->delete();

        $_SESSION['flash'] = serialize($this->flash);
        header('Location: '.BUNZ_HTTP_DIR.'report/view/'.$dest);
        exit;        
    
    }

    protected function updateStatus($status,$priority)
    {
        if(selectCount('statuses','id = '.$status)&&selectCount('priorities','id = '.$priority))
        {
            $statlog = ['status' => $status, 'priority' => $priority];
            isset($_POST['toggleClosed']) && $statlog['closed'] = -1;
            Statuslog::reportMetadata($this->id, $statlog);

            db()->query(
                'UPDATE reports 
                 SET status = '.$status.', 
                     priority = '.$priority.
                (isset($_POST['toggleClosed']) ? ', closed = NOT(closed) ' : '')
              .' WHERE id = '.$this->id
            );
            $this->flash[] = 'Status updated successfully.';
        } else {
            $this->flash[] = 'Invalid form data!';
        }
        return 'report/view/'.$this->id;
    }

    protected function delete()
    {
        $catid = db()->query(
            'SELECT category FROM reports WHERE id = '.$this->id
        )->fetchColumn(0);

//        Statuslog::create('category', $catid, 'deleted a report');

        db()->query('DELETE FROM comments WHERE report = '.$this->id);
        db()->query('DELETE FROM tag_joins WHERE report = '.$this->id);
        db()->query('DELETE FROM status_log WHERE report = '.$this->id);
        db()->query('DELETE FROM reports WHERE id = '.$this->id);

        $this->flash[] = 'Report deleted.';

        return 'report/category/'.$catid;
    }
}// this file could use some work
