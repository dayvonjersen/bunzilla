<?php
require_once BUNZ_TPL_DIR . 'bunnies.php';
/*if(isset($_GET['hax'])){
unset($_SESSION);
session_destroy();
session_start();}*/
function getIconList($indice = 0)
{
    static $return;
    if(!$return)
    {
        $return = [[],[]];

        preg_match_all('/^\.(icon-[a-z0-9-_]+):before\s*\{\s*content:\s*\'([^\';]+)/m',file_get_contents(str_replace(BUNZ_HTTP_DIR,BUNZ_DIR,BUNZ_CSS_DIR).'bunzilla-icons.css'),$icons,PREG_SET_ORDER);
        foreach($icons as $icon)
        {
            $return[0][] = $icon[1];
            $return[1][] =json_decode('"'.'\\u'.sprintf('%04x',str_replace('\\e','',$icon[2])).'"');
        }
    }
    return $return[$indice];
        
}
function iconSelectBox($selected = false)
{
/**/
    $id = uniqid();
    $icons = getIconList();
    $unicode = getIconList(1);
    $q=0;
    $select = '';
    foreach($icons as $i=> $icon){
        $select .= '<li data-icon="'.$unicode[$i].'" val="'.$icon.'" class="'.$icon.'">'.$icon.'</li>';
        if($selected === $icon)$q=$i;}
    $select .= '</ul>';
    $asdf = '<input type="hidden" name="icon" id="'.$id.'"/>';
    $asdf .= '<ul class="rui-selectable" data-selectable=\'{"multiple":false,"update":"'.$id.'","selected":['.$q.']}\'>';
    return $asdf.$select;
/**
    $fakebox = '<div class="selectBox">';
    $icons = getIconList();
    $selected === false && $selected = current($icons);
    foreach($icons as $icon)
    {
        $id = uniqid();
        $fakebox .= '<input type="radio" id="'.$id.'" name="icon" value="'.$icon.'"'.($selected === $icon?' checked':'').'/><label for="'.$id.'" class="'.$icon.'">'.$icon.'</label>';
    }
    return $fakebox . '</div>';*/
}
function statusButton($statusId)
{
    static $buttons = [];
    if(!isset($buttons[$statusId]) && selectCount('statuses','id = '.(int)$statusId))
    {
        list($id,$title,$color,$icon) = current(db()->query('SELECT * FROM statuses WHERE id = '.(int)$statusId)->fetchAll(PDO::FETCH_NUM));
        $buttons[$id] = '<span class="pure-button '.$icon.'" style="background: #'.$color.'">'.$title.'</span>';
    }
    return isset($buttons[$statusId]) ? $buttons[$statusId] : '';
}
function tagButton($tagId)
{
    static $buttons = [];
    $tagId = (int)$tagId;
    if(!isset($buttons[$tagId]) && selectCount('tags','id = '.$tagId))
    {
        list($title,$color,$icon) = current(db()->query('SELECT title,color,icon FROM tags WHERE id = '.$tagId)->fetchAll(PDO::FETCH_NUM));
        $buttons[$tagId] = '<span class="pure-button '.$icon.'" style="background: #'.$color.'"><small>'.$title.'</small></span>';
    }
    return isset($buttons[$tagId]) ? $buttons[$tagId] : '';
}
function statusSelectBox($selected = false)
{
    if(!selectCount('statuses'))
        return '<div class="danger">No statuses created <a href="'.BUNZ_HTTP_DIR.'admin">go make one</a>.</div>';

    $select = '<select name="status" class="rui-selectable">';
//    $select = '<div style="position: relative; "><div class="selectBox">';
    foreach(db()->query('SELECT * FROM statuses ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC) as $status)
    {
    
       $select .= '<option value="'.$status['id'].'"'.($selected === $status['id'] || !$selected && $status['default'] ?' selected':'').' class="'.$status['icon'].'" style="background: #'.$status['color'].'">'.$status['title'].'</option>';
        $id = uniqid();
//       $select .= '<input type="radio" name="status" id="'.$id.'" value="'.$status['id'].'"'.($selected === $status['id']?' checked':'').'/><label for="'.$id.'" class="'.$status['icon'].'" style="background: #'.$status['color'].'">'.$status['title'].'</label>';
    }
    $select .= '</select>';
//    $select .='</div></div>';
    return $select;
}
?>
<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='<?= BUNZ_PROJECT_MISSION_STATEMENT ?>'>

        <title><?= isset($pageTitle) ? "$pageTitle :: " : '',BUNZ_PROJECT_TITLE, ' :: tracked by Bunzilla' ?></title>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>pure-min.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>grids-responsive-min.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>style.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bunzilla-icons.css'>
<?php
/**
 * highlight.js has a ton of awesome styles
 * this is a shitty way to directory list them 
 * and use View > Page Style in Firefox to pick 

$hljsdir = str_replace(BUNZ_HTTP_DIR,BUNZ_DIR,BUNZ_CSS_DIR).'highlight.js';
$ls = dir($hljsdir);
while(($f = $ls->read()) !== false)
{
    echo is_file($hljsdir.'/'.$f) ? '<link rel="alternate stylesheet" href="'.BUNZ_CSS_DIR.'highlight.js/'.$f.'" title="'.$f.'">'."\n" : '';
}
*/

?>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>highlight.js/foundation.css'>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bounce.css'>

        <script src="<?= BUNZ_JS_DIR ?>right.js"></script>
        <script src="<?= BUNZ_JS_DIR ?>right-selectable-src.js"></script>

        <script>
function hereComeTheHAX()
{
    // bang bang
    var nbdd = new Selectable('navbar-dropdown');
        nbdd.on('show', function(evt)
        {
            var rsc = document.querySelector('#holyshit .rui-selectable-container');
                rsc.style.width = 'calc(100vw - 2.75em)';
                rsc.style.position = 'fixed';
                rsc.style.left = '1em';
                rsc.style.top = '2em';
        });
        nbdd.on('hide', function(evt)
        {
            var rsc = document.querySelector('#holyshit .rui-selectable-container');
                rsc.style.width = 'auto';
                rsc.style.position = 'static';
        });
        var fml = document.getElementById('navbar-dropdown-placeholder');
        fml.addEventListener('click',function(evt)
        {
            if(evt.target == fml)
                nbdd.toggleList(new Event(evt));
        },false);
        nbdd.on('click', function(evt)
        {
//            console.log(evt.target);
              window.location = evt.target._.href;
//                window.location = evt.target.href
        });

    // pond sieve
    var mql = window.matchMedia("(max-width:600px)");
    mql.addListener(function(q){
        if(q.matches){
            var menu = document.getElementById('bunny-bar');

            menu.className = menu.className.replace("pure-menu-open", "");

            function toggleMenu()
            {
                var cssClass = "pure-menu-open";                    

                menu.className = menu.className.includes(cssClass) ? menu.className.replace(cssClass,"") : menu.className + " " + cssClass;
            }
            
            menu.addEventListener('click',function(evt){
                if(evt.target == menu)
                    toggleMenu();
            },true);

            if(!document.getElementById('burger'))
            {
                var burger = document.createElement('a');
                burger.setAttribute('id','burger');
                burger.setAttribute('class','icon-hamburger');
                burger.setAttribute('title','toggle menu');
                burger.setAttribute('href','javascript:void(0);');
                burger.addEventListener('click',toggleMenu,true);
                menu.appendChild(burger);
            }
        }
    });
    var mql2 = window.matchMedia("(min-width:601px)");
    mql2.addListener(function(q){
        if(q.matches){
            var menu = document.getElementById('bunny-bar'),
                burger = document.getElementById('burger');

            if(!menu.className.includes("pure-menu-open"))
                menu.className = menu.className + " pure-menu-open";

            if(burger)
                menu.removeChild(burger);
        }
    });

    // bop
    window.bunnyFooFoo = setInterval(function(){
        var hop = document.querySelector('#bunny-bar a.pure-menu-heading');
        hop.className = hop.className.includes("bounce") ? hop.className.replace("bounce", "") : hop.className + " bounce";
    }, 5000);

    if(typeof additionalHax != "undefined")
        additionalHax();
}
        </script>
    </head>
    <body id='bunzilla' onload="hereComeTheHAX()">
        <header class='header'>
            <nav id="bunny-bar" class='home-menu pure-menu pure-menu-open pure-menu-horizontal'>
                <a class='pure-menu-heading' href='<?= BUNZ_HTTP_DIR ?>'><?= $_BUNNIES[array_rand($_BUNNIES)]?></a>

                <ul>
<?php
if($this->auth())
{
?>
                    <li><a href='<?= BUNZ_HTTP_DIR, $_GET['url'] ?>?logout' class='icon-logout' title='logout'></a></li>
                    <li><a href='<?= BUNZ_HTTP_DIR ?>admin' class='icon-cog-alt' title='cpanel'></a></li>
<?php
} else {
?>
                    <li><a href='<?= BUNZ_HTTP_DIR, $_GET['url'] ?>?login' class='icon-key' title='login'></a></li>
<?php
}
?>

                    <li id="holyshit"><a href='#' class="icon-emo-shoot" id="navbar-dropdown-placeholder">&nbsp;&nbsp;</a><dl id="navbar-dropdown" data-selectable='{"hCont": "","multiple":false}'>
<?php
if(!isset($this->data['categories']))
    $this->data['categories'] = db()->query('SELECT * FROM categories ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);
foreach($this->data['categories'] as $cat)
{
$cat['color'] === null && $cat['color'] = '000000';
?>
    <dd class="pure-g" style=" background: #<?= $cat['color'] ?>">
<?php/*
rgba(<?=sprintf('%02d,%02d,%02d', hexdec(substr($cat['color'],0,2)),hexdec(substr($cat['color'],2,2)),hexdec(substr($cat['color'],4,2)))?>,0.5);">
*/?>
<a href="<?= BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="pure-u-1-2 <?=$cat['icon']?>" style="text-align: right; " title="<?=$cat['caption']?>"><?= $cat['title'] ?></a> | <a href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="pure-u-1-2 icon-plus">post new</a></dd>
<?php
}
unset($cat);
?>
    
</dl></li>
                </ul>
            </nav>
        </header>
        <nav class='pure-menu pure-menu-open pure-menu-horizontal breadcrumb'>
            <ul>
<?php
$crumbs = [
    BUNZ_PROJECT_TITLE => ['icon' => 'icon-home',
                           'href' => BUNZ_HTTP_DIR]
];
if(isset($bread))
    $crumbs += $bread;

//$iconz = getIconList();
foreach($crumbs as $text => $stuff)
{
    $icon = isset($stuff['icon']) ? $stuff['icon'] : '';
    $href = isset($stuff['href']) ? $stuff['href'] : '#';
    $color = isset($stuff['color']) ? $stuff['color'] : 
false;
    echo "\t\t\t\t",'<li',($href === BUNZ_HTTP_DIR . $_GET['url']) || count($crumbs) === 1 ? ' class="pure-menu-selected"' : '',$color?' style="color: #'.$color.';"':'','><a href="',$href,'" title="',$text,'"><span class="',$icon,'">',$text,'</span></a></li>',"\n";
}
?>
            </ul>
        </nav>
<aside class="flash is-center icon-emo-coffee">come check out <h1><a href="/bunzilla/?material">the future</a></h1></aside>
<?php
       
if(!empty($this->flash))
{
    echo '<aside class="flash is-center">';
    foreach($this->flash as $msg)
        echo '<div>',$msg,'</div>';
    echo '</aside>';
}       
