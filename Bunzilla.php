<?php
/**
 *  Herbert Hoover took office just nine months before the Stock Market Crash
 *  of 1929 and was known for his pull-yourself-up-by-your-bootstraps mentality
 */

/**
 * Preliminary stuff */
define('BUNZ_START_TIME', microtime(1));
define('BUNZ_SIGNATURE', 'tracked by Bunzilla');
define('BUNZ_VERSION', '0.2b');
define('BUNZ_DEVELOPMENT_MODE', false); // always true

/**
 * Error handling~ */
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors',0);

function err_handler($no,$str,$file,$line,$context)
{
    switch($no) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
            $type = 'what did you do jesus christ this should never happen';
            break;

        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $type = 'bretty srs';
            break;

        case E_RECOVERABLE_ERROR:
            $type = 'we dodged a bullet this time guys';
            break;

        case E_STRICT:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $type = 'DEPRECATED.';
            break;
        
        case E_NOTICE:
        case E_USER_NOTICE:
        default:
            $type = 'you can probably ignore this, but you should fix it anyway';
    }

    $_ERROR = ['severity' => $type,
        'where' => basename($file).':'.$line,
        'what' => $str
    ];

    if(BUNZ_DEVELOPMENT_MODE)
        $_ERROR['witnesses'] = print_r($context,1);
            
    if(defined('BUNZ_TPL_DIR'))
    {
        require_once BUNZ_TPL_DIR . 'error.inc.php';
        exit;
    }

    if(!headers_sent())
        header('Content-Type: text/plain');

    print_r($_ERROR);
    exit(1);    
}

set_error_handler('err_handler');
set_exception_handler(function($e)
{
    err_handler(E_PARSE, 
        $e->getMessage(), 
        $e->getFile(), 
        $e->getLine(),
        $e->getTrace()
    );
});

/*
 * Internal Directories */
define('BUNZ_DIR',  rtrim(realpath(__DIR__), '/').'/');
define('BUNZ_RES_DIR', BUNZ_DIR . 'res/');
define('BUNZ_LIB_DIR', BUNZ_DIR . 'lib/');
define('BUNZ_CTL_DIR', BUNZ_DIR . 'ctl/');
define('BUNZ_TPL_BASE_DIR', BUNZ_DIR . 'tpl/');
define('BUNZ_CACHE_DIR', BUNZ_DIR . 'cache/');

/**
 * External */
define('BUNZ_HTTP_DIR', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/');

// bad ideas++
define('BUNZ_DIFF_DIR', BUNZ_HTTP_DIR . 'diff/');

// why did they get rid of __autoload() ._.
spl_autoload_register(function($class) {
    $class = BUNZ_LIB_DIR . strtolower(basename($class)) . '.inc.php';
    if(file_exists($class))
        require_once $class;
});
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
    $cfg = (
        parse_ini_file(BUNZ_RES_DIR.'settings.ini',1) +
        ['bunzilla' => [], 'project' => [], 'theme' => []]
    );
    $filt = new Filter;
    $filt->addBool('allow_anonymous');
    $filt->addBool('require_captcha');
    $filt->addBool('do_the_cron');
    $filt->add('date_format',FILTER_SANITIZE_STRING);
    $cfg['bunzilla'] = $filt->var_array($cfg['bunzilla']);

    $filt = new Filter;
    $filt->addString('title');
    $filt->addString('version');
    $filt->addString('mission_statement');
    $filt->add('website', FILTER_VALIDATE_URL);
    $cfg['project'] = $filt->var_array($cfg['project']);

    $filt = new Filter;
    $filt->addString('default_template');
    $filt->addString('primary_color');
    $filt->addString('secondary_color');
    $filt->addString('shade_color');
    $filt->addString('alert_color');
    $filt->addString('danger_color');
    $filt->addString('success_color');
    $cfg['theme'] = $filt->var_array($cfg['theme']);

    foreach($cfg as $cat => $settings)
        foreach($settings as $def => $val)
            define('BUNZ_'.strtoupper($cat).'_'.strtoupper($def),$val);
} else
    throw new RuntimeException('pls fix res/settings.ini kthxbai');


/**
 * this only takes the URL forwarded by apache
 * and calls the appropriate method 
 * from the appropriate controller */
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

        if(is_array($url) && !preg_match('/^[a-z]+$/',$url[0]))
            exit('fuk u');

        if(file_exists(BUNZ_CTL_DIR.$url[0].'.php'))
        {
            $this->controller = $url[0];
        }

        require_once BUNZ_CTL_DIR.$this->controller.'.php';

        $this->controller = new $this->controller;

        if(isset($url[1]))
        {
            if(method_exists($this->controller, $url[1]))
            {
                $this->method = $url[1];

                // SECURITY IS FUN
                // ...
                $method = (new ReflectionClass($url[0]))->getMethod($url[1]);
                if($method->isStatic() || !$method->isPublic())
                    $this->controller->abort('Nice try.');
                unset($url[0],$url[1]);
            }
        }
        /**
         * CSRF Stuff
         * with love from flussence.eu */
        if(!empty($_POST) && !http_referer_is_host() && !$this->controller->auth()) 
            unset($_POST);

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

/**
 * Intended to be extended upon
 * Maybe I should use "abstract" or "interface" or something */
class Controller 
{
    protected $data = [];
    protected $tpl  = null;
    protected $flash = [];
    
    private $auth = null;

    protected function setTemplate()
    {
        $tpl = isset($_SESSION['tpl']) ? basename($_SESSION['tpl']) : BUNZ_THEME_DEFAULT_TEMPLATE;
        if(isset($_GET['json']))
            $tpl = 'json';
        elseif(isset($_GET['rss']))
            $tpl = 'rss';
        elseif(isset($_GET['nofrills']))
            $tpl = 'nofrills';
        elseif(isset($_GET['material']))
            $tpl = 'material';

        if(!is_dir(BUNZ_TPL_BASE_DIR . $tpl))
        {
            unset($_SESSION['tpl']);
            exit("Template directory for $tpl doesn't exist.");
        }

        // persistence++
        if(in_array($tpl,['nofrills','material']))
            $_SESSION['tpl'] = $tpl;

        define('BUNZ_TPL_DIR', BUNZ_TPL_BASE_DIR . $tpl . '/');
        define('BUNZ_TPL_HTTP_DIR', str_replace(BUNZ_DIR, BUNZ_HTTP_DIR, BUNZ_TPL_DIR));
        define('BUNZ_CSS_DIR', BUNZ_TPL_HTTP_DIR . 'assets/css/');
        define('BUNZ_JS_DIR',  BUNZ_TPL_HTTP_DIR . 'assets/js/');
    }

    public function __construct()
    {
        $this->setTemplate();

        ob_start();

        $this->tpl = get_called_class();
        $this->auth = $this->auth();

        if(isset($_GET['logout']) && $this->auth())
            $this->logout();
        elseif(isset($_GET['login']) && !$this->auth())
        {
            unset($_SESSION['login']);
            $this->auth = null;
            $this->login();
        }        

        if(isset($_SESSION['flash']))
        {
            $this->flash = unserialize($_SESSION['flash']);
            unset($_SESSION['flash']);
        }

        if(isset($_SESSION['params']))
        {
            $this->data['params'] = unserialize($_SESSION['params']);
            unset($_SESSION['params']);
        }
        
        if(isset($_SESSION['captcha']) 
            && (!BUNZ_BUNZILLA_REQUIRE_CAPTCHA || $this->auth))
            unset($_SESSION['captcha']);
    }

    public function __destruct()
    {
        $this->data['categories'] = Cache::read('categories');
        $this->data['statuses']   = Cache::read('statuses');
        $this->data['tags']       = Cache::read('tags');
        $this->data['priorities'] = Cache::read('priorities');

        if(!headers_sent() && isset($this->tpl))
        {
            if(file_exists(BUNZ_TPL_DIR . $this->tpl . '.inc.php'))
                require_once BUNZ_TPL_DIR . $this->tpl . '.inc.php';
            else
                trigger_error('Missing template.',E_USER_DEPRECATED);
        }
    }

    public function abort($error = false)
    {
        if($error)
            $this->flash[] = $error;
        $this->tpl = 'error';
        if(!defined('BUNZ_TPL_DIR'))
            $this->setTemplate();
        exit;
    }

    protected function requireLogin($ulevel = 0)
    {
        is_null($this->auth) && $this->auth();
        if(!$this->auth())
        {
            $this->flash[] = 'You must be logged in to view that page.';
            $this->login();
        }
    }

    protected function redirectWithMessage($location, $message = null)
    {
        if($message)
            $this->flash[] = $message;
        if(!empty($this->flash))
            $_SESSION['flash'] = serialize($this->flash);
        header('Location: '.BUNZ_HTTP_DIR.$location);
        exit;
    }

    /**
     * begin Terrible HTTP Authentication */
    public function auth()
    {
        if(!is_null($this->auth))
            return $this->auth;

        if(isset($_SESSION['login']))
            $login = $_SESSION['login'];
        elseif(isset($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']))
            $login = $_SERVER['PHP_AUTH_USER'] . ':' . 
                crypt($_SERVER['PHP_AUTH_PW'],
                      base64_encode($_SERVER['PHP_AUTH_PW'])
            );

        $this->auth = (bool) (isset($login) && $this->checkPassword($login));        

        return $this->auth;
    }

    protected function login()
    {
        if(!isset($_SESSION['login_attempts']))
            $_SESSION['login_attempts'] = 0;

        if($_SESSION['login_attempts'] >= 4)
            $this->flash[] = 'Please wait a while before trying to log in again.';

        if(!$this->auth())
        {
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: Basic realm="'.BUNZ_SIGNATURE
                .' :: LoGiN P0rT@L~~ ::'.uniqid().' ::'.BUNZ_PROJECT_TITLE.'"'
            );
            $_SESSION['login_attempts']++;
            $this->abort('Authorization failed.');
        } else {
                $this->flash[] = 'welcome back '.htmlentities($_SERVER['PHP_AUTH_USER']);
        }
            
    }

    public function logout()
    {
        $_SESSION['login'] = 'hacky:as:fuck';
        unset($_SERVER['PHP_AUTH_PW'],$_SERVER['PHP_AUTH_USER']);
        $this->auth = null;
        $this->flash[] = 'goodbye';
    }

    private function checkPassword($login)
    {        
        if(!file_exists(BUNZ_RES_DIR.'.htpasswd'))
            $this->abort('Please use res/generatepasswd.php');

        foreach(file(BUNZ_RES_DIR.'.htpasswd') as $ln)
        {
            $ln = trim($ln);
            if(strlen($ln) && preg_match('/^'.preg_quote($login,'/').'$/', $ln))
            {
                $_SESSION['login'] = $login;
                list($_SERVER['PHP_AUTH_USER'],) = explode(':',$login);
                return true;                
            }
        }
        return false;
    }
    /**
     * end Terrible HTTP Authentication */
}

/**
 * this is probably a major vulnerability
 * and probably belongs in lib/db.inc.php */
function selectCount($table,$where = 1,$field='*')
{
    $result = db()->query(
        'SELECT COUNT('.$field.') 
         FROM '.$table.' 
         WHERE '.$where
    );
    if(!$result->rowCount())
        return 0;
    return (int) $result->fetchColumn();
}

/**
 * GOOD LUCK I'M BEHIND SEVEN PROXIES
 */
function remoteAddr() {
    static $ip = null;
    if($ip === null)
    {
        $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? 
            trim(end(explode (',', $_SERVER['HTTP_X_FORWARDED_FOR']))) 
            : $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
