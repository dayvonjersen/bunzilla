<?php
//
// individual reports : yay
//

$cat = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];

$pageTitle = $report['subject'];
$background = 'transparent';//"category-{$cat['id']}-base";
require BUNZ_TPL_DIR . 'header.inc.php';
//require BUNZ_TPL_DIR . 'post/utils.inc.php';
require_once BUNZ_TPL_DIR . 'displayfuncs.inc.php';
require_once BUNZ_TPL_DIR . 'color.php';

?>
<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>


<!--
    report view
-->

<article>
    <header class="row">

        <!--
            tab toolbar thing
        -->
        <section class="col s12 z-depth-5">
            <div class="row">
            <ul class="tabs z-depth-3">
                <!-- default yet-as-unnamed tab -->
                <li class="tab col s2"><a href="#status" class="waves-effect active icon-doc-text-inv category-<?=$cat['id']?>-text"></a></li>
                <!-- status log -->
                <li class="tab col s2">
                    <a href="#history" class="waves-effect icon-history grey white-text"><span class="hide-on-small-only">History</span></a>
                </li>
<?php
if($this->auth())
{
?>
                <!-- admin actions -->
                <li class="tab col s2">
                    <a href="#update" class="waves-effect icon-magic light-blue white-text"><span class="hide-on-small-only">Update</span></a>
                </li>
                <li class="tab col s2">
                    <a href="#move" class=" waves-effect icon-move yellow black-text"><span class="hide-on-small-only">Move</span></a>
                </li>
                <li class="tab col s2">
                    <a href="#delete" class="waves-effect icon-delete red white-text"><span class="hide-on-small-only">Delete</span></a>
                </li>
<?php
}
?>
            </ul>

            <!--
                about: time, edits + actions: edit and reply
            -->
            <section class="section col s12 category-<?= $cat['id'] ?>-base" id="status">
                <!--
                    author and time
                -->
                <section class="section col s8 m8 z-depth-3 category-<?=$cat['id']?>-text">
                    <!--
                        email and authlevel
                    -->
                    <p class="icon-mail"><?= $report['email'], 
$report['epenis'] ? ' <span class="badge blue white-text" style="color: white !important">## Developer</span>' : '' ?></p>
                    <!--
                        submission and edit time
                    -->
                    <p class="icon-time" title="submitted at"><?= datef($report['time']) ?></p><?=
$report['edit_time'] ? '<p class="icon-pencil-alt"><a class="icon-time" href="'.BUNZ_DIFF_DIR.'reports/'.$report['id'].'">'.datef($report['edit_time']).'</a></p>' : ''

?>
                </section>

                <!--
                    edit and reply buttons
                -->
                <section class="section col s4 m4 transparent ">

                     <!-- nice javascript m8 -->
                     <a href="#comment" onclick="(function(evt){evt.preventDefault();document.getElementById('comment').focus()})(event)" class="waves-effect z-depth-5 btn-large btn-floating blue right" title="post a comment!"><i class="icon-chat"></i></a>

<?php
if($this->auth() || compareIP($report['ip']))
{
?>
                     <!-- edit post -->
                    <a href="<?= BUNZ_HTTP_DIR ?>post/edit/<?= $report['id'] ?>" class="btn-floating green black-text z-depth-4 right waves-effect " style="margin-right: 2px" title="edit this report"><i class="icon-pencil-alt"></i></a>
<?php
}
?>
                </section>
            </section>

            <!--
                status log
            -->
            <section class="section col s12 grey" id="history">
                    <div class="white section grey-text">
<?php
if(empty($this->data['status_log']))
    echo '<p><em>No history for this report!</em></p>';

foreach($this->data['status_log'] as $log)
{
?>
                        <p><strong><?= $log['who'] ?></strong> <?= $log['message'] ?><br><small><?=datef($log['time'])?></small></p>
<?php
}
?>
                    </div>
            </section>

<?php
if($this->auth())
{
?>
           <!--
                admin actions : update status
            -->        
            <section class="section col s12 light-blue" id="update">
                <h5 class="white-text">update status to</h5>

                <form class="category-<?=$cat['id']?>-lighten-5 z-depth-5 section" 
                      action="<?= BUNZ_HTTP_DIR ?>report/action/<?= $report['id'] ?>?material" 
                      method="post">

                    <fieldset class="row">
                        <div class="input-field col s12 m6">
                            <p>Status:</p>
                            <?= statusDropdown($report['status']) ?>
                        </div>

                        <div class="input-field col s12 m6">
                            <p>Priority:</p>
                            <div class="range-field">
                                <?= rangeOptions(Cache::read('priorities')) ?>
                                <input name="priority" 
                                        type="range" 
                                        min="0" 
                                        max="<?= 
count($this->data['priorities']) 
? count($this->data['priorities']) - 1 
: '"0 disabled title="priority levels have not been defined.'?>" 
                                        value="<?= $report['priority'] ?>">
                            </div>
                        </div>

                        <div class="input-field col s12 center">
                            <button type="submit"
                                    name="updateStatus" 
                                    value="1"
                                    class="btn pink icon-ok">Make Changes</button>
                            <button type="submit" 
                                    name="toggleClosed" 
                                    class="btn icon-<?= $report['closed'] ? 'unlock light-blue' : 'lock grey' ?>"
                                    value="1">AND <?= $report['closed'] ? 'Open' : 'Close' ?> This Report</button>
                            <button type="reset" class="btn-flat icon-cancel">Reset Form</button>
                        </div>
                    </fieldset>
                </form>
            </section>

            <!--
                admin actions : move (todo: consolidate)
            -->   
            <section class="section col s12 yellow" id="move">
                <h5 class="black-text">move report to</h5>
                <form class="white z-depth-5 section grey-text">
                    <h1><em>Coming soon!</em></h1>
<?php
/*
<?= categoryDropdown(false,$cat['id']) ?>
<button class="btn yellow black-text waves-effect icon-move">Move</button>
                           
*/
?>
                </form>
            </section>

            <!--
                admin actions : delete 
            -->   
            <section class="col s12 red" id="delete">
                <form class="white z-depth-5 center section"
                      action="<?= BUNZ_HTTP_DIR ?>report/action/<?= $report['id'] ?>" 
                      method="post">
                    <button name="delete" class="btn red icon-delete waves-red" onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this report and all associated comments?')) evt.preventDefault();})(event);">Delete (no undo)</button>
                </form>
            </section>
<?php
}
?>
        </div>
        </section>

        
    
        <!--
            subject
        -->
        <section class='section no-pad-top no-pad-bot col s12'>

            <section class="z-depth-1 category-<?=$cat['id']?>-text">

<?php

    echo '<p class="left">';
if(!empty($report['tags']))
{
    echo '<i class="icon-tags"></i>';
    //
    // tags!
    //
    foreach($report['tags'] as $tag)
        echo tag($tag[0],0);
}
?>

                <?= priority($report['priority']) ?>
          
           <?= '</p>' ?>


<!-- 
    status, closed 
-->
        <?= status($report['status']) ?>

                <span class="badge right z-depth-2 white-text icon-<?= 
$this->data['closed'] ? 'lock grey' : 'unlock light-blue'?>" title="
<?= 
$this->data['closed'] ? 'closed' : 'open'?>"><span class="hide-on-small-only"><?=
$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span></span>
 <!-- actual subject text -->
<div style="clear: both;"></div>
            <a href="#subject" title="subject" class="small left"><em>subject</em>:</a><br>
            <h2 id="subject" class="" style="margin: 0 1em; clear: both"><?= $report['subject'] ?></h2>



        </section>
    </header>

    <!--
        description reproduce expected actual
    -->
    <main id="report" class="section no-pad-top no-pad-bot">
<?php
$d = 5;
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($cat[$field])
    {
?>
<section class="section no-pad z-depth-<?= $d-- ?> category-<?=$cat['id']?>-text">
        <blockquote id="<?=$field?>">
            <a href="#<?=$field?>" title="<?=$field?>" class="small left"><em><?=$field?></em>:</a><br>
            <?= $report[$field] ?>
        </blockquote>
</section>
<?php
    }
}
?>
    </main>

    <!--
        comments
    -->
    <footer id="comments" class="section no-pad-top no-pad-bot" style="text-align: left !important">
        
<?php
if(!empty($this->data['comments']))
{
    $i = 0;
    foreach($this->data['comments'] as $comment)
    {
?>
            <section class="category-<?=$cat['id']?>-text z-depth-5" id="reply-<?=$comment['id']?>" style="margin: 0 1em;">
                <header class="section no-pad-top no-pad-bot category-<?=$cat['id']?>-darken-3">
                    <p class="icon-chat" style="margin: 10px 0"><?= $comment['email'], $comment['epenis'] ? '<span class="badge light-blue white-text left">## Developer</span>' : '' ?> <a class="right" href="#reply-<?= $comment['id'] ?>"><?= datef($comment['time']) ?> #<?=$i++?></a>

<?php
if($this->auth() || compareIP($comment['ip']))
{
?>
                        <a href="<?= BUNZ_HTTP_DIR,'post/edit/',$report['id'],'/',$comment['id'] ?>" class='badge small green-text right' title="edit this comment"><i class='icon-pencil-alt'></i></a>
<?php
}
?></p>
                </header>
                <blockquote><?= $comment['message'] ?><?php
if($comment['edit_time'])
{
?>
                <span class="badge right icon-pencil-alt"><em><a href="<?= BUNZ_DIFF_DIR ?>comments/<?= $comment['id']?>"><?= datef($comment['edit_time']) ?></a></em></span>
<?php
}
?></blockquote>
            </section>
                
<?php
    }
?>
<?php
}
if(BUNZ_BUNZILLA_ALLOW_ANONYMOUS || $this->auth())
{
require BUNZ_TPL_DIR .'toolsModal.html';
?>
            <!--
                spammer's delight
            -->
            <section class="section">
                <form class="" action="<?= BUNZ_HTTP_DIR,'post/comment/',$this->data['id'] ?>" method="post" id="withToolsModal">
                    <div class="section category-<?=$cat['id']?>-text z-depth-3">
                        <h2><i class="icon-chat prefix"></i>post a comment</h2>
    
                    <div class="input-field">
                        <i class="icon-mail prefix"></i>
                        <input type="email" id="email" maxlength='255' name='email' value="<?= $this->auth() ? $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled="disabled"' : (isset($this->data['params']) ? $this->data['params']['email'] : '') . '" required' ?>>
                        <label for="email">email</label>
                        <span class="material-input"></span>
                    </div>
                    <div class="input-field">
                        <i class="icon-chat prefix"></i>
                        <textarea id="comment" class="materialize-textarea" required name='message'><?= empty($_POST) ? '' : unfiltermessage($this->data['params']['message']) ?></textarea>
<a href="#toolsModal" data-for="message" class="modal-trigger btn-flat waves-effect secondary-lighten-2" onclick="(function(evt){evt.preventDefault()})(event)" title="insert html into your post"><i class="icon-code"></i><span class="hide-on-small-only">insert html</span></a>
                        <label for="comment">your insight on this issue</label>
                        <span class="material-input"></span>
                    </div>
         <p class="input-field">
            <i class="icon-paragraph prefix" style="text-decoration: line-through"></i>
            <input type="checkbox" id="disable_nlbr" name="disable_nlbr" value=1"<?= isset($_POST['disable_nlbr']) ? ' checked' : ''?>>
            <label for="disable_nlbr">Disable insertion of automatic linebreaks (&lt;br/&gt;)</label>
        </p>
<?php
if($this->auth())
{
?>
        <p class="input-field">
            <i class="icon-ok prefix"></i>
            <input type="checkbox" id="changelog" name="changelog" value=1"<?= isset($_POST['changelog']) ? ' checked' : ''?>>
            <label for="changelog">Update Changelog With this Comment</label>
        </p>
<?php
}
?>
        <div class="input-field center">
            <button type="reset" class="btn-flat white shade-text icon-cancel waves-effect"<?php
if(empty($_POST))
 echo <<<JAVASCRIPT
onclick="(function(evt){if(!window.confirm('This action will delete everything you typed.')) evt.preventDefault()})(event)"
JAVASCRIPT;
?>><?= empty($_POST) ? 'Clear' : 'Reset'?> Form</button>
                    <button type="submit" class="btn category-<?= $cat['id'] ?>-darken-4 icon-chat waves-effect">post!</button></div>
        </div>
            
                </form>
            </section>
<?php
}
?>
        </footer>
    </article>

<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
