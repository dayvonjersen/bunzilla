<?php
class StatusLog {
    private static function create( $who, $msg, $reports )
    {
        if(is_array($reports))
            // other stuff;

        db()->query();

        //k
    }

    abstract public static function read();
    abstract public static function update();
    abstract public static function delete();

    public static function single( $msg, $report = 1 ) {}
    public static function multiple( $msg, $reports = [] ) {}
    public static function all( $msg, $reports = [] ) {}
    
}
