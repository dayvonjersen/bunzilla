<?php
$pageTitle = 'Edit &quot;'.$this->data['priority']['title'].'&quot;';
require BUNZ_TPL_DIR . 'header.inc.php';

$cat = $this->data['priority'];
?>
<script src="<?= BUNZ_JS_DIR ?>jscolor.js"></script>
   <section id="editPriority" class="secondary-base section no-pad-top no-pad-bot">
        <form class="secondary-text z-depth-5 section no-pad" action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/priority/<?=$cat['id']?>" method="post">
            <h1 class="icon-pencil-alt">Edit <?= $cat['title'] ?></h1>
            <div class="input-field">
                <input id="edit-priority-title" type="text" name="title" maxlength="255" value="<?= $cat['title'] ?>"/>
                <span class="material-input"></span>
                <label for="edit-priority-title">Title</label>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="edit-priority-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color'  value="<?= $cat['color'] ?>" maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList(), str_replace('icon-', '', $cat['icon']) ) ?>
                </div>
            </div>
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-pencil-alt">Edit Priority!</button>
                <button type="reset" class="btn btn-flat icon-cancel secondary-text">Clear Form</button>
            </div>
        </form>
    </section>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
