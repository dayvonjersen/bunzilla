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

if($rows)
{
?>
<style>
#postFormat {
     width: 100%;
    box-shadow: 0 2px 5px ;
    margin-bottom: 5px;
}

#postFormat .pure-button:not(.danger) {
     background: rgba(0,0,0,0.3);
     font-size: 16px;
}

#postFormat span.pure-button, #postFormat a.pure-button {
     width: 100%;
}

#postFormat p, #postFormat div {
     text-align: center;
}

#postFormat .pure-button {
    border: 1px solid;
}

#postFormat select {
     font-size: xx-small;
}

</style>
<script src="<?= BUNZ_JS_DIR ?>postFormat.js"></script>
<script>
function additionalHax()
{
    postFormat.init(document.forms[0]);
}
</script>
    <fieldset id="postFormat" class="pure-g">
<p class="pure-u-1-12">
        <span class='pure-button icon-bold' data-markup="b" title="bold"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-italic' data-markup="i" title="italic"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-underline' data-markup="u" title="underline"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-strike' data-markup="s" title="strikethrough"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-fontsize-large' data-markup="big" title="bigger"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-fontsize-small' data-markup="small" title="smaller"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-superscript' data-markup="sup" title="superscript"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-subscript' data-markup="sub" title="subscript"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-ul' data-markup="ul" title="unordered list"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-ol' data-markup="ol" title="ordered list"></span>
</p>
<p class="pure-u-1-12">
        <span class='pure-button icon-keyboard' data-markup="kbd" title="kbd"></span>
</p>
<p class="pure-u-1-12">
        <a href="javascript:postFormat.deactivate()" class='pure-button danger icon-cancel' title="close toolbar"></a>
</p>
<p class="pure-u-1-6">
        <span class='pure-button icon-link' data-markup="link" title="insert link"></span>
</p>
<p class="pure-u-1-6">
        <span class='pure-button icon-picture' data-markup="image" title="insert image"></span>
</p>
        <div class='pure-u-1-2 pure-button icon-code' data-markup="code" title="insert code" >
<select id="codelang">
    <option>pick a language</option>
<?php
foreach(json_decode(file_get_contents(str_replace(BUNZ_HTTP_DIR,BUNZ_DIR,BUNZ_JS_DIR).'highlight.js-languages.json')) as $lang)
    echo '<option value="',$lang,'">',$lang,'</option>';
?>
</select></div>
<p class="pure-u-1-6">
        <span class='pure-button icon-pencil-alt' data-markup="delins" title="delete and insert"></span>
</p>
</fieldset>

<a href="javascript:postFormat.activate()" id="postFormat-on" class="pure-button icon-wrench">toolbar</a>

<?php
}
?>

        <label class='icon-paragraph'><input id="disable_nlbr" type="checkbox" name="disable_nlbr" value="1"/>disable line breaks everywhere except inside of &lt;code&gt;</span>

<h2 class="icon-tags">tags!!111</h2>
<?php
foreach($this->data['tags'] as $tag)
{
?>
        <label><input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"/><?= tagButton($tag['id']) ?></label>
<?php
}
?>

                
                 <button class='pure-button' type='submit'><i class='icon-plus'></i> submit report</button>
                </fieldset>
            </form>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
