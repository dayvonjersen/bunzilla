<?php
$pageTitle = 'Pick a Category';
$bread  = [
    $pageTitle => BUNZ_HTTP_DIR . $_GET['url']
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='box'>
            <h1><?= $pageTitle ?></h1>
<?php
if(empty($this->data['categories']))
    echo '<p>Oops! No categories here! 
<a href="',BUNZ_HTTP_DIR,'/admin">Go create one!</a></p>',"\n";
else {
    foreach($this->data['categories'] as $cat)
    {
?>
 <article style="background: #<?= $cat['color'] ?>" class='card'>
            <header class="box">
                <h2 class=""><a href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>" class='pure-button <?=$cat['icon']?>' style="color: #<?= $cat['color'] ?> !important"><?= $cat['title'] ?></a></h2>
            </header>

                <p class='box' title='caption'><small><?= $cat['caption'] ?></small></p></article>
<?php
    }
}

require BUNZ_TPL_DIR . 'footer.inc.php';
