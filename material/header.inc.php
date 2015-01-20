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
<!--        <link rel='stylesheet' href='/bunzilla/material/materialize.min.css'>-->
        <link rel='stylesheet' href='/bunzilla/material/matfix03.css'>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bunzilla-icons.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>highlight.js/foundation.css'>
        <link rel='stylesheet' href='/bunzilla/material/temp.css'>
        <link rel='stylesheet' type='text/css' href='/bunzilla/material/customcolors.css.php'>

        <link rel='stylesheet' href='/bunzilla/material/gn-codrops.css'>
    </head>

<!-- 
    main screen turn on
-->
    <body id="bunzilla">
   <div id="page-wrap">
        <header>

<!--
    google nexus 7 product page menu from codrops
-->
        <ul id="gn-menu" class="gn-menu-main z-depth-1">
<!--
    sidebar
-->
            <li class="gn-trigger">
                <a class="gn-icon gn-icon-menu icon-hamburger"><span>Navigation</span></a>
                <nav class="gn-menu-wrapper z-depth-5">
                    <div class="gn-scroller">
<!--
    all sidebar items go in this ul:
-->
                        <ul class="gn-menu">
                            <li class="gn-search-item">
                                <a class="gn-icon icon-search"><span>Search</span><input placeholder="Search" type="text" class="gn-search"></a>
                            </li>
<!--
    ex:

    <li>
        <a class="icon-whatever">Heading</a>
        <ul class="gn-submenu"><li><a>etc</li></ul>
    </li>
-->
                            <li>
                                <a class="gn-icon icon-male">test</a>
                                <ul class="gn-submenu"><li class="gn-icon icon-person">test</li></ul>
                            </li>
                            <li>
                                <a class="gn-icon icon-male">test</a>
                                <ul class="gn-submenu"><li class="gn-icon icon-person">test</li></ul>
                            </li>
                            <li>
                                <a class="gn-icon icon-male">test</a>
                                <ul class="gn-submenu"><li class="gn-icon icon-person">test</li></ul>
                            </li>
                            <li>
                                <a class="gn-icon icon-male">test</a>
                                <ul class="gn-submenu"><li class="gn-icon icon-person">test</li></ul>
                            </li>
                            <li>
                                <a class="gn-icon icon-male">test</a>
                                <ul class="gn-submenu"><li class="gn-icon icon-person">test</li></ul>
                            </li>

                        </ul>
                    </div>
                </nav>
            </li>
<!--
    ~brand~ of our ~product~
-->
            <li>
                    <a href='<?= BUNZ_HTTP_DIR ?>?material'>bunzilla<?php // $_BUNNIES[array_rand($_BUNNIES)] ?></a>

            </li>

            <li class="hide-on-small-only"><a href="#" class="icon-home">Bread</a></li>
            <li class="hide-on-small-only"><a href="#" class="icon-list-dl">Crumb</a></li>
            <li class="hide-on-small-only"><a href="#" class="icon-bug">Trail</a></li>

            <li class="hide-on-small-only"><a href="<?= BUNZ_HTTP_DIR ?>?material&login" class="icon-key">Login</a></li>
        </ul>
        </header>

        <noscript>
            <aside class="yellow h1">
                <p><b>Note:</b> This layout uses a large amount of Javascript.</p>
                <p>Certain things may not work as intended with Javascript disabled.</p>
                <p>You may find <a href="?nofrills">this layout</a> more suitable to your tastes.</p>
                <p>By the way, the 1970s called. When are you coming home, grandpa?</p>
            </aside>
        </noscript>
        <main>
