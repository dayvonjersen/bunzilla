<?php
require_once '../../Bunzilla.php';
require_once 'color.php'; // see also this file

// CSS_CACHE_FILE is defined in:
require_once '../../lib/cache.inc.php';

if( !isset($_GET['prettyPrint']) && $cached = Cache::read(CSS_CACHE_FILE,'txt') )
{
    $_HTTP = apache_request_headers();
    
    header('Content-Type: text/css; charset=utf-8');
    header('Cache-Control: max-age=10');
    header('ETag: "'.$cached['ETag'].'"');

    if(isset($_HTTP['If-None-Match']))
    { 
        if(preg_match('/^"'.preg_quote($cached['ETag']).'(\-gzip)?"$/',$_HTTP['If-None-Match']))
        {
            header('HTTP/1.1 304 Not Modified');
            exit;
        }

    }

    echo $cached['CSS'];
    exit;
}

require_once '../../lib/db.inc.php';
/**
 * standard theming */
function createCSSRule( $className, $color, $inverse = false )
{
    if(!($color instanceof Color))
        $color = new Color($color);

    return [$className =>  
                ['background-color' => $inverse ? 
                    $color->getTextColor() : (string)$color,
                 'color' => $inverse ? 
                    (string)$color : $color->getTextColor(),
                ]
    ];
}
/**
 * useful alternate colors */
function createShades( $className, $color )
{
    $color = new Color($color);
    $ret = [];
    $ret += createCSSRule("$className-base",$color);
    $ret += createCSSRule("$className-text",$color,true);
    $ret += createCSSRule("$className-invert",$color,true);

    for($i = 1; $i <= 5; $i++)
    {
        $color->lighten($i);
        $ret += createCSSRule("$className-lighten-$i",$color);
    }
    $color->undo();
    for($i = 1; $i < 5; $i++)
    {
        $color->darken($i);
        $ret += createCSSRule("$className-darken-$i",$color);
    }
    return $ret;
}
/**
 * holds all the css rules */
$_ = [];
/**
 * defined in res/settings.ini */
foreach(['primary','secondary','shade', 'alert', 'danger', 'success'] as $className)
{
    if(defined('BUNZ_THEME_'.strtoupper($className).'_COLOR'))
    {
        $_ += createShades(
                $className,
                constant('BUNZ_THEME_'.strtoupper($className).'_COLOR')
        );
        // hax 
        if(!in_array($className,['primary','shade','alert']))
            unset($_["$className-text"]['background-color']);
    }
}
/**
 * weeee */
foreach(Cache::read('statuses') as $id => $data)
    $_ += createCSSRule("status-$id",$data['color']);

foreach(Cache::read('tags') as $id => $data)
{
    $tag = createCSSRule("tag-$id",$data['color']);
    $tag["tag-$id::after"]['border-left-color'] = '#'.$data['color'];
    $_ += $tag;
}

foreach(Cache::read('priorities') as $id => $data)
    $_ += createCSSRule("priority-$id",$data['color']);

foreach(Cache::read('categories') as $id => $data) {
    $_ += createShades("category-$id", $data['color']);
    // more hax
    $_["category-$id-text"]['color'] = $_["category-$id-text"]['background-color'];
    $_["category-$id-text"]['background-color'] = 'transparent';
}

/**
 * output tiem */
header('Content-Type: text/css; charset=utf-8');

$def = isset($_GET['prettyPrint']) ? ".%s {\n\t%s\n}\n\n" : '.%s{%s}';
$del = isset($_GET['prettyPrint']) ? ";\n\t" : ';';

$CSS = '';
foreach($_ as $selector => $rules)
{
    $css = [];
    foreach($rules as $property => $value)
        $css[] = sprintf('%s: %s',$property,$value);
    $CSS .= sprintf($def, $selector, implode($del,$css));
}

if(!isset($_GET['prettyPrint']))
    Cache::create(CSS_CACHE_FILE, 'txt', ['ETag' => md5($CSS), 'CSS' => $CSS]);

echo $CSS;
