<?php
require BUNZ_TPL_DIR . 'constants.inc.php';

$category = $this->data['category'];
$pageTitle = $category['title'];
$thisPage  = "report/category/{$category['id']}";
$rss = [];
foreach($this->data['reports'] as $report)
{
    $item = [];
    $item['title'] = $report['subject'];
    $item['link'] = $item['guid'] = SITE_URL . "report/view/{$report['id']}";
    if(isset($report['preview_text']))
    {
        $item['description']  = '<![CDATA['.htmlentities(substr(strip_tags($report['preview_text']),0,100)).']]>';
    }
    
    $item['author'] = $report['email'];
    if($report['tags'])
    {
        foreach($report['tags'] as $tag)
            $tags[] = $this->data['tags'][$tag]['title'];
        $item['category'] = implode(', ',$tags);
    }
    if($report['comments'])
        $item['comments'] = $report['comments'];

    $item['pubDate'] = date(RSS_DATE_FORMAT, $report['time']);
    $item['source'] = SITE_URL . "$thisPage?rss";
    $rss[] = $item;
}

require BUNZ_TPL_DIR . 'template.rss.php';
