<?php
$pageTitle = 'bunzilla settings';

require BUNZ_TPL_DIR . 'header.inc.php';
?>
<script src='<?= BUNZ_JS_DIR ?>jscolor.js'></script>
<script>
function confirmDelete(evt){if(!window.confirm('you know what you doing'+"\n\n"+'(this action will permanently delete all associated reports)')) evt.preventDefault();}
</script>
        <article class='box'>
            <h1>edit categories</h1>
<?php
if(empty($this->data['categories']))
{
?>
            <h2>No categories yet!</h2>
<?php
} else {
?>
            <table class='pure-table pure-table-horizontal'>
                <thead>
                    <tr>
                        <th>title</th>
                        <th># reports</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
<?php
    foreach($this->data['categories'] as $i => $cat)
    {
?>
                    <tr<?=$i&1?' class="pure-table-odd"':''?>>
                        <td><?=$cat['title']?></td>
                        <td><?=$cat['total_reports']?></td>
                        <td>
                            <a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class='pure-button info'>View</a>
                            <a href="<?=BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id']?>" class='pure-button success'>Edit</a>
                            <a href="<?=BUNZ_HTTP_DIR,'admin/delete/category/',$cat['id']?>" class='pure-button danger' onclick="confirmDelete(event)">Delete</a>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
<?php
}
?>
            <form action="<?=BUNZ_HTTP_DIR,'admin/add/category'?>" method="post" class='pure-form pure-form-aligned'>
                <fieldset class='is-center'>
                    <legend>create new category</legend>
                    <p class='pure-control-group'>
                        <label>title</label>
                        <input maxlength='255' placeholder='e.g. bug reports...' name='title' type="text">
                    </p>
                    <p class='pure-control-group'>
                        <label>caption</label>
                        <input maxlength='255' placeholder='e.g. report problems here...' name='caption' type="text">
                    </p>
                    <p class='pure-controls'>
                        <label>requires description<input type="checkbox" name='description'></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires reproduce<input type="checkbox" name='reproduce'></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires expected<input type="checkbox" name='expected'></label>
                    </p>
                    <p class='pure-controls'>
                        <label>requires actual<input type="checkbox" name='actual'></label>
                    </p>
                    <p class='pure-control-group'>
                        <label>pick a color</label>
                        <input type="text" class='color' name='color' value='ffffff'>
                    </p>

                    <p class='pure-control-group'>
                        <label>pick an icon</label>
                        <?= iconSelectBox() ?>
                    </p>
                    <button class='pure-button' type='submit'><i class='icon-plus'></i> create new category</button>
                </fieldset>
            </form>
        </article>        
        <article class='box'>
            <h1>edit statuses</h1>
<?php
if(empty($this->data['statuses']))
{
?>
            <h2>No statuses yet!</h2>
<?php
} else {
?>
            <table class='pure-table pure-table-horizontal'>
                <thead>
                    <tr>
                        <th>title</th>
                        <th># reports</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
<?php
    foreach($this->data['statuses'] as $i => $stat)
    {
?>
                    <tr<?=$i&1?' class="pure-table-odd"':''?>>
                        <td>
                            <?= statusButton($stat['id']) ?>
                        </td>
                        <td><?=$stat['total_reports']?></td>
                        <td>
                            <a href="<?=BUNZ_HTTP_DIR,'report/status/',$stat['id']?>" class='pure-button info'>View</a> 
                            <a href="<?=BUNZ_HTTP_DIR,'admin/edit/status/',$stat['id']?>" class='pure-button success'>Edit</a> 
                            <a href="<?=BUNZ_HTTP_DIR,'admin/delete/status/',$stat['id']?>" class='pure-button danger' onclick="confirmDelete()">Delete</a>
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
<?php
}
?>
            
                    
            <form action="<?=BUNZ_HTTP_DIR,'admin/add/status'?>" method="post" class='pure-form'>
                <fieldset class='is-center'>
                    <legend>create new status</legend>
                        <input type="text" name='title' placeholder='title'>
                        <input type="text" name='color' class='color' placeholder='pick a color...'>
                        <?= iconSelectBox() ?>
                    <button class='pure-button'><i class='icon-plus'></i> create new status</button>
                </fieldset>
            </form>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
