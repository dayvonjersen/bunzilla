<?php
// ok let's do this right this time

// random bunny emotes
// http://japaneseemoticons.net/ <- go there, highly recommended
require_once BUNZ_TPL_DIR . 'bunnies.php';
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
        <link rel='stylesheet' href='/bunzilla/tpl/material/assets/css/materialize.min.css'>

        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>bunzilla-icons.css'>
        <link rel='stylesheet' href='<?= BUNZ_CSS_DIR ?>highlight.js/foundation.css'>
        <link rel='stylesheet' href='/bunzilla/tpl/material/temp.css'>
        <link rel='stylesheet' type='text/css' href='/bunzilla/tpl/material/customcolors.css.php'>

        <link rel='stylesheet' href='/bunzilla/tpl/material/gn-codrops.css'>
    </head>

<!-- 
    main screen turn on
-->
    <body id="bunzilla">
   <div id="page-wrap"<?= isset($background) ? " class='$background'" : ''?>>
        <header>

<!--
    google nexus 7 product page menu from codrops
-->
        <ul id="gn-menu" class="gn-menu-main">
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
                                <form action="<?= BUNZ_HTTP_DIR ?>search" method="get">
                                <button type="submit" title="Search!" class="btn btn-floating right secondary-base gone" id="searchsubmit" style="margin-top: 0.5em; margin-right: 1em">&#8617;</button>
                                <div class="input-field">
                                    <i class="gn-icon icon-search">
                                    <span>Search</span></i>
                                    <input placeholder="Search" type="text" name="q" class="gn-search" onfocus="document.getElementById('searchsubmit').classList.remove('gone')" onblur="if(this.value === ''){document.getElementById('searchsubmit').classList.add('gone')}">
                                    <span class="material-input"></span>
                                </div>
                                </form>
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
            <li class="gn-multiline">
                    <a title="<?= $_BUNNIES[array_rand($_BUNNIES)] ?>" href='<?= BUNZ_HTTP_DIR ?>'><?= BUNZ_PROJECT_TITLE ?><small><?= BUNZ_SIGNATURE ?></small></a>
            </li>

<?php
/* todo
            <li class="hide-on-small-only"><a href="#" class="icon-home">Bread</a></li>
            <li class="hide-on-small-only"><a href="#" class="icon-list-dl">Crumb</a></li>
            <li class="hide-on-small-only"><a href="#" class="icon-bug">Trail</a></li>
*/
?>
<?php
if($this->auth())
{
?>
            <li class="gn-multiline right">
                <a href="?logout" class="icon-logout">Logout<small><?= $_SERVER['PHP_AUTH_USER'] ?></small></a>
            </li>
            <li class="hide-on-small-only right">
                <a href="<?= BUNZ_HTTP_DIR ?>cpanel" class="icon-cog-alt">Cpanel</a>
            </li>
<?php
} else {
?>
            <li>
                <a href="?login" class="icon-key">Login</a>
            </li>
<?php
}
?>
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
