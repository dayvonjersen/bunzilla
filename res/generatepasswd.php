#!/usr/bin/php
<?php
/**
 * hacky .htpasswd generator
 * it works, I'll say that
 */
if(!defined('STDIN'))
    exit('plz run from terminal kthxbai');

/** ~ rice ~ rice ~ baby */
define('OK', "\033[1;32m");
define('NO', "\033[0;31m");
define('U',  "\033[1;33m");
define('SUX',"\033[0m");

$file = '.htpasswd';

if(isset($argv[1]) && preg_match('/^--out=([\w\/]+)/',$argv[1],$m))
{
    $file = is_writable($m[1]) ? $m[1] : false;
}

function read($noecho = false) {
    if($noecho)
        `stty -echo`; 
    $stdin  = fopen('php://stdin','r');
    $return = '';
    while(!in_array(($c = fgetc($stdin)),["\n","\r",false]))
    {
        $return .= $c;
    }
    fclose($stdin);
    if($noecho)
        `stty echo`;
    return $return;
}

echo U,'.htpasswd generator~',SUX,"\n\nEnter a name:\n";

$name = read();

echo "Enter a password:\n";

$pass = read(1);

echo "\nDo it again:\n";
if(read(1) !== $pass)
{
    echo NO,"\nPasswords don't match!\n\n",SUX;
    exit(1);
}

echo "\n";

$pass = $name.':'.crypt($pass, base64_encode($pass));

if(!$file)
    exit(OK."Put this in your .htpasswd:\n".SUX.$pass."\n");

$passwd = file_exists($file) ? file_get_contents($file) : '';
if(preg_match("/^($name:.*)$/m",$passwd,$m))
{
    $passwd = str_replace($m[1],$pass,$passwd);
} else {
    $passwd .= "$pass\n";
}
file_put_contents($file,$passwd);
exit(OK.$file.' updated.'.SUX."\n");
