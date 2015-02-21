<?php
//
// reports by category : this isn't a message board, honest!
//
$cat = $this->data['categories'][$this->data['category_id']];
$pageTitle = $cat['title'];
$background="category-{$cat['id']}-lighten-5";
//'transparent';

require BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
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
                <section class='section no-pad-top col s12 z-depth-5 category-<?=$cat['id']?>-base'>

                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-plus"></i></a>
<?php
if($this->auth())
{
?>
                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/category/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-pencil-alt"></i></a>
<?php
}
?>
                    <h2 class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></h2>
                    <h6><?= $cat['caption'] ?></h6>

                <!--
                    actions
                -->

                    

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
            ok I will
        -->
<script src="/bunzilla/tpl/material/list.min.js"></script>
<script>
//
// list.js! http://listjs.com
//
document.body.onload = function(){
    var options = {
            valueNames: [
'closed','subject','comments','submitted','lastactive','status','priority'
        ]
    },
    myList = new List('list', options);
};
</script>

<!--
    dear god
-->
<div class="section" id="list">
    <div class="no-pad-bot ">
    <div class="row z-depth-5 secondary-text"  id="fuck"><!-- me -->
    <div class="col s12 m4 ">

        <div class="col s3  right-align">
        <button data-sort="closed" 
           class="sort btn-flat waves-effect icon-lock tooltipped" data-position="bottom" data-tooltip="sort by open/closed"
        ><i class="icon-sort"></i></button>
        </div>

        <div class="col s3  center-align">
        <button data-sort="priority" 
           class="sort btn-flat waves-effect waves-light icon-attention tooltipped" data-position="bottom" data-tooltip="sort by priority"
        ><i class="icon-sort"></i></button>
        </div>

        <div class="col s3  left-align">
        <button data-sort="status" 
           class="sort btn-flat waves-effect waves-light icon-pinboard tooltipped" data-position="bottom" data-tooltip="sort by status"
        ><i class="icon-sort"></i></button>
        </div>

    </div>
    <div class="col s12 m8 ">
        <div class="col s3  right-align">
        <button data-sort="subject" 
           class="sort btn-flat waves-effect waves-light icon-doc-text-inv tooltipped" data-position="bottom" data-tooltip="sort by subject"
        ><i class="icon-sort"></i></button>
        </div>
        <div class="col s3  center-align">
        <button data-sort="comments" 
           class="sort btn-flat waves-effect waves-light icon-chat tooltipped" data-position="bottom" data-tooltip="sort by # comments"
        ><i class="icon-sort"></i></button>
        </div>
        <div class="col s3  center-align">
        <button data-sort="submitted" 
           class="sort btn-flat waves-effect waves-light icon-time tooltipped" data-position="bottom" data-tooltip="sort by submission time"
        ><i class="icon-sort"></i></button>

        </div>
        <div class="col s3  left-align">
        <button data-sort="lastactive" 
           class="sort btn-flat waves-effect waves-light icon-time tooltipped" data-position="bottom" data-tooltip="sort by last activity"
        ><i class="icon-sort"></i></button>
        </div>
    </div>
    </div>
    </div><!-- asdfasdfasdfasdf -->

    <ul class="list collapsible section no-pad-top">
<?php
//
// tidy can be used to fix up html from truncated message "previews"
// but it's not required because we can just strip_tags()
//
    $tidy = extension_loaded('tidy') ? new tidy() : false;

    foreach($this->data['reports'] as $i => $report)
    {
        $report['last_active'] = max($report['time'],$report['updated_at'],$report['edit_time']);

/**
 * logical smooth sailing 
 * shoutouts to sorttable.js tho 
 *
 * check the history for this file if the above comment doesn't make any sense
 */
?>
        <li>
<?php // these values are hidden by/for purely presentational purposes ?>
            <div class="gone">
            <span class="closed"><?= $report['closed'] ?></span>
            <span class="priority"><?= $report['priority'] ?></span>
            <span class="status"><?= $this->data['statuses'][$report['status']]['title'] ?></span>
            <span class="submitted"><?= date('YmdHis', $report['time']) ?></span>
            <span class="lastactive"><?= date('YmdHis', $report['last_active']) ?></span>
            <span class="comments"><?= $report['comments'] ?></span>
            </div>

<?php // it looks like a lot of markup because it is. ?>
            <div class="collapsible-header no-select <?= $report['closed'] ? 'shade-text' : 'category-'.$cat['id'].'-darken-3' ?>">

<?php // [icon] subject line blablabla [status] ?>

                        <span class="left">
<?php //  '<i class="icon-lock grey-text" title="CLOSED."></i>' : priority($report['priority'],1) 
?>
                        </span>


                        <?= $report['closed'] ? '<span class="right">'.status($report['status']).'</span>' : '' ?>

                        <span class="badge right blue-text" title="comments">
                            <a class=" icon-chat" href="<?= BUNZ_HTTP_DIR, 'report/view/',$report['id'],'#comments'?>"><?= $report['comments'] ?></a>
                        </span>

<?php // no point in redundancy ?>
<?= 
($report['last_active'] == $report['time']) ? '' 
: '<span class="icon-time small right" title="last active">'.datef($report['last_active']).'</span>' 
?>
 <span class="submitted icon-history small right" title="submitted at"><?= datef($report['time']) ?></span>

<?php // x comments | 4 hours ago | [php] [DIVitis] ?>

<?php 
//
// tags!
//

        if(!$report['closed'])
        {
            echo '<div class="left',empty($report['tags']) ? '' : ' icon-tags','">';
            if(!empty($report['tags']))
            {
                foreach($report['tags'] as $tag)
                    echo tag($tag[0],0);
            }
            echo priority($report['priority']),'</div>';
        }
?>
                        <div class="z-depth-3 subject-line<?= $report['closed'] ? '  transparent' : ' category-'.$cat['id'].'-text" style="clear: both'?>" title="<?= $report['subject'] ?>">
                            <a class="subject icon-<?= $report['closed'] ? 'lock shade-text' : 'doc-text-inv'?>" 
                               href="<?= BUNZ_HTTP_DIR, 'report/view/',$report['id']?>"><?= $report['subject'] ?></a>

                        <?= $report['closed'] ? '' : '<span class="right">'.status($report['status']).'</span>' ?>
                        </div>
            </div>
            <div class="collapsible-body">
                <blockquote class="z-depth-5 category-<?= $cat['id'] ?>-text z-depth-1 icon-article-alt">
<!--<span class="subject"><?= $report['subject'] ?></span>--><?=
$report['edit_time'] ? '<p class="icon-pencil-alt"><a class="icon-time" href="'.BUNZ_DIFF_DIR.'reports/'.$report['id'].'">'.datef($report['edit_time']).'</a></p>' : '' 
?>
<?php
//
// as mentioned above, cleaning up message previews in case they're too long
// and/or contain HTML
//
        if(isset($report['preview_text']))
        {
            if(strlen(strip_tags($report['preview_text'])) > 400)
            {
                if($tidy)
                {            
                    $report['preview_text'] = substr($report['preview_text'],0,400);
                    $report['preview_text'] = $tidy->repairString($report['preview_text'],["doctype" => "omit","show-body-only" => "yes"]);
                    $report['preview_text'] = preg_replace('/.*\<body\>(.*)\<\/body\>.*\<\/html\>/mis', '$1', $report['preview_text']);
                } else {
                    $report['preview_text'] = substr(strip_tags($report['preview_text']),0,100);
                }
                $report['preview_text'] .= '. . .';
            }
            echo "<p>{$report['preview_text']}</p>";
        }  
?>
                    <p class="section no-pad-bot"><a class="icon-doc-text-inv btn-flat category-<?= $cat['id'] ?>-darken-2 waves-effect" 
                          href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id']?>">Full Report &rarr;</a></p>
                </blockquote>
            </div>
        </li>       
<?php
    }
?>
    </ul>
</div>
<?php
}
?>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
