<?php
/**
 *  Herbert Hoover took office just nine months before the Stock Market Crash
 *  of 1929 and was known for his pull-yourself-up-by-your-bootstraps mentality
 */

/**
 * Preliminary stuff */
define('BUNZ_START_TIME', microtime(1));
define('BUNZ_SIGNATURE', 'Bunzilla bug tracker');
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
        'do_the_cron'     => FILTER_VALIDATE_BOOLEAN,
        'date_format'     => FILTER_SANITIZE_STRING
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

/**
 * Intended to be extended upon
 * Maybe I should use "abstract" or "interface" or something */
class Controller 
{
    protected $data = [];
    protected $tpl;
    protected $flash = [];

    private $auth = null;

    public function __construct()
    {
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
        is_null($this->auth) && $this->auth();
        if(!$this->auth())
        {
            $this->flash[] = 'You must be logged in to view that page.';
            $this->login();
        }
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
            $this->abort('Please wait a while before trying to log in again.');

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
 * Filter is neat but a little verbose at times
 * These mitigate that
 * ...hopefully */

function _filterFlag($f)
{
    return constant(
        'FILTER_'
        .($f==='null_on_failure'||$f==='require_array'?'':'FLAG_')
        .strtoupper($f)
    );
}

function filterOptions($validate, $id, $flag = null, $opts = null)
{
    $const = constant('FILTER'.
        (stripos($id,'callback')===0?'':'_'.($validate?'VALIDATE':'SANITIZE'))
        .'_'.strtoupper($id)
    );
    if($flag === null && $opts === null)
        return $const;

    if($flag||$opts)
    {       
        $return = ['filter'=>$const];
        if($flag)
        {   $flags = 0;
            if(is_array($flag))
                foreach($flag as $f)
                    $flags |= _filterFlag($f);
            else
                $flags = _filterFlag($flag);
            $return['flags'] = $flags;
        }
        if(is_array($opts)||is_scalar($opts))
            $return['options'] = $opts;
        return $return;
    }
    return $const; // no options exist without flags
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
    return dtr_pton($ip);
}

/**
* dtr_pton
* dtr_ntop
*
* @author Mike Mackintosh - mike@bakeryphp.com
* @param string $ip
* @return string $bin
*/
function dtr_pton( $ip ){
 
    if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
        return current( unpack( "A4", inet_pton( $ip ) ) );
    }
    elseif(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
        return current( unpack( "A16", inet_pton( $ip ) ) );
    }

}

function dtr_ntop( $str ){
    if( strlen( $str ) == 16 OR strlen( $str ) == 4 ){
        return inet_ntop( pack( "A".strlen( $str ) , $str ) );
    }
}
