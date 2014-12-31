<?php
class post extends Controller
{
    public function index()
    {
        $this->tpl .= '/index';
        $this->data = ['categories' => db()->query('SELECT id,title,caption,color,icon FROM categories ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function category($id)
    {
        $this->tpl .= '/category';
        $result = db()->query('SELECT * FROM categories WHERE id = '.(int)$id);
        if(!$result->rowCount())
            $this->abort('No such category!');

        $this->data['category'] = $result->fetch(PDO::FETCH_ASSOC);
        $this->data['params'] = array_flip(array_keys($this->data['category']));
    }
}
