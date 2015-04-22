<?php
define('RESPONSE',200);
$tags = [];
$total = array_sum($this->data['count']);
foreach($this->data['count'] as $id => $count)
{
    $tags[] = [
        'id'      => $id,
        'title'   => $this->data['tags'][$id]['title'],
        'icon'    => $this->data['tags'][$id]['icon'],
        'count'   => (int)$count,
        'percent' => (int)($count / $total * 100)
    ];
}
$json = [
    'total' => $total,
    'max_percent' => (int)(max($this->data['count']) / $total * 100),
    'min_percent' => (int)(min($this->data['count']) / $total * 100),
    'tags'  => $tags
];
require BUNZ_TPL_DIR . 'template.inc.php';
