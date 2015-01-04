<?php
function categorySelectBox($disable,$cats)
{
    if(count($cats) == 1)
        return '';

    $id = uniqid();
    $select = '<input type="hidden" name="move_to" id="'.$id.'"/><select class="rui-selectable" data-selectable=\'{"multiple":false,"assignTo":"'.$id.'","hCont":""}\'><option>move to...</option>';
    foreach($cats as $cat)
        $select .= $cat['id'] !== $disable ? '<option value="'.$cat['id'].'" class="'.$cat['icon'].'" style="background: #'.$cat['color'].'">'.$cat['title'].'</value>' : '';
    $select .= '</select>';        
    return $select;
}
$pageTitle = 'cpanel';
$bread = [
    $pageTitle => ['href' => BUNZ_HTTP_DIR.$_GET['url'],
                   'icon' => 'icon-cog-alt']
];
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
                        <th colspan="2">title</th>
                        <th># reports</th>
                        <th>last submission</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
<?php
    foreach($this->data['categories'] as $i => $cat)
    {
?>
                    <tr<?=$i&1?' class="pure-table-odd"':''?>>
                        <td><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class='<?=$cat['icon']?>' style="color: #<?= $cat['color'] ?>"><?=$cat['title']?></a><br /><small><?=$cat['caption']?></small></td>
                        <td><a href="<?=BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id']?>" class='pure-button icon-pencil-alt success'>Edit</a></td>
                        <td><?=$cat['total_reports']?></td>
                        <td><?=$cat['last_submission'] ? date(BUNZ_BUNZILLA_DATE_FORMAT,$cat['last_submission']) : '<em>Never</em>'?></td>
                        <td>
                            
                            <a href="<?=BUNZ_HTTP_DIR,'admin/delete/category/',$cat['id']?>" class='pure-button icon-cancel danger' onclick="confirmDelete(event)">Delete</a>
                            <!-- categorySelectBox($cat['id'],$this->data['categories']) -->
                    </tr>
<?php
    }
?>
                </tbody>
            </table>
<?php
}
?>
<style>
table[border][style] label {
    display: inline;
    border-color: #888;
    border-spacing: 2px;
}
table[border][style] td:first-child:not(:only-child) {
    text-align: right;
}
</style>
            <form action="<?=BUNZ_HTTP_DIR,'admin/add/category'?>" method="post" class="pure-form">
                <table border='1' style="margin: auto;">
                    <thead>
                        <tr>
                            <th colspan="2">create new category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                 <label>title</label>
                            </td>
<td>
                        <input maxlength='255' placeholder='e.g. bug reports...' name='title' type="text">
</td>
                        </tr>
                        <tr class='pure-table-odd'><td>
                        <label>caption</label></td><td>
                        <input maxlength='255' placeholder='e.g. report problems here...' name='caption' type="text">
</td></tr><tr>
<td rowspan="4">
                        <label class='pure-u-1-4'>required fields</label>
</td><td>
                            <label class='pure-u-1-4'><input type="checkbox" name='description'>description</label>
</td></tr><tr class='pure-table-odd'><td>
                            <label class='pure-u-1-4'><input type="checkbox" name='reproduce'>reproduce</label>
</td></tr><tr><td>
                            <label class='pure-u-1-4'><input type="checkbox" name='expected'>expected</label>
</td></tr><tr class='pure-table-odd'><td>
                            <label class='pure-u-1-4'><input type="checkbox" name='actual'>actual</label>
</td></tr>
<tr><td>
                        <label>pick a color</label>
</td><td>
                        <input type="text" class='color' name='color' value='ffffff'>
</td></tr>
<tr class='pure-table-odd'><td>
                        <label>pick an icon</label>
</td><td style="position: relative;">
                        <?= str_replace('<select','<select style="width: 100%"',iconSelectBox()) ?>
</td></tr>
</tbody><tfoot><tr><th colspan="2">
                    <button class='pure-button info' type='submit'><i class='icon-plus'></i> create new category</button>
</th></tr></tfoot></table>
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
                        <th>default</th>
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
                            <input type="radio" name="default_status" id="<?= $id = uniqid() ?>" value="<?=$stat['id']?>"<?=$stat['default']?' checked':''?>/>
                        </td>
                        <td>
                            <label for="<?=$id?>"><?= statusButton($stat['id']) ?></label>
                        </td>
                        <td><?=$stat['total_reports']?></td>
                        <td>
                            <a href="<?=BUNZ_HTTP_DIR,'report/status/',$stat['id']?>" class='pure-button icon-search info'>View</a> 
                            <a href="<?=BUNZ_HTTP_DIR,'admin/edit/status/',$stat['id']?>" class='pure-button icon-pencil-alt success'>Edit</a> 
                            <a href="<?=BUNZ_HTTP_DIR,'admin/delete/status/',$stat['id']?>" class='pure-button icon-cancel danger' onclick="confirmDelete()">Delete</a></td>
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
