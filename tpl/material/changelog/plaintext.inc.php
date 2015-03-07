<?php
header('Content-Type: text/plain; charset=utf-8');

echo BUNZ_PROJECT_TITLE," changelog";
foreach($this->data['versions'] as $ver)
{
    echo "\n\nversion {$ver}:","\n\n";
    foreach($this->data['messages'] as $msg)
        echo $msg['version'] == $ver ? " - {$msg['message']}\n" : '';
}
