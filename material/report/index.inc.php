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
        <article class="col offset-m6 m6 s12 z-depth-2 pink white-text darken-3">
            <header class="section no-pad-top z-depth-5">
                <h1><?= BUNZ_PROJECT_TITLE ?></span>
            </header>
            <section class="section row no-pad-top no-pad-bot">
                <p class="z-depth-3 col s4 section">version <?= BUNZ_PROJECT_VERSION ?></p>
                <p class="col s8 flow-text right-align"><em><?= BUNZ_PROJECT_MISSION_STATEMENT ?></em></p>
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
<?= $i == 0 ? '<div class="row">' : '' ?>
    <div class="col s12 l6">
        <article class='z-depth-1 container category-<?= $cat['id'] ?>-base'>
            <div class="row">
                <!-- 
                    title 
                -->
                <section class='section col s8 z-depth-5 category-<?= $cat['id'] ?>-text'>
                    <h4><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>?material" class="category-<?= $cat['id'] ?>-text <?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h4>
                    <h6><?= $cat['caption'] ?></h6>
                </section>

                <!--
                    actions
                -->
                <section class="col offset-m3 offset-s1 s1">
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="btn btn-floating z-depth-5 transparent" title="submit new"><i class="green-text darken-2 icon-plus"></i></a>
                </section>
            </div>

            <section class="section no-pad-top no-pad-bot col s12 z-depth-5 category-<?= $cat['id'] ?>-lighten-1">
            <!-- 
                preview of latest post
            -->
<?php
        if(empty($stats['latest_issue']))
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
                    <a href="<?= BUNZ_HTTP_DIR,'report/view/',$stats['latest_issue']['id'] ?>" 
                       class="icon-doc-text-inv"><?= $stats['latest_issue']['subject'] ?></a>
                    <!--
                        # of comments and timestamp
                    -->
                    <span class="badge category-<?= $cat['id'] ?>-text">
                        <a class="icon-chat" 
                           href="<?= BUNZ_HTTP_DIR,'report/view/',$stats['latest_issue']['id'] ?>#comments" 
                           title="<?= $stats['latest_issue']['comments'] ?> comment(s)"><?= $stats['latest_issue']['comments'] ?></a>
                    </span>
                    <span class="badge"><em><small><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$stats['latest_issue']['time'])?></small></em></span>
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
                echo '<span class=" z-depth-3 tag-',$tag[0],' ',$this->data['tags'][$tag[0]]['icon'],'" title="',$this->data['tags'][$tag[0]]['title'],'"></span>';
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
        }
?>
                
            </section>

            <!--
                stats!
            -->
            <section class="section row no-pad-top">

                <p class="col offset-s1 category-<?= $cat['id'] ?>-lighten-5 s2 z-depth-5">
                    <small>open</small><br>
                    <span class="icon-unlock"><?= $stats['open_issues'] ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-4 offset-s1 s2 z-depth-2">
                    <small>resolved</small><br>
                    <span class="icon-chart"><?= 
$stats['total_issues'] > 0 ? 
    round(($stats['total_issues'] - $stats['open_issues'])/$stats['total_issues'],4)*100 . '%' 
    : 'n/a' ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-3 s2 z-depth-5">
                    <small>total</small><br>
                    <span class="icon-doc-text-inv"><?= $stats['total_issues'] ?></span>
                </p>

                <p class="col category-<?= $cat['id'] ?>-lighten-2 offset-s1 s2 z-depth-2">
                    <small>users</small><br>
                    <span class="icon-users"><?= $stats['unique_posters'] ?></span>
                </p>

<?php
        if($stats['last_activity'])
        {
?>
                <p class="col category-<?= $cat['id'] ?>-lighten-1 s12 z-depth-5">
                    <small>last activity</small><br>
                    <?= date(BUNZ_BUNZILLA_DATE_FORMAT,$stats['last_activity']) ?>
                </p>
<?php
        }
?>

            </section>
        </article>
</div> 
<?php
        // configure rows if you want or something
        if($i++ >= 2)
        {
            echo '</div>';
            $i = 0;
        } else
            $i++;
    }
}
require BUNZ_TPL_DIR . 'footer.inc.php';
