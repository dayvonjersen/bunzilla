<?php
$cat = $this->data['category'];

$pageTitle = 'Submit New &quot;'.$cat['title'].'&quot;';

require BUNZ_TPL_DIR . 'header.inc.php';

?>
<div class="category-<?= $cat['id'] ?>-base container">
<h1><?= $pageTitle ?></h1>
<form action="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>?material"
      method="post"
      class="category-<?= $cat['id'] ?>-lighten-5">

    <fieldset>
         <div class="input-field">
            <i class="icon-mail prefix"></i>
            <input type="email" 
                    id="email" 
                    maxlength='255' 
                    name='email' 
                    value=<?= 
$this->auth() 
? '"' . $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled' 
: '"' . $this->data['params']['email'] . '" required' ?>>
            <label for="email">email</label>
        </div>
        <div class="input-field">
            <i class="icon-doc-text-inv prefix"></i>
            <input type="text"
                    maxlength="255"
                    id="subject"
                    name="subject"
                    autofocus
                    required
                    value="<?= $this->data['params']['subject'] ?>">
            <label for="subject">subject</label>
        </div>
<?php
$fields = [
    'description' => 'A synopsis or summary of the report you wish to submit',
    'reproduce'   => 'Instructions on how to create the behavior',
    'expected'    => 'The result, output, or other behavior you expect',
    'actual'      => 'The actual result, output, or other behavior that occurs'
];
foreach($fields as $name => $placeholder)
{
?>
        <div class="input-field">
            <i class="icon-doc-text-inv prefix"></i>
            <textarea id="<?= $name ?>"
                      class="materialize-textarea" 
                      required 
                      name='<?= $name ?>'><?= empty($_POST) ? $this->data['params'][$name] : unfiltermessage($this->data['params'][$name]) ?></textarea>
            <label for="<?= $name ?>"><?= $placeholder ?></label>
        </div>
<?php
}
?>
        <p class="input-field">
            <strike class="icon-paragraph prefix"></strike>
            <input type="checkbox" id="disable_nlbr" name="disable_nlbr" value=1"<?= isset($_POST['disable_nlbr']) ? ' checked' : ''?>>
            <label for="disable_nlbr">Disable insertion of automatic linebreaks (&lt;br/&gt;)</label>
        </p>

        <div class="input-field icon-tags" id="tagz">
            <?= dropdown('addTag\' onchange="(function(){ document.getElementById(\'tagz\').insertAdjacentHTML(\'afterbegin\', \'<p class=input-field><input type=checkbox name=tags[] value=\' + this.value + \' id=tagno\' + this.value + \'><label for=tagno\' + this.value + \'>o_k</label></p>\')})()" data-hello=\'', Cache::read('tags')) ?>
        </div>
        <div class="input-field center">
            <button type="reset" class="btn-flat white grey-text icon-cancel waves-effect"<?php
if(!empty($_POST))
 echo <<<JAVASCRIPT
onclick="function(evt){if(!window.confirm('This action will delete everything you typed.')) evt.preventDefault()}(event)"
JAVASCRIPT;
?>><?= !empty($_POST) ? 'Clear' : 'Reset'?> Form</button>
            <button type="submit" class="btn icon-plus light-blue white-text waves-effect">Submit</button>
        </div>
    </fieldset>
</form>
</div>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
