<?php
$pageTitle = 'Pick a Category';
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article>
            <h1><?= $pageTitle ?></h1>
<?php
if(empty($this->data['categories']))
    echo '<p>Oops! No categories here! 
<a href="',BUNZ_HTTP_DIR,'/admin">Go create one!</a></p>',"\n";
else {
    foreach($this->data['categories'] as $cat)
    {
?>
            <p style='background: #<?= $cat['color'] ?>'>
                <a href="<?= $_SERVER['PHP_SELF'],'/category/',$cat['id'] ?>" class='<?= $cat['icon'] ?>'><?=$cat['title']?></a><br/>
                <small><?=$cat['caption']?></small>
            </p>
<?php
    }
}

require BUNZ_TPL_DIR . 'footer.inc.php';
