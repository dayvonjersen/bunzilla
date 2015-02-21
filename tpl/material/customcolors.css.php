<?php
require_once '../../Bunzilla.php';
require_once 'color.php'; // see also this file
require_once '../../lib/cache.inc.php';
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
        $_ += createShades(
                $className,
                constant('BUNZ_THEME_'.strtoupper($className).'_COLOR')
        );
}
/**
 * weeee */
foreach(Cache::read('statuses') as $id => $data)
    $_ += createCSSRule("status-$id",$data['color']);

foreach(Cache::read('tags') as $id => $data)
    $_ += createCSSRule("tag-$id",$data['color']);

foreach(Cache::read('priorities') as $id => $data)
    $_ += createCSSRule("priority-$id",$data['color']);

foreach(Cache::read('categories') as $id => $data)
    $_ += createShades("category-$id", $data['color']);
/**
 * output tiem */
header('Content-Type: text/css; charset=utf-8');

$def = isset($_GET['prettyPrint']) ? ".%s {\n\t%s\n}\n\n" : '.%s{%s}';
$del = isset($_GET['prettyPrint']) ? ";\n\t" : ';';

foreach($_ as $selector => $rules)
{
    $css = [];
    foreach($rules as $property => $value)
        $css[] = sprintf('%s: %s',$property,$value);
    printf($def, $selector, implode($del,$css));
}
