<?php
//
// index page : category listing and project overview
//
require BUNZ_TPL_DIR . 'header.inc.php';

$stats = [];
foreach($this->data['stats'] as $category => $stuff)
{
    foreach($stuff as $key => $value)
    {
        if(!isset($stats[$key]))
            $stats[$key] = 0;
        if($key == 'last_activity')
            $stats[$key] = max($stats[$key],$value);
        else
            $stats[$key] += $value;
    }
}
$stats['percent_resolved'] = $stats['total_issues'] > 0 
    ? round(($stats['total_issues'] - $stats['open_issues'])/$stats['total_issues'],2)*100 . '%' 
    : 'n/a';
//
// About the project (title, version, mission statement...)
// Configure these in res/settings.ini
//
?>
<style>
.full-height {
    height: 100vh;
}
</style>
<div class="row">
    <article class="col s12 m4 l2 primary-base full-height">
        <header class="section no-pad-bot row">
            <section class="col s8 section primary-base z-depth-4 waves-effect right-align "
                     
                     onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?= BUNZ_PROJECT_WEBSITE ?>'; }})(event);">
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h5><a href="<?= BUNZ_PROJECT_WEBSITE ?>"><?= BUNZ_PROJECT_WEBSITE ?></a></h5>
            </section>
            <section class="col s4 left-align">
                <p class="z-depth-3 section primary-lighten-3 center"><span class="h2"><?= BUNZ_PROJECT_VERSION ?></span></p>
            </section>
        </header>
        <section class="section row no-pad-top ">
            <div class="z-depth-2 section no-pad-bot primary-text center-align">
               <em class="tooltipped" data-tooltip="mission statement">&ldquo;<em><?= BUNZ_PROJECT_MISSION_STATEMENT ?></em>&rdquo;</em>
               <div class="divider" style="margin-top: 1em"></div>
               <div class="h6 shade-text right-align"><?= BUNZ_SIGNATURE, ' v', BUNZ_VERSION ?>&emsp;</div>
            </div>
        </section>
<?php
//
// Tag Cloud, other things might appear here later
//
if(count(Cache::read('tags')))
{
?>
        <article class="section no-pad-top  icon-tags">
            <section class="shade-text">
                <div class="section secondary-text z-depth-3"
                     style="overflow-y: auto; max-height: 28em; text-align: justify"
                     id="tagCloud" 
                     data-uri="<?= BUNZ_HTTP_DIR ?>search">Loading...</div>
            </section>
        </article>
        <script src="<?= BUNZ_JS_DIR ?>tagCloud.js"></script>
<?php
}
?>
        <article class="section  no-pad-top  icon-search">
                <form  class="section shade-text z-depth-3" action="<?= BUNZ_HTTP_DIR ?>search" method="get">
                    <div class="input-field">
                        <input type="text" name="q">
                        <span class="material-input"></span>
                    </div>
                    <div class="input-field">
                    <button type="submit" style="display: block; width: 100%"
                            class="btn secondary-darken-1 waves-effect "
                        ><i class="icon-search"></i> Search</button>
                    </div>
                </form>
        </article>
        <article class="section no-pad-top icon-chart">
            <section class="row center">
<?php
//
// "Card" body (stats)
//
        if($stats['last_activity'])
        {
?>
                <p class="section col primary-lighten-1 s3 z-depth-2 tooltipped" 
                          data-position="right" data-tooltip="<?= $stats['open_issues'] ?> open issue<?= 
                            $stats['open_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-unlock"></i><br/><?= $stats['open_issues'] ?></span>
                </p>
                <p class="section col primary-lighten-2 s3 z-depth-3 tooltipped"
                          data-position="right" data-tooltip="percentage resolved: <?= $stats['percent_resolved'] ?>">
                    <span><i class="icon-ok"></i><br/><?= $stats['percent_resolved']?></span>
                </p>
                <p class="section col primary-lighten-3 s3 z-depth-4 tooltipped" 
                          data-position="left" data-tooltip="<?= $stats['total_issues'] ?> total issue<?= 
                            $stats['total_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-doc-text-inv"></i><br/><?= $stats['total_issues'] ?></span>
                </p>
                <p class="section col primary-lighten-4 s3 z-depth-5 tooltipped" 
                          data-position="left" data-tooltip="<?= $stats['unique_posters'] ?> unique poster<?= 
                            $stats['unique_posters'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-users"></i><br/><?= $stats['unique_posters'] ?></span>
                </p>
            </section>
        </article>
        <article class="section no-pad-top icon-time">
            <section class="row center">
                <p class="section col primary-lighten-5 s12 z-depth-5">
                    <span><i class="icon-time"></i><?= datef($stats['last_activity'],'top') ?></span>
                </p>
<?php
        } else {
?>
                <p class="section col primary-lighten-5 s12 right-align"><em>Nothing to see here!</em></p>
<?php
    }
?>
            </section>
        </article>
    </article>
<?php
//
// Category Listing
//
if(empty($this->data['categories']))
{
?>
        <div class="full-height col l10 shade-lighten-5 center-align valign-wrapper">
            <h4 class=" alert-base section icon-attention ">No categories have been created yet!</h4>
            &emsp;
            <a href="/cpanel#categories" class="btn btn-floating btn-large secondary-base waves-effect z-depth-5 tooltipped" data-tooltip="Go make one!"><i class=" icon-plus"></i></a>
        </div>
<?php
} else {
    $i = 1; 
    $cards_per_row = 6; // use this to create how many of these cards per row
    $end = count($this->data['categories'])-1;
    $index = 0;
    foreach($this->data['categories'] as $k => $cat)
    {
        $stats = $this->data['stats'][$cat['id']];
        $stats['percent_resolved'] = $stats['total_issues'] > 0 
            ? round(($stats['total_issues'] - $stats['open_issues'])/$stats['total_issues'],2)*100 . '%' 
            : 'n/a';
        echo $i == 0 ? '<div class="row">' : ''
//
// "Card" heading 
//
?>
        <article class="col s12 m<?= $index == $end ? (int)$i/2 : 4?> l<?= $index++ == $end ? 12-$i*2 : 2?> full-height category-<?= $cat['id'] ?>-base">
            <div style="height: 100%; overflow-y: auto">
            <header class="section no-pad-bot row">
                <section class='section col s12 no-pad-bot z-depth-5 category-<?= $cat['id'] ?>-base waves-effect' onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>'; }})(event);">
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="btn-large waves-effect btn btn-floating z-depth-5 right category-<?= $cat['id'] ?>-base" title="submit new"><i class="icon-plus"></i></a>
                    <h2><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
                    <h6><?= $cat['caption'] ?></h6>
                </section>
            </header>
<?php for($zig = 0; $zig < min($stats['open_issues'],5); $zig++) { ?>
            <article class="section no-pad">
              <div class="section " style="margin: 1em 0">
                        <p class=" icon-doc-text-inv">This is an example!</p>
                        <div class="left">
                            <?= priority(array_rand($this->data['priorities'])) ?>
                            <?= tag(array_rand($this->data['tags']),1) ?>
                            <?= tag(array_rand($this->data['tags']),1) ?>
                        </div>
                        <div class="right" style="margin-top:-26px">
                            <button title="Mark as completed!" class="btn btn-floating z-depth-3 transparent success-text "><i class="icon-ok"></i></button>
                        </div>
                <blockquote class="category-<?= $cat['id'] ?>-text" style="margin:0; padding:0; width: 100%">
                    <p style="padding: 1em">Don't take it too seriously, you silly boys ~_~</p></blockquote>
                        <div class="left">
                            <button class="btn btn-floating z-depth-3 secondary-lighten-3"><i class="icon-chat"></i></button>&emsp;&emsp;&emsp;
                            <button class="btn right z-depth-3 category-<?=$cat['id']?>-lighten-3">View &rarr;</button>
                        </div>
                        <div>
                            <?= status(array_rand($this->data['statuses']),1) ?>
                        </div>
            </div></article>
<?php } ?>
            <article class="section icon-chart">
                <section class=" row center">
<?php
//
// "Card" body (stats)
//
        if($stats['last_activity'])
        {
?>
                <p class="section col category-<?= $cat['id'] ?>-lighten-1 s3 z-depth-2 tooltipped" 
                          data-position="right" data-tooltip="<?= $stats['open_issues'] ?> open issue<?= 
                            $stats['open_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-unlock"></i><br/><?= $stats['open_issues'] ?></span>
                </p>
                <p class="section col category-<?= $cat['id'] ?>-lighten-2 s3 z-depth-3 tooltipped"
                          data-position="right" data-tooltip="percentage resolved: <?= $stats['percent_resolved'] ?>">
                    <span><i class="icon-ok"></i><br/><?= $stats['percent_resolved']?></span>
                </p>
                <p class="section col category-<?= $cat['id'] ?>-lighten-3 s3 z-depth-4 tooltipped" 
                          data-position="left" data-tooltip="<?= $stats['total_issues'] ?> total issue<?= 
                            $stats['total_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-doc-text-inv"></i><br/><?= $stats['total_issues'] ?></span>
                </p>
                <p class="section col category-<?= $cat['id'] ?>-lighten-4 s3 z-depth-5 tooltipped" 
                          data-position="left" data-tooltip="<?= $stats['unique_posters'] ?> unique poster<?= 
                            $stats['unique_posters'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-users"></i><br/><?= $stats['unique_posters'] ?></span>
                </p>
            </section>
            </article>
            <article class="section no-pad-top icon-time">
                <section class="row center ">
                <p class="section col category-<?= $cat['id'] ?>-lighten-5 s12 z-depth-5">
                    <span><i class="icon-time"></i><?= datef($stats['last_activity'],'top') ?></span>
                </p>
<?php
        } else {
?>
                <p class="section col category-<?=$cat['id']?>-lighten-5 s12 right-align"><em>Nothing has been posted here yet!</em></p>
<?php
    }
?>
            </section>
        </article>
        </div>
    </article>
<?php
        if(++$i == $cards_per_row || $end == $k)
        {
            echo '</div>';
            $i = 0;
        } 
    }
}
?>
</div>
<?php

require BUNZ_TPL_DIR . 'footer.inc.php';
