<?php
class search extends Controller
{
    protected $includeComments = false;

    public function index() 
    {
        $criteria = func_get_args();

        
        $specific = [];
        foreach($criteria as $criterion)
        {
            if(preg_match(
                '/(category|status|tag|priority):(\d+)/i',
                $criterion,$matches
            )) {
                list($field, $id) = $matches;
                if(selectCount(getTableByColumnName($field),'id = '.$id))
                    $specific[] = $field . ' = '. $id;
                else
                    $this->flash[] = "$field with specified id does not exist";
                continue;
            }

            if(preg_match(
                '/(subject|description|reproduce|expected|actual):([^%_]+)/i',
                $criterion,$matches
            )) {
                list($field, $value) = $matches;
                $specific[] = $field .' LIKE '.db()->quote("%$value%")
                continue;
            }

            if($criterion == 'includeComments')
                $this->includeComments = true;
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

        return implode(',',array_keys(
            array_intersect($titles, $array) + array_intersect($ids, $array)
        ));
    }
    
    public function tag()
    {
        $tags = self::getIds('tags', func_get_args());

        return db()->query(
            'SELECT report FROM tag_joins WHERE tag IN ('.$tags.')'
        )->fetchAll(PDO::FETCH_NUM);
    }
    
    public function status()
    {
        $statuses = self::getIds('statuses', func_get_args());

        return db()->query(
            'SELECT id FROM reports WHERE status IN ('.$statuses.')'
        )->fetchAll(PDO::FETCH_NUM);
    }
    
    public function priority()
    {
        $priorities = self::getIds('priorities', func_get_args());

        return db()->query(
            'SELECT report FROM tag_joins WHERE priority IN ('.$priorities.')'
        )->fetchAll(PDO::FETCH_NUM);
    }

    public function comments( $query )
    {
        return db()->query(
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
            $ret[$row[0]] = $row[1]
        }
        return $ret;
    }
}
