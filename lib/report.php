<?php
class report extends Controller
{
    public function index()
    {
        $this->tpl .= '/index';

        $this->data = db()->query('SELECT * FROM reports')->fetchAll(PDO::FETCH_ASSOC);
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
}
