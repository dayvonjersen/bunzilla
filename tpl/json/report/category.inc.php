<?php 
define('RESPONSE', 200);

function qdate($time)
{
    return $time ? date(BUNZ_BUNZILLA_DATE_FORMAT,$time) : 'never';
}

function qurl($route, $id)
{
    return sprintf(
        "http%s://%s%s$route%d",
        isset($_SERVER['HTTPS']) ? 's' : '',
        $_SERVER['HTTP_HOST'],
        BUNZ_HTTP_DIR,
        $id
    );
}

$reports = [];
foreach($this->data['reports'] as $report)
{
    $priority = $this->data['priorities'][$report['priority']];
    $status   = $this->data['statuses'][$report['status']];
    $tags     = [];
    foreach($report['tags'] as $t)
        $tags[] = $this->data['tags'][$t];

    $new_report = [
        'id'        => $report['id'],
        'subject'   => $report['subject'],
        'closed'    => (bool)$report['closed'],
        'priority'  => $priority,
        'status'    => $status,
        'tags'      => $tags,
        'comments'  => $report['comments'],
        'submitted' => qdate($report['time']),
        'updated'   => qdate($report['updated_at']),
        'url'       => qurl('report/view/',$report['id'])
    ];

    if($report['edit_time'])
        $new_report['edit'] = 
            ['time' => qdate($report['edit_time']),
             'diff_url' => qurl('diff/reports/',$report['id'])
            ];

    $reports[] = $new_report;    
}

$category = $this->data['categories'][$this->data['category_id']];
$issues = selectCount('reports','category = '.$category['id']);
$json = [];
if($issues > 50)
    $json['pagination'] = 
        ['total_issues' => $issues,
         'page_offset'  => $this->data['page_offset']
        ];

$json += [
    'category' => [
        'id'               => $category['id'],
        'title'            => $category['title'],
        'caption'          => $category['caption'],
        'requires'         => [
            'description' => (bool) $category['description'],
            'reproduce' => (bool) $category['reproduce'],
            'expected' => (bool) $category['expected'],
            'actual' => (bool) $category['actual']
        ],
        'url' => sprintf(
            'http%s://%s%sreport/category/%d',
            isset($_SERVER['HTTPS']) ? 's' : '',
            $_SERVER['HTTP_HOST'],
            BUNZ_HTTP_DIR,
            $category['id']
        )
    ],
    'reports' => $reports
];
require BUNZ_TPL_DIR . 'template.inc.php';
