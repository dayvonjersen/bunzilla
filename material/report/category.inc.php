<?php
//
// reports by category : this isn't a message board, honest!
//
$cat = $this->data['categories'][$this->data['category_id']];
$pageTitle = $cat['title'];

require BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src="/bunzilla/material/sorttable.js"></script>
<div class="category-<?= $cat['id'] ?>-base" style="height: 100%;">
<!--
    about:category
-->
<div class="row">
    <div class="col s12">
        <article>
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
        <div class="z-depth-5 yellow section flow-text icon-attention center-align blue-text">Nothing here yet! <a class="btn-flat icon-plus" href="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>">Submit Something!</a></div>
<?php
} else {

?>
        <!--
            kill me now
        -->
        <table class="sortable container z-depth-5">
            <thead>
                <tr>
                    <th class="gone" id="sortClosed"></th>
                    <th class="gone" id="sortSubject"></th>
                    <th class="gone" id="sortStatus"></th>
                    <th class="gone" id="sortComments"></th>
                    <th class="gone" id="sortSubmitted"></th>
                    <th class="gone" id="sortLastActive"></th>
                    <td class="sorttable_nosort category-<?=$cat['id']?>-darken-1">
                        <div id="sorttable_override" class="right-align">
                            <span>
                                <a data-th="sortClosed"
                                   class="btn-flat waves-effect waves-light icon-lock"
                                   href="javascript:void(0);"
                                >open/closed<span id="ico-sortClosed" class="icon-sort"></span></a>
                            </span>
                            <span>
                                <a data-th="sortSubject"
                                   class="btn-flat waves-effect waves-light icon-doc-text-inv"
                                   href="javascript:void(0);"
                                >subject<span id="ico-sortSubject" class="icon-sort"></span></a>
                            </span>
                            <span>
                                <a data-th="sortComments"
                                   class="btn-flat waves-effect waves-light icon-chat"
                                   href="javascript:void(0);"
                                >comments<span id="ico-sortComments" class="icon-sort"></span></a>
                            </span>
                            <span>
                                <a data-th="sortSubmitted"
                                   class="btn-flat waves-effect waves-light icon-time"
                                   href="javascript:void(0);"
                                >submitted<span id="ico-sortSubmitted" class="icon-sort"></span></a>
                            </span>
                            <span>
                                <a data-th="sortLastActive"
                                   class="btn-flat waves-effect waves-light icon-time"
                                   href="javascript:void(0);"
                                >last activity<span id="ico-sortLastActive" class="icon-sort"></span></a>
                            </span>
                            <span>
                                <a data-th="sortStatus"
                                   class="btn-flat waves-effect waves-light icon-pinboard"
                                   href="javascript:void(0);"
                                >status<span id="ico-sortStatus" class="icon-sort"></span></a>
                            </span>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
<?php
    $tidy = extension_loaded('tidy') ? new tidy() : false;
    foreach($this->data['reports'] as $i => $report)
    {
        $report['last_active'] = max($report['time'],$report['updated_at'],$report['edit_time']);

//
// awful hax
//
?>
                <tr id="report-<?= $report['id'] ?>" class="z-depth-4">
                    <td class="gone" sorttable_customkey="<?= $report['closed'] ?>"></td>
                    <td class="gone" sorttable_customkey="<?= strtolower(preg_replace('/\s+/','',$report['subject'])) ?>"></td>
                    <td class="gone" sorttable_customkey="<?= strtolower($this->data['statuses'][$report['status']]['title']) ?>"></td>
                    <td class="gone" sorttable_customkey="<?= $report['comments'] ?>"></td>
                    <td class="gone" sorttable_customkey="<?= date('YmdHis', $report['time']) ?>"></td>
                    <td class="gone" sorttable_customkey="<?= date('YmdHis', $report['last_active']) ?>"></td>

                    <td>
                        <div class="collapsible">

                            <div class="collapsible-header no-select category-<?= $cat['id'] ?>-lighten-3 z-depth-5">
                                <i class="icon-<?=$report['closed'] ? 'lock' : 'doc-text-inv'?> category-<?=$cat['id']?>-text"></i>
                                <span class="hide-on-small-only"><?= $report['subject'] ?>
                                    <span class="right"><?= status($report['status']) ?></span>
                                </span>
                                <span class="hide-on-med-and-up">
                                <?= 
                                    substr($report['subject'],0,15),
                                    strlen($report['subject']) > 15 ? '...' : '' 
                                ?>
                                    <span class="right"><?= status($report['status']) ?></span>
                                </span>

                                <span class="badge right icon-time" title="last active"><?= datef($report['last_active']) ?></span>
                                <span class="badge right icon-history" title="submitted at"><?= datef($report['time']) ?></span> 
                                <span class="badge right icon-chat blue-text" title="comments"><?= $report['comments'] ?></span>

                                <!-- float: right makes me confus @_@ -->
                            </div>

                            <div class="collapsible-body category-<?= $cat['id'] ?>-lighten-3 z-depth-5">
                                <div class="hide-on-med-and-up">
                                    <p><strong><?= $report['subject'] ?></strong></p>
                                    <p class="icon-time"><?= datef($report['last_active']) ?></p>
                                </div>
                                <blockquote class="icon-article-alt"><?= 
$report['edit_time'] ? '<strong>**EDIT** '.datef($report['edit_time']).'</strong><br>' : '' 
?>
<?php
        if(isset($report['preview_text']))
        {
            if(strlen(strip_tags($report['preview_text'])) > 100)
            {
                if($tidy)
                {            
                    $report['preview_text'] = substr($report['preview_text'],0,100);
                    $report['preview_text'] = $tidy->repairString($report['preview_text'],["doctype" => "omit","show-body-only" => "yes"]);
                } else {
                    $report['preview_text'] = substr(strip_tags($report['preview_text']),0,100);
                }
                $report['preview_text'] .= '. . .';
            }
            echo $report['preview_text'];
        }  ?>
                                    <p><a href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id'],'?material'?>">Keep reading &rarr;</a></p>
                                </blockquote>
                            </div>
<?php
        if(!empty($report['tags']))
        {
            echo '<p class="icon-tags">';
            foreach($report['tags'] as $tag)
                echo tag($tag[0],0);
            echo '</p>';
        }
?>
                        </div>
                    </td>
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
