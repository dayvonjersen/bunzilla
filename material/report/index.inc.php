<?php
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<style>
    @media (max-width: 992px)
    {
        *{}
        [class*='icon-'] { font-size: 0; }
        [class*='icon-']:before { font-size: 24px; }
    }
</style>
<div class="row"><div class="col s6">
        <article class='card small'>
            <header class='card-image light-blue lighten-5'>
                <span class="card-title"><?= BUNZ_PROJECT_TITLE ?></span>
            </header>
            <section class="card-content">
                <p>version <?= BUNZ_PROJECT_VERSION ?></p>
                <p class="flow-text"><?= BUNZ_PROJECT_MISSION_STATEMENT ?></p>
            </section>
        </article>
        </div><div class="col s6">
        <article class="card small"><p class='card-content'>stats or something</article>
</div>
</div>
<?php
if(empty($this->data['categories']))
{
?>
        <div class='flash'>No categories have been created yet! <a href="<?= BUNZ_HTTP_DIR ?>admin">Go make one!</a></div>
<?php
}

$i = 0;
foreach($this->data['categories'] as $cat)
{
/*    $open_issues = selectCount('reports','closed = 0 AND category = '.$cat['id']);
    $total_issues = selectCount('reports','category = '.$cat['id']);
    $this_week = selectCount('reports','time >= UNIX_TIMESTAMP()-60*60*24*7 AND category = '.$cat['id']);
*/

?>
<?= $i == 0 ? '<div class="row">' : '' ?>
<div class="col s12 m3">
        <article class='card small'>
            <header class="card-image" style="background: #<?= $cat['color'] ?>">
                <span class="card-title <?=$cat['icon']?>"><?=$cat['title']?></span>
            </header>
            <section class="card-content">
                <p><?=$cat['caption']?></p>
                <p>
            </section>
            <footer class="card-action white">
                <a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>?material" class="icon-flashlight">Browse</a>
                <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="icon-plus">Submit</a>
            </footer>
        </article>
</div> <?= ($i < 4 && ($i = 0)===0)? '' : '</div>' ?>
<?php
$i++;
}/*
    if(!$total_issues)
    {
?>
            <section class='box pure-u-1'><em>nothing here yet!</em></section>
<?php
    } else {
?>
                <p class='box pure-u-1-6' title='open issues'><?= $open_issues ?></p>
                <p class='box pure-u-1-6' title='total issues'><?= $total_issues ?></p>
                <p class='box pure-u-1-6' title='% resolved'><?= (round(($total_issues - $open_issues)/$total_issues,4)*100) ?>%</p>
                <p class='box pure-u-1-6' title='added this week'><?= $this_week ?></p>
                <p class='box pure-u-1-6' title='total comments'><?= selectCount('comments LEFT JOIN reports ON comments.report = reports.id','reports.category = '.$cat['id']) ?></p>
                <p class='box pure-u-1 pure-u-md-1-6' title='last activity'><?= date(BUNZ_BUNZILLA_DATE_FORMAT,db()->query('SELECT MAX(time) FROM reports WHERE category = '.$cat['id'])->fetchColumn()) ?></p>
<?php
    }
?>
        </article>
<hr style="border: 0; margin: 1em;"/>
<?php
}*/
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
