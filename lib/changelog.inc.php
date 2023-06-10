<?php
//
// I'm making models now please help
//
class Changelog {
    public static function append( $msg )
    {
        $stmt = db()->prepare(
            'INSERT INTO change_log (version, time, message)
                VALUES (:ver, UNIX_TIMESTAMP(), :msg)'
        );
        return $stmt->execute([
            'ver' => BUNZ_PROJECT_VERSION,
            'msg' => $msg
        ]);
    }
}
