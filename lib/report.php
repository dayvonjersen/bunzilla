<?php
class report extends Controller
{
    public function index()
    {
        $this->tpl .= '/index';

        $this->data = [
            'categories' => db()->query(
                'SELECT *
                 FROM categories
                 ORDER BY title ASC'
            )->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public function view($id)
    {
        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report.');

        $this->tpl .= '/view';

        $this->data = current(db()->query(
            'SELECT * FROM reports WHERE id = '.(int)$id
        )->fetchAll(PDO::FETCH_ASSOC));
    }

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

}
