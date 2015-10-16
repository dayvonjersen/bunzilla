<?php
$category = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title><?= BUNZ_PROJECT_TITLE, ': ', $report['subject'] ?></title>
    </head>
    <body>
        <article>
            <header>
                <h1><?= $report['subject'], $report['closed'] ? ' [CLOSED]' : '' ?></h1>
		<h2><?= $report['email'], $report['epenis'] ? ' [developer] ' : ' ', date(BUNZ_BUNZILLA_DATE_FORMAT, $report['time']) ?></h2>
                <h3><?= date(BUNZ_BUNZILLA_DATE_FORMAT, $report['time']) ?></h3>
            </header>
            <hr/>
            <p>[<?=  $this->data['priorities'][$report['priority']]['title'] ?>] [<?= $this->data['statuses'][$report['status']]['title'] ?>]</p>
	    <hr/>
            <!--<p><button>actions</button><button>or</button><button>something</button></p>-->
            <main>
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($category[$field])
    {
?>
                <h5><?= $field ?></h5>
                <section><?= $report[$field] ?></section>
<?php
    }
}
?>
            </main>
            <hr/>
<?php
if(!empty($this->data['comments']))
{
?>
	   <footer>
<?php
foreach($this->data['comments'] as $comment)
{
?>
		<article>
			<header><?= $comment['email'], $comment['epenis'] ? $comment['epenis'] == 2 ? ' [system] ' : ' [developer] ' : ' ', date(BUNZ_BUNZILLA_DATE_FORMAT, $comment['time']) ?></header>
			<main><?= $comment['message'] ?></main>
		</article>
		<hr/>
<?php
}
?>
	   </footer>
<?php
}
?>
        </article>
	<footer>
		<address><?= BUNZ_PROJECT_TITLE, ' ', BUNZ_SIGNATURE ?></address>
	</footer>
    </body>
</html>
