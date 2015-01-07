<?php
$pageTitle = 'Edit '.(isset($this->data['params']['comment_id']) ? 'Your Comment' : $this->data['params']['subject']);
$bread = [
    $this->data['category']['title'] => ['href' => BUNZ_HTTP_DIR . 'report/category/'.$this->data['category']['id'],
        'icon' => $this->data['category']['icon'],
        'color' => $this->data['category']['color']
    ],
    $this->data['params']['subject'] => ['href' => BUNZ_HTTP_DIR .' report/view/'.$this->data['params']['report_id'],
        'icon' => 'icon-doc-text-inv'],
    $pageTitle => ['href' => BUNZ_HTTP_DIR . $_GET['url'],
            'icon' => 'icon-pencil-alt']
];
require BUNZ_TPL_DIR . 'header.inc.php';
require 'utils.inc.php';
?>
        <article class="box">
        <h1><?= $pageTitle ?></h1>
        <form action="<?= BUNZ_HTTP_DIR,'post/edit/',$this->data['params']['report_id'],isset($this->data['params']['comment_id']) ? '/'.$this->data['params']['comment_id'] : '' ?>" method="post" class='pure-form pure-form-aligned'>
            <fieldset class='is-center'>
<?php
if(!isset($this->data['params']['comment_id']))
{
$q = true;
?>
                <p class='pure-control-group'>
                    <label>subject</label>
                    <input autofocus maxlength='255' name='subject' type="text" value="<?= $this->data['params']['subject'] ?>">
                </p>
<?php
}

$fields = [
    'description' => 'A synopsis or summary of the report you wish to submit',
    'reproduce'   => 'Instructions on how to create the behavior',
    'expected'    => 'The result, output, or other behavior you expect',
    'actual'      => 'The actual result, output, or other behavior that occurs',
    'message'     => 'Your unique and insightful voice'
];

$rows = isset($this->data['params']['comment_id']) ? 16 : round(16/(array_sum(array_intersect_key($this->data['category'],$fields))));

foreach($fields as $field => $placeholder)
{
    if(isset($this->data['params']['comment_id']) && $field != 'message')
        continue;
    if($this->data['category'][$field])
    {
?>
                <p class='pure-control-group' title="<?=$placeholder?>">
                    <label><?= $field ?></label>
                    <textarea <?= isset($q) ? '' : ($q = 'autofocus') ?> rows='<?=$rows?>' name='<?=$field?>' placeholder='<?=$placeholder?>'><?= unfiltermessage($this->data['params'][$field]) ?></textarea>
                </p>
<?php
    }
}

tagList($this->data['tags']);
?>

                
                 <button class='pure-button success' type='submit'><i class='icon-plus'></i> edit</button>
                </fieldset>
            </form>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
