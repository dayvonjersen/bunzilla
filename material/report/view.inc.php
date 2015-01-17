<?php
//
// individual reports : yay
//
$pageTitle = $this->data['report']['subject'];

require BUNZ_TPL_DIR . 'header.inc.php';
//require BUNZ_TPL_DIR . 'post/utils.inc.php';
require_once BUNZ_TPL_DIR . 'displayfuncs.inc.php';
require_once BUNZ_TPL_DIR . 'color.php';

$cat = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];
?>
<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>

<div class="" style="height: 100%">

<!--
    category info
-->
<div class="row">
    <div class="col s12">
        <article>
            <div class="row">
                <!-- 
                    title 
                -->
 <section class='section col s12 z-depth-5 category-<?= $cat['id'] ?>-base'>

                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-plus"></i></a>
<?php
if($this->auth())
{
?>
                    <a href="<?=BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-2 icon-pencil-alt"></i></a>
<?php
}
?>
                    <h4 class="<?= $cat['icon'] ?>"><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>?material"><?= $cat['title'] ?></a></h4>
                    <h6><?= $cat['caption'] ?></h6>

                <!--
                    actions
                -->
                </section>
        </article>
    </div>
</div>


<!--
    report view
-->

<article class="container category-<?= $cat['id'] ?>-base">
    <header class="row">

        <!--
            tab toolbar thing
        -->
        <section class="col s12">
            <div class="row">
            <ul class="tabs waves-effect waves-light z-depth-3">
                <li class="tab col s2"><a href="#status" class="active icon-doc-text-inv category-<?=$cat['id']?>-text"></a></li>
                <li class="tab col s2">
                    <a href="#history" class="icon-history grey white-text"><span class="hide-on-small-only">History</span></a>
                </li>
<?php
if($this->auth())
{
?>
        <!-- admin actions -->
                <li class="tab col s2">
                    <a href="#update" class="icon-magic light-blue white-text"><span class="hide-on-small-only">Update</span></a>
                </li>
                <li class="tab col s2">
                    <a href="#move" class="icon-move yellow black-text"><span class="hide-on-small-only">Move</span></a>
                </li>
                <li class="tab col s2">
                    <a href="#delete" class="icon-delete red white-text"><span class="hide-on-small-only">Delete</span></a>
<?php
}
?>
                </li>
            </ul>

            <!--
                about: time, edits + actions: edit and reply
            -->
            <section class="section col s12" id="status">
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
            edit and reply
        -->
        <section class="section col s4 m4 transparent ">

             <!-- nice javascript m8 -->
            <a href="#comment" onclick="(function(evt){evt.preventDefault();document.getElementById('comment').focus()})(event)" class="waves-effect z-depth-5 btn-large btn-floating blue right" title="post a comment!"><i class="icon-chat"></i></a>

<?php
if($this->auth() || compareIP($report['ip']))
{
?>
            <!-- edit -->
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
                        <p><?= $log['message'] ?><br><small><?=datef($log['time'])?></small></p>
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
                <form class="white z-depth-5 section" action="">
                <?= statusDropdown(false, $report['status']) ?>
                <div class="input-field">
                    <input type="radio" id="toggleClosed_1" name="toggleClosed" class="with-gap"<?=$this->data['closed'] ? ' checked' : ''?>>
                    <label for="toggleClosed_1">
                    <i  class="icon-lock"></i></label>
                
                                <input type="radio" id="toggleOpen_1" name="toggleClosed" class="with-gap"<?=!$this->data['closed'] ? ' checked' : ''?>>
                                <label for="toggleOpen_1" >
                                <i  class="icon-unlock"></i></label>
                            </div>
                            <div class="input-field">
                                <p style="color: #000 !important">priority</p>
                                <div class="range-field">
                                <?= rangeOptions(Cache::read('priorities')) ?>
                                <input name="priority" type="range" min="0" max="<?= count($this->data['priorities']) - 1 ?>" value="<?= $report['priority']?>">
                            </div>
                            </div>
                            <div class="center">
                            <button class="btn light-blue waves-effect icon-magic">Update Status</button>
                            </div>
            </form>
            </section>

        <!--
            admin actions : move (todo: consolidate)
        -->   
            <section class="section col s12 yellow" id="move">
                <h5 class="black-text">move report to</h5>
                <form class="white z-depth-5 section">
                                <?= categoryDropdown(false,$cat['id']) ?>
                <div class="input-field">
                    <input type="radio" id="toggleClosed_2" name="toggleClosed" class="with-gap"<?=$this->data['closed'] ? ' checked' : ''?>>
                    <label for="toggleClosed_2">
                                <i  class="icon-lock"></i></label>
                
                                <input type="radio" id="toggleOpen_2" name="toggleClosed" class="with-gap"<?=!$this->data['closed'] ? ' checked' : ''?>>
                                <label for="toggleOpen_2" >
                                <i  class="icon-unlock"></i></label>
                            </div>
                            <div class="input-field">
                                <p style="color: #000 !important">priority</p>
                                <div class="range-field">
                                <?= rangeOptions(Cache::read('priorities')) ?>
                                <input name="priority" type="range" min="0" max="<?= count($this->data['priorities']) - 1 ?>" value="<?= $report['priority']?>">
                            </div>
    <div>
                            <div class="center">
                            <button class="btn yellow black-text waves-effect icon-move">Move</button>
                            </div>
</div>
                </form>
            </section>


        <!--
            admin actions : delete 
        -->   

            <section class="col s12 red" id="delete">
                <form class="white z-depth-5 center section">
                    <button class="btn red icon-delete waves-red" onclick="alert('y u do dis :<');">Delete (no undo)</button>
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
        <section id="subject" class='section no-pad-top no-pad-bot col s12 z-depth-5 category-<?=$cat['id']?>-text'>

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
<div class="divider" style="clear: both;"></div>
            <a href="#subject" title="subject" class="small left"><em>subject</em>:</a>
            <h2 class="flow-text"><?= $report['subject'] ?></h2>



        </section>
    </header>

    <!--
        description reproduce expected actual
    -->
    <main id="report" class="section z-depth-5 category-<?=$cat['id']?>-darken-1">
<?php
$d = 5;
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($cat[$field])
    {
?>
        <blockquote id="<?=$field?>" class='section no-pad-top z-depth-<?= $d-- ?> category-<?=$cat['id']?>-text'>
            <a href="#<?=$field?>" title="<?=$field?>" class="small left"><em><?=$field?></em>:</a><br>
            <?= $report[$field] ?>
        </blockquote>
<?php
    }
}
?>
    </main>

    <!--
        comments
    -->
    <footer id="comments" style="text-align: left !important; margin: 0 !important">
<?php
if(!empty($this->data['comments']))
{
    $i = 0;
    foreach($this->data['comments'] as $comment)
    {
?>
            <section class="z-depth-2 category-<?=$cat['id']?>-lighten-5" id="reply-<?=$comment['id']?>">
                <header>
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
                <p class='z-depth-2'><?= $comment['message'] ?><?php
if($comment['edit_time'])
{
?>
                <span class="badge right icon-pencil-alt"><em><a href="<?= BUNZ_DIFF_DIR ?>comments/<?= $comment['id']?>"><?= datef($comment['edit_time']) ?></a></em></span>
<?php
}
?></p>
            </section>
                
<?php
    }
?>
<?php
}
if(BUNZ_BUNZILLA_ALLOW_ANONYMOUS || $this->auth())
{
?>
            <!--
                spammer's delight
            -->
            <section class="category-<?=$cat['id']?>-lighten-5 z-depth-3">
                <form class="" action="<?= BUNZ_HTTP_DIR,'post/comment/',$this->data['id'] ?>" method="post">
                    <div class="section">
                        <h4>post a comment</h4>
    
                    <div class="input-field">
                        <i class="icon-mail prefix"></i>
                        <input type="email" id="email" maxlength='255' name='email' value="<?= $this->auth() ? $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled="disabled"' : $this->data['params']['email'] . '" required' ?>>
                        <label for="email">email</label>
                    </div>
                    <div class="input-field">
                        <i class="icon-chat prefix"></i>
                        <textarea id="comment" class="materialize-textarea" required name='message'><?= empty($_POST) ? $this->data['params']['message'] : unfiltermessage($this->data['params']['message']) ?></textarea>
                        <label for="comment">your insight on this issue</label>
                    </div>
                    <div class="input-field center"><button type="submit" class="btn">post!</button></div>
            
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
