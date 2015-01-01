<?php
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='box'>
            <header>
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h6>version <?= BUNZ_PROJECT_VERSION ?></h6>
            </header>
            
            <p><?= BUNZ_PROJECT_MISSION_STATEMENT ?></p>
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
        <article style="background: #<?= $cat['color'] ?>">
            <section class="box">
                <h2 class="<?=$cat['icon']?>"><a href="<?= BUNZ_HTTP_DIR,'report/category/',$cat['id'] ?>"><?= $cat['title'] ?></a></h2>
                <p><small><?= $cat['caption'] ?></small></p>
                <p><strong>Open issues:</strong> <?= selectCount('reports','closed = 0 AND category = '.$cat['id']) ?></p>
            </section>
        </div>
<?php
}
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
