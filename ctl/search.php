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
        throw new OutOfBoundsException('what the fuck dude');
    }

    public function index() 
    {
        /**
         * Get this page either by search/my term/another term ...
            or search?q=my+term,+another+term...*/
        $criteria = func_get_args();
        array_shift($criteria);
        if(isset($_GET['q']))
            $criteria[] = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_ENCODED);

        if(empty($criteria))
            exit;

        $search = implode(' ', $criteria);
        /**
         * search by meta-data */
        preg_match_all(
            '/([!\-])?(category|status|tag|priority):(\w+)/i', 
            $search, $matches, PREG_SET_ORDER
        );
        $require = $include = $exclude = [];
        foreach($matches as $match)
        {
            $col = strtolower($match[2]);
            $match[3] = is_numeric($match[3]) ? (int)$match[3] : strtolower($match[3]);
            switch($match[1])
            {
                case '!': $require[$col][] = $match[3]; break;
                case '-': $exclude[$col][] = $match[3]; break;
                default:  $include[$col][] = $match[3]; 
            }
            $search = trim(str_replace($match[0],'',$search));
        }
/*        $require = empty($require) ? '' : implode(' AND ', $this->dostuff($require));
        $include = empty($include) ? '' : implode(' OR ',  $this->dostuff($include));
        $exclude = empty($exclude) ? '' : implode(' AND ', $this->dostuff($exclude,true));*/
        $require = $this->dostuff($require);
        $include = $this->dostuff($include);
        $exclude = $this->dostuff($exclude);

        //$include = array_unique($require+$include+$exclude);

        $query = '';
        if(!empty($require))
            $query .= ' AND id IN('.implode(',',$require).') ';

        if(!empty($include))
            $query .= ' OR id IN('.implode(',',$include).') ';

        if(!empty($exclude))
            $query .= ' AND id NOT IN('.implode(',',$exclude).') ';

//        $query = implode(' AND ', array_filter([$require,$include,$exclude],function($val){return $val!=='';}));

        if(!empty($search))
        {
            /**
             * search by field */
            preg_match_all(
                '/([!\-])?(subject|description|reproduce|expected|actual|comment)(:.+)?/i',
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
                        case '!': /** require **/ $require_fields[$match[2]] = true; break;
                        case '-': /** exclude **/ unset($fields[$match[2]]); break;
                        default:  /** include as literal search term **/ continue 2;
                    }
                }
//                $this->data['test']['pcre'][] = $match;
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
            $query = !empty($search) ? 'MATCH('.implode(',',array_keys($fields)).') AGAINST ('.db()->quote($search).') '.$query : preg_replace('/^\s+(AND|OR)/','',$query);
        }

        $this->data['reports'] = [];
        $this->data['test']['query'] = 'SELECT id,subject FROM reports WHERE '.$query;
        $this->data['test']['results'] = db()->query($this->data['test']['query'])->fetchAll(PDO::FETCH_ASSOC);    
        $this->__TEMP();
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
            $ret = array_merge($ret,array_flip($ids));
        }
        return array_flip($ret);
    }

    private static function getIds( $tbl, $array )
    {
        $ids = $titles = [];

        foreach(Cache::read($tbl) as $id => $row)
        {
            $ids[$id] = $id;
            $titles[$id] = strtolower($row['title']);
        }

        return  implode(',',array_keys(
            array_intersect($titles, $array) + array_intersect($ids, $array)
        ));
    }
    
    protected function tag()
    {
        $tags = self::getIds('tags', func_get_args());

        return empty($tags) ? [] : db()->query(
            'SELECT report FROM tag_joins WHERE tag IN ('.$tags.')'
        )->fetchAll(PDO::FETCH_COLUMN);
    }
    
    protected function status()
    {
        $statuses = self::getIds('statuses', func_get_args());

        return empty($statuses) ? [] : db()->query(
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

        return empty($priorities) ? [] : db()->query(
            'SELECT report FROM tag_joins WHERE priority IN ('.$priorities.')'
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

    private function __TEMP()
    {
header('Content-Type: text/plain');
print_r($this->data['test']);
echo "\n", file_get_contents(__FILE__);
exit;

/** XXX SUPER TEMPORARY please ignore to EOF **/
        $this->data['categories'] = [13 => ['id' => 13,
            'title' => 'SUPER TEMPORARY SEARCH PAGE',
            'caption' => '<form action="'.BUNZ_HTTP_DIR.'search" method="get"><div class="input-field"><input type="text" name="q"><label>ENTER TEXT HERE</label><span class="material-input"></span><input type="submit"></form>',
            'icon' => 'icon-search',
            'color' => 'ffffff']];
        $this->data['category_id'] = 13;
$this->data['reports'] = count($this->data['test']) ? $this->data['test']['results'] : [];
$this->data['page_offset'] = 0;
$this->data['statuses'] = Cache::read('statuses');
$this->data['tags'] = Cache::read('tags');
$this->data['priorities'] = Cache::read('priorities');
        foreach($this->data['reports'] as $i => $report)
        {
            $this->data['reports'][$i]['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$report['id'])->fetchAll(PDO::FETCH_COLUMN);
            $this->data['reports'][$i]['comments'] = selectCount(
                'comments','report = '.$report['id']
            );
            $this->data['reports'][$i]['preview_text'] = 'Nothing to see here...';
        }

require_once BUNZ_DIR . 'tpl/material/report/category.inc.php';

    }
}
