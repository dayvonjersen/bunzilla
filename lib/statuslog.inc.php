<?php
//
// terrible model for a terrible idea
//
class StatusLog {
    private static $validColumns = [
        'report','category','priority','status','tag'
    ];

    private static function getTableByColumn($column)
    {
        self::checkColumn($column);
        if(preg_match('/y$/', $column))
            return substr($column,0,-1) . 'ies';
        if(preg_match('/s$/', $column))
            return $column . 'es';
        return $column;
    }

    private static function checkColumn( $column )
    {
        if(!in_array($column,self::$validColumns))
            throw new InvalidArgumentException();
    }

    public static function create( $column, $column_id, $msg, $usr = null )
    {
        self::checkColumn($column);
        if($column == 'report')
            db()->query(
                'UPDATE reports 
                 SET updated_at = UNIX_TIMESTAMP() 
                 WHERE id = '.$column_id
            );

        if(is_null($usr))
            $usr = $_SERVER['PHP_AUTH_USER'];

        $sql = 
        'INSERT INTO status_log 
            ('.$column.', who, time, message)
         VALUES (:column_id, :usr, UNIX_TIMESTAMP(), :msg)';

        $stmt = db()->prepare($sql);
        return $stmt->execute([
            'column_id' => $column_id,
            'usr' => $usr,
            'msg' => $msg
        ]);
    }

    public static function globalMessage( $msg ) {
       $stmt = db()->prepare(
            'INSERT INTO status_log (who, time, message) 
             VALUES (:usr,UNIX_TIMESTAMP(),:msg)');
       return $stmt->execute(['usr'=>'**GLOBAL**','msg'=>$msg]);
    }

    // maybe later
    //abstract public static function read();
    //abstract public static function update();

    public static function delete( $column, $column_id )
    {
        self::checkColumn($column);

        $stmt = db()->prepare(
            'DELETE FROM status_log WHERE '.$column.' = :column_id'
        );

        return $stmt->execute(['column_id' => $column_id]);
    }

    public static function reportMetadata( $id, $fields )
    {
        $old = current(db()->query(
            'SELECT '.implode(',', array_keys($fields)).'
             FROM reports
             WHERE id = '.$id)->fetchAll(PDO::FETCH_ASSOC)
        );

        foreach($fields as $column => $value)
        {
            if($old[$column] != $value)
            {
                if($column == 'closed')
                    $msg = ($old['closed'] ? 'Re-opened':'Closed'). ' report';
                else
                {
                    $tbl = Cache::read(self::getTableByColumn($column));
                    $msg = sprintf('changed %s from %s to %s',
                        $column,
                        $tbl[$old[$column]]['title'],
                        $tbl[$value]['title']
                    );
                }
                self::create('report', $id, $msg);
            }
        }
    }


}
