<?php

function unfiltermessage($msg)
{
    // FUCK
    $msg = str_replace('<br />','',$msg);

    preg_match_all('/\<pre\>\<code( class="language-(\w+)")?/ims', $msg, $codes,PREG_SET_ORDER);
    foreach($codes as $code)
        $msg = str_replace($code[0],'<code'.(isset($code[2]) ? ' '.$code[2] : ''),$msg);
    $msg = str_replace('</code></pre>','</code>',$msg);

    preg_match_all('/\<a href="(.*?)" .+\>(.*)\<\/a\>/im',$msg,$links,PREG_SET_ORDER);
    foreach($links as $link)
        $msg = str_replace($link[0],'<link>'.$link[1].($link[1]!=$link[2]?'{'.$link[2].'}':'').'</link>',$msg);
    preg_match_all('/\<img src="(.*?)" .+\>/im',$msg, $images,PREG_SET_ORDER);
    foreach($images as $image)
        $msg = str_replace($image[0], '<image>'.$image[1].'</image>',$msg);

    $msg = str_replace('<','&lt;',$msg);
    $msg = str_replace('>','&gt;',$msg);

    return $msg;
}

function postFormatWidget()
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
<div id="postFormat-ctn">
    <fieldset id="postFormat" class="pure-g" style="display: none">
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

<a href="javascript:postFormat.activate()" id="postFormat-on" class="pure-button icon-wrench success" style="width: auto; display: none;"><small>toggle post tools</small></a>
</div>
<?php
}

function tagList($tags)
{
?>
<fieldset class="pure-g">
<h2 class="icon-tags pure-u-1">tags!!111</h2>
<?php
foreach($tags as $tag)
{
?>
        <label class="pure-u-1-4"><input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"<?= isset($_POST['tags']) && in_array($tag['id'],$_POST['tags'],true) ? '" checked' : '' ?>/><?= tagButton($tag['id']) ?></label>
<?php
}
?>
</fieldset>
<?php
}
