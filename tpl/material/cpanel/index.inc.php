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

                <section id="statistics">
                    <img src="/pfsc/picturesforsadchildren/00000263.png" alt="[insert pointless graph here]"/>
                </section>

                <section id="changelog">
                    <nav>
                        <a href="#" class="dropdown-button btn" data-activates="changelog-exports">Export as</a>
                        <ul id="changelog-exports" class="dropdown-content">
                            <li><a href="#" class="icon-html">HTML</a></li>
                            <li><a href="#" class="icon-list-dl">Markdown</a></li>
                            <li><a href="#" class="icon-ol">Plain text</a></li>
                        </ul>
                        <a href="#" class="dropdown-button btn" data-activates="changelog-versions">View version</a>
                        <ul id="changelog-versions" class="dropdown-content">
                            <li><a href="#">All</a></li>
                            <li><a href="#">1.0</a></li>
                            <li><a href="#">2.0</a></li>
                            <li><a href="#">...</a></li>
                        </ul>
                    </nav>
<form>
<h1 class="icon-pencil-alt">Edit changelog</h1>
<ol>
<?php
foreach($this->data['changelog'] as $ln)
{
    $ln = current($ln);
    echo '<li class="input-field"><input type="text" value="',htmlspecialchars($ln),'"/><span class="material-input"></span></li>';
}
?>
</ol>
                    <p class="input-field"><input type="text" placeholder="Add a line..."/><span class="material-input"></span><button class="btn btn-floating left" type="submit"><i class="icon-plus"></i></button></p>
</form>
                </section> 
<?php
include 'categories.inc.php';
?>
                <section id="statuses">
<pre><?= print_r($this->data['statuses'],1) ?></pre>
                </section>

                <section id="tags">
<pre><?= print_r($this->data['tags'],1) ?></pre>
                </section>

                <section id="priorities">
<pre><?= print_r($this->data['priorities'],1) ?></pre>
                </section>
            </div>
        </section>
    </header>
</article>
<?php
require_once BUNZ_TPL_DIR . 'footer.inc.php';
