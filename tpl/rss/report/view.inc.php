<?php
require BUNZ_TPL_DIR . 'constants.inc.php';

$category = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];
$pageTitle = $report['subject'];
$thisPage  = "report/view/{$report['id']}";
define('TEST', $thisPage);
function createItem ($title, $link, $description, $author, $time) {
    global $thisPage;
    $link = SITE_URL . TEST . ( $link != -1 ? '#' . ($link ? $link : $title) : '');
    return [
        'title' => ucfirst($title),
        'link'  => $link,
        'guid'  => $link,
        'description' => $description,
        'author' => $author,
        'pubDate' => date(RSS_DATE_FORMAT, $time)
    ];
}

$time = max($report['edit_time'],$report['time']);

$rss = [
    createItem('subject',0,$report['subject'],$report['email'],$time)
];
$tags = [];
foreach($report['tags'] as $tag)
    $tags[] = $this->data['tags'][$tag]['title'];
$rss[0]['category'] = implode(', ',$tags);

foreach(['description','reproduce','expected','actual'] as $field)
{
    if($category[$field])
        $rss[] = createItem($field,0,htmlentities($report[$field]),$report['email'],$time);
}
$comments = [];
foreach($this->data['comments'] as $c)
{
    $comments[$c['id']] = $c;
}
$comment_ids = array_keys($comments);
$statuslog = [];
foreach($this->data['status_log'] as $s)
{
    $statuslog[$s['id']] = $s;
}
$statuslog_ids = array_keys($statuslog);

foreach($this->data['timeline'] as $item)
{
    if(in_array($item['id'],$comment_ids))
    {
        $comment = $comments[$item['id']];
        $title = 'comment';
        $link  = 'reply-'.$item['id'];
        $description = $comment['message'];
        $author = $comment['email'];
        $time = max($comment['edit_time'],$comment['time']);
    } else {
        $log = $statuslog[$item['id']];
        $title = 'status update';
        $link = -1;
        $description = $log['message'];
        $author = $log['who'];
        $time = $log['time'];
    }
    $rss[] = createItem($title,$link,$description,$author,$time);
}

require BUNZ_TPL_DIR . 'template.rss.php';
