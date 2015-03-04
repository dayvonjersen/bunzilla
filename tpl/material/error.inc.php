<?php
ob_clean();


if(defined('BUNZ_TPL_DIR'))
{
    $pageTitle = 'Whoops!';
    $this->breadcrumbs[] = ['href' => BUNZ_HTTP_DIR,
        'title' => BUNZ_PROJECT_TITLE,
        'icon' => 'icon-home'];
    $this->breadcrumbs[] = ['href' => '#',
        'title' => '',
        'icon' => 'icon-bomb'];
    require BUNZ_TPL_DIR .'header.inc.php';
}

if(isset($_ERROR))
{
?>
    <article class="alert-base container">
        <header><h1>PHP Error</h1></header>
<?php
    foreach($_ERROR as $thing => $problem)
        echo '<section class="section shade-text"><strong>',$thing,':</strong> <pre>',$problem,'</pre></section>',"\n";
?>
    </article>
<?php
    exit;
}
?>
<article class="container section">
    <img src="<?= BUNZ_HTTP_DIR ?>/tpl/material/assets/css/img/00.jpg" alt="/(x.x)\" class="left z-depth-5 circle responsive-img">
    <hgroup class="section shade-text z-depth-5">
        <h1 class="alert-text">HTTP/1.1 404: Duck Season</h1>
        <h2 class="secondary-text">That's an error</h2>
        <h2 class="secondary-text">That's all we know</h2>
        <h6><em>I hope that isn't copyrighted<em></h6>
    </hgroup>
</article>

<?php
if(defined('BUNZ_TPL_DIR'))
    require BUNZ_TPL_DIR .'footer.inc.php';
