<?php
$cat = $this->data['category'];
$pageTitle = 'Edit &quot;'.$cat['title'].'&quot;';
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
        <h1><?= $pageTitle ?></h1>
        <form action="<?= BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id'] ?>" method="post" class='pure-form pure-form-aligned'>
                <fieldset class='is-center'>
                    <legend>create new category</legend>
                    <p class='pure-control-group'>
                        <label>title</label>
                        <input maxlength='255' placeholder='e.g. bug reports...' name='title' type="text" value="<?= $cat['title'] ?>">
                    </p>
                    <p class='pure-control-group'>
                        <label>caption</label>
                        <input maxlength='255' placeholder='e.g. report problems here...' name='caption' type="text" value="<?= $cat['caption'] ?>">
                    </p>
                    <p class='pure-controls'>
                        <label>requires description<input type="checkbox" name='description'<?= $cat['description'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires reproduce<input type="checkbox" name='reproduce'<?= $cat['reproduce'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires expected<input type="checkbox" name='expected'<?= $cat['expected'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires actual<input type="checkbox" name='actual'<?= $cat['actual'] ? ' checked' : ''?>></label>
                    </p>
                    <p class='pure-control-group'>
                        <label>pick a color</label>
                        <input type="text" class='color' name='color' value="<?= $cat['color'] ?>">
                    </p>

                    <p class='pure-control-group'>
                        <label>pick an icon</label>
                        <?= iconSelectBox($cat['icon']) ?>
                    </p>
                    <button class='pure-button' type='submit'><i class='icon-ok'></i> make changes</button>
                </fieldset>
            </form>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
