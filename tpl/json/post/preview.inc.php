<?php
define('RESPONSE', 200);

if(isset($this->data['params']['message']))
{
    $json = ['message' => $this->data['params']['message']];
} else {
    $json = [
        'subject' => $this->data['params']['subject']
    ];
    if(isset($this->data['params']['description']))
        $json['description'] = $this->data['params']['description'];
    if(isset($this->data['params']['reproduce']))
        $json['reproduce'] = $this->data['params']['reproduce'];
    if(isset($this->data['params']['expected']))
        $json['expected'] = $this->data['params']['expected'];
    if(isset($this->data['params']['actual']))
        $json['actual'] = $this->data['params']['actual'];
}
require BUNZ_TPL_DIR . 'template.inc.php';
