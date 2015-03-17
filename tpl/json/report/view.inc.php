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

$report = $this->data['report'];

$category = $this->data['categories'][$report['category']];
$category = [
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
        'url' => qurl('report/category/',$category['id'])
    ],
];

$status   = $this->data['statuses'][$report['status']];
$priority = $this->data['priorities'][$report['priority']];
$tags     = [];
    foreach($report['tags'] as $t)
        $tags[] = $this->data['tags'][$t];

$comments = [];
foreach($this->data['comments'] as $comment)
{
    $new_comment = [
        'id'        => $comment['id'],
        'message'   => $comment['message'],
        'author'    => [
            'email' => $comment['email']
        ],
        'time' => qdate($comment['time'])
    ];
    if($comment['epenis'] == 1)
        $new_comment['author']['is_developer'] = true;
    if($comment['epenis'] == 2)
        $new_comment['author']['is_system'] = true;

    if($comment['reply_to'])
        $new_comment['reply_to'] = [
            'id' => $comment['reply_to']
        ];

    if($comment['edit_time'])
        $new_comment['edit'] = 
            ['time' => qdate($comment['edit_time']),
             'diff_url' => qurl('diff/comments/',$comment['id'])
            ];

    $comments[] = $new_comment;    
}

$status_log = [];
foreach($this->data['status_log'] as $log)
    $status_log[] = [
        'message' => $log['message'],
        'author'  => $log['who'],
        'time'    => qdate($log['time'])
    ];

$json = [
    'report' => [
        'id' => $report['id'],
        'subject' => $report['subject'],
        'description' => $report['description'],
        'reproduce' => $report['reproduce'],
        'expected' => $report['expected'],
        'actual' => $report['actual'],
        'category' => $category,
        'status' => $status,
        'priority' => $priority,
        'tags' => $tags,
        'comments' => $comments,
        'status_log' => $status_log
    ]
];
        
require BUNZ_TPL_DIR . 'template.inc.php';
