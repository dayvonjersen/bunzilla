<?php
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='card pure-g'>
            <header class='box pure-u-1 pure-u-md-1-3'>
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h6>version <?= BUNZ_PROJECT_VERSION ?></h6>
            </header>
            
            <p class='box pure-u-1 pure-u-md-2-3' title="mission statement"><?= BUNZ_PROJECT_MISSION_STATEMENT ?></p>
        </article>
<hr style="border: 0; margin: 1em;"/>
<?php
if(empty($this->data['categories']))
{
?>
        <div class='flash'>No categories have been created yet! <a href="<?= BUNZ_HTTP_DIR ?>admin">Go make one!</a></div>
<?php
}
foreach($this->data['categories'] as $cat)
{
$open_issues = selectCount('reports','closed = 0 AND category = '.$cat['id']);
$total_issues = selectCount('reports','category = '.$cat['id']);
$this_week = selectCount('reports','time >= UNIX_TIMESTAMP()-60*60*24*7 AND category = '.$cat['id']);

?>
        <article style="cursor: pointer; background: #<?= $cat['color'] ?>; margin: 1em 0" class='card pure-g' onclick="window.location='<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>';">
            <header  title="<?=$cat['caption']?>" class="box pure-u-1" style="margin-top: 1em;">
            <h2>
                <a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class='<?=$cat['icon']?>' style="display: block; color: #<?= $cat['color'] ?>"><?=$cat['title']?></a>
            </h2>
            </header>
<?php
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
}
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
