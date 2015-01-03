<?php
$stat = $this->data['status'];
$pageTitle = 'Edit &quot;'.$stat['title'].'&quot;';
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
        <form action="<?= BUNZ_HTTP_DIR,'admin/edit/status/',$stat['id'] ?>" method="post" class='pure-form pure-form-aligned'>
                <fieldset class='is-center'>
                    <legend>create new status</legend>
                    <p class='pure-control-group'>
                        <label>title</label>
                        <input maxlength='255' placeholder='e.g. bug reports...' name='title' type="text" value="<?= $stat['title'] ?>">
                    </p>
                   
                    <p class='pure-control-group'>
                        <label>pick a color</label>
                        <input type="text" class='color' name='color' value="<?= $stat['color'] ?>">
                    </p>

                    <p class='pure-control-group'>
                        <label>pick an icon</label>
                        <?= iconSelectBox($stat['icon']) ?>
                    </p>
                    <button class='pure-button' type='submit'><i class='icon-ok'></i> make changes</button>
                </fieldset>
            </form>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
