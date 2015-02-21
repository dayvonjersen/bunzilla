<?php
$pageTitle = 'cpanel';
$background = 'primary-base';
require_once BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
<article class=" primary-text">
    <header class="row">
        <h1><?= BUNZ_PROJECT_TITLE, ' :: ', $pageTitle ?></h1>

        <section class="col s12">
            <div class="row secondary-text">
                <ul class="tabs">
                    <li class="tab col s2"><a class="icon-chart" href="#statistics">Statistics</a></li>
                    <li class="tab col s2"><a class="icon-doc-text-inv" href="#changelog">Changelog</a></li>
                    <li class="tab col s2"><a class="icon-cog" href="#cron">Cron Settings</a></li>

                    <li class="tab col s1"><a class="icon-list-dl" href="#categories">Categories</a></li>
                    <li class="tab col s1"><a class="icon-pinboard" href="#statuses">Statuses</a></li>
                    <li class="tab col s1"><a class="icon-tags" href="#tags">Tags</a></li>
                    <li class="tab col s1"><a class="icon-attention" href="#priorities">Priorities</a></li>

                    <li class="tab col s1"><a class="icon-delete" href="#purge">Purge</a></li>
                    <li class="tab col s1"><a class="icon-database" href="#export">Export</a></li>
                </ul>
<?php
/**
 * TODO (possibly): think of a way to load these dynamically with ajax 
 */
include 'statistics.inc.php';
include 'changelog.inc.php';
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
