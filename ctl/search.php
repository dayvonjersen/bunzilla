<?php
class search extends Controller
{
    protected $includeComments = false;

    public function viewSource()
    {
        highlight_file(__FILE__);
        $this->data['test'] = null;
        exit;
    }

    public static function getTableByColumnName( $column )
    {
        switch($column)
        {
            case 'category': return 'categories';
            case 'status': return 'statuses';
            case 'tag': return 'tags';
            case 'priority': return 'priorities';
        }
        throw new OutOfBoundsException('what the fuck dude');
    }
    public function index() 
    {
        $criteria = func_get_args();
        if(isset($_GET['q']))
            $criteria[] = $_GET['q'];

        
        $this->data['test'] = $specific = [];
        foreach($criteria as $criterion)
        {
            if(preg_match(
                '/(category|status|tag|priority):(\d+)/i',
                $criterion,$matches
            )) {
                list(,$field, $id) = $matches;
                if(selectCount(self::getTableByColumnName($field),'id = '.$id))
                    $specific[] = $field . ' = '. $id;
                else
                    $this->flash[] = "$field with specified id does not exist";
                continue;
            }

            if(preg_match(
                '/(subject|description|reproduce|expected|actual):([^%_]+)/i',
                $criterion,$matches
            )) {
                list(,$field, $value) = $matches;
                $specific[] = $field .' LIKE '.db()->quote("%$value%");
                continue;
            }

            if($criterion == 'includeComments')
                $this->includeComments = true;
        }

        if(!empty($specific))
        {
            $this->data['test']['query'] = 'SELECT id, subject FROM reports WHERE '.implode(' AND ', $specific);
            $this->data['test']['results'] = db()->query($this->data['test']['query'])->fetchAll(PDO::FETCH_ASSOC);    
        }
    }

    private static function getIds( $tbl, $array )
    {
        $ids = $titles = [];

        foreach(Cache::read($tbl) as $id => $row)
        {
            $ids[$id] = $id;
            $titles[$id] = $row['title'];
        }

        return  implode(',',array_keys(
            array_intersect($titles, $array) + array_intersect($ids, $array)
        ));
    }
    
    public function tag()
    {
        $tags = self::getIds('tags', func_get_args());

        $this->data['test'] =  db()->query(
            'SELECT report FROM tag_joins WHERE tag IN ('.$tags.')'
        )->fetchAll(PDO::FETCH_NUM);
    }
    
    public function status()
    {
        $statuses = self::getIds('statuses', func_get_args());

        $this->data['test'] =  db()->query(
            'SELECT id FROM reports WHERE status IN ('.$statuses.')'
        )->fetchAll(PDO::FETCH_NUM);
    }
    
    public function priority()
    {
        $priorities = self::getIds('priorities', func_get_args());

        $this->data['test'] =  db()->query(
            'SELECT report FROM tag_joins WHERE priority IN ('.$priorities.')'
        )->fetchAll(PDO::FETCH_NUM);
    }

    public function comments( $query )
    {
        $this->data['test'] =  db()->query(
            'SELECT report FROM comments WHERE message LIKE '.db()->quote("%$query%")
        )->fetchALL(PDO::FETCH_NUM);
    }

    public static function getTagCloud()
    {
        $ret = [];
        foreach(db()->query('
            SELECT t.id, COUNT(tj.id) 
            FROM tags AS t 
            LEFT JOIN tag_joins AS tj
                ON t.id = tj.tag
            GROUP BY t.id
            ORDER BY t.title
        ')->fetchAll(PDO::FETCH_NUM) as $row)
        {
            $ret[$row[0]] = $row[1];
        }
        return  $ret;
    }
}
