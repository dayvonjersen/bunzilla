<?php
require_once '../Bunzilla.php';
require_once 'color.php';
require_once '../lib/cache.inc.php';
require_once '../lib/db.inc.php';

header('Content-Type: text/css; charset=utf-8');

foreach(Cache::read('statuses') as $id => $data)
{
    $color = new Color($data['color']);

    echo '.status-',$id,' {
    background-color: ',$color,';
    color: ',$color->getTextColor(),' !important;
}',"\n";
}

foreach(Cache::read('tags') as $id => $data)
{
    $color = new Color($data['color']);

    echo '.tag-',$id,' {
    background-color: ',$color,';
    color: ',$color->getTextColor(),' !important;
}',"\n";
}

foreach(Cache::read('priorities') as $data)
{
    $color = new Color($data['color']);

    echo '.priority-',$data['id'],' {
        color: ',$color,' !important;
}
',"\n";
}

foreach(Cache::read('categories') as $id => $data)
{
    $color = new Color($data['color']);

    echo '.category-',$id,'-base {
    background-color: ',$color,';
    color: ',$color->getTextColor(),';
}
.category-',$id,'-text {
    color: ',$color,' !important;
    background-color: ',$color->getTextColor(),';
}',"\n";

    for($i = 1; $i <= 5; $i++)
    {
        $color->lighten($i * 5);
        
        echo '.category-',$id,'-lighten-',$i,' {
    background-color: ',$color,';
    color: ',$color->getTextColor(),';
}',"\n";
    }

    $color->undo();

    for($i = 1; $i < 5; $i++)
    {
        $color->darken($i * 5);
        
        echo '.category-',$id,'-darken-',$i,' {
    background-color: ',$color,';
    color: ',$color->getTextColor(),';
}',"\n";
    }
}

/*** XXX idea
$_ = [
    'class-name' => ['background'=>'#fff','color'=>'#000']
];
foreach($_ as $class => $rules)
{
    foreach($rules as $property => $value)
    {   $rules[] = sprintf('%s: %s',$property,$value);
        unset($rules[$property]); }
    printf('.%s{%s}',implode(';',$rules));
}***/
