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

$cat = $this->data['categories'][$this->data['category_id']];
?>
<script src="/bunzilla/material/sorttable.js"></script>
<div class="category-<?= $cat['id'] ?>-base">
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
        <table class="sortable striped hoverable category-<?= $cat['id'] ?>-lighten-5">
            <thead>
                <tr>
                    <th style="width: 10%">status<i class="icon-up-open-mini"></i><i class="icon-down-open-mini"></i></th>
                    <th style="width: 50%">subject<i class="icon-up-open-mini"></i><i class="icon-down-open-mini"></i></th>
                    <th style="width: 10%">comments<i class="icon-up-open-mini"></i><i class="icon-down-open-mini"></i></th>
                    <th style="width: 15%">submitted<i class="icon-up-open-mini"></i><i class="icon-down-open-mini"></i></th>
                    <th style="width: 15%">last activity<i class="icon-up-open-mini"></i><i class="icon-down-open-mini"></i></th>
                </tr>
            </thead>
            <tbody>
<?php
    $tidy = extension_loaded('tidy') ? new tidy() : false;
    foreach($this->data['reports'] as $report)
    {
?>
                <tr id="report-<?= $report['id'] ?>">
                    <td><?= status($report['status']) ?></td>
                    <td>
<?php
        if(isset($report['preview_text']))
        {
            if(strlen(strip_tags($report['preview_text'])) > 100)
            {
                if($tidy)
                {            
                    $report['preview_text'] = substr($report['preview_text'],0,100);
                    $report['preview_text'] = $tidy->repairString($report['preview_text']);
                } else {
                    $report['preview_text'] = substr(strip_tags($report['preview_text']),0,100);
                }
                $report['preview_text'] .= '. . .';
            }

?>
                        <div class="collapsible">
                            <h6 class="collapsible-header category-<?= $cat['id'] ?>-text"><i class="icon-<?=$report['closed'] ? 'lock' : 'doc-text-inv'?>"></i><?= $report['subject'] ?></h6>
                            <div class="collapsible-body">
                                <blockquote class="icon-article-alt"><?= 
$report['edit_time'] ? '<strong>**EDIT** '.datef($report['edit_time']).'</strong><br>' : '', 
$report['preview_text'] ?></blockquote>
                                <p><a href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id'],'?material'?>">Keep reading &rarr;</a></p></blockquote>
                            </div>
                        </div>
<?php
        } else {
?>
                        <a class="h6 icon-<?=$report['closed'] ? 'lock' : 'doc-text-inv'?>" href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id'],'?material'?>"><?= $report['subject'] ?></a>
<?php
        }
        if(!empty($report['tags']))
        {
            echo '<p class="icon-tags">';
            foreach($report['tags'] as $tag)
                echo tag($tag[0],0);
            echo '</p>';
        }
?>
                    </td>
                    <td><i class="icon-chat"></i> <?= $report['comments'] ?></td>
                    <td><?= datef($report['time']) ?></td>
                    <td><?= datef(max($report['time'],$report['updated_at'],$report['edit_time'])) ?></td>
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
