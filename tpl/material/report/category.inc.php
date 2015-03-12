<?php
//
// reports by category : this isn't a message board, honest!
//
$cat        = $this->data['categories'][$this->data['category_id']];
$pageTitle  = $cat['title'];
$background = 'transparent'; //'category-'.$cat['id'].'-base'; //I CAN'T DECIDE D:

require BUNZ_TPL_DIR . 'header.inc.php';

// highlight.js for code highlighting the preview_text
?>
<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>

<?php // category banner ?>
<div class="row">
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
?>
<?= pagination( BUNZ_HTTP_DIR.'report/category/'.$cat['id'], 
        selectCount('reports','category = '.$cat['id']),
        $this->data['page_offset']
    ) ?>
<script src="<?= BUNZ_JS_DIR ?>list.min.js"></script>
<script>
//
// list.js! http://listjs.com
//
// NOTE: Chrome requires script to be loaded *before* the buttons that control sorting
// I'd prefer to put this closer to the list itself...
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

<div class="section no-pad-top" id="list">
    <div class="">
        <div class="row z-depth-5 category-<?=$cat['id']?>-text"  data-textlabel="sort by" id="fuck"><!-- me -->
            <div class="col s12 m4">
                <div class="col s3 right-align">
                    <button
                       class="sort btn-flat waves-effect icon-lock tooltipped" 
                       data-position="bottom" 
                       data-sort="closed" 
                       data-tooltip="sort by open/closed">
                        <i class="icon-sort"></i>
                    </button>
                </div>
                <div class="col s3 center-align">
                    <button
                       class="sort btn-flat waves-effect waves-light icon-attention tooltipped" 
                       data-position="bottom"
                       data-sort="priority" 
                       data-tooltip="sort by priority">
                        <i class="icon-sort"></i>
                    </button>
                </div>
                <div class="col s3 left-align">
                    <button
                       class="sort btn-flat waves-effect waves-light icon-pinboard tooltipped" 
                       data-position="bottom"
                       data-sort="status" 
                       data-tooltip="sort by status">
                        <i class="icon-sort"></i>
                    </button>
                </div>
            </div>
            <div class="col s12 m8">
                <div class="col s3 right-align">
                    <button
                        class="sort btn-flat waves-effect waves-light icon-doc-text-inv tooltipped"
                        data-position="bottom"
                        data-sort="subject"
                        data-tooltip="sort by subject">
                        <i class="icon-sort"></i>
                    </button>
                </div>
                <div class="col s3 center-align">
                    <button
                        class="sort btn-flat waves-effect waves-light icon-chat tooltipped"
                        data-position="bottom"
                        data-sort="comments"
                        data-tooltip="sort by # comments">
                        <i class="icon-sort"></i>
                    </button>
                </div>
                <div class="col s3 center-align">
                    <button
                       class="sort btn-flat waves-effect waves-light icon-time tooltipped"
                       data-position="bottom"
                       data-sort="submitted" 
                       data-tooltip="sort by submission time">
                        <i class="icon-sort"></i>
                    </button>
                </div>
                <div class="col s3 left-align">
                    <button
                       class="sort btn-flat waves-effect waves-light icon-time tooltipped"
                       data-position="bottom"
                       data-sort="lastactive"
                       data-tooltip="sort by last activity">
                        <i class="icon-sort"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <ul class="list collapsible section " data-collapsible="accordion" data-textlabel="reports">
<?php
//
// tidy can be used to fix up html from truncated message "previews"
// but it's not required because we can just strip_tags()
//
    $tidy = extension_loaded('tidy') ? new tidy() : false;

    foreach($this->data['reports'] as $i => $report)
    {
        $report['last_active'] = max($report['time'],$report['updated_at'],$report['edit_time']);
?>
        <li>
<?php 
/**
 * XXX so we have to include the data like this because of how list.js works
 * and how HTML formatting will screw with the sorting
 * alternatively, I believe we could assign these values to the above "myList"
 * list.js List instance directly OR present the list contents differently */
?>
            <div class="gone">
                <span class="subject"><?= $report['subject'] ?></span>
                <span class="closed"><?= $report['closed'] ?></span>
                <span class="priority"><?= $report['priority'] ?></span>
                <span class="status"><?= $this->data['statuses'][$report['status']]['title'] ?></span>
                <span class="submitted"><?= date('YmdHis', $report['time']) ?></span>
                <span class="lastactive"><?= date('YmdHis', $report['last_active']) ?></span>
                <span class="comments"><?= $report['comments'] ?></span>
            </div>

<?php // Click-to-expand-style heading ?>
            <div class="collapsible-header waves-effect waves-light-blue no-select">
                <div class="info-bar">
<?php // Link to comments section ?>
<?php if($report['comments']) { ?>
                <span class="comment-badge secondary-darken-2 z-depth-2 waves-effect">
                    <a class="icon-chat" href="<?= BUNZ_HTTP_DIR, 'report/view/',$report['id'],'#comments'?>"><?= $report['comments'] ?><span class="hide-on-small-only"> comment<?= $report['comments'] == 1 ? '' : 's' ?></span></a>
                </span>
<?php } else echo '&nbsp;' ?> <!-- don't worry about it -->

<?php // timestamps ?>
                    <span class="time_lastactive icon-time shade-text z-depth-2">
<?php if($report['last_active'] != $report['time']) { ?>
                        <span class="secondary-text"><span class="hide-on-med-and-down">last active: </span><?= datef($report['last_active']) ?></span>
                        <div class="hide-on-med-and-up icon-time"></div>&nbsp;|&nbsp;
<?php } ?>
                        <span class="hide-on-med-and-down">submitted: </span><?= datef($report['time']) ?>
                        <span class="dontworryaboutit"></span>
                    </span>
<?php // Tags and Priority ?>
<?php
        if(!$report['closed'])
        {
            echo '<div class="left">';
            if(!empty($report['tags']))
            {
                foreach($report['tags'] as $tag)
                    echo tag($tag,0);
            }
            echo priority($report['priority']),'</div>';
        }
?>
</div>
<?php // Subject ?>
                <div class="z-depth-3 subject-line <?= $report['closed'] ? 'shade-text' : "category-{$cat['id']}-text" ?>" style="clear: both"
                    title="<?= $report['subject'] ?>">
                    <a class="waves-effect icon-<?= $report['closed'] ? 'lock shade-text' : 'doc-text-inv category-'.$cat['id'].'-text'?>" 
                       href="<?= BUNZ_HTTP_DIR, 'report/view/',$report['id']?>"><?= $report['subject'] ?></a>
                        <span class="right"><?=status($report['status'])?></span>
                </div>
            </div>

<?php // Report Preview  ?>
            <div class="collapsible-body">
                <blockquote class="z-depth-5 category-<?= $cat['id'] ?>-text z-depth-1 icon-article-alt">
<?php
if($report['edit_time']) {
?>
                    <p class="icon-pencil-alt">
                        <a class="icon-time" href="<?= BUNZ_DIFF_DIR, 'reports/',$report['id'] ?>">
                            <?= datef($report['edit_time']) ?></a>
                    </p>
<?php
}
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
<?php // Quick Links to Actions ?>
                    <div class="divider"></div>
                    <p class="section no-pad-bot">
                        <a class="icon-doc-text-inv btn-flat category-<?= $cat['id'] ?>-darken-2 waves-effect" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id']?>">Full Report &rarr;</a>
<?php if($this->auth()) { ?>
                        <a class="icon-magic btn-flat secondary-base waves-effect" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id']?>#update"><span class="hide-on-small-only">Update</span></a>
                        <a class="icon-move btn-flat alert-text waves-effect" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id']?>#move"><span class="hide-on-small-only">Move/Merge</span></a>
                        <a class="icon-delete btn-flat danger-base waves-effect" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$report['id']?>#delete"><span class="hide-on-small-only">Delete</span></a>
<?php } ?>
                    </p>
                </blockquote>
            </div>
        </li>       
<?php
    }
?>
    </ul>
</div>

<?= pagination( BUNZ_HTTP_DIR.'report/category/'.$cat['id'], 
        selectCount('reports','category = '.$cat['id']),
        $this->data['page_offset']
    ) ?>
<?php
}

// I'm glad it's over.

require BUNZ_TPL_DIR .'footer.inc.php';
