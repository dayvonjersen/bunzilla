<?php
class search extends Controller
{
    public static function getTableByColumnName( $column )
    {
        switch($column)
        {
            case 'category': return 'categories';
            case 'status': return 'statuses';
            case 'tag': return 'tags';
            case 'priority': return 'priorities';
        }
        throw new OutOfBoundsException('Unrecognized column '.$column);
    }

    public function index() 
    {
        /**
         * Get this page either by search/my term/another term ...
         *  or search?q=my+term,+another+term...*/
        $criteria = func_get_args();
        if(isset($criteria[0]) && $criteria[0] == __CLASS__)
            array_shift($criteria);

        if(isset($_GET['q']))
            $criteria[] = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_ENCODED);

        if(!count($criteria))
            exit;

        $search = implode(' ', $criteria);
        /**
         * search by meta-data */
        preg_match_all(
            '/([!\-])?(category|status|tag|priority)(%3A|:)(.+)/i', 
            $search, $matches, PREG_SET_ORDER
        );
        $require = $include = $exclude = [];
        foreach($matches as $match)
        {
            $col = strtolower($match[2]);
            $match[4] = is_numeric($match[3]) ? (int)$match[3] : strtolower(urldecode($match[4]));
            switch($match[1])
            {
                case '!': $require[$col][] = $match[4]; break;
                case '-': $exclude[$col][] = $match[4]; break;
                default:  $include[$col][] = $match[4]; 
            }
            $search = trim(str_replace($match[0],'',$search));
        }

        $require = $this->dostuff($require);
        $include = $this->dostuff($include);
        $exclude = $this->dostuff($exclude);

        $query = '';
        if(count($require))
            $query .= ' AND id IN('.implode(',',$require).') ';

        if(count($include))
            $query .= ' OR id IN('.implode(',',$include).') ';

        if(count($exclude))
            $query .= ' AND id NOT IN('.implode(',',$exclude).') ';

        if(!empty($search))
        {
            /**
             * search by field */
            preg_match_all(
                '/([\+!\-])?(subject|description|reproduce|expected|actual|comment)((%3A|:).+)?/i',
                $search, $matches, PREG_SET_ORDER
            );
            $fields = array_flip(['subject','description','reproduce','expected','actual','comment']);
            $require_fields = [];
            foreach($matches as $match)
            {
                $match[2] = strtolower($match[2]);
                if(isset($match[3]))
                {
                    $match[3] = substr($match[3],1);
                    if($match[2] == 'comment')
                    {
                        $fields['comment'] = str_replace(' ',',',$match[3]);
                        continue;
                    }
                    $query .= sprintf(' %s MATCH(%s) AGAINST(%s IN BOOLEAN MODE) ',
                        $match[1] == '-' ? 'NOT' : '',
                        $match[2],
                        db()->quote($match[3])
                    );
                } else {
                    switch($match[1])
                    {
                        case '!': case '+': /** require **/ $require_fields[$match[2]] = true; break;
                        case '-': /** exclude **/ unset($fields[$match[2]]); break;
                        default:  /** include as literal search term **/ continue 2;
                    }
                }
                $search = str_replace($match[0],'',$search);
            }

            $search = str_replace(' ',',',$search);

            $fields = array_diff_key($fields,$require_fields);

            if(isset($fields['comment']))
            {
                if(!$fields['comment'])
                    $fields['comment'] = $search;
                $comments = db()->query(
                    'SELECT report 
                     FROM comments 
                     WHERE MATCH(message) AGAINST ('.db()->quote($fields['comment']).')'
                )->fetchAll(PDO::FETCH_COLUMN);

                if(count($comments))
                    $query .= ' OR id IN('.implode(',',$comments).') ';
                unset($fields['comment']);
            }
        }
        if(empty($search))
            $query =  preg_replace('/^\s+(AND|OR)/','',$query);
        else {
            $temp = [];
            foreach(array_keys($fields) as $field)
                $temp[] = "MATCH($field) AGAINST(".db()->quote($search).") ";
            $query = implode(' OR ', $temp) . $query;
        }
    

        $query == '' ? $query = 0 : '';

        $this->data['reports'] = [];
        $this->data['test']['term']  = !strlen($search) ? implode(',', $criteria) : $search;
        $this->data['test']['query'] = 
                'SELECT 
                    r.id, r.category, r.email, r.epenis, r.subject, r.description AS preview_text,
                    r.priority, r.status, r.closed,
                    r.time, r.edit_time, r.updated_at,
                    COUNT(c.id) AS comment_count, MAX(c.time) AS last_comment
                 FROM reports AS r
                    LEFT JOIN comments AS c
                    ON r.id = c.report
                 WHERE r.id IN (SELECT id FROM reports WHERE '.$query.')
                 GROUP BY r.id';
        $benchmark_it = microtime(1);
        try {
            $this->data['test']['results'] = db()->query($this->data['test']['query'])->fetchAll(PDO::FETCH_ASSOC);    
        } catch(PDOException $e) {
            $this->data['error'] = '<pre><strong>'.$e->getMessage()."\n".print_r($this->data['test'],1).'</pre>';
            exit;
        }
        $this->data['reports'] = $this->data['test']['results'];
        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id']
            )->fetchAll(PDO::FETCH_COLUMN);
            $this->data['reports'][$i]['comments']   = $report['comment_count'];
            $this->data['reports'][$i]['updated_at'] = max($report['updated_at'],$report['last_comment']);
        }
        $benchmark_it = microtime(1) - $benchmark_it;
        $this->data['test']['time'] =  $benchmark_it;
    }

    private function dostuff( $array )
    {
        $ret = [];
        foreach($array as $col => $ids)
        {
            $ids = call_user_func_array([$this,$col],$ids);
            if(empty($ids))
                continue;
            //$ret[] = "id ".($not ? 'NOT ' : '').'IN ('.implode(',',$ids).')';
            $ret = array_merge($ret,$ids);
        }
        return $ret;
    }

    private static function getIds( $tbl, $array )
    {
        $ids = $titles = [];

        foreach(Cache::read($tbl) as $id => $row)
        {
            $ids[$id] = $id;
            $titles[$id] = strtolower($row['title']);
        }

        return implode(',',array_keys(
            array_intersect($titles, $array) + array_intersect($ids, $array)
        ));
    }
    
    protected function tag()
    {
        $tags = self::getIds('tags', func_get_args());

        return !count($tags) ? [] : db()->query(
            'SELECT report FROM tag_joins WHERE tag IN ('.$tags.')'
        )->fetchAll(PDO::FETCH_COLUMN);
    }
    
    protected function status()
    {
        $statuses = self::getIds('statuses', func_get_args());

        return !count($statuses) ? [] : db()->query(
            'SELECT id FROM reports WHERE status IN ('.$statuses.')'
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function category()
    {
        $categories = self::getIds('categories', func_get_args());

        return empty($categories) ? [] : db()->query(
            'SELECT id FROM reports WHERE category IN ('.$categories.')'
        )->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function priority()
    {
        $priorities = self::getIds('priorities', func_get_args());

        return !count($priorities) ? [] : db()->query(
            'SELECT id FROM reports WHERE priority IN ('.$priorities.')'
        )->fetchAll(PDO::FETCH_COLUMN);
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

    public function tagcloud()
    {
        $this->tpl .= '/tagcloud';
        $this->data['count'] = self::getTagCloud();
    }
}
