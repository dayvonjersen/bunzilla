<?php 
define('RESPONSE', 400);
$json = [
    'message' => 'Something bad happened.'
];
if(isset($_ERROR))
    $json['errors'] = [$_ERROR];
require BUNZ_TPL_DIR . 'template.inc.php';
