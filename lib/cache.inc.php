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
}
