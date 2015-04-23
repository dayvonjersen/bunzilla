<?php
$pageTitle = 'Edit Category &quot;'.$this->data['category']['title'].'&quot;';
$background = 'primary-base';
require BUNZ_TPL_DIR . 'header.inc.php';
$cat = $this->data['category'];
?>
<script src="<?= BUNZ_JS_DIR ?>jscolor.js"></script>
   <section id="editCategory" class="category-<?=$cat['id']?>-base section">
        <form class="category-<?=$cat['id']?>-text z-depth-5 section" action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/category/<?=$cat['id']?>" method="post">
            <h3 class="icon-pencil-alt"><?= $pageTitle ?></h3>
            <div class="input-field">
                <input id="edit-category-title" type="text" name="title" maxlength="255" value="<?= $cat['title'] ?>"/>
                <span class="material-input"></span>
                <label for="edit-category-title">Title</label>
            </div>
            <div class="input-field">
                <input id="edit-category-caption" type="text" name="caption" maxlength="255" value="<?= $cat['caption'] ?>"/>
                <span class="material-input"></span>
                <label for="edit-category-caption">Caption</label>
            </div>
            <div class="divider" style="margin: 0 0 2em;"></div>
            <div class="input-field">
                <input type="checkbox" id="edit-category-desc" name="description" value="1" <?= $cat['description'] ? ' checked' : '' ?>/>
                <label for="edit-category-desc">Require Description</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="edit-category-repr" name="reproduce" value="1" <?= $cat['reproduce'] ? ' checked' : '' ?>/>
                <label for="edit-category-repr">Require Reproduce</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="edit-category-expe" name="expected" value="1" <?= $cat['expected'] ? ' checked' : '' ?>/>
                <label for="edit-category-expe">Require Expected</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="edit-category-actu" name="actual" value="1" <?= $cat['actual'] ? ' checked' : '' ?>/>
                <label for="edit-category-actu">Require Actual</label>
            </div>
            <div class="divider" style="margin-top: -0.5em;"></div>
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="edit-category-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color'  value="<?= $cat['color'] ?>" maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList(), str_replace('icon-', '', $cat['icon']) ) ?>
                </div>
            </div>
            <div class="input-field col s12 center">
                <button type="submit" class="btn success-base icon-pencil-alt">Edit Category!</button>
                <button type="reset" class="btn btn-flat icon-cancel shade-text">Reset Form</button>
            </div>
        </form>
    </section>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
