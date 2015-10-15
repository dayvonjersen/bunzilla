<?php
/**
 * this is actually the default route
 *
 * it does way too much for what it is
 */

class report extends Controller
{
    const ITEMS_PER_PAGE = 50; // this should be made configurable
                               // on a per-user basis 
                               // once there is a users table

    protected $id = null;
    
    //
    // helper functions and abstractions
    //
    public static function getPreviewField( $catId )
    {
        static $cats = null;
        if($cats === null)
            $cats = Cache::read('categories');
        foreach(['description','reproduce','expected','actual'] as $field)
            if($cats[$catId][$field])
                return $field;
        return false;
    }

    // should implement this kind of abstraction in more places
    protected function checkId($id)
    {
        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report.');
        $this->id = (int)$id;
    }

    // breadcrumbs for this are heirarchial and nice
    public $breadcrumbs = [];
    public function setBreadcrumbs($method)
    { 
        $this->breadcrumbs[] = [
            'href'  => 'report/index', 
            'title' => 'Category Listing',
            'icon'  => 'icon-ul'];
        if($method == 'index')
            return;

        $category = Cache::read('categories')[$this->data['category_id']];
        $this->breadcrumbs[] = [
            'href'  => 'report/category/'.$category['id'],
            'title' => $category['title'],
            'icon'  => $category['icon']
        ];
        if($method == 'category')
            return;

        $this->breadcrumbs[] = [
            'href'  => 'report/view/'.$this->data['report']['id'],
            'title' => $this->data['report']['subject'],
            'icon'  => $this->data['report']['closed'] ? 
                'icon-lock' : 'icon-doc-text-inv'];
    }

    //
    // routes
    //

    // this is default index page for bunzilla
    public function index()
    {
        $this->tpl .= '/index';

        // "stats" refers to report statistics per category
        $stats = [];
        $cats = Cache::read('categories');
        foreach($cats as $id => $cat)
        {
            $stats[$id] = db()->query(
            'SELECT
                COUNT(*) AS total_issues,
                COUNT(DISTINCT(email)) AS unique_posters,
                GREATEST(COALESCE(MAX(edit_time),0),MAX(time),COALESCE(MAX(updated_at),0))
                    AS last_activity
             FROM reports
             WHERE category = '.$id
            )->fetch(PDO::FETCH_ASSOC);

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
            
            $stats[$id]['open_issues'] = selectCount(
                'reports','closed = 0 AND category = '.$id
            );
        }
        $this->data['stats'] = $stats;
        $this->setBreadcrumbs(__FUNCTION__);
    }

    // reports by category
    public function category($id, $offset = 0)
    {
        if(!selectCount('categories','id = '.(int)$id))
            $this->abort('No such category.');

        $offset = ((int) abs($offset)) * self::ITEMS_PER_PAGE;
        if($offset && $offset > selectCount('reports', 'category = '.(int) $id))
            $this->abort('Stop that.');

        $this->tpl .= '/category';

        $field = self::getPreviewField((int)$id);
        $field = $field ? "r.$field AS preview_text," : '';

        $this->data['reports'] = db()->query(
                'SELECT 
                    r.id, r.email, r.epenis, r.subject, '.$field.' 
                    r.priority, r.status, r.closed,
                    r.time, r.edit_time, r.updated_at,
                    COUNT(c.id) AS comment_count, MAX(c.time) AS last_comment,
                    GREATEST(COALESCE(MAX(r.edit_time),0),MAX(r.time),COALESCE(MAX(r.updated_at),0),COALESCE(MAX(c.time),0)) AS last_activity
                    
                 FROM reports AS r
                    LEFT JOIN comments AS c
                    ON r.id = c.report
                 WHERE r.category = '.(int)$id.'
                 GROUP BY r.id
                 ORDER BY r.closed ASC,
                    r.priority DESC,
                    last_activity DESC
                 LIMIT '.$offset.',50'
        )->fetchAll(PDO::FETCH_ASSOC);

        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id']
            )->fetchAll(PDO::FETCH_COLUMN);
            $this->data['reports'][$i]['comments']   = $report['comment_count'];
            $this->data['reports'][$i]['updated_at'] = max($report['updated_at'],$report['last_comment']);
        }

        $this->data['category_id'] = (int)$id;
        $this->data['page_offset'] = ceil($offset / self::ITEMS_PER_PAGE);
        $this->setBreadcrumbs(__FUNCTION__);
    }

    // individual reports
    public function view($id)
    {
        $this->checkId($id);

        $this->tpl .= '/view';

        $this->data['report'] = db()->query(
            'SELECT id, email, INET6_NTOA(ip) AS ip,
                time, category, subject, description,
                reproduce, expected, actual, 
                closed, epenis, edit_time, status, priority, updated_at
             FROM reports WHERE id = '.$this->id
        )->fetch(PDO::FETCH_ASSOC);

        $this->data['comments'] = [];
        if(selectCount('comments','report = '.$this->id))
        {
            $this->data['comments'] = db()->query(
                'SELECT id,email,epenis,
                    time,INET6_NTOA(ip) AS ip,edit_time,
                    message,reply_to
                 FROM comments WHERE report = '.$this->id
            )->fetchAll(PDO::FETCH_ASSOC);
        }

        // this is not great because the chance of 
        // a status_log and and a comment sharing the same ID is possible
        // but eh
        $this->data['timeline'] = db()->query(
            '(SELECT id, time FROM status_log WHERE report = '.$this->id.')
             UNION
             (SELECT id, time FROM comments   WHERE report = '.$this->id.')
             ORDER BY time ASC'
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->data['report']['tags'] = db()->query(
            'SELECT tag
             FROM tag_joins 
             WHERE report = '.$this->id
        )->fetchAll(PDO::FETCH_COLUMN);

        $this->data['status_log'] = db()->query(
            'SELECT id, who, message, time 
             FROM status_log 
             WHERE report = '.$this->id
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->data['category_id'] = $this->data['report']['category'];
        $this->setBreadcrumbs(__FUNCTION__);
        if(!$this->auth() && empty($_POST))
            captcha::set();
        exit;
    }

    // moderation actions
    public function action($id)
    {
        if(empty($_POST))
            $this->abort();

        $this->requireLogin();
        $this->checkId($id);
        
        if(isset($_POST['delete']))
            $location = $this->delete();
        elseif(isset($_POST['status'],$_POST['priority']))
            $location = $this->updateStatus((int)$_POST['status'],(int)$_POST['priority']);
        elseif(isset($_POST['delete_comments']))
            $location = $this->deleteComments();
        $this->redirectWithMessage($location);
    }

    public function move($id,$destination_category = -1)
    {
        if(empty($_POST))
            $this->abort();

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

        $message = 'Move operation failed.'; // default message
        if($report['category'] == $destination_category)
        {
            $message = 'I\'m afraid I can\'t let you do that, Dave.';
        } else {
            $categories = Cache::read('categories');

            if(self::moveBulk([$report],$report['category'],$destination_category))
            {
                $message = ' moved report to category "'
                    .$categories[(int)$destination_category]['title'].'"';
                StatusLog::create('report',$report['id'],$message,null);
            }
        }

        $this->redirectWithMessage('report/view/'.$report['id'],$message);
    }

    public static function moveBulk( $reports, $current_category, $destination_category )
    {
        $current_category = (int) $current_category;
        $destination_category = (int) $destination_category;

        if(selectCount('categories',"id IN ($current_category, $destination_category)") != 2)
            $this->abort('Invalid parameter.');

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
        // preliminary stuff
        if(empty($_POST))
            $this->abort();

        $this->requireLogin();

        $this->checkId($id);
        $curr = (int)$id;

        $destination_report = isset($_POST['zig'],$_POST['report'])
            ? (int) $_POST['report'] : -1;
        $this->checkId($destination_report);
        $dest = (int)$destination_report;

        // obtain datas
        $results = [];
        foreach(db()->query(
            "SELECT id, email,INET6_NTOA(ip) AS ip, epenis, 
                time, edit_time, category, status, priority,
                subject, description, reproduce, expected, actual
             FROM reports 
             WHERE id IN($curr,$dest)"
        )->fetchAll(PDO::FETCH_ASSOC) as $report)
            $results[$report['id']] = $report;

        $current_report     = $results[$curr];
        $destination_report = $results[$dest];
        
        $current_report['tags'] = db()->query(
            'SELECT t.title
             FROM tags AS t
                LEFT JOIN tag_joins AS tj
                ON t.id = tj.tag
             WHERE tj.report = '.$current_report['id']
        )->fetchAll(PDO::FETCH_COLUMN);

        $stmt = db()->prepare(
            'INSERT INTO comments 
                (report,email,epenis,time,ip,message,reply_to) 
             VALUES 
                (:report,:email,:epenis,:time,INET6_ATON(:ip),:message,:reply_to)'
        );

        // format array for pdo
        $makeInsert = function($report,$email,$epenis,$ip,$message,$time,$reply_to){
            return ['report'=>$report,'email'=>$email,'epenis'=>$epenis,'ip'=>$ip,
                    'message'=>$message,'time'=>$time,'reply_to'=>$reply_to
            ];
        };

        // make a new comment in the destination report
        // to hold the contents of the current report
        $stmt->execute($makeInsert(
            $dest,
            __METHOD__,
            2, // epenis 2 is "System"
            remoteAddr(),
            nl2br(
            '<em>Merge is an experimental feature. '.
            'If you encounter any problems or '. 
            'if you have any suggestions or feedback, '. 
            'please leave a message on '.
            '<a href=\'http://meta.bunzilla.ga/\'>'.
            'the Bunzilla meta-tracker</a>.</em>

            submitted: '
            .date(BUNZ_BUNZILLA_DATE_FORMAT, $current_report['time'])."\n"
            .($current_report['edit_time'] ? '<b>edit at</b> '
                .date(BUNZ_BUNZILLA_DATE_FORMAT, $current_report['edit_time'])
                .' (diffs included this could get real ugly)'."\n" : ''
            )."subject: {$current_report['subject']}
            priority: ".Cache::read('priorities')[$current_report['priority']]['title']."
            status: " . Cache::read('statuses'  )[$current_report['status']]['title']."\n"
            .(empty($current_report['tags']) ? '' 
                : 'tagged: '.implode(', ',$current_report['tags'])."\n"
            )),
            time(),
            null
        ));

        // add the contents of the current report 
        // as replies to this initial comment we just made
        $reply_to = db()->lastInsertId();
        $inserts = [];
        foreach(['description','reproduce','expected','actual'] as $field)
        {
            if($current_report[$field])
            {
                $inserts[] = $makeInsert(
                    $dest,
                    $current_report['email'],
                    $current_report['epenis'],
                    $current_report['ip'],
                    "<h3>$field</h3>".$current_report[$field],
                    $current_report['time'],
                    $reply_to
                );
            }
        }

        // include the contents of edits
        // since we split the report (which has multiple fields)
        // into individual comments of these fields, 
        // and since the diff is taken against the entire report
        // we *can't* just move the "diff/reports/some_number" 
        // file to "diff/comments/some_other_number"
        if($current_report['edit_time'])
        {
            if(!file_exists(BUNZ_DIR.'diff/reports/'.$curr))
                $this->flash[] = 'This report has been edited but the diff is missing.';
            else
            {
                $inserts[] = $makeInsert(
                    $dest,
                    $current_report['email'],
                    2,
                    $current_report['ip'],
                    '<h3>diff</h3>'.nl2br(htmlentities(file_get_contents(
                        BUNZ_DIR.'diff/reports/'.$curr))),
                    $current_report['edit_time'],$reply_to
                );
                unlink(BUNZ_DIR.'diff/reports/'.$curr);
            }
        }

        // run all the insert statements at once
        foreach($inserts as $values)
            $stmt->execute($values);

        // transfer the comments to the destination report
        db()->query(
           'UPDATE comments 
            SET time = UNIX_TIMESTAMP(), 
                report = '.$dest.', 
                reply_to = '.$reply_to.' 
            WHERE report = '.$curr);

        // delete the original report
        $this->id = $curr;
        $this->delete();

        $this->redirectWithMessage('report/view/'.$dest,'Report merged.');    
    }

    protected function updateStatus($status,$priority)
    {
        if(selectCount('statuses','id = '.$status)
            && selectCount('priorities','id = '.$priority))
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

        Statuslog::create('category', $catid, 'deleted a report');

        db()->query('DELETE FROM comments WHERE report = '.$this->id);
        db()->query('DELETE FROM tag_joins WHERE report = '.$this->id);
        db()->query('DELETE FROM status_log WHERE report = '.$this->id);
        db()->query('DELETE FROM reports WHERE id = '.$this->id);

        $this->flash[] = 'Report deleted.';

        return 'report/category/'.$catid;
    }

    protected function deleteComments()
    {
        $ids = [];
        $rows = 0;
        foreach($_POST['delete_comments'] as $c)
            $ids[] = (int)$c;
        if(count($ids))
        {
            $in = implode(',',$ids);
            $rows += db()->query(
                'DELETE FROM comments 
                 WHERE report = '.$this->id.' 
                 AND id IN ('.$in.')'
            )->rowCount();
            db()->query(
                'UPDATE comments 
                 SET reply_to = NULL
                 WHERE reply_to IN ('.$in.')'
            );
        }
        $this->flash[] = sprintf('%d comment%s deleted.',$rows,$rows==1?'':'s');
        return 'report/view/'.$this->id;
    }
}
