<?php
/**
 * (http://stackoverflow.com/a/219599)
 * 
 * A dream within a dream */
class DatabaseConnectionFactory
{
    private static $factory;
    private $dbconn;

    private function __construct() {}

    /**
     * We have to go deeper. */
    public static function getFactory()
    {
        if(!self::$factory)
            self::$factory = new self;
        return self::$factory;
    }

    /**
     * Three layers */
    public function getDatabaseConnection()
    {
        if(!$this->dbconn)
        {
            try
            {
                $dbconf = parse_ini_file(BUNZ_RES_DIR.'db.config.ini',1);
                if($dbconf === false)
                    throw new Exception('Check db.config.ini');

                $driver = key($dbconf);

                $dbconf = array_merge(
                    array_fill_keys(['dbname','host','user','pass'],''),
                    $dbconf[$driver]
                );

                $user = $dbconf['user'];
                $pass = $dbconf['pass'];

                unset($dbconf['user'],$dbconf['pass']);

                $dsn = $driver . ':' . http_build_query($dbconf,'',';');

                $this->dbconn = new PDO($dsn,$user,$pass);
                $this->dbconn->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );
                $this->dbconn->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC
                );

                unset($driver,$dsn,$user,$pass,$dbconf);
            } catch(Exception $e) {
                echo 'Database connection error! - ',$e->getMessage();
                exit(1);
            }
        }
        return $this->dbconn;
    }
}
/**
 * This concept brought to you by Christopher Nolan */
function db()
{
    return DatabaseConnectionFactory::getFactory()->getDatabaseConnection();
}
