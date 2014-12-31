<?php
class report extends Controller
{
    public function index()
    {
        $this->tpl .= '/index';

        $this->data = db()->query('SELECT * FROM reports')->fetchAll(PDO::FETCH_ASSOC);
    }
}
