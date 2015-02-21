<?php
//
// index page : category listing : all bugs at a glance
//
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<div style="height: 100%">
<!--
    about:bunzilla
-->
<div class="row">
        <article class="col s12 m6">

            <header class="section no-pad-bot">
                <section class="section primary-base z-depth-5 waves-effect tooltipped"
                         data-tooltip="Go to project website: <?= BUNZ_PROJECT_WEBSITE ?>"
                         onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?= BUNZ_PROJECT_WEBSITE ?>'; }})(event);">
                    <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                    <h6><em><?= BUNZ_SIGNATURE, ' v', BUNZ_VERSION ?></em></h6>
                </section>
            </header>

            <section class="section row no-pad-top">
                <p class="z-depth-5 col s4 section primary-lighten-3">version <?= BUNZ_PROJECT_VERSION ?></p>
                <p class="z-depth-5 col s8 section primary-lighten-5 right-align tooltipped" 
                   data-tooltip="mission statement"><em><?= BUNZ_PROJECT_MISSION_STATEMENT ?></em></p>
            </section>

        </article>
        <article class="section col s12 m6">
            <ul class="tabs z-depth-3">
                <li class="tab"><a href="#recent" class="waves-effect shade-base icon-history"><span class="hide-on-med-and-down">Recent Activity</span></a></li>
                <li class="tab"><a href="#changelog" class="waves-effect secondary-base icon-doc-text-inv"><span class="hide-on-med-and-down">Changelog</span></a></li>
                <li class="tab"><a href="#tagCloud" class="waves-effect primary-darken-2 icon-tags"><span class="hide-on-med-and-down">Popular Tags</span></a></li>
                <li class="tab"><a href="#" class="active waves-effect red icon-cancel"></a></li>
            </ul>
            <section id="recent" class="section shade-base z-depth-5" >
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
            <section id="changelog" class="section secondary-base z-depth-5">
                <pre class="section secondary-lighten-3" style="max-height: 14em; overflow-y: auto">
<?php
foreach(db()->query('SELECT message FROM change_log ORDER BY time DESC')->fetchAll(PDO::FETCH_NUM) as $msg)
                echo " - {$msg[0]}\n";
?>
                </pre>
            </section>
            <section id="tagCloud" class="section primary-darken-2 z-depth-5">
                <div class="section primary-lighten-4 center" style="max-height: 14em; overflow-y: auto">
<?php
require_once BUNZ_CTL_DIR . 'search.php';
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
        <div class="z-depth-5 yellow section flow-text icon-attention center-align blue-text">No categories have been created yet! <a class="btn-flat icon-right-open-mini" href="<?= BUNZ_HTTP_DIR ?>cpanel">Go make one!</a></div>
<?php
} else {

    $i = 0; // use this to create how many of these cards per row
    foreach($this->data['categories'] as $cat)
    {
        $stats = $this->data['stats'][$cat['id']];
        $stats['percent_resolved'] = $stats['total_issues'] > 0 
            ? round(($stats['total_issues'] - $stats['open_issues'])/$stats['total_issues'],2)*100 . '%' 
            : 'n/a';
?>
<?= $i == 0 ? '<div class="row">' : '' ?>
    <div class="col s12 m6 l3">
        <article>
            <div class="section no-pad-top">
                <section class='section col s12 z-depth-5 category-<?= $cat['id'] ?>-base waves-effect' onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>'; }})(event);">
                <!--
                    actions
                -->
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="waves-effect btn btn-floating z-depth-5 right category-<?= $cat['id'] ?>-base" title="submit new"><i class="icon-plus"></i></a>
                <!-- 
                    title 
                -->
                    <h2><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
                    <h6><?= $cat['caption'] ?></h6>
                </section>

            </div>



            <!--
                stats!
            -->
            <section class="section row center no-pad-top">
<?php
        if($stats['last_activity'])
        {
?>
                <p class="section col category-<?= $cat['id'] ?>-lighten-1 s3 z-depth-2 tooltipped" 
                          data-tooltip="<?= $stats['open_issues'] ?> open issue<?= 
                            $stats['open_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-unlock"></i><br/><?= $stats['open_issues'] ?></span>
                </p>

                <p class="section col category-<?= $cat['id'] ?>-lighten-2 s3 z-depth-3 tooltipped"
                          data-tooltip="percentage resolved: <?= $stats['percent_resolved'] ?>">
                    <span><i class="icon-ok"></i><br/><?= $stats['percent_resolved']?></span>
                </p>

                <p class="section col category-<?= $cat['id'] ?>-lighten-3 s3 z-depth-4 tooltipped" 
                          data-tooltip="<?= $stats['total_issues'] ?> total issue<?= 
                            $stats['total_issues'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-doc-text-inv"></i><br/><?= $stats['total_issues'] ?></span>
                </p>

                <p class="section col category-<?= $cat['id'] ?>-lighten-4 s3 z-depth-5 tooltipped" 
                          data-tooltip="<?= $stats['unique_posters'] ?> unique poster<?= 
                            $stats['unique_posters'] == 1 ? '' : 's' ?>">
                    <span><i class="icon-users"></i><br/><?= $stats['unique_posters'] ?></span>
                </p>
                <p class="section col category-<?= $cat['id'] ?>-lighten-5 s12 z-depth-5 tooltipped"
                          data-tooltip="last activity">
                    <span class="icon-time"></i><br/><?= datef($stats['last_activity']) ?></span>
                </p>
<?php
        } else {
?>
                <p class="section col category-<?= $cat['id'] ?>-lighten-5 s12 z-depth-1"><em>Nothing has been posted here yet!</em></p>
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
?>
    </div>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
