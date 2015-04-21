<?php
$cat = $this->data['category'];

$pageTitle = 'Edit '
    .(isset($this->data['params']['comment_id']) 
    ? 'Your Comment' 
    : $this->data['params']['subject']);
$background = "category-{$cat['id']}-base";
$pageIcon = 'icon-pencil-alt';
$pageAction = 'post/edit/'
    .$this->data['params']['report_id']
    .(isset($this->data['params']['comment_id']) 
    ? '/'.$this->data['params']['comment_id']
    : '');
$pageMode = 'edit';

require BUNZ_TPL_DIR . 'header.inc.php';

require BUNZ_TPL_DIR .'toolsModal.html';
require 'form.phtml';
require BUNZ_TPL_DIR .'previewModal.html';
