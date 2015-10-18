<?php
$cat = $this->data['category'];

$pageTitle = $cat['title'];
$background = "category-{$cat['id']}-base";
$pageIcon = 'icon-plus';
$pageAction = 'post/category/'.$cat['id'];
$pageMode = 'new';

require BUNZ_TPL_DIR . 'header.inc.php';

require BUNZ_TPL_DIR .'toolsModal.html';
require 'form.phtml';
require BUNZ_TPL_DIR .'previewModal.html';

require BUNZ_TPL_DIR . 'footer.inc.php';
