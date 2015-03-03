<?php
$category = $this->data['categories'][$this->data['category_id']];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title><?= BUNZ_PROJECT_TITLE, ': ', $category['title'] ?></title>
    </head>
    <body>
        <header>
            <h1><?= BUNZ_PROJECT_TITLE ?> version <?= BUNZ_PROJECT_VERSION ?></h1>
            <h2><?= $category['title'] ?></h2>
            <h3><?= $category['caption'] ?></h3>
        </header>
        <nav>
            <a href="<?= BUNZ_HTTP_DIR , 'post/category/', $category['id'] ?>">Submit New</a>
<?php
$number_reports = selectCount('reports', 'category = '.$category['id']);

if($number_reports > 50)
{
$this->data['page_offset'] += 1;
$pages = ceil($number_reports/50);
$url = BUNZ_HTTP_DIR . "report/category/{$category['id']}";
?>
            <ul>
                <li>You are on page <?= $this->data['page_offset'] ?> of <?= $pages ?>.</li>
<?= $this->data['page_offset'] != 1 ? "\t\t\t<li><a href='$url'>First Page</a></li>\n" : '',
 $this->data['page_offset'] > 2  ? "\t\t\t<li><a href='$url/".($this->data['page_offset'] - 1)."'>Previous Page</a></li>\n" : '',
 $this->data['page_offset'] <= $pages - 2 ? "\t\t\t<li><a href='$url/".($this->data['page_offset'] + 1)."'>Next Page</a></li>\n" : '',
 $this->data['page_offset'] != $pages ? "\t\t\t<li><a href='$url/".($pages-1)."'>Last Page</a></li>\n" : ''
?>
            </ul>
<?php
}
?>
        <main>
<?php
if(!$number_reports)
{
?>
            <p>Nothing here yet!</p>
<?php
} else {
?>
            <ol>
<?php
foreach($this->data['reports'] as $report)
{
    $report['last_active'] = max($report['time'],$report['updated_at'],$report['edit_time']);
    $tags = [];
    foreach($report['tags'] as $tag)
        $tags[] = $this->data['tags'][$tag]['title'];
?>
                <li>
                    [<?= $report['closed'] ? 'CLOSED' : $this->data['priorities'][$report['priority']]['title'] ?>]
                    <a href="<?= BUNZ_HTTP_DIR, 'report/view/', $report['id'] ?>"><?= 
                        $report['subject'] ?></a> [<?= $this->data['statuses'][$report['status']]['title']?>]
                    <br/>
                    <?= count($tags) ? '<small>Tagged: '.implode(', ', $tags).'</small> | ' : '' ?>
                    <small>Submitted: <?= date(BUNZ_BUNZILLA_DATE_FORMAT, $report['time']) ?></small>
                  <?= $report['last_active'] != $report['time'] ? '| <small>Modified: '. date(BUNZ_BUNZILLA_DATE_FORMAT, $report['last_active']).'</small>' : '' ?>
                    | <small># Comments: <?= $report['comments'] ?></small>
                </li>
<?php
}
?>
            </ol>
        </main>
<?php
}
if($number_reports > 50)
{
?>
        <nav>
            <h5>Go to page:</h5>
<?php
$this->data['page_offset'] -= 1;
    for($i = 0; $i < $pages; $i++)
        printf("\t\t\t<%s>%d</%s>\n",
            $i == $this->data['page_offset'] ? 'b' : "a href='$url/$i'",
            $i+1,
            $i == $this->data['page_offset'] ? 'b' : 'a'
        );
?>
        </nav>
<?php
}
?>
    </body>
</html>
