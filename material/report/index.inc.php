<?php
//
// index page : category listing : all bugs at a glance
//
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<!--
    about:bunzilla
-->
<div class="row">
        <article class="col s12 m6 z-depth-2 primary-darken-1">

            <header class="section no-pad-top z-depth-5 primary-base">
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h6><em><?= BUNZ_SIGNATURE, ' v', BUNZ_VERSION ?></em></h6>
            </header>

            <section class="section row no-pad-top no-pad-bot">
                <p class="z-depth-3 col s4 section primary-base">version <?= BUNZ_PROJECT_VERSION ?></p>
                <p class="col s8 right-align"><em><?= BUNZ_PROJECT_MISSION_STATEMENT ?></em></p>
            </section>

        </article>
        <article class="col s12 m6 z-depth-2 shade-base">
            <ul class="tabs">
                <li class="tab"><a href="#recent" class="shade-base icon-history"><span class="hide-on-small-only">Recent Activity</span></a></li>
                <li class="tab"><a href="#changelog" class="secondary-base icon-doc-text-inv"><span class="hide-on-small-only">Changelog</span></a></li>
                <li class="tab"><a href="#tagCloud" class="primary-darken-2 icon-tags"><span class="hide-on-small-only">Popular Tags</span></a></li>
            </ul>
            <section id="recent" class="section shade-base" >
                <div class="section shade-lighten-3" style="max-height: 14em; overflow-y: auto">
<?php
if(empty($this->data['recent_activity']))
    echo '<p><em>Nothing yet!</em></p>';

function getURLbyColumn($col)
{
    $return = BUNZ_HTTP_DIR;
    switch($col)
    {
        case 'report': $return .= "report/view/%d"; break;
        case 'category': $return .= "report/category/%d"; break;
        case 'status': $return .= "search/status/%d"; break;
        case 'priority': $return .= "search/priority/%d"; break;
        case 'tag': $return .= "search/tag/%d"; break;
    }
    return $return;
}

foreach($this->data['recent_activity'] as $log)
{
    $href = null;
    foreach(['report','category','status','priority','tag'] as $col)
    {
        if(isset($log[$col]))
        {
            $href = sprintf(getURLbyColumn($col),$log[$col]);
            break;
        }
    }
    echo '<p>',
            isset($href) ? "<a href='$href'>" : '',
            '<strong>',$log['who'],'</strong> ',
            $log['message'],'<br>',
            '<small>',datef($log['time']),'</small>',
            isset($href) ? '</a>' : '',
        '</p>',"\n";
}
?>
                </div>
            </section>
            <section id="changelog" class="section secondary-base">
                <pre class="section secondary-lighten-3" style="max-height: 14em; overflow-y: auto">
<?php
foreach(db()->query('SELECT message FROM change_log ORDER BY time DESC')->fetchAll(PDO::FETCH_NUM) as $msg)
                echo " - {$msg[0]}\n";
?>
                </pre>
            </section>
            <section id="tagCloud" class="section primary-darken-2">
                <div class="section primary-lighten-4 center" style="max-height: 14em; overflow-y: auto">
<?php
require_once BUNZ_LIB_DIR . 'search.php';
$tags = search::getTagCloud();
$unused = [];
$sum = array_sum($tags);
foreach($tags as $id => $count)
{
    if($count == 0)
    {
        $unused[] = $id;
        continue;
    }
    if(isset($this->data['tags'][$id]))
        echo '<a href="',BUNZ_HTTP_DIR,'search/tag/',$id,'" style="display: inline-block; font-size: ',max(round(10*($count/$sum),4),0.5),'em" class="tag-',$id,' ',$this->data['tags'][$id]['icon'],'" title="',$this->data['tags'][$id]['title'],'">',$this->data['tags'][$id]['title'],'</a>';
}
if(!empty($unused))
{
    echo '<p><em>Unused tags:</em> ';
    foreach($unused as $id){
        echo isset($this->data['tags'][$id]) ? tag($id) : "[MISSING_TAG_id#$id]";}
    echo '</p>';
}
?>
                </div>
            </section>
        </article>
</div>
<!--
    main screen turn on
-->
<?php
if(empty($this->data['categories']))
{
?>
        <div class="z-depth-5 yellow section flow-text icon-attention center-align blue-text">No categories have been created yet! <a class="btn-flat icon-right-open-mini" href="<?= BUNZ_HTTP_DIR ?>admin">Go make one!</a></div>
<?php
} else {

    $i = 0; // use this to create how many of these cards per row
    foreach($this->data['categories'] as $cat)
    {
        $stats = $this->data['stats'][$cat['id']];
?>
<?= $i == 0 ? '<div class="row center">' : '' ?>
    <div class="col s12 m6 l3">
        <article class='z-depth-1 category-<?= $cat['id'] ?>-darken-1'>
            <div class="section">
                <section class='section col s12 z-depth-5 category-<?= $cat['id'] ?>-base'>
                <!--
                    actions
                -->
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>?material" class="btn btn-floating z-depth-5 right category-<?= $cat['id'] ?>-text" title="submit new"><i class="category-<?= $cat['id'] ?>-text icon-plus"></i></a>
                <!-- 
                    title 
                -->
                    <h2><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
                    <h6><?= $cat['caption'] ?></h6>
                </section>

            </div>

<!--            <section class="section no-pad-top no-pad-bot col s12 z-depth-5 category-<?= $cat['id'] ?>-lighten-1">
            
                preview of latest post
            -->
<?php
/*        if(empty($stats['latest_issue']))
        {
?>
                <blockquote class='z-depth-1 category-<?= $cat['id'] ?>-lighten-5'><em>Nothing has been posted here yet!</em></blockquote>
<?php
        } else {
?>
                <p><small>latest issue:</small></p>
                <blockquote class='z-depth-5 category-<?= $cat['id'] ?>-lighten-5'>
                    <!--
                        subject, quick link
                    -->
                    <p>
                    <a href="<?= BUNZ_HTTP_DIR,'report/view/',$stats['latest_issue']['id'] ?>?material" 
                       class="icon-doc-text-inv"><?= $stats['latest_issue']['subject'] ?></a>
                    <!--
                        # of comments and timestamp
                    -->
                    <span class="badge category-<?= $cat['id'] ?>-text">
                        <a class="icon-chat" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$stats['latest_issue']['id'] ?>?material#comments" 
                           title="<?= $stats['latest_issue']['comments'] ?> comment(s)"><?= $stats['latest_issue']['comments'] ?></a>
                    </span>
                    <span class="badge"><em><small><?= datef($stats['latest_issue']['time'])?></small></em></span>
                    </p>

<?php
    //
    // preview_text has already been determined in lib/report.php
    // from possible desc,repr,expect,actual or none
    //
            if(isset($stats['latest_issue']['preview_text']))
            {
                $preview = strip_tags($stats['latest_issue']['preview_text']);
                $preview = strlen($preview) > 50 ? substr($preview,0,50) . '. . .' : $preview;
?>
                    <!--
                        wordz
                    -->
                    <p class="icon-article-alt"><?= $preview ?></p>
<?php
            }
?>
                    <!--
                        tagz
                    -->
                    <p>
<?php
            foreach($stats['latest_issue']['tags'] as $tag)
            {
//                echo '<span class=" z-depth-3 tag-',$tag[0],' ',$this->data['tags'][$tag[0]]['icon'],'" title="',$this->data['tags'][$tag[0]]['title'],'"></span>';
                echo tag($tag[0]);
            }
?>
                    </p>
                </blockquote>
                <!--
                    browse! (redundant maybe)
                -->
                <div class="center row">
                    <a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>?material" class="btn category-<?= $cat['id'] ?>-darken-2 icon-flashlight">browse category</a>
                </div>
<?php
        }*/
?>
                
       <!--     </section> -->

            <!--
                stats!
            -->
            <section class="section row no-pad-top">

                <p class="col offset-s1 category-<?= $cat['id'] ?>-lighten-5 s2 z-depth-5">
<!--                    <small class="hide-on-small-only">open</small><br>-->
                    <span class="icon-unlock"><?= $stats['open_issues'] ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-4 offset-s1 s2 z-depth-2">
       <!--             <small class="hide-on-small-only">resolved</small><br>-->
                    <span class="icon-ok"><?= 
$stats['total_issues'] > 0 ? 
    round(($stats['total_issues'] - $stats['open_issues'])/$stats['total_issues'],2)*100 . '%' 
    : 'n/a' ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-3 s2 z-depth-5">
        <!--            <small class="hide-on-small-only">total</small><br>-->
                    <span class="icon-doc-text-inv"><?= $stats['total_issues'] ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-2 offset-s1 s2 z-depth-2">
<!--                    <small class="hide-on-small-only">users</small><br>-->
                    <span class="icon-users"><?= $stats['unique_posters'] ?></span>
                </p>

<?php
        if($stats['last_activity'])
        {
?>
                <p class="col category-<?= $cat['id'] ?>-lighten-1 s12 z-depth-5">
              <!--      <small class="hide-on-small-only">last activity</small><br>-->
                    <span class="icon-time"><?= datef($stats['last_activity']) ?></span>
                </p>
<?php
        }
?>

            </section>
        </article>
</div> 
<?php
        // configure rows if you want or something
        if($i++ >= 5 || end($this->data['categories']) === $cat)
        {
            echo '</div>';
            $i = 0;
        } else
            $i++;
    }
}
require BUNZ_TPL_DIR . 'footer.inc.php';
