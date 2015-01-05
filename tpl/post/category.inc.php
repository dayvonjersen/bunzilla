<?php
$pageTitle = 'Submit New '.$this->data['category']['title'];
$bread = [
    $this->data['category']['title'] => ['href' => BUNZ_HTTP_DIR . 'report/category/'.$this->data['category']['id'],
        'icon' => $this->data['category']['icon'],
        'color' => $this->data['category']['color']
    ],
    $pageTitle => ['href' => BUNZ_HTTP_DIR . $_GET['url'],
        'icon' => 'icon-plus'
    ]
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class="box">
        <h1><?= $pageTitle ?></h1>
        <form action="<?= BUNZ_HTTP_DIR,'post/category/',$this->data['category']['id'] ?>" method="post" class='pure-form pure-form-aligned'>
            <fieldset class='is-center'>
                <p class='pure-control-group'>
                    <label>email</label>
                    <input maxlength='255' name='email' type="text" value="<?= $this->auth ? $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled="disabled' : $this->data['params']['email'] ?>">
                </p>
                <p class='pure-control-group'>
                    <label>subject</label>
                    <input autofocus maxlength='255' name='subject' type="text" value="<?= $this->data['params']['subject'] ?>">
                </p>
<?php
$fields = [
    'description' => 'A synopsis or summary of the report you wish to submit',
    'reproduce'   => 'Instructions on how to create the behavior',
    'expected'    => 'The result, output, or other behavior you expect',
    'actual'      => 'The actual result, output, or other behavior that occurs'
];

$rows = array_sum(array_intersect_key($this->data['category'],$fields));

$rows = $rows > 0 ? round(16/($rows)) : 0;
foreach($fields as $field => $placeholder)
{
    if($this->data['category'][$field])
    {
?>
                <p class='pure-control-group' title="<?=$placeholder?>">
                    <label><?= $field ?></label>
                    <textarea rows='<?=$rows?>' name='<?=$field?>' placeholder='<?=$placeholder?>'><?= $this->data['params'][$field] ?></textarea>
                </p>
<?php
    }
}
/*
                <p class='pure-control-group'>
                    <label>initial status</label>
                    <?= statusSelectBox($this->data['params']['status']) ?>
                </p>
*/
?>
                
                 <button class='pure-button' type='submit'><i class='icon-plus'></i> submit report</button>
                </fieldset>
            </form>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
