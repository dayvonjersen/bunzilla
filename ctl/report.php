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
            $field = false;
        return $field;
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
                GREATEST(MAX(edit_time),MAX(time),MAX(updated_at))
                    AS last_activity
             FROM reports
             WHERE category = '.$id
            )->fetchAll(PDO::FETCH_ASSOC));

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
            '(SELECT id, time FROM status_log WHERE report = '.$this->id.')
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
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->data['category_id'] = $this->data['report']['category'];
        $this->setBreadcrumbs(__FUNCTION__);
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
                    id, email, subject, '.$field.' 
                    priority, status, closed,
                    time, edit_time, updated_at
                 FROM reports
                 WHERE category = '.(int)$id.'
                 ORDER BY closed ASC,
                    priority DESC,
                    time ASC,
                    updated_at DESC,
                    edit_time DESC
                 LIMIT '.$offset.',50'
            )->fetchALL(PDO::FETCH_ASSOC)
        ];

        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id'])->fetchAll(PDO::FETCH_COLUMN);
            $this->data['reports'][$i]['comments'] = selectCount(
                'comments','report = '.$report['id']
            );
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

        Statuslog::create('category', $catid, 'deleted a report');

        db()->query('DELETE FROM comments WHERE report = '.$this->id);
        db()->query('DELETE FROM tag_joins WHERE report = '.$this->id);
        db()->query('DELETE FROM status_log WHERE report = '.$this->id);
        db()->query('DELETE FROM reports WHERE id = '.$this->id);

        $this->flash[] = 'Report deleted.';

        return 'report/category/'.$catid;
    }
}// this file could use some work
