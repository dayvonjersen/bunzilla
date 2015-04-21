<?php
$pageTitle = BUNZ_PROJECT_TITLE . ' Changelog';
$background = 'transparent';//primary-base';
require BUNZ_TPL_DIR . 'header.inc.php';

?>
    <div class="row section">
        <div class="section no-pad-top z-depth-4 primary-base col s12 m8">
            <h1 class="icon-history"><?= $pageTitle ?></h1>
            <h4>current version: <?= BUNZ_PROJECT_VERSION ?></h4>
            
            <section class="section no-pad-bot center" data-textlabel="viewing:">

            <a data-activates="version-list" 
               class="dropdown-button secondary-base btn-flat waves-effect btn">
                <span id="dropdown-button-text">All Versions</span><i class="icon-down-open-mini"></i></a>

            <ul class="dropdown-content" id="version-list">
                <li><a href="#" onclick="$('#dropdown-button-text').text('All Versions'); document.getElementById('expand-all').dispatchEvent(new Event('click'));">All Versions</a></li>
<?php
foreach($this->data['versions'] as $key => $ver)
{
?>
                <li><a href="#version-<?=$key?>" onclick="$('#dropdown-button-text').text('Version <?=$ver?>');$('li#version-<?=$key?>').addClass('active');$('li#version-<?=$key?> .collapsible-body').addClass('animated fuckityfuckfuck');$('li:not(#version-<?=$key?>)').removeClass('active');$('li:not(#version-<?=$key?>) .collapsible-body').removeClass('animated fuckityfuckfuck');document.getElementById('version-<?=$key?>').scrollIntoView({behavior:'smooth'});"><?= $ver ?></a></li>
<?php
}
?>
            </ul>
            <a href="#" 
               onclick="$('li').removeClass('active');$('.collapsible-body').removeClass('animated fuckityfuckfuck')"
               class="icon-cancel btn waves-effect shade-lighten-4" title="collapse all"><span class="hide-on-med-and-down">Collapse All</span></a>
            <a href="#" id="expand-all"
               onclick="$('li').addClass('active');$('.collapsible-body').addClass('animated fuckityfuckfuck')"
               class="icon-ul btn waves-effect shade-lighten-4" title="expand all"><span class="hide-on-med-and-down">Expand All</span></a>
            </section>
        </div>
        <div class="col s12 m4 right-align section">
<!--            <a href="<?= BUNZ_HTTP_DIR ?>changelog/edit"
                data-tooltip="manually edit"
               class="large tooltipped waves-effect  btn btn-floating success-darken-4">
                <i class="icon-pencil-alt"></i></a>-->

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/plaintext" 
               data-tooltip="view as plaintext"
               class="btn-large tooltipped waves-effect  btn btn-floating shade-text z-depth-3">
                <i class="icon-article-alt"></i></a>

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/plainhtml" 
               data-tooltip="view as unstyled html"
               class="btn-large tooltipped waves-effect  btn btn-floating danger-darken-4 z-depth-3">
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
<!--        <p>You can also manually add (and remove) entries <a href="#" class="secondary-text">here</a>.</p> -->
    </div>
<?php
}
?>
<ol class="collapsible" data-collapsible="expandable" style="list-style: none">
<?php
foreach($this->data['versions'] as $key => $ver)
{

?>
    <li style="margin: 0.5rem 0" class='shade-text z-depth-3' id="version-<?=$key?>"> 
        <div class="right section ">
            <!--<a href="<?= BUNZ_HTTP_DIR ?>changelog/edit"
                data-tooltip="manually edit "
               class="btn-small tooltipped waves-effect  btn btn-floating success-darken-4">
                <i class="small icon-pencil-alt"></i></a>-->

            <a href="<?= BUNZ_HTTP_DIR ?>changelog/plaintext/<?= $ver ?>" 
               data-tooltip="view as plaintext"
               class="btn-small tooltipped waves-effect  btn btn-floating shade-text z-depth-2">
                <i class="small icon-article-alt"></i></a>

            <a href="<?= BUNZ_HTTP_DIR?>changelog/plainhtml/<?= $ver ?>" 
               data-tooltip="view as unstyled html"
               class="btn-small tooltipped waves-effect  btn btn-floating danger-darken-4 z-depth-2">
                <i class="small icon-html "></i></a>
        </div>
        <div class="<?= $ver == BUNZ_PROJECT_VERSION ? ' active ' : ''?>collapsible-header primary-text no-select waves-effect"><h2><?= $ver ?></h2></div>
        <div class="collapsible-body section">
            <div class="divider"></div>
            <ul>
<?php
    foreach($this->data['messages'] as $msg)
    {
        if($msg['version'] != $ver)
            continue;
?>
                <li><span class="h5 right">(<?=datef($msg['time'])?>)&emsp;</span><?= $msg['message'] ?></li>
<?php
    }
?>
            </ul>
        </div>
    </li>
<?php
}
?>
</ol>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
