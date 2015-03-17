<?php 
define('RESPONSE', 200);

$categories = [];
foreach($this->data['categories'] as $category)
{
    $stats = $this->data['stats'][$category['id']];
    $stats['last_activity'] = $stats['last_activity'] ? 
        date(BUNZ_BUNZILLA_DATE_FORMAT, $stats['last_activity']) : 'never';

    $categories[] = [
        'id'               => $category['id'],
        'title'            => $category['title'],
        'caption'          => $category['caption'],
        'requires'         => [
            'description' => (bool) $category['description'],
            'reproduce' => (bool) $category['reproduce'],
            'expected' => (bool) $category['expected'],
            'actual' => (bool) $category['actual']
        ],
        'statistics' => $stats,
        'url' => sprintf(
            'http%s://%s%sreport/category/%d',
            isset($_SERVER['HTTPS']) ? 's' : '',
            $_SERVER['HTTP_HOST'],
            BUNZ_HTTP_DIR,
            $category['id']
        )
    ];
}
$json = [
    'project' => [
        'title' => BUNZ_PROJECT_TITLE,
        'version' => BUNZ_PROJECT_VERSION,
        'mission_statement' => BUNZ_PROJECT_MISSION_STATEMENT,
        'url' => BUNZ_PROJECT_WEBSITE
    ],
    'categories' => $categories
];
require BUNZ_TPL_DIR . 'template.inc.php';
