<?php
$pageTitle = 'Edit &quot;'.$this->data['category']['title'].'&quot;';
$bread = [
    'cpanel' => ['href' => BUNZ_HTTP_DIR.'admin',
                   'icon' => 'icon-cog-alt'],
    $pageTitle => ['href' => BUNZ_HTTP_DIR.$_GET['url'],
                    'icon' => 'icon-cog']
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
        <h1><?= $pageTitle ?></h1>
        <form action="<?= BUNZ_HTTP_DIR,'admin/edit/category/',$this->data['category']['id'] ?>" method="post" class='pure-form pure-form-aligned'>
                <fieldset class='is-center'>
                    <legend>create new category</legend>
                    <p class='pure-control-group'>
                        <label>title</label>
                        <input maxlength='255' placeholder='e.g. bug reports...' name='title' type="text" value="<?= $this->data['category']['title'] ?>">
                    </p>
                    <p class='pure-control-group'>
                        <label>caption</label>
                        <input maxlength='255' placeholder='e.g. report problems here...' name='caption' type="text" value="<?= $this->data['category']['caption'] ?>">
                    </p>
                    <p class='pure-controls'>
                        <label>requires description<input type="checkbox" name='description'<?= $this->data['category']['description'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires reproduce<input type="checkbox" name='reproduce'<?= $this->data['category']['reproduce'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires expected<input type="checkbox" name='expected'<?= $this->data['category']['expected'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires actual<input type="checkbox" name='actual'<?= $this->data['category']['actual'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-control-group'>
                        <label>pick a color</label>
<<<<<<< Updated upstream
                        <input type="text" class='color' name="color {pickerMode:'HVS',pickerPosition:'top',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'black'}" value="<?= $this->data['category']['color'] ?>">
=======
                        <input type="text" class='color' name='color' value="<?= $this->data['category']['color'] ?>">
>>>>>>> Stashed changes
                    </p>

                    <p class='pure-control-group'>
                        <label>pick an icon</label>
                        <?= iconSelectBox($this->data['category']['icon']) ?>
                    </p>
                    <button class='pure-button' type='submit'><i class='icon-ok'></i> make changes</button>
                </fieldset>
            </form>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
