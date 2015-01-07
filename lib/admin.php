<?php
/**
 * Control Panel Controller */
class admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireLogin();
    }

    /**
     * The cpanel itself */
    public function index()
    {
        $this->data = [
            'categories' => selectCount('categories') ? db()->query(
                'SELECT c.*, COUNT(r.id) AS total_reports, MAX(r.time) AS last_submission
                 FROM categories AS c
                    LEFT JOIN reports AS r
                    ON c.id = r.category
                 GROUP BY c.id
                 ORDER BY c.title ASC'
                )->fetchAll(PDO::FETCH_ASSOC) : null,
            'statuses' => selectCount('statuses') ? db()->query(
                'SELECT s.*, COUNT(r.id) AS total_reports
                 FROM statuses AS s
                    LEFT JOIN reports AS r
                    ON s.id = r.status
                 GROUP BY s.id
                 ORDER BY s.title ASC'
                )->fetchAll(PDO::FETCH_ASSOC) : null,
            'tags' => selectCount('tags') ? db()->query(
                'SELECT t.*, COUNT(tj.id) AS total_reports
                 FROM tags AS t
                    LEFT JOIN tag_joins AS tj
                    ON t.id = tj.tag
                GROUP BY t.id
                ORDER BY t.title ASC'
                )->fetchAll(PDO::FETCH_ASSOC) : null
        ];
    }

    /**
     * INSERT */
    public function add( )
    {
        $args = func_get_args();
        $mode = empty($args) ? null : array_shift($args);
        switch($mode)
        {
            case 'category':
            case 'status':
            case 'tag':
                break;

            default:
                $this->abort('Unsupported action!');
        }

        $mode .= 'Add';
      
        call_user_func_array([$this,$mode],$args);
        $this->index();
    }

    /**
     * UPDATE */
    public function edit()
    {
        $args = func_get_args();
        $mode = empty($args) ? null : array_shift($args);
        switch($mode)
        {
            case 'category':
            case 'status':
                if(isset($_POST['default_status']))
                {
                    $id = (int)$_POST['default_status'];
                    if(!selectCount('statuses','id = '.$id))
                        $this->abort('invalid status');
                    db()->query('UPDATE statuses SET `default` = 1 WHERE id = '.$id);
                    db()->query('UPDATE statuses SET `default` = 0 WHERE id != '.$id);
                    $this->flash[] = 'default status updated';
                    $this->index();
                    exit;
                }
                break;

            case 'tag':
                break;

            default:
                $this->abort('Unsupported action!');
        }
        $mode .= 'Edit';
        call_user_func_array([$this,$mode],$args);
        $this->index();
    }

    /**
     * DELETE FROM */
    public function delete($mode,$id)
    {
        $id = (int)$id;
        switch($mode)
        {
            case 'category':
                $table = 'categories';
                $field = 'category';
                $comments = db()->query('DELETE FROM comments WHERE report IN (SELECT id FROM reports WHERE category = '.$id.')')->rowCount();
                db()->query('DELETE FROM tag_joins WHERE report IN (SELECT id FROM reports WHERE category = '.$id.')');
                $reports = db()->query('DELETE FROM reports WHERE category = '.$id)->rowCount();

                $this->flash[] = $reports .' report(s) and '.$comments.' comment(s) were purged as a result of this action.';
                break;
            case 'status':
                $table = 'statuses';
                $field = 'status';
                $default_status = db()->query('SELECT id FROM statuses WHERE `default` = 1')->fetchColumn();
                if(selectCount('statuses') == 1)
                    $this->abort('Can\'t delete the only status! (Try editing it instead)');
                if($default_status == $id)
                {
                    $default_status = db()->query('SELECT id FROM statuses WHERE id != '.$id.' ORDER BY RAND() LIMIT 1')->fetchColumn();
                    db()->query('UPDATE statuses SET `default` = 1 WHERE id = '.$default_status);
                    $this->flash[] = 'New default status is '.statusButton($default_status);
                }
                db()->query('UPDATE reports SET status = '.$default_status.' WHERE status = '.$id);
                break;
            case 'tag':
                $table = 'tags';
                $field = 'tag';
                db()->query('DELETE FROM tag_joins WHERE tag = '.$id);
                break;

            default:
                $this->abort('Unsupported action!');
        }
        
        db()->query('DELETE FROM '.$table.' WHERE id = '.$id);

        $this->flash[] = $field .' permanently deleted. You monster.';
        $this->index();        
    }

    /**
     * for to consolidating the reports */
    public function move( $from, $to )
    {
        $reports = db()->query('UPDATE reports SET category = '.(int)$to.' WHERE category = '.(int)$from)->rowCount();
        $this->index();
        $this->flash[] = $reports .' were moved to ';
    }
    // Controller::__construct() checks for method_exists()
    // so much for efficiency
    /**
     * public function __call( $method, $args )
     * {
     * }
     */

    /**
     * Don't Repeat Yourself!
     * I mean you did already with add()/edit()/delete()
     * asdf */
    private function _exec($sql,$params)
    {
        if($params === false || empty($params))
            $this->abort('Bad form!');

        foreach($params as $k => $v)
            $params[$k] = (string)$v;

        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * OK but really factoring all this shit out is awesome */
    private function categoryAdd()
    {
        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars'),
            'caption' => filterOptions(0,'full_special_chars'),
            'description' => filterOptions(1,'boolean'),
            'reproduce' => filterOptions(1,'boolean'),
            'actual' => filterOptions(1,'boolean'),
            'expected' => filterOptions(1,'boolean'),
            'color' => filterOptions(1,'regexp',null,
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars')
        ]);
        $sql = 
            'INSERT INTO categories

(id,title,caption,description,reproduce,expected,actual,color,icon)

            VALUES

(\'\',:title,:caption,:description,:reproduce,:actual,:expected,:color,:icon)';

        if($this->_exec($sql,$params))
            $this->flash[] = 'Category added.';
    }

    private function statusAdd()
    {
        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars'),
            'color' => filterOptions(1,'regexp',null,
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars')
        ]);
        $params['def'] = !selectCount('statuses') ? 1 : 0;
        $sql = 
            'INSERT INTO statuses
                (id,title,color,icon,`default`)
            VALUES
                (\'\',:title,:color,:icon,:def)';
        if($this->_exec($sql,$params))
            $this->flash[] = 'Status added';
    }

    private function tagAdd()
    {
        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars'),
            'color' => filterOptions(1,'regexp',null,
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars')
        ]);
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
       
        $this->data['category'] = current(db()->query(
            'SELECT * FROM categories WHERE id = '.(int)$id
        )->fetchAll(PDO::FETCH_ASSOC));

        if(empty($_POST))
        {
            $this->tpl .= '/categoryEdit';
            exit;
        }

        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars','null_on_failure'),
            'caption'=>filterOptions(0,'full_special_chars','null_on_failure'),
            'description' => filterOptions(1,'boolean','null_on_failure'),
            'reproduce' => filterOptions(1,'boolean','null_on_failure'),
            'actual' => filterOptions(1,'boolean','null_on_failure'),
            'expected' => filterOptions(1,'boolean','null_on_failure'),
            'color' => filterOptions(1,'regexp','null_on_failure',
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars','null_on_failure')
        ]);

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
       
        $this->data['status'] = current(db()->query(
            'SELECT * FROM statuses WHERE id = '.(int)$id
        )->fetchAll(PDO::FETCH_ASSOC));

        if(empty($_POST))
        {
            $this->tpl .= '/statusEdit';
            exit;
        }

        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars','null_on_failure'),
            'color' => filterOptions(1,'regexp','null_on_failure',
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars','null_on_failure')
        ]);

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

    private function tagEdit($id)
    {
        if(!selectCount('tags','id = '.(int)$id))
            $this->abort('No such status!');
       
        $this->data['tag'] = current(db()->query(
            'SELECT * FROM tags WHERE id = '.(int)$id
        )->fetchAll(PDO::FETCH_ASSOC));

        if(empty($_POST))
        {
            $this->tpl .= '/tagEdit';
            exit;
        }

        $params = filter_input_array(INPUT_POST, [
            'title' => filterOptions(0,'full_special_chars','null_on_failure'),
            'color' => filterOptions(1,'regexp','null_on_failure',
                ['regexp'=>'/^[0-9a-f]{6}/i']),
            'icon' => filterOptions(0,'full_special_chars','null_on_failure')
        ]);

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
            $this->flash[] = 'Status updated.';
    }
}
