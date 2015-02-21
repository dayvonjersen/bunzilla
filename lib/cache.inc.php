<?php
class Cache
{
    public static function create( $table )
    {
        $data = [];
        foreach(db()->query('SELECT * FROM '.$table)->fetchAll(PDO::FETCH_ASSOC)
            as $i => $row)
            $data[isset($row['id'])?$row['id']:$i] = $row;
        file_put_contents(BUNZ_CACHE_DIR.$table.'.cache', serialize($data));
    }

    public static function read( $table )
    {
        if(!file_exists(BUNZ_CACHE_DIR.$table.'.cache'))
            self::create($table);

        return unserialize(file_get_contents(BUNZ_CACHE_DIR.$table.'.cache'));
    }

    public static function clear( $table )
    {
        unlink(BUNZ_CACHE_DIR.$table.'.cache');
    }

    /**
     * TODO (possibly): combine this with the above functionality
     * i.e. function create( $name, $type = 'table' )
     *      switch($type) { case 'table': ... case 'icon': ... case 'idk' ...
     */

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
