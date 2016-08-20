<?php
/**
 * only because I need to use this in two different places
 *
 * this really doesn't need to be a class at all */
class captcha 
{    
    public static function set()
    {
        if(!BUNZ_BUNZILLA_REQUIRE_CAPTCHA)
            return false;

        $_SESSION['captcha'] = json_decode(file_get_contents(
            'http://api.textcaptcha.com/bunzilla.json'
        ));
        if(!$_SESSION['captcha'])
        {
            $this->flash[] = 'textcaptcha is down expect robots.';
            $_SESSION['captcha'] = (object)[
                'q'=>'Is api.textcaptcha.com down?',
                'a'=>[md5('yes')]
            ];
        }
    }
}
