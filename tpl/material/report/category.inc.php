<?php
//
// reports by category : this isn't a message board, honest!
//
$cat        = $this->data['categories'][$this->data['category_id']];
$pageTitle  = $cat['title'];
$background = 'category-'.$cat['id'].'-base';

require BUNZ_TPL_DIR . 'header.inc.php';

//
// category banner
//
?>
<div class="row">
<?php
if($this->auth() && $_SERVER['HTTP_HOST'] === 'meta.bunzilla.ga' && $cat['id'] != 16)
{
?>
    <div class="col s12 section h5 alert-text center">Please report any issues you encounter in <a href="/report/category/16">Bug Reports</a>.</div>
<?php
}
?>
    <div class="col s12">
        <article>
            <div class="row">
                <section class='section col s12 z-depth-5 category-<?=$cat['id']?>-base'>
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" 
                       class="right btn-large waves-effect btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="icon-plus"></i></a>
<?php
// edit category link for admins
if($this->auth())
{
?>
                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/category/',$cat['id']?>" 
                       class="right btn-small z-depth-4 waves-effect btn btn-floating success-base" 
                       title="edit category"><i class="icon-pencil-alt"></i></a>
<?php
}
?>
                    <a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>?rss" 
                       class="right btn-small z-depth-3 btn btn-floating waves-effect waves-orange" 
                       style="background: #fff; color: #f86e00;"
                       title="subscribe!"><i class="icon-rss-squared"></i></a>

                    <h2 class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></h2>
                    <h6><?= $cat['caption'] ?></h6>
                </section>
        </article>
    </div>
</div>
<?php
// message to show for empty categories
if(empty($this->data['reports']))
{
?>
        <article class="container section">
            <div class="z-depth-5 shade-text section">
                <h1 class="icon-attention secondary-text">Nothing here yet!</h1>
                <a class="btn-flat waves-effect icon-plus" 
                   href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>">Submit a report to <?= $cat['title']?></a>
            </div>
        </article>
<?php
} else {

echo pagination( BUNZ_HTTP_DIR.'report/category/'.$cat['id'], 
        selectCount('reports','category = '.$cat['id']),
        $this->data['page_offset']
    );

// abstracted this out to use it elsewhere
$pageMode = 'category';
require 'listing.phtml';

echo pagination( BUNZ_HTTP_DIR.'report/category/'.$cat['id'], 
        selectCount('reports','category = '.$cat['id']),
        $this->data['page_offset']
    );
}

// I'm glad it's over.
require BUNZ_TPL_DIR .'diffModal.html';
require BUNZ_TPL_DIR .'footer.inc.php';
