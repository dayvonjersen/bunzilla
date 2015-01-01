<?php
$pageTitle = 'Pick a Category';
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
            <blockquote class='box'>
                <p style='border: 2px #<?= $cat['color'] ?> groove'>
                    <a href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>" class='<?= $cat['icon'] ?>' style='display: block; border: 0; color: #<?= $cat['color']?>'><?=$cat['title']?></a><br/>
                    <small><?=$cat['caption']?></small>
                </p>
            </blockquote>
<?php
    }
}

require BUNZ_TPL_DIR . 'footer.inc.php';
