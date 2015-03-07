<?php
//
// individual reports : the only page that actually matters
//
/**
 * 3/3/2015 11:51:10 AM
 * OK this page and category.inc.php have gotten way out of hand
 * in terms of complexity and bullshit
 */

/**
 * 3/5/2015 10:13:32 AM
 * BUT LETS KEEP GOING AND SEE WHAT HAPPENS */
function epenis($epenis)
{
    switch($epenis)
    {
        default: return '';
        case 1:
            $q = ['secondary-base', 'icon-person', 'Developer'];
            break;
        case 2:
            $q = ['alert-base', 'icon-terminal', 'System'];
            break;
    }
    return vsprintf('<span class="badge %s left"><i class="%s"></i>%s</span>',$q);
}

$cat    = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];

$pageTitle = $report['subject'];
$background = 'transparent';
require BUNZ_TPL_DIR . 'header.inc.php';
require_once BUNZ_TPL_DIR . 'displayfuncs.inc.php';
require_once BUNZ_TPL_DIR . 'color.php';
?>
<script src="<?= BUNZ_JS_DIR, 'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
<article>
    <header class="row">
        <!--
            tab toolbar thing
        -->
<?php 
/**
 * 3/3/2015 11:51:16 AM
 * tab toolbar now only for admins
 * we will merge status_log history into the comments section
 * (how github does it) 
 *
 * 3/4/2015 6:00:02 PM that's been done btw */
if($this->auth())
{ 
?>
        <section class="col s12 z-depth-5">
            <div class="row">
            <ul class="tabs z-depth-3">
                <li class="tab col s3">
                    <a href="#status" 
                       class="waves-effect active icon-doc-text-inv category-<?=$cat['id']?>-text"><span class="hide-on-med-and-down">Details</span></a>
                </li>
                <li class="tab col s3">
                    <a href="#update" class="waves-effect icon-magic secondary-base"><span class="hide-on-med-and-down">Update</span></a>
                </li>
                <li class="tab col s3">
                    <a href="#move" class=" waves-effect icon-move alert-text"><span class="hide-on-med-and-down">Move</span></a>
                </li>
                <li class="tab col s3">
                    <a href="#delete" class="waves-effect icon-delete danger-base"><span class="hide-on-med-and-down">Delete</span></a>
                </li>
            </ul>
            </div>
        </section>
<?php
}
?>
            <!--
                DETAILS: time, edits + actions: edit and reply
            -->
            <section class="section no-pad-bot col s12 category-<?= $cat['id'] ?>-darken-1" id="status">
                <!--
                    author and time
                -->
                <section class="section no-pad-bot col s8 m8 z-depth-3 category-<?=$cat['id']?>-text">
                    <!--
                        email and authlevel
                    -->
                    <p class="icon-mail"><?= $report['email'], 
epenis($report['epenis'])?></p>
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
                     <a href="#comment" onclick="(function(evt){evt.preventDefault();document.getElementById('comment').focus()})(event)" class="waves-effect z-depth-5 btn-large btn-floating secondary-base right" title="post a comment!"><i class="icon-chat"></i></a>

<?php
if($this->auth() || compareIP($report['ip']))
{
?>
                     <!-- edit post -->
                    <a href="<?= BUNZ_HTTP_DIR ?>post/edit/<?= $report['id'] ?>" class="btn-small btn btn-floating success-base z-depth-4 right waves-effect " title="edit this report"><i class="icon-pencil-alt"></i></a>
<?php
}
?>
                    <a href="<?=BUNZ_HTTP_DIR,'report/view/',$report['id']?>?rss" 
                       class="right z-depth-3 btn btn-small btn-floating waves-effect waves-orange" style="background: #fff; color: #f86e00;"
                       title="subscribe!"><i class="icon-rss-squared"></i></a>
                </section>
            </section>
<?php
if($this->auth())
{
?>
           <!--
                admin actions : update status
            -->        
            <section class="section col s12 secondary-base" id="update">
                <form class="category-<?=$cat['id']?>-text z-depth-5 section" 
                      action="<?= BUNZ_HTTP_DIR ?>report/action/<?= $report['id'] ?>" 
                      method="post">

                    <div class="row">
                        <div class="input-field col s12 m6 section">
                            <p>Status:</p>
                            <?= statusDropdown($report['status']) ?>
                        </div>

                        <div class="input-field col s12 m6 section">
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
                                    class="waves-effect btn success-base icon-ok">Make Changes</button>
                            <button type="submit" 
                                    name="toggleClosed" 
                                    class="waves-effect btn icon-<?= $report['closed'] ? 'unlock success-text' : 'lock shade-base' ?>"
                                    value="1">AND <?= $report['closed'] ? 'Open' : 'Close' ?> This Report</button>
                            <button type="reset" class="waves-effect waves-light btn-flat transparent shade-text icon-cancel">Reset Form</button>
                        </div>
                    </div>
                </form>
            </section>

            <!--
                admin actions : move (todo: consolidate)
            -->   
            <section class="section col s12 alert-base" id="move">
                <form class="category-<?=$cat['id']?>-text z-depth-5 section" 
                      action="<?= BUNZ_HTTP_DIR ?>report/move/<?= $report['id'] ?>" 
                      method="post">

                    <div class="row">
                        <div class="input-field col s12 m6 section">
                            <p>Move to:</p>
                            <?= categoryDropdown(null, $cat['id']) ?>
                        </div>

                        <div class="input-field col s12 center">
                            <button type="submit"
                                    name="zig" 
                                    value="1"
                                    class="waves-effect btn alert-base icon-move">Move</button>
                        </div>
                    </div>
                </form>
                <form class="category-<?=$cat['id']?>-text z-depth-5 section" 
                      action="<?= BUNZ_HTTP_DIR ?>report/merge/<?= $report['id'] ?>" 
                      method="post">

                    <div class="row">
<div class='col s12 section z-depth-5 shade-lighten-4'><p><i class='icon-move'></i><em class='h2'>Merge is an experimental feature. Its behaviour is untested and subject to change at any time. Please be cautious.</em><p>If you have any suggestions, feedback, or concerns please leave a message on <a href='http://meta.bunzilla.ga/'>the Bunzilla meta-tracker</a>.<p>Thank you and have a <em>very</em> safe and productive day.</div>
                        <div class="input-field col s12 m6 section">
                            <p>Merge Entire Report Into:</p>
                            <input type="text" name="report" maxlength="4" placeholder="yes you have to type the id manually right now"/>
                        </div>

                        <div class="input-field col s12">
                            <input type="checkbox" disabled>
                            <label>Include Subject</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="checkbox" disabled>
                            <label>Include Description</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="checkbox" disabled>
                            <label>Include Comments</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="checkbox" disabled>
                            <label>Delete Original</label>
                        </div>
                        <div class="input-field col s12">
                            <input type="checkbox" disabled>
                            <label>Et cetera, just thinking out loud</label>
                        </div>

                        <div class="input-field col s12 center">
                            <button type="submit"
                                    name="zig" 
                                    value="1"
                                    class="waves-effect btn danger-base icon-move">Merge</button>
                        </div>
                    </div>
                </form>
            </section>

            <!--
                admin actions : delete 
            -->   
            <section class="col s12 danger-lighten-5" id="delete">
                <form class="z-depth-5 center section"
                      action="<?= BUNZ_HTTP_DIR ?>report/action/<?= $report['id'] ?>" 
                      method="post">
                    <button name="delete" class="btn danger-base icon-delete waves-red" onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this report and all associated comments?')) evt.preventDefault();})(event);">Delete (no undo)</button>
                </form>
            </section>
<?php
}
?>

        
    
        <!--
            subject
        -->
        <section class='section category-<?=$cat['id']?>-base col s12'>

            <section class="z-depth-5 category-<?=$cat['id']?>-text no-pad-bot">

<?php

    echo '<p class="left">';
if(!empty($report['tags']))
{
    echo '<i class="icon-tags"></i>';
    //
    // tags!
    //
    foreach($report['tags'] as $tag)
        echo tag($tag,0);
}
?>

                <?= priority($report['priority']) ?>
          
           <?= '</p>' ?>


<!-- 
    status, closed 
-->
        <?= status($report['status']) ?>

                <span class="badge right z-depth-2 icon-<?= 
$this->data['closed'] ? 'lock shade-base' : 'unlock secondary-base'?>" title="
<?= 
$this->data['closed'] ? 'closed' : 'open'?>"><span class="hide-on-small-only"><?=
$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span></span>
 <!-- actual subject text -->
<div style="clear: both;"></div>
            <a href="#subject" title="subject" class="small left"><em>subject</em>:</a><br>
            <h2 id="subject" class="" style="margin: 0 1em; padding-bottom: 1em; clear: both"><?= $report['subject'] ?></h2>



        </section>
    </header>

    <!--
        description reproduce expected actual
    -->
    <main id="report" class="section no-pad-top category-<?=$cat['id']?>-base">
        <div class=" z-depth-5">
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($cat[$field])
    {
?>
    <section class="section category-<?=$cat['id']?>-text">
        <blockquote id="<?=$field?>">
            <a href="#<?=$field?>" title="<?=$field?>" class="small left"><em><?=$field?></em>:</a><br>
            <?= $report[$field] ?>
        </blockquote>
    </section>
<?php
    }
}
?>
        </div>
    </main>

    <!--
        comments
    -->
    <footer id="comments" class="section no-pad-top no-pad-bot category-<?=$cat['id']?>-lighten-1" style="text-align: left !important; margin-top: -1em;">
        
<?php
/**
 * all aboard the failtrain
 * 3/3/2015 12:55:51 PM */
if(!empty($this->data['timeline']))
{
    $statuslog_ids = $statuslog = [];
    foreach($this->data['status_log'] as $log_entry)
    {
        $statuslog_ids[] = $log_entry['id'];
        $statuslog[$log_entry['id']] = $log_entry;
    }
    $comment_ids = $comments = $nested = [];
    foreach($this->data['comments'] as $comment)
    {
        /** 3/3/2015 2:09:26 PM
         * this is what started all this in the first place */
        if(isset($comment['reply_to']) && in_array($comment['reply_to'], $comment_ids))
        {
            if(isset($nested[$comment['reply_to']]))
                $nested[$comment['reply_to']][] = $comment['id'];
            else
                $nested[$comment['reply_to']] = [$comment['id']];
        } else {
            $comment_ids[] = $comment['id'];
        }
        $comments[$comment['id']] = $comment;
    }

    $i = 0;
    foreach($this->data['timeline'] as $eh)
    {
        /**
         * choo choo */
        if(in_array($eh['id'], $statuslog_ids) && in_array($eh['id'],$comment_ids))
            throw new RuntimeException("IT HAPPENED. FUCK. DEFCON9\n\nahem. a status and a comment share the same id. tell tso.");

        if(in_array($eh['id'], $statuslog_ids))
        {
            $log = $statuslog[$eh['id']];
?>
            <section class="section no-pad shade-text z-depth-2" style="margin: 10px 1em 0 1em">
                <blockquote class="small">
                    <span class="right" ><?=datef($log['time'])?></span>
                        <strong><?= $log['who'] ?>&emsp;&emsp;</strong>
                        <p><?= $log['message'] ?></p>
                </blockquote>
            </section>
<?php
        } elseif(in_array($eh['id'],$comment_ids)) {
            $comment = $comments[$eh['id']];
?>
            <section class="category-<?=$cat['id']?>-text z-depth-5" 
                     id="reply-<?=$comment['id']?>" 
                     style="margin: 0 1em;">

                <header class="section no-pad-top no-pad-bot category-<?=$cat['id']?>-darken-3">

                    <p style="margin: 10px 0"><?= $comment['email'], epenis($comment['epenis']) ?> 

                        <a class="right" href="#reply-<?= $comment['id'] ?>"> #<?=$i++?></a>
                        <span class="right small"><?= datef($comment['time']) ?>&emsp;&emsp;</span>
                    </p>

                </header>
                <blockquote>
<?php
if($this->auth() || compareIP($comment['ip']))
{
?>
                        <a href="<?= BUNZ_HTTP_DIR,'post/edit/',$report['id'],'/',$comment['id'] ?>" 
                           class='btn btn-floating btn-small waves-effect waves-green right' 
                           title="edit this comment" position: absolute; right: 2rem;"><i class='icon-pencil-alt'></i></a>
<?php
}
?><?= $comment['message'] ?><?php
if($comment['edit_time'])
{
?>
                <div class="divider"></div>
                <p class="icon-pencil-alt"><strong>edit</strong> <em><a href="<?= BUNZ_DIFF_DIR ?>comments/<?= $comment['id']?>"><?= datef($comment['edit_time']) ?></a></em></p>
<?php
}
?></blockquote>
<?php
/**
 * nested view; only 1 deep right now */
if(isset($nested[$comment['id']]))
{
    $reply_to = $comment['id'];
?>
                <footer class="section no-pad-top no-pad-bot z-depth-3"><span class="h1 hide-on-small-only">&#8600;</span>
<?php
    $j = 0;
    foreach($nested[$reply_to] as $reply)
    {
        $comment = $comments[$reply];
?>
            <section class="category-<?=$cat['id']?>-text z-depth-5" id="reply-<?=$comment['id']?>" style="margin: 0 1em;">

                <header class="section no-pad-top no-pad-bot category-<?=$cat['id']?>-darken-3">
                    <p style="margin: 10px 0">
                        <?= $comment['email'], epenis($comment['epenis']) ?> 
                        <a class="right" href="#reply-<?= $comment['id'] ?>"> #<?=$j++?></a>
                        <span class="right small"><?= datef($comment['time']) ?>&emsp;&emsp;</span>
                    </p>
                </header>

                <blockquote><?php
if($this->auth() || compareIP($comment['ip']))
{
?>
                        <a href="<?= BUNZ_HTTP_DIR,'post/edit/',$report['id'],'/',$comment['id'] ?>" class='btn btn-floating btn-small waves-effect waves-green right' title="edit this comment" position: absolute; right: 4rem;"><i class='icon-pencil-alt'></i></a>
<?php
}
?><?= $comment['message'] ?><?php
if($comment['edit_time'])
{
?>
                <div class="divider"></div>
                <p class="icon-pencil-alt"><strong>edit</strong> <em><a href="<?= BUNZ_DIFF_DIR ?>comments/<?= $comment['id']?>"><?= datef($comment['edit_time']) ?></a></em></p>
<?php
}
?>
                </blockquote>
            </section>
<?php
    }
?>
                    

                <a href="#reply-<?=$reply_to?>" 
                   onclick="(function(evt){evt.preventDefault();document.getElementById('comment').value = '&gt;&gt;<?= $reply_to?>';document.getElementById('comment').focus()})(event)"
                   style="margin-left: 1rem"
                   class="waves-effect z-depth-5 large center btn-floating secondary-base tooltipped"
                   data-tooltip="quotereply: use with caution"
                   data-position="right">&#8618;</a>
            </footer>
<?php
}
 /** fucking shoot me 3/3/2015 2:20:42 PM */
?>
            </section>
                
<?php
        }
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
                        <label for="comment">your insight on this issue</label>
                        <span class="material-input"></span>
                    </div>
         <p class="input-field" style="margin-top: 1em">
            <input type="checkbox" id="disable_nlbr" name="disable_nlbr" value=1"<?= isset($_POST['disable_nlbr']) ? ' checked' : ''?>>
            <label for="disable_nlbr">
            <i class="icon-paragraph" style="text-decoration: line-through"></i>Disable insertion of automatic linebreaks (&lt;br/&gt;)</label>
        </p>
<?php
if($this->auth())
{
?>
        <p class="input-field">
            <input type="checkbox" id="changelog" name="changelog" value=1"<?= isset($_POST['changelog']) ? ' checked' : ''?>>
            <label for="changelog">
            <i class="icon-ok"></i>Update Changelog With this Comment</label>
        </p>
<?php
}
?>
        <div class="input-field center">
            <a href="#toolsModal" data-for="message" class="btn-flat waves-effect secondary-lighten-3" onclick="toggleModal(event)"><i class="icon-code"></i> toggle toolbar</a>
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
