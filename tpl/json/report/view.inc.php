<?php header('Content-Type: text/plain'); ?>
<?= json_encode($this->data['report'], JSON_PRETTY_PRINT) ?>
