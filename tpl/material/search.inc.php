<?php
$pageTitle = 'Advanced Search';
$background = 'secondary-base';
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<header class="row">
    <form action="<?= BUNZ_HTTP_DIR ?>search" method="get">
        <section class="section col s12 shade-text z-depth-5">
            <div class="input-field">
                <input type="text" id="searchBox" name="q" value="<?= filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING) ?>"/>
                <label for="searchBox">Search Terms...</label>
                <span class="material-input"></span>
            </div>
            <ol class="collapsible">
                <li>
                    <div class="section no-pad icon-cog-alt collapsible-header secondary-text no-select waves-effect">
                        Advanced...</div>

                    <div class="collapsible-body section">
                        <div class="divider" style="margin-bottom: 1em"></div>
                        <h2>nothing here yet</h2>
                    </div>
                </li>
            </ol>
            <div class="center ">
                <button type="submit"
                        class="btn secondary-darken-1">
                    <i class="icon-search"></i>
                    Search
                </button>
            </div>
        </section> 
    </form>
</header>
<?php
if(isset($this->data['test']['results']))
{
?>
<main class="section">
    <header class="section shade-text">
<?php
if(empty($this->data['test']['results']))
{
?>
        <h1>No results found!</h1>
    </header>
<?php
} else {
?>
        <h1>Results for <q class="secondary-text"><?= empty($this->data['test']['term']) ? filter_input(INPUT_GET,'q',FILTER_SANITIZE_STRING) : urldecode($this->data['test']['term']) ?></q></h1>
        <h5>(<?= count($this->data['test']['results']) ?> results; <?= $this->data['test']['time'] ?>s)</h5>
    </header>
    <section class="section z-depth-5 shade-text">
    <ol> 
<?php
foreach($this->data['test']['results'] as $report)
{
?>
    <li>
        <a class="icon-doc-text-inv" href="<?= BUNZ_HTTP_DIR ?>report/view/<?= $report['id']?>"><?= $report['subject'] ?></a>
    </li>
<?php
}
?>
    </ol>
    </section>
<?php
}
?>
</main>
<?php
}
require BUNZ_TPL_DIR . 'footer.inc.php';
