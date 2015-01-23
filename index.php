<?php
require_once 'Bunzilla.php';

require_once BUNZ_LIB_DIR . 'db.inc.php';
require_once BUNZ_LIB_DIR . 'cache.inc.php';

/**
 * Sessions */
ini_set('session.use_cookies', 1);
ini_set('session.referer_check', $_SERVER['HTTP_HOST']);
ini_set('session.hash_function', 1);
session_name('s');
session_cache_limiter('private, must-revalidate');
session_start();
/**
 * Session fixation limitation
 *
 * If client has been inactive for 1 hour (time can change), logout (basically)
 */
if(isset($_SESSION['last_active'])
    && $_SESSION['last_active'] < time() - 3600)
{   session_unset();
    session_destroy();
}
/**
 * If client session is more than half an hour old (again, time can change),
 * change session id */
if(!isset($_SESSION['created_at']))
    $_SESSION['created_at'] = time();
elseif($_SESSION['created_at'] < time() - 1800)
{   session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}
/**
 * Update session var containing time of last activity */
$_SESSION['last_active'] = time();

/**
 * (end of session fixation stuff) 
 */

/**
 * CSRF Stuff
 * with love from flussence.eu */
if(!empty($_POST) && !http_referer_is_host())
    unset($_POST);

/**
 * http_referer_is_host()
 * 
 * @comment does what the name implies
 * @usage csrf checks and redirects
 * @return boolean */
function http_referer_is_host()
{
    return (isset($_SERVER['HTTP_REFERER']) && 
        stristr($_SERVER['HTTP_REFERER'],$_SERVER['HTTP_HOST']));
}

(new Bunzilla);
