<?php
//
// individual reports : the only page that actually matters
//
$cat    = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];

$pageTitle  = $report['subject'];
$background = 'category-'.$cat['id'].'-base';

require_once BUNZ_TPL_DIR . 'header.inc.php';
//
// note: always refer to displayfuncs.inc.php 
// for what custom function calls do
//
require_once BUNZ_TPL_DIR . 'displayfuncs.inc.php';
require_once BUNZ_TPL_DIR . 'color.php';

//
// highlight.js for code highlighting
//
?>
<script src="<?= BUNZ_JS_DIR, 'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
<article>
    <header class="row">
<?php 
//
// tab toolbar for admin actions
//
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
//
// author and time
//
?>
            <section class="section no-pad-bot col s12 category-<?= $cat['id'] ?>-darken-1" id="status">
                <section class="section col s8 m8 z-depth-3 category-<?=$cat['id']?>-text">
                    <p class="icon-mail"><?= $report['email'], epenis($report['epenis'])?></p>
                    <p class="icon-time" title="submitted at"><?= datef($report['time'],'right') ?></p>
                </section>

<?php
// 
// post a comment
//
?>
                <section class="col s4 m4 transparent ">
                     <a href="#message" 
                        onclick="(function(evt){evt.preventDefault();document.getElementById('message').focus()})(event)" 
                        class="waves-effect z-depth-5 btn-large btn-floating secondary-base right" 
                        title="post a comment!"><i class="icon-chat"></i></a>
<?php
//
// edit original post
//
if($this->auth() || remoteAddr() === $report['ip'])
{
?>
                     <!-- edit post -->
                    <a href="<?= BUNZ_HTTP_DIR ?>post/edit/<?= $report['id'] ?>" 
                       class="btn-small btn btn-floating success-base z-depth-4 right waves-effect " 
                       title="edit this report"><i class="icon-pencil-alt"></i></a>
<?php
}
//
// subscribe with rss
//
?>
                    <a href="<?=BUNZ_HTTP_DIR,'report/view/',$report['id']?>?rss" 
                       class="right z-depth-3 btn btn-small btn-floating waves-effect waves-orange" 
                       style="background: #fff; color: #f86e00;"
                       title="subscribe!"><i class="icon-rss-squared"></i></a>
                </section>
            </section>
<?php
//
// admin actions
//
if($this->auth())
{
//
// update status, priority, and/or close report
//
?>
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
<?php
    if(count($this->data['priorities']))
        echo ' max="',count($this->data['priorities']) - 1,'"';
    else
        echo ' max="0" disabled title="priority levels have not been defined."';
?>
                                        value="<?= $report['priority'] ?>">
                            </div>
                        </div>
                        <div class="input-field col s12 center">
                            <button type="submit"
                                    name="updateStatus" 
                                    value="1"
                                    class="waves-effect btn secondary-base icon-ok">Make Changes</button>
                            <button type="submit" 
                                    name="toggleClosed" 
                                    class="waves-effect btn icon-<?= $report['closed'] ? 'unlock success-lighten-3' : 'lock shade-base' ?>"
                                    value="1">AND <?= $report['closed'] ? 'Open' : 'Close' ?> This Report</button>
                            <button type="reset" class="waves-effect waves-light btn-flat transparent shade-text icon-cancel">Reset Form</button>
                        </div>
                    </div>
                </form>
            </section>
<?php
//
// move or merge report
//
?>
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
                        <div class='col s12 section z-depth-5 shade-lighten-4 h5'>
                            <p><i class='icon-move'></i>Merge is an experimental
                            feature. Please be cautious.</p>
                            <p>If you have any suggestions, feedback, or concerns
                            please leave a message on 
                            <a href='http://meta.bunzilla.ga/'>the Bunzilla meta-tracker</a>.</p>
                            <p>Thank you and have a <em>very</em> safe and productive day.</p>
                        </div>
                        <div class="input-field col s12 m6 section">
                            <p>Merge Entire Report Into:</p>
                            <input type="text" name="report" maxlength="4" placeholder="yes you have to type the id manually right now"/>
                        </div>
<?php
/**
 * TODO: auto-suggest search for reports
 * implement these types of options to merge specific details
 - Include Subject
 - Include Description/Expected/Reproduce/Actual
 - Include Comments
 - Add Tags
 - Set target report to open/closed
 - Set target report to priority
 - Set target report to status
 */
?>
                        <div class="input-field col s12 center">
                            <button type="submit"
                                    name="zig" 
                                    value="1"
                                    class="waves-effect btn danger-base icon-move">Merge</button>
                        </div>
                    </div>
                </form>
            </section>
<?php
//
// delete entire report
//
?>
            <section class="col s12 danger-lighten-5" id="delete">
                <form class="z-depth-5 center section"
                      action="<?= BUNZ_HTTP_DIR ?>report/action/<?= $report['id'] ?>" 
                      method="post">
                    <button name="delete" 
                            class="btn danger-base icon-delete waves-red" 
                            onclick="(function(evt){if(!window.confirm(
                    'Are you sure you want to PERMANENTLY(!) DELETE this report and all associated comments?')) evt.preventDefault();})(event);"
                    >Delete (no undo)</button>
                </form>
            </section>
<?php
}
//
// end admin actions
//

//
// subject, tags, status, priority, whether it's closed or not
//
?>
      <section class='section no-pad-top category-<?=$cat['id']?>-base col s12'>
            <section class="z-depth-5 category-<?=$cat['id']?>-text no-pad-bot">
                <p class="left" style="position:relative;top:-3px">
<?php
if(!empty($report['tags']))
    foreach($report['tags'] as $tag)
        echo tag($tag,0);
?>
                    <?= priority($report['priority']); ?>
                </p>
                <?= status($report['status']) ?>
                <span class="badge right z-depth-2 icon-<?= 
$report['closed'] ? 'lock shade-base' : 'unlock secondary-base'?>" 
                      title="<?= $report['closed'] ? 'closed' : 'open'?>">
                    <span class="hide-on-small-only"><?= $report['closed'] ? 'CLOSED' : 'OPEN'?></span>
                </span>
                <div style="clear: both;"></div>
                <a href="#subject" title="subject" class="small left"><em>subject</em>:</a><br/>
                <h2 id="subject" 
                    style="margin: 0 1em; padding-bottom: 1em; clear: both"
                ><?= $report['subject'] ?></h2>
            </section>
        </section>
    </header>
<?php
//
// description reproduce expected actual
//
?>
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
//
// was it edited?
//
if($report['edit_time'])
{
?>
        <section class="section category-<?=$cat['id']?>-text">
            <div class="divider"></div>
            <p class="icon-pencil-alt"><strong>edit</strong> <em><a href="<?= BUNZ_DIFF_DIR ?>reports/<?= $report['id']?>"><?= datef($report['edit_time'],'right') ?></a></em></p>
        </section>
<?php
}
?>
        </div>
    </main>
<?php
//
// timeline of comments and status updates
//
if(!empty($this->data['timeline']))
{
//
// delete comments is a form for admins
//
    if($this->auth())
    {
?>
    <form action="<?= BUNZ_HTTP_DIR ?>report/action/<?=$report['id']?>" method="post">
<?php
    }
?>
    <footer id="comments" 
            class="section no-pad-top no-pad-bot category-<?=$cat['id']?>-lighten-1" 
            style="text-align: left !important; margin-top: -1em;">
<?php
    /**
     * all aboard the failtrain */
    $statuslog_ids = $statuslog = [];
    foreach($this->data['status_log'] as $log_entry)
    {
        $statuslog_ids[] = $log_entry['id'];
        $statuslog[$log_entry['id']] = $log_entry;
    }
    $comment_ids = $comments = $nested = [];
    foreach($this->data['comments'] as $comment)
    {
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
        if(in_array($eh['id'], $statuslog_ids))
        {
            $log = $statuslog[$eh['id']];
?>
            <section class="shade-text z-depth-2" style="padding-bottom: 0.5rem; margin: 10px 1em 0 1em">
                <blockquote class="small" style="padding-bottom: 0">
                    <span class="right" ><?=datef($log['time'])?></span>
                        <strong><?= $log['who'] ?>&emsp;</strong>
                        <span><?= $log['message'] ?></span>
                </blockquote>
            </section>
<?php
        } 

        if(in_array($eh['id'],$comment_ids)) {
            $comment = $comments[$eh['id']];
            displayComment($comment,$i++,$this->auth(),$cat,$report,$nested,$comments);
        }
    }
?>

        </footer>
<?php
    if($this->auth())
    {
?>
        <div class="section no-pad-bot input-field center">
            <button type="submit" 
                   class="btn danger-base icon-delete waves-effect" 
                   onclick="(function(evt){if(!window.confirm('There is no undo for this action!')) evt.preventDefault()})(event)"
            >Delete Selected Comments</button>
            <button type="reset" class="btn-flat waves-effect icon-cancel">Reset Form</button>
        </div>
        </form>
<?php
    }
}

//
// abstracted this out for nesting
//
function displayComment( $comment, $number, $authUser = false, $cat, $report,$nested=[],$comments=[] )
{
?>
<article class="category-<?=$cat['id']?>-text z-depth-5" 
         id="reply-<?=$comment['id']?>" 
         style="margin: 0 1em;">
    <header class="category-<?=$cat['id']?>-darken-3">
        <p style="margin: 10px 1rem 0 0">
<?php
// delete checkbox for admins
if($authUser)
{
?>
        <input type="checkbox" 
               name="delete_comments[]"
               value="<?= $comment['id']?>" 
               id="del_comment_<?=$comment['id']?>">
        <label title="Delete this comment" class="shade-text" for="del_comment_<?=$comment['id']?>">
            <i class="icon-delete"></i>
        </label>
<?php
}
?>
        <?= $comment['email'], epenis($comment['epenis']) ?> 
            <a class="right" href="#reply-<?= $comment['id'] ?>"> #<?=$number?></a>
            <span class="right"><?= datef($comment['time']) ?>&emsp;&emsp;</span>
        </p>
    </header>
    <section class="section" style="padding-left: 0">
    <blockquote>
<?php
    if($authUser || remoteAddr() === $comment['ip'])
    {
?>
<a href="<?= BUNZ_HTTP_DIR,'post/edit/',$report['id'],'/',$comment['id'] ?>" 
   class='btn btn-floating btn-small waves-effect waves-green right' 
   title="edit this comment" 
   style="position: absolute; right: 2rem;"><i class='icon-pencil-alt'></i></a>
<?php
    }
    echo $comment['message'], '</blockquote></section>';

    if($comment['edit_time'])
    {
?>
<div class="divider"></div>
<p class="icon-pencil-alt">
    <strong>edit</strong> 
    <em>
        <a href="<?= BUNZ_DIFF_DIR ?>comments/<?= $comment['id']?>">
            <?= datef($comment['edit_time'],'right') ?></a>
    </em>
</p>
<?php
    }
?>
<?php
    /**
     * nested view; only 1 deep right now */
    if(isset($nested[$comment['id']]))
    {
        $reply_to = $comment['id'];
?>
<footer class="section no-pad-top no-pad-bot z-depth-3">
    <span class="h1 hide-on-small-only">&#8600;</span>
<?php
        $j = 0;
        foreach($nested[$reply_to] as $reply)
        {
            displayComment($comments[$reply],$j++,$authUser,$cat,$report);
        }
?>
<a href="#reply-<?=$reply_to?>" 
       onclick='(function(evt){evt.preventDefault();document.getElementById("message").value += "&gt;&gt;<?= $reply_to?>\n";document.getElementById("message").focus()})(event)'
       style="margin-left: 1rem"
       class="waves-effect z-depth-5 large center btn-floating secondary-base tooltipped"
       data-tooltip="quotereply: use with caution"
       data-position="right">&#8618;</a>
</footer>
<?php
    }
    echo '</article>';
}

//
// post a comment
//
if(BUNZ_BUNZILLA_ALLOW_ANONYMOUS || $this->auth())
{
require BUNZ_TPL_DIR .'toolsModal.html';
?>
        <footer class="row">
            <section class="section col s12 m8 offset-m2 l6 offset-l3">
                <form action="<?= BUNZ_HTTP_DIR,'post/comment/',$report['id'] ?>" 
                      method="post" 
                      id="withToolsModal">
                        <div class="section no-pad-bot" style="margin-top: -6.5em">
                            <div class="valign-wrapper" style="justify-content: flex-end;-webkit-box-pack:end;-webkit-justify-content:flex-end;-ms-flex-pack:end;position:relative;top:5.5em;right:-.25em">
                                <a onclick="toggleModal(event)" 
                                   class="btn btn-floating btn-flat waves-effect secondary-lighten-3 icon-code tooltipped"
                                   data-tooltip="toolbar"
                                   data-for="message"
                                   href="#toolsModal"></a>&emsp;
                                <a onclick="previewtest();"
                                   class="btn btn-floating btn-flat icon-magic waves-effect primary-base tooltipped"
                                   data-tooltip="preview"
                                   href="#message"></a>&emsp;
                                <button 
<?php
            if(empty($_POST))
             echo <<<JAVASCRIPT
                                    onclick="(function(evt){if(!window.confirm('This action will delete everything you typed.')) evt.preventDefault()})(event)"
JAVASCRIPT;
?>
                                        class="btn btn-flat btn-floating btn-flat white shade-text icon-cancel waves-effect tooltipped"
                                        data-tooltip="reset"
                                        type="reset"></button>&emsp;
                                <button class="btn btn-floating z-depth-3 btn-large h1 category-4-darken-4 icon-chat waves-effect tooltipped"
                                        data-tooltip="post!"
                                        type="submit"></button>
                            </div>
                        </div>
                        <div class=" section no-pad-bot category-<?=$cat['id']?>-text">
                    <div class="input-field" style="width:50%">
                        <i class="icon-mail prefix"></i>
                        <input type="email" 
                               id="email" 
                               name='email'
                               maxlength='255'
                               value=<?= 
$this->auth() ? '"' . $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled' 
    : '"'.(isset($this->data['params']) ? $this->data['params']['email'] : '') . '" required' 
?>>
                        <label for="email">email</label>
                        <span class="material-input"></span>
                    </div>
                    <div class="input-field">
                        <i class="icon-chat prefix"></i>
                        <textarea id="message" 
                                  class="materialize-textarea" 
                                  required 
                                  name='message'
                                  rows='10'><?= 
empty($_POST) ? '' : unfiltermessage($this->data['params']['message']) 
?></textarea>
                        <label for="comment">your insight on this issue</label>
                        <span class="material-input"></span>
                    </div>
                    <div class="collapsible no-select" style="padding: 1em 0">
                    <div class="collapsible-header" style="position: relative; top: -1em; left: 1em">
                        <i class="icon-cog" style="margin-top: -.5em; margin-left: -1em; font-size: 1.5rem"></i>Options...</div>
                    <div class="collapsible-body">
                    <p class="input-field" style="margin-top: 1em">
                        <input type="checkbox" 
                               id="disable_nlbr" 
                               name="disable_nlbr" 
                               value="1" <?= isset($_POST['disable_nlbr']) ? ' checked' : ''?>>
                        <label for="disable_nlbr">
                            <i class="icon-paragraph" style="text-decoration: line-through"></i>
                            Disable insertion of automatic linebreaks (&lt;br/&gt;)</label>
                    </p>
                    <p class="input-field">
                        <input type="checkbox" id="literal_tabs" checked>
                        <label for="literal_tabs">
                            <i class="icon-tab"></i>
                            Pressing the [TAB] key inserts &quot;\t&quot;</label>
                    </p>
                    <p class="input-field">
                        <input type="checkbox" 
                               id="disable_html" 
                               name="disable_html" 
                               value="1" <?= isset($_POST['disable_html']) ? ' checked' : ''?>>
                        <label for="disable_html">
                            <i class="icon-cancel"></i>
                            Disable HTML/bbCode entirely</label>
                    </p>
<?php
    if($this->auth())
    {
?>
                    <p class="input-field">
                        <input type="checkbox" 
                               id="changelog" 
                               name="changelog" 
                               value="1" <?= isset($_POST['changelog']) ? ' checked' : ''?>>
                        <label for="changelog">
                            <i class="icon-ok"></i>
                            Update Changelog With this Comment</label>
                    </p>
<?php
    }
?>
                    </div>
<?php

    if(isset($_SESSION['captcha']))
    {
?>
                    <div class="input-field">
                        <i class="icon-emo-shoot prefix"></i>
                        <input type="text"
                               style="padding-left:2em"
                                id="captcha"
                                name="captcha"
                                required
                                value="">
                        <span class="material-input"></span>
                        <label for="captcha"
                               style="padding-left:2em"
                        >CAPTCHA: <?= htmlspecialchars($_SESSION['captcha']->q) ?></label>
                    </div>
<?php
    }
?>

                </form>
            </section>
<?php
}
?>
        </footer>
    </article>
<?php
require BUNZ_TPL_DIR . 'diffModal.html';

/**
 * don't ask */
$this->data['params']['comment_id'] = -1;
$fields = ['message'=>-1];
$cat['message'] = -1;
$pageAction = 'post/comment/'.$report['id'];
require BUNZ_TPL_DIR . 'previewModal.html';
require BUNZ_TPL_DIR . 'footer.inc.php';
