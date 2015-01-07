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

    public function index()
    {
        $this->tpl .= '/index';

        $this->data = [
            'categories' => db()->query(
                'SELECT c.*, COUNT(r.id) AS asdf
                 FROM categories AS c
                    LEFT JOIN reports AS r
                    ON c.id = r.category
                 GROUP BY c.id
                 ORDER BY asdf DESC'
            )->fetchAll(PDO::FETCH_ASSOC)
        ];
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

        $this->data += 
            current(
                db()->query(
                    'SELECT * FROM reports WHERE id = '.$this->id
                )->fetchAll(PDO::FETCH_ASSOC)
        );
        $this->data['comments'] = selectCount('comments','report = '.$this->id)             ? db()->query('SELECT * FROM comments WHERE report = '.$this->id
                )->fetchAll(PDO::FETCH_ASSOC) 
            : null;
        $this->data['category'] =  current(db()->query(
                'SELECT * FROM categories WHERE id = '.(int)$this->data['category']
            )->fetchAll(PDO::FETCH_ASSOC));
        $this->data['tags'] = db()->query(
            'SELECT tag
             FROM tag_joins 
             WHERE report = '.$this->id)->fetchAll(PDO::FETCH_NUM);

        exit;
    }

    // reports by category
    public function category($id)
    {
        if(!selectCount('categories','id = '.(int)$id))
            $this->abort('No such category.');

        $this->tpl .= '/category';

        $this->data = [
            'category' => current(db()->query(
                'SELECT * FROM categories WHERE id = '.(int)$id
            )->fetchAll(PDO::FETCH_ASSOC)),
            'reports' => db()->query(
                'SELECT id, subject, time, status, closed
                 FROM reports
                 WHERE category = '.(int)$id.'
                 ORDER BY closed ASC,
                    time DESC'
            )->fetchALL(PDO::FETCH_ASSOC)
        ];
    }

    // moderation actions
    public function action($id)
    {
        $this->requireLogin();
        $this->checkId($id);

        if(isset($_POST['status'],$_POST['updateStatus']))
            $this->updateStatus((int)$_POST['status']);

        if(isset($_POST['toggleClosed']))
            $this->toggleClosed();

        if(isset($_POST['delete']))
            $this->delete();

        $this->abort('What are you doing!? No GET access baka!');

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
