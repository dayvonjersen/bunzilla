<!doctype html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='<?= BUNZ_PROJECT_MISSION_STATEMENT ?>'>

        <title><?= BUNZ_PROJECT_TITLE, isset($pageTitle) ? ": $pageTitle" : '' ?></title>
        <link rel='stylesheet' href='http://yui.yahooapis.com/pure/0.5.0/pure-min.css'>
        <link rel='stylesheet' href='http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css'>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>style.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bunzilla-icons.css'>
    </head>
    <body id='bunzilla'>
        <header class='header'>
            <nav class='home-menu pure-menu pure-menu-open pure-menu-horizontal'>
                <a class='pure-menu-heading' href='<?= BUNZ_HTTP_DIR ?>'>／(≡・ x ・≡)＼</a>
                <ul>
                    <li><a href='<?= BUNZ_HTTP_DIR ?>admin' class='icon-cog-alt' title='bunzilla settings'></a></li>
                    <li><a href='<?= BUNZ_HTTP_DIR ?>post' class='icon-plus' title='submit new'></a></li>
                </ul>
            </nav>
        </header>
<?php
if(!empty($this->flash))
{
    echo '<aside>';
    foreach($this->flash as $msg)
        echo '<div>',$msg,'</div>';
    echo '</aside>';
}       
