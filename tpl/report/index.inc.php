<?php
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='card'>
            <header class='box'>
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h6>version <?= BUNZ_PROJECT_VERSION ?></h6>
            </header>
            
            <p class='box' title="mission statement"><?= BUNZ_PROJECT_MISSION_STATEMENT ?></p>
        </article>
<?php
if(empty($this->data['categories']))
{
?>
        <div class='flash'>No categories have been created yet! <a href="<?= BUNZ_HTTP_DIR ?>admin">Go make one!</a></div>
<?php
}
foreach($this->data['categories'] as $cat)
{
?>
        <article style="background: #<?= $cat['color'] ?>" class='card'>
            <header class="box">
                <h2 class=""><a href="<?= BUNZ_HTTP_DIR,'report/category/',$cat['id'] ?>" class='pure-button <?=$cat['icon']?>' style="color: #<?= $cat['color'] ?> !important"><?= $cat['title'] ?></a></h2>
            </header>

                <p class='box' title='caption'><small><?= $cat['caption'] ?></small></p>
                <p class='box' title='open issues'><?= selectCount('reports','closed = 0 AND category = '.$cat['id']) ?></p>
        </article>
<?php
}
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
