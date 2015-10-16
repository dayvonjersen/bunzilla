<?php
$pageTitle = 'cpanel';
$background = 'primary-base';
require_once BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
<script src='<?= BUNZ_JS_DIR ?>list.min.js'></script>
<script>
//
// list.js!
//
document.body.onload = function(){
    statusList = new List('statusList', {valueNames: [
        'status_usage',
        'status_title',
        'status_color',
        'status_icon'
    ]});
    tagList = new List('tagList', {valueNames: [
        'tag_usage',
        'tag_title',
        'tag_color',
        'tag_icon'
    ]});
    priorityList = new List('priorityList', {valueNames: [
        'priority_usage',
        'priority_title',
        'priority_color',
        'priority_icon',
        'priority_id'
    ]});
};

</script>
<article class=" primary-text">
    <header class="row">

        <section class="col s12">
            <div class="row secondary-text">
                <ul class="tabs">
                    <li class="tab col s3" title="Statistics">
                        <a class="icon-chart shade-text" href="#statistics">
                            <span class="hide-on-med-and-down">Statistics</span></a></li>
                    <li class="tab col s3" title="Cron Settings">
                        <a class="icon-cog shade-text" href="#cron">
                            <span class="hide-on-med-and-down">Cron Settings</span></a></li>
                    <li class="tab col s1" title="Categories">
                        <a class="icon-list-dl primary-base" href="#categories">
                            <span class="hide-on-med-and-down">Categories</span></a></li>
                    <li class="tab col s1" title="Statuses">
                        <a class="icon-pinboard shade-base" href="#statuses">
                            <span class="hide-on-med-and-down">Statuses</span></a></li>
                    <li class="tab col s1" title="Tags">
                        <a class="icon-tags secondary-base" href="#tags">
                            <span class="hide-on-med-and-down">Tags</span></a></li>
                    <li class="tab col s1" title="Priorities">
                        <a class="icon-attention alert-base" href="#priorities">
                            <span class="hide-on-med-and-down">Priorities</span></a></li>
                    <li class="tab col s1" title="Purge Reports">
                        <a class="icon-delete danger-base" href="#purge">
                            <span class="hide-on-med-and-down">Purge</span></a></li>
                    <li class="tab col s1"  title="Export Database">
                        <a class="icon-database success-base" href="#export">
                        <span class="hide-on-med-and-down">Export</span></a></li>
                </ul>
<?php
/**
 * TODO (possibly): think of a way to load these dynamically with ajax 
 */
include 'statistics.inc.php';
include 'cron.inc.php';
include 'categories.inc.php';
include 'statuses.inc.php';
include 'tags.inc.php';
include 'priorities.inc.php';
include 'purge.inc.php';
include 'export.inc.php';
?>
            </div>
        </section>
    </header>
</article>
<?php
require_once BUNZ_TPL_DIR . 'footer.inc.php';
