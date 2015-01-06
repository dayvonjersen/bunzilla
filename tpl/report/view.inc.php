<?php
$pageTitle = $this->data['subject'];
$bread = [
    
    $this->data['category']['title'] => ['href' => BUNZ_HTTP_DIR.'report/category/'.$this->data['category']['id'],
        'icon' => $this->data['category']['icon'],
        'color' => $this->data['category']['color']
    ],
    $pageTitle => ['href' => BUNZ_HTTP_DIR.$_GET['url'],
        'icon' => 'icon-doc-text-inv',
    ]
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>

<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
  <p class='box'><a href="<?= BUNZ_HTTP_DIR, 'post/category/', $this->data['category']['id'] ?>" class='pure-button info icon-plus pure-u-1'>Submit New <?= $this->data['category']['title'] ?></a></p>
        <article class='card'>
            <header class='msginfo box'>
                <div class='pure-g'>
                    <p title='author' class='pure-u-1-2'><span><?= $this->data['email'], $this->data['epenis'] ? '<span class="info">## Developer</span>' : '' ?></span></p>
                    <p title='date' class='pure-u-1-2'><span><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$this->data['time']) ?></span></p>
                </div>
                <div  class='pure-g'>
                    <div title='subject' class='pure-u-1 pure-u-md-3-4'>
                        <h1><?= $this->data['subject'] ?></h1>
                    </div>
                    <p title="status" class='pure-u-1 pure-u-md-1-4'>
                        <span><?= statusButton($this->data['status'])?></span>
                        <span><span class="icon-<?= $this->data['closed'] ? 'lock inactive' : 'unlock info' ?> pure-button"><?=$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span></span>
<?php
if($this->auth() || dtr_ntop(remoteAddr()) == dtr_ntop($comment['ip']))
{
?>
                        <span><a href="<?= BUNZ_HTTP_DIR,'post/edit/',$this->data['id'] ?>" class='pure-button success icon-pencil-alt'>edit</a></span>
<?php
}
?>
                    </p>
                </div>
<?php
if(!empty($this->data['tags']))
{
?>
                <p class='box icon-tags' title="tagged">
<?php
        foreach($this->data['tags'] as $tag)
            echo tagButton($tag[0]);
?>
                </p>
<?php
}
if(!is_null($this->data['edit_time']))
{
?>
                <h4><strong>**EDIT**</strong> @ <?= date(BUNZ_BUNZILLA_DATE_FORMAT,$this->data['edit_time']) ?></h4>
<?php
}
?>
            
            </header>
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if(isset($this->data[$field]))
    {
?>
            <blockquote class='box' title="<?=$field?>"><?= $this->data[$field] ?></blockquote>
<?php
    }
}

if(!empty($this->data['comments']))
{
?>
            <article class='card icon-chat' title='comments and replies &darr;'>
<?php
    $i = 0;
    foreach($this->data['comments'] as $comment)
    {
?>
            <section id="reply-<?=$comment['id']?>" title="#<?=$i?>">
                <header class='msginfo box'>
                    <p><?= $comment['email'], $comment['epenis'] ? '<span class="info">## Developer</span>' : '' ?> @ <a href="#reply-<?= $comment['id'] ?>"><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$comment['time']) ?> #<?=$i++?></a>

<?php
if($this->auth() || dtr_ntop(remoteAddr()) == dtr_ntop($comment['ip']))
{
?>
                        <a href="<?= BUNZ_HTTP_DIR,'post/edit/',$this->data['id'],'/',$comment['id'] ?>" class='pure-button icon-pencil-alt success'>edit</a>
<?php
}
?></p>
                </header>
                <blockquote class='box'><?= $comment['message'] ?><?php
if(!is_null($comment['edit_time']))
{
?>
                <h4><strong>**EDIT**</strong> @ <?= date(BUNZ_BUNZILLA_DATE_FORMAT,$comment['edit_time']) ?></h4>
<?php
}
?></blockquote>
            </section>
                
<?php
    }
?>
            </article>
<?php
}

if(BUNZ_BUNZILLA_ALLOW_ANONYMOUS || $this->auth())
{
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
                            <textarea rows='3' name='message' placeholder='your insight on this issue'><?= $this->data['params']['message'] ?></textarea>
                        </p>
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
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
