<?php
$pageTitle = 'Edit Status &quot;'.$this->data['status']['title'].'&quot;';
$background='shade-base';
require BUNZ_TPL_DIR . 'header.inc.php';

$cat = $this->data['status'];
?>
<script src="<?= BUNZ_JS_DIR ?>jscolor.js"></script>
   <section id="editStatus" class="shade-text section no-pad-top no-pad-bot">
            <h1 class="icon-pencil-alt">Edit Status &quot;<?= $cat['title'] ?>&quot;</h1>
        <form class="z-depth-5 section no-pad" action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/status/<?=$cat['id']?>" method="post">
            <?= status($cat['id']) ?>
            <div class="input-field">
                <input id="edit-status-title" type="text" name="title" maxlength="255" value="<?= $cat['title'] ?>"/>
                <span class="material-input"></span>
                <label for="edit-status-title">Title</label>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="edit-status-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color'  value="<?= $cat['color'] ?>" maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList(), str_replace('icon-', '', $cat['icon']) ) ?>
                </div>
            </div>
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-pencil-alt">Edit Status!</button>
                <button type="reset" class="btn btn-flat icon-cancel secondary-text">Clear Form</button>
            </div>
        </form>
    </section>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
