<?php
//
// reports by category : this isn't a message board, honest!
//
$pageTitle = $this->data['category']['title'];
$bread = [
    $pageTitle => ['href' => BUNZ_HTTP_DIR.$_GET['url'],
        'icon' => $this->data['category']['icon'],
        'color' => $this->data['category']['color']
    ],
];

require BUNZ_TPL_DIR . 'header.inc.php';

$cat = $this->data['categories'][(int)$id];
?>
<div class="container category-<?= $cat['id'] ?>-base">
<!--
    about:category
-->
<div class="row">
    <div class="col s12">
        <article class='z-depth-1 category-<?= $cat['id'] ?>-lighten-1'>
            <div class="row">
                <!-- 
                    title 
                -->
                <section class='section col s8 z-depth-5 category-<?= $cat['id'] ?>-text'>
                    <h4 class="category-<?= $cat['id'] ?>-text <?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h4>
                    <h6><?= $cat['caption'] ?></h6>
                </section>
                <!--
                    actions
                -->
                <section class="col s4 right-align">
                    
<?php
if($this->auth())
{
?>
                    <a href="<?=BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id']?>" 
                       class="btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-pencil-alt"></i></a>
<?php
}
?>
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" 
                       class="btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-plus"></i></a>
                </section>
        </article>
    </div>
</div>
<?php
if(empty($this->data['reports']))
{
?>
        <!--
            consistency++
        -->
        <div class="z-depth-5 yellow section flow-text icon-attention center-align blue-text">Nothing here yet! <a class="btn-flat icon-right-open-mini" href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>">Got a submission?</a></div>
<?php
} else {

?>
        <!--
            kill me now
        -->
        <table class="striped hoverable category-<?= $cat['id'] ?>-lighten-5">
            <thead>
                <tr>
                    <th>status</th>
                    <th>subject</th>
                    <th>comments</th>
                    <th>submitted</th>
                    <th>last activity</th>
                </tr>
            </thead>
            <tbody>
<?php
    foreach($this->data['reports'] as $report)
    {
?>
                <tr>
                    <td><span class="z-depth-5 status-2 icon-bug">AHHH</span></td>
                    <td>
                        <a href="#"><i class="icon-<?=$report['closed'] ? 'lock' : 'doc-text-inv'?>"></i><?= $report['subject'] ?></a><br><blockquote><i class="icon-article-alt"></i>blablabla</blockquote>
                        <p>
                        <span class=" z-depth-3 tag-1 icon-bomb" title="">dabomb</span>
                        <span class=" z-depth-3 tag-1 icon-bomb" title="">dabomb</span>
                        </p>
                    </td>
                    <td>9001</td>
                    <td><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$report['time']) ?></td>
                    <td><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$report['time']) ?></td>
                </tr>
<?php
    }
?>
            </tbody>
        </table>
<?php
}
?>
</div>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
