<?php
/**
 * Preliminary stuff */
define('BUNZ_START_TIME', microtime(1));
define('BUNZ_SIGNATURE', 'Bunzilla bug tracker:github.com/generaltso/bunzilla');
define('BUNZ_VERSION', '0.1a');
define('BUNZ_DEVELOPMENT_MODE', true); // always true

/**
 * Error handling~ */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors',0);

/*
 * Internal Directories */
define('BUNZ_DIR',  rtrim(realpath(__DIR__), '/').'/');
define('BUNZ_RES_DIR', BUNZ_DIR . 'res/');
define('BUNZ_LIB_DIR', BUNZ_DIR . 'lib/');
define('BUNZ_TPL_DIR', BUNZ_DIR . 'tpl/');

/**
 * External */
define('BUNZ_HTTP_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/');
define('BUNZ_CSS_DIR', BUNZ_HTTP_DIR . 'css/');
define('BUNZ_JS_DIR',  BUNZ_HTTP_DIR . 'js/');

/*
 * More definitions... 
 *
 * XXX XXX XXX XXX XXX
 * XXX HEY, LISTEN XXX
 * XXX XXX XXX XXX XXX
 *
 * User config (find in res/settings.ini) */
if(file_exists(BUNZ_RES_DIR.'settings.ini'))
{
    $cfg = parse_ini_file(BUNZ_RES_DIR.'settings.ini',1);
    filter_var_array($cfg['bunzilla'], [
        'allow_anonymous' => FILTER_VALIDATE_BOOLEAN,
        'require_captcha' => FILTER_VALIDATE_BOOLEAN,
        'do_the_cron'     => FILTER_VALIDATE_BOOLEAN
    ]);
    filter_var_array($cfg['project'], [
        'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'version' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'mission_statement' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
    ]);
    foreach($cfg as $cat => $settings)
        foreach($settings as $def => $val)
            define('BUNZ_'.strtoupper($cat).'_'.strtoupper($def),$val);
} else
    throw new RuntimeException('pls fix res/settings.ini kthxbai');

class Bunzilla
{
    /**
     * Default route */
    protected $controller = 'report';
    protected $method     = 'index'; 
    protected $params     = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if(file_exists(BUNZ_LIB_DIR.$url[0].'.php'))
        {
            $this->controller = $url[0];
            unset($url[0]);
        }

        require_once BUNZ_LIB_DIR.$this->controller.'.php';

        $this->controller = new $this->controller;

        if(isset($url[1]))
        {
            if(method_exists($this->controller, $url[1]))
            {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = !empty($url) ? array_values($url) : [];

        call_user_func_array([$this->controller,$this->method],$this->params);
    }

    protected function parseUrl()
    {
        if(isset($_GET['url']))
        {
            $url = filter_var(rtrim($_GET['url'],'/'),FILTER_SANITIZE_URL);
            return stristr($url,'/') ? explode('/',$url) : [$url];
        }
    }
}

class Controller 
{
    protected $data;
    protected $tpl;
    protected $flash = [];

    public function __construct()
    {
        $this->tpl = get_called_class();
    }

    public function __destruct()
    {
        require_once BUNZ_TPL_DIR . $this->tpl . '.inc.php';
    }

    public function abort($error = false)
    {
        if($error)
            $this->flash[] = $error;
        $this->tpl = 'error';
        exit;
    }

    public function requireLogin($ulevel = 0)
    {
        //todo: login or something
        if(strpos($_SERVER['REMOTE_ADDR'],'192.168.1.') !== 0)
            $this->abort('You must be logged in to view that page.');
    }
}
