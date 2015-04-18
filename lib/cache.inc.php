<?php
define('CSS_CACHE_FILE', 'customcolors.css');

class Cache
{
    public static function create( $what, $mode = 'sql', $data = null )
    {
        switch($mode)
        {
            case 'sql':
                $data = [];
                $result = db()->query('SELECT * FROM '.$what);
                foreach($result->fetchAll(PDO::FETCH_ASSOC) as $i => $row)
                    $data[ isset($row['id']) ? $row['id'] : $i ] = $row;
                break;
            case 'txt':
                break;
            default:
                throw new InvalidArgumentException("Unsupported type of cache data!");
        }

        file_put_contents(BUNZ_CACHE_DIR.$what.'.cache', serialize($data));
    }

    public static function read( $what, $mode = 'sql' )
    {
        if(!file_exists(BUNZ_CACHE_DIR.$what.'.cache'))
        {
            if($mode == 'txt')
                return false;
            self::create($what);
        }

        return unserialize(file_get_contents(BUNZ_CACHE_DIR.$what.'.cache'));
    }

    public static function clear( $what, $mode = 'sql' )
    {
        if(file_exists(BUNZ_CACHE_DIR.$what.'.cache'))
            unlink(BUNZ_CACHE_DIR.$what.'.cache');
        // quick hack for cache invalidation
        if($mode == 'sql')
            self::clear(CSS_CACHE_FILE,'txt');
    }

    public static function createIconList()
    {
        $iconList = [];

        preg_match_all(
            '/^\.(icon-[a-z0-9-_]+):before\s*\{\s*content:\s*\'([^\';]+)/m',
            file_get_contents(
                str_replace(BUNZ_HTTP_DIR,BUNZ_DIR,BUNZ_CSS_DIR)
                .'bunzilla-icons.css'),
            $icons,
            PREG_SET_ORDER
        );

        foreach($icons as $icon)
        {
            $iconList[] = [
                'title' => $icon[1],
                'icon'  => $icon[1],
                'id' => str_replace('icon-', '', $icon[1]),
                'unicode' => json_decode(
                    '"'.'\\u'
                    .sprintf('%04x',str_replace('\\e','',$icon[2]))
                    .'"'
                )
            ];
        }

        file_put_contents(BUNZ_CACHE_DIR.'icons.cache', serialize($iconList));
    }
    
    public static function getIconList()
    {
        if(!file_exists(BUNZ_CACHE_DIR.'icons.cache'))
            self::createIconList();
        return unserialize(file_get_contents(BUNZ_CACHE_DIR.'icons.cache'));
    }

    // if you want to clear just delete icons.cache for now
}
