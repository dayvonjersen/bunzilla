<?php
$pageTitle = 'cpanel';
$background = 'primary-base';
require_once BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
<article class=" primary-text">
    <header class="row">

        <section class="col s12">
            <div class="row secondary-text">
                <ul class="tabs">
                    <li class="tab col s3"><a class="icon-chart shade-darken-2" href="#statistics"><span class="hide-on-med-and-down">Statistics</span></a></li>
                    <li class="tab col s3"><a class="icon-cog shade-darken-4" href="#cron"><span class="hide-on-med-and-down">Cron Settings</span></a></li>

                    <li class="tab col s1"><a class="icon-list-dl primary-base" href="#categories"><span class="hide-on-med-and-down">Categories</span></a></li>
                    <li class="tab col s1"><a class="icon-pinboard shade-base" href="#statuses"><span class="hide-on-med-and-down">Statuses</span></a></li>
                    <li class="tab col s1"><a class="icon-tags secondary-base" href="#tags"><span class="hide-on-med-and-down">Tags</span></a></li>
                    <li class="tab col s1"><a class="icon-attention alert-base" href="#priorities"><span class="hide-on-med-and-down">Priorities</span></a></li>

                    <li class="tab col s1"><a class="icon-delete danger-base" href="#purge"><span class="hide-on-med-and-down">Purge</span></a></li>
                    <li class="tab col s1"><a class="icon-database success-base" href="#export"><span class="hide-on-med-and-down">Export</span></a></li>
                </ul>
<?php
/**
 * TODO (possibly): think of a way to load these dynamically with ajax 
 */
include 'statistics.inc.php';
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
