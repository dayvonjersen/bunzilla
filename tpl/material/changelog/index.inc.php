<?php
$pageTitle = BUNZ_PROJECT_TITLE . ' Changelog';
$background = 'primary-base';
require BUNZ_TPL_DIR . 'header.inc.php';

?>
<div class="">
    <div class="row section">
        <div class="col s12 m8">
            <h1 class="icon-history"><?= $pageTitle ?></h1>
            <div class="divider"></div>
            <section class="primary-text section">

            <a data-activates="version-list" 
               style="display: block"
               class="dropdown-button secondary-text btn-flat waves-effect btn">
                <span id="dropdown-button-text">All Versions</span><i class="icon-down-open-mini"></i></a>

            <ul class="dropdown-content" id="version-list">
                <li><a href="#" onclick="$('#dropdown-button-text').text('All Versions'); document.getElementById('expand-all').dispatchEvent(new Event('click'));">All Versions</a></li>
<?php
foreach($this->data['versions'] as $key => $ver)
//for($key = 0, $ver = '0.2'; $key < 5; $key++)
{
?>
                <li><a href="#version-<?=$key?>" onclick="$('#dropdown-button-text').text('Version <?=$ver?>');$('li#version-<?=$key?>').addClass('active');$('li#version-<?=$key?> .collapsible-body').addClass('animated fuckityfuckfuck');$('li:not(#version-<?=$key?>)').removeClass('active');$('li:not(#version-<?=$key?>) .collapsible-body').removeClass('animated fuckityfuckfuck');document.getElementById('version-<?=$key?>').scrollIntoView({behavior:'smooth'});"><?= $ver ?></a></li>
<?php
}
?>
            </ul>
            <a href="#" 
               onclick="$('li').removeClass('active');$('.collapsible-body').removeClass('animated fuckityfuckfuck')"
               class="icon-cancel btn-flat waves-effect danger-text">Collapse All</a>
            <a href="#" id="expand-all"
               onclick="$('li').addClass('active');$('.collapsible-body').addClass('animated fuckityfuckfuck')"
               class="icon-ul btn-flat waves-effect success-text">Expand All</a>
            </section>
        </div>
        <div class="col s12 m4 right-align">
            <h4 class="">current version: <?= BUNZ_PROJECT_VERSION ?></h4>

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/edit"
                data-tooltip="manually edit"
               class="large tooltipped waves-effect  btn btn-floating success-darken-4">
                <i class="icon-pencil-alt"></i></a>

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/plaintext" 
               data-tooltip="view as plaintext"
               class="btn-large tooltipped waves-effect  btn btn-floating shade-text">
                <i class="icon-article-alt"></i></a>

            <a href="?nofrills" 
               data-tooltip="view as unstyled html"
               class="btn-large tooltipped waves-effect  btn btn-floating danger-darken-4">
                <i class="icon-html "></i></a>


        </div>
    </div>
    
<?php
if(empty($this->data['messages']))
{
?>
    <div class="shade-text section">
        <h1 class="alert-text">Nothing to see here!</h1>
        <p>If you would like to see that change, tick the "Update changelog with this comment" when commenting on a report.</p>
        <p>i.e., when you squash a bug, instead of just closing the report, say something like</p>
        <p class="h3 z-depth-3 section"><q>BUGFIX: WidgetFactory no longer causes system crash</q></p>
        <p>and tick the checkbox. That message will appear here.</p>
        <p>You can also manually add (and remove) entries <a href="#" class="secondary-text">here</a>.</p>
    </div>
<?php
}
?>
<ol class="collapsible" data-collapsible="expandable">
<?php
for($i=0;$i<5;$i++){
foreach($this->data['versions'] as $key => $ver)
{

?>
                                                                <?php /** XXX change $i to $key :: **/ ?>
    <li style="margin: 0.5rem 0" class='shade-text z-depth-3' id="version-<?=$i?>"> 
        <div class="right">
            <a href="<?= BUNZ_HTTP_DIR ?>changelog/edit"
                data-tooltip="manually edit "
               class="btn-small tooltipped waves-effect  btn btn-floating success-darken-4">
                <i class="small icon-pencil-alt"></i></a>

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/plaintext/<?= $ver ?>" 
               data-tooltip="view as plaintext"
               class="btn-small tooltipped waves-effect  btn btn-floating shade-text">
                <i class="small icon-article-alt"></i></a>

            <a href="<?= BUNZ_HTTP_DIR?>changelog/<?= $ver ?>?nofrills" 
               data-tooltip="view as unstyled html"
               class="btn-small tooltipped waves-effect  btn btn-floating danger-darken-4">
                <i class="small icon-html "></i></a>
        </div>
        <div class="<?= $ver == BUNZ_PROJECT_VERSION ? ' active ' : ''?>collapsible-header primary-text no-select waves-effect">Version <big class="large"><?= $ver ?></big></div>
        <div class="collapsible-body section">
            <div class="divider"></div>
            <ul>
<?php
    foreach($this->data['messages'] as $msg)
    {
        if($msg['version'] != $ver)
            continue;
?>
                <li><?= $msg['message'] ?><br/><time class="small"><?=date(BUNZ_BUNZILLA_DATE_FORMAT, $msg['time'])?></time></li>
<?php
    }
?>
            </ul>
        </div>
    </li>
<?php
}}
?>
</ol>
</div>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
