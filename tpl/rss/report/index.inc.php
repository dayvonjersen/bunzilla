<?php
require BUNZ_TPL_DIR . 'constants.inc.php';

$pageTitle = 'Overview Feed Coming Shortly! For now have some links...';
$thisPage = 'report/index';

$rss = [];
foreach($this->data['categories'] as $category)
{
    if(!$this->data['stats'][$category['id']]['last_activity'])
        continue;
    $item = [];
    $item['title'] = $category['title'];
    $item['link']  = SITE_URL . "report/category/{$category['id']}";
    $item['description']  = $category['caption'];
//  $item['author'] = 'nobody'
//  $item['category'] = 'lol'
//  $item['comments'] = 'nothing'
    $item['guid'] = SITE_URL . "report/category/{$category['id']}";
    $item['pubDate'] = date(RSS_DATE_FORMAT, $this->data['stats'][$category['id']]['last_activity']);
    $item['source'] = SITE_URL . "$thisPage?rss";
    $rss[] = $item;
}

require BUNZ_TPL_DIR . 'template.rss.php';
