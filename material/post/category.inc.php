<?php
$cat = $this->data['category'];

$pageTitle = 'Submit New &quot;'.$cat['title'].'&quot;';

require BUNZ_TPL_DIR . 'header.inc.php';

?>
<div class="category-<?= $cat['id'] ?>-base container">
<form action="<?= BUNZ_HTTP_DIR,'post/category/',$cat['id'] ?>?material"
      method="post"
      class="category-<?= $cat['id'] ?>-lighten-5">

    <div class="section">

         <h1 class="icon-plus"><?= $pageTitle ?></h1>
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
    if(!$cat[$name])
        continue;
?>
        <div class="input-field">
            <i class="icon-doc-text-inv prefix"></i>
            <textarea id="<?= $name ?>"
                      class="materialize-textarea" 
                      required 
                      name='<?= $name ?>'><?= empty($_POST) ? $this->data['params'][$name] : unfiltermessage($this->data['params'][$name]) ?></textarea>
            <label for="<?= $name ?>"><?= $name, ': ', $placeholder ?></label>
        </div>
<?php
}
?>
        <p class="input-field">
            <label for="disable_nlbr"><s class="icon-paragraph prefix"></s></label>
            <input type="checkbox" id="disable_nlbr" name="disable_nlbr" value=1"<?= isset($_POST['disable_nlbr']) ? ' checked' : ''?>>
            <label for="disable_nlbr">Disable insertion of automatic linebreaks (&lt;br/&gt;)</label>
        </p>

        <div class="divider"></div>

        <div class="input-field collapsible no-select section" id="tagz">
            <i class="icon-tags prefix"></i>
            <div class="collapsible-header center large">Tags! <span class="icon-down-open-mini"></span></div>
            <div class="collapsible-body section">
            <div class="row section">
<?php
//
// todo: figure out Javascript solution for this
// either <menu> or asJSON to do it
//
$checked = isset($_POST['tags']) && is_array($_POST['tags']) ? $_POST['tags'] : [];
$i = 1;
foreach($this->data['tags'] as $id => $tag)
{
?>
    <div class="input-field col s6 m3">
        <input type="checkbox" name="tags[]" id="tag_<?= $id ?>" value="<?= $id ?>"<?= in_array($id, $checked) ? ' checked' : ''?>>
        <label for="tag_<?= $id ?>"><?= $tag['title'] ?></label>
    </div>
<?php
    if($i%4 == 0 || $tag === end($this->data['tags']))
        echo '</div>', $tag !== end($this->data['tags']) ? '<div class="row section">' : '';
    $i++;
}
?>
        </div>
        </div>
        <div class="input-field center">
            <button type="reset" class="btn-flat white grey-text icon-cancel waves-effect"<?php
if(empty($_POST))
 echo <<<JAVASCRIPT
onclick="(function(evt){if(!window.confirm('This action will delete everything you typed.')) evt.preventDefault()})(event)"
JAVASCRIPT;
?>><?= empty($_POST) ? 'Clear' : 'Reset'?> Form</button>
            <button type="submit" class="btn icon-plus light-blue white-text waves-effect">Submit</button>
        </div>
    </div>
</form>
</div>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
