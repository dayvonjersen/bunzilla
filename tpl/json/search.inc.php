<?php header('Content-Type: text/plain'); ?>
<?= json_encode($this->data['test'], JSON_PRETTY_PRINT) ?>
