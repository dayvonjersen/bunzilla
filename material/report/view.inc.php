<?php
//
// individual reports : yay
//
$pageTitle = $this->data['report']['subject'];

require BUNZ_TPL_DIR . 'header.inc.php';
//require BUNZ_TPL_DIR . 'post/utils.inc.php';
$cat = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];
?>
<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>

<div class="category-<?= $cat['id'] ?>-base">

<div class="row">
    <div class="col s12">
        <article>
            <div class="row">
                <!-- 
                    title 
                -->
<a href="<?= BUNZ_HTTP_DIR,'report/category/',$cat['id'] ?>?material" class="category-<?=$cat['id']?>-text">
                <section class='section col s8 z-depth-5 category-<?= $cat['id'] ?>-text'>
                    <h4 class="category-<?= $cat['id'] ?>-text <?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h4>
                    <h6><?= $cat['caption'] ?></h6>
                </section>
</a>
                <!--
                    actions
                -->
                <section class="col s4 right-align">
                    
<?php
if($this->auth())
{
?>
                    <a href="<?=BUNZ_HTTP_DIR,'admin/edit/category/',$cat['id']?>" 
                       class="btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-4 icon-pencil-alt"></i></a>
<?php
}
?>
                    <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" 
                       class="btn btn-floating z-depth-5 transparent" 
                       title="submit new"><i class="green-text darken-4 icon-plus"></i></a>
                </section>
        </article>
    </div>
</div>

        <article>
            <header class='row'>
                <section class='section col s6 z-depth-2 category-<?=$cat['id']?>-lighten-1'>
                    <p><?= $report['email'], $report['epenis'] ? ' <span class="badge blue white-text">## Developer</span>' : '' ?></span></p>
                    <p><span><?= datef($report['time']) ?></span></p>
                </section>
                <section class='section col s6 z-depth-1 category-<?=$cat['id']?>-lighten-5'>
                    <?= status($report['status']) ?>
                    <span class="badge icon-<?= $this->data['closed'] ? 'lock grey' : 'unlock light-blue' ?> "><?=$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span>
<?php
if($this->auth() || dtr_ntop(remoteAddr()) == dtr_ntop($report['ip']))
{
?>
                        <span><a href="<?= BUNZ_HTTP_DIR,'post/edit/',$this->data['id'] ?>" class='btn icon-pencil-alt'>edit</a></span>
<?php
}
?>
                </section>
            </header>

            <header class='row' id="hi">
                <section class='section col s12 z-depth-3 category-<?=$cat['id']?>-lighten-5'>
                    <h2 class="center-align"><?= $report['subject'] ?></h2>
                </section>
<?php
if(!empty($report['tags']))
{
    echo '<p class="icon-tags">';
    foreach($report['tags'] as $tag)
        echo tag($tag[0],0);
    echo '</p>';
}

if(!is_null($report['edit_time']))
{
?>
                <h6 class="z-depth-4 icon-pencil-alt"><strong>**EDIT**</strong> @ <?= datef($report['edit_time']) ?></h6>
<?php
}
?>
            </header>
<?php
$d = 5;
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($cat[$field])
    {
?>
            <section class='container flow-text z-depth-<?= $d-- ?> category-<?=$cat['id']?>-lighten-5'><span class="badge grey white-text"><?=$field?></span><p><?= $report[$field] ?></blockquote></p>
<?php
    }
}

if(!empty($this->data['comments']))
{
?>
        </article>
        <article id="comments" class="container">
<?php
    $i = 0;
    foreach($this->data['comments'] as $comment)
    {
?>
            <section class=" z-depth-2 category-<?=$cat['id']?>-lighten-4" id="reply-<?=$comment['id']?>">
                <header>
                    <p class="icon-chat"><?= $comment['email'], $comment['epenis'] ? '<span class="badge light-blue white-text">## Developer</span>' : '' ?> @ <a href="#reply-<?= $comment['id'] ?>"><?= datef($comment['time']) ?> #<?=$i++?></a>

<?php
/*
if($this->auth() || dtr_ntop(remoteAddr()) == dtr_ntop($comment['ip']))
{
?>
                        <a href="<?= BUNZ_HTTP_DIR,'post/edit/',$this->data['id'],'/',$comment['id'] ?>" class='btn icon-pencil-alt'>edit</a>
<?php
}*/
?></p>
                </header>
                <section class='z-depth-2'><?= $comment['message'] ?><?php
if($comment['edit_time'])
{
?>
                <h6 class="icon-pencil-alt"><strong>**EDIT**</strong> @ <?= datef(BUNZ_BUNZILLA_DATE_FORMAT,$comment['edit_time']) ?></h6>
<?php
}
?></section>
            </section>
                
<?php
    }
?>
            </article>
<?php
}
/*
if(BUNZ_BUNZILLA_ALLOW_ANONYMOUS || $this->auth())
{
postFormatWidget();
?>
            <section class='box' title='post a comment'>
                <form action="<?= BUNZ_HTTP_DIR,'post/comment/',$this->data['id'] ?>" method="post" class='pure-form pure-form-aligned'>
                    <fieldset class='is-center'>
                         <p class='pure-control-group'>
                            <label>email</label>
                            <input maxlength='255' name='email' type="text" value="<?= $this->auth() ? $_SERVER['PHP_AUTH_USER'] .'@'. $_SERVER['SERVER_NAME'] .'" disabled="disabled' : $this->data['params']['email'] ?>">
                        </p>
                        <p class='pure-control-group' title="<?=$placeholder?>">
                            <label>message</label>
                            <textarea rows='3' name='message' placeholder='your insight on this issue'><?= empty($_POST) ? $this->data['params']['message'] : unfiltermessage($this->data['params']['message']) ?></textarea>
                        </p>
<?= tagList(db()->query('SELECT * FROM tags')->fetchAll(PDO::FETCH_ASSOC)) ?>
                        <button type='submit' class='pure-button icon-plus'>post!</button>
                    </fieldset>
                </form>
            </section>

<?php
}
if($this->auth())
{
?>
<script>
function confirmDelete(evt){if(!window.confirm('you know what you doing'+"\n\n"+'(this action will permanently delete all associated comments)')) evt.preventDefault();}
</script>
            <section class='box' title='actions'>
                <form action="<?= BUNZ_HTTP_DIR,'report/action/',$this->data['id'] ?>" method="post" class='pure-form'>
                    <fieldset class='is-center'>

                        <?= statusSelectBox($this->data['status']) ?> <button class='pure-button success' type='submit' name='updateStatus' value="1">&rarr;Update Status</button>
                  
                        <button class='pure-button icon-<?= $this->data['closed'] ? 'unlock' : 'lock' ?>' type='submit' name='toggleClosed' value="1"><?=$this->data['closed'] ? 'Open' : 'Close' ?> Report</button>
                        <button class='pure-button danger icon-cancel' type='submit' onclick="confirmDelete(event)" name='delete' value="1">Delete Report</button>
                    </fieldset>
                </form>
            </section>
<?php
}
?>
        </article>
<?php
*/
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
