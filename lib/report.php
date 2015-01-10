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

    public function index()
    {
        $this->tpl .= '/index';

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

            if($stats[$id]['total_issues'])
            {
                $field = $this->getPreviewField($id);
                $field = $field 
                    ? 'r.' . $field . ' AS preview_text,' : '';

                $latest_issue = current(db()->query(
                    'SELECT r.id, r.subject, '.$field.' r.time, 
                        COUNT(c.id) AS comments
                        FROM reports AS r
                            LEFT JOIN comments AS c
                            ON r.id = c.report
                        WHERE r.category = '.$id.'
                        GROUP BY r.id
                        ORDER BY r.time DESC
                        LIMIT 1'
                )->fetchAll(PDO::FETCH_ASSOC));
                $latest_issue['tags'] = db()->query(
                    'SELECT tag 
                     FROM tag_joins 
                     WHERE report = '.$latest_issue['id']
                )->fetchAll(PDO::FETCH_NUM);
            } else {
                $latest_issue = null;
            }
            $stats[$id]['latest_issue'] = $latest_issue;

            $stats[$id]['open_issues'] = selectCount('reports','closed = 0 AND category = '.$id);
        }
        $this->data['stats'] = $stats;
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

        $this->data['comments'] = selectCount('comments','report = '.$this->id)             ? db()->query('SELECT * FROM comments WHERE report = '.$this->id
                )->fetchAll(PDO::FETCH_ASSOC) 
            : null;
//todo: remove xxx
        $this->data['category'] =  current(db()->query(
                'SELECT * FROM categories WHERE id = '.(int)$this->data['category']
            )->fetchAll(PDO::FETCH_ASSOC));

        $this->data['report']['tags'] = db()->query(
            'SELECT tag
             FROM tag_joins 
             WHERE report = '.$this->id)->fetchAll(PDO::FETCH_NUM);

        $this->data['category_id'] = $this->data['report']['category'];
        exit;
    }

    // reports by category
    public function category($id, $offset = 0)
    {
        if(!selectCount('categories','id = '.(int)$id))
            $this->abort('No such category.');

        $offset = abs($offset);
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
                    updated_at DESC,
                    edit_time DESC,
                    time DESC
                 LIMIT '.$offset.',50'
            )->fetchALL(PDO::FETCH_ASSOC)
        ];

        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id'])->fetchAll(PDO::FETCH_NUM);
            $this->data['reports'][$i]['comments'] = selectCount(
                'comments','report = '.$report['id']
            );
        }

        $this->data['category_id'] = (int)$id;
    }

    // moderation actions
    public function action($id)
    {
        $this->requireLogin();
        $this->checkId($id);

        if(empty($_POST))
            $this->abort('What are you doing!? No GET access baka!');
        
        if(isset($_POST['delete']))
            $this->delete();

        if(isset($_POST['status'],$_POST['updateStatus']))
            $this->updateStatus((int)$_POST['status']);

        if(isset($_POST['toggleClosed']))
            $this->toggleClosed();
    }

    protected function updateStatus($status)
    {
        if(selectCount('statuses','id = '.(int)$status))
        {
            db()->query(
                'UPDATE reports 
                 SET status = '.(int)$status
              .' WHERE id = '.$this->id
            );
            $this->flash[] = 'Status changed.';
        } else {
            $this->flash[] = 'No such status!';
        }
        $this->view($this->id);
    }

    protected function toggleClosed() 
    {
        db()->query(
            'UPDATE reports
             SET closed = NOT(closed)
             WHERE id = '.$this->id
        );
        $this->flash[] = 'k.';
        $this->view($this->id);
    }

    protected function delete()
    {
        $catid = db()->query(
            'SELECT category FROM reports WHERE id = '.$this->id
        )->fetchColumn(0);

        db()->query('DELETE FROM comments WHERE report = '.$this->id);
        db()->query('DELETE FROM reports WHERE id = '.$this->id);
        $this->flash[] = 'Report deleted.';
        $this->category($catid);
    }
}// this file could use some work
