<?php
// ok let's do this right this time

// random bunny emotes
// http://japaneseemoticons.net/ <- go there, highly recommended
require_once BUNZ_DIR . 'tpl/bunnies.php';
require_once BUNZ_TPL_DIR . 'displayfuncs.inc.php';
?>
<!DOCTYPE html>
<html>
    <head>
<!--
    muh semantic web
-->
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='<?= BUNZ_PROJECT_MISSION_STATEMENT ?>'>

        <title><?= isset($pageTitle) ? "$pageTitle :: " : '',BUNZ_PROJECT_TITLE, ' :: tracked by Bunzilla' ?></title>

<!--        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>materialize.min.css'>-->
        <link rel='stylesheet' href='/bunzilla/material/materialize.min.css'>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bunzilla-icons.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>highlight.js/foundation.css'>
        <link rel='stylesheet' href='/bunzilla/material/temp.css'>
        <link rel='stylesheet' type='text/css' href='/bunzilla/material/customcolors.css.php'>
    </head>

<!-- 
    main screen turn on
-->
    <body id='bunzilla'>
<header>
<!--
    categories at a glance
-->
        <ul id="categories-dropdown" class="dropdown-content">
<?php
foreach($this->data['categories'] as $id => $info)
{
?>
            <li class="row">
               <a href="<?= BUNZ_HTTP_DIR,'report/category/',$id ?>" 
                   class="<?= $info['icon'] ?> s8"><?= $info['title'] ?></a>
<!--
                <span class="badge new s1">1</span>
                <a href="<?= BUNZ_HTTP_DIR,'post/category/',$id ?>" 
                   class="btn s3 icon-plus"
                   title="Post New <?= $info['title'] ?>"></a>-->
            </li>
<?php
}
?>
        </ul>
<!--
    header bar
-->
        <nav class="top-nav">
            <div class="nav-wrapper pink">
                <div class="col s12">

<!--
    ~brand~ of our ~product~
-->
                    <a class='brand-logo right'
                       href='<?= BUNZ_HTTP_DIR ?>'
                    ><?= $_BUNNIES[array_rand($_BUNNIES)]?></a>

<!--
    hamburger icon
-->
                    <div class="container">
                        <a href="#" 
                           data-activates="nav-mobile"
                           class="button-collapse top-nav full"
                        ><i class="icon-hamburger"></i></a>
                    </div>
<!--
    nav links
-->
                    <ul id="nav-mobile" class="left side-nav">
</ul></nav>
<?php
/********** TODO fix this
// for admins
if($this->auth())
{
?>
                        <li>
                            <a href='<?= BUNZ_HTTP_DIR, $_GET['url'] ?>?logout' 
                               title='logout'
                            ><i class='icon-logout'></i>logout</a>
                        </li>
                        <li>
                            <a href='<?= BUNZ_HTTP_DIR, 'cpanel' ?>?logout' 
                               title='cpanel'
                            ><i class='icon-cog-alt'></i>cpanel</a>
                        </li>
<?php
// everybody else
} else {
?>
                        <li>
                            <a href='<?= BUNZ_HTTP_DIR, $_GET['url'] ?>?login' 
                               title='login' class='icon-key'></i>login</a>
                        </li>

<?php
}
?>
<!--
    the category listing above
-->
                        <li style="width: 100% !important">
                            <a class="dropdown-button"
style="width: 100% !important" 
                               href="#" 
                                data-activates="categories-dropdown"
                              class="icon-emo-shoot">bang</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
<!--
    end header

    begin breadcrumb trail
-->

<?php
/***
############ TODO
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
<?php
       
if(!empty($this->flash))
{
    echo '<aside class="flash is-center">';
    foreach($this->flash as $msg)
        echo '<div>',$msg,'</div>';
    echo '</aside>';
}
****************/
?>
</header>
<main>
