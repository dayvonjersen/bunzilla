<?php
$pageTitle = $this->data['subject'];
$bread = [
    db()->query('SELECT title FROM categories WHERE id = '.$this->data['category'])->fetchColumn(0) => BUNZ_HTTP_DIR.'report/category/'.$this->data['category'],
    $pageTitle => BUNZ_HTTP_DIR.$_GET['url']
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>

<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
        <article class='card'>
            <header class='msginfo box'>
                <h1 title='subject'><?= $this->data['subject'] ?></h1>
                <p title='author'><?= $this->data['email'], $this->data['epenis'] ? '<span class="info">## Developer</span>' : '' ?></p>
                <p title='date'><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$this->data['time']) ?></p>
                <p title='status'>
                    <?= statusButton($this->data['status'])?>
                    <span class="icon-<?= $this->data['closed'] ? 'lock inactive' : 'unlock info' ?> pure-button"><?=$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span>
                </p>
            
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
?>
            <section>insert comments here</section>
<?php
if($this->auth)
{
?>
<script>
function confirmDelete(evt){if(!window.confirm('you know what you doing'+"\n\n"+'(this action will permanently delete all associated comments)')) evt.preventDefault();}
</script>
            <section class='box' title='actions'>
                <form action="<?= BUNZ_HTTP_DIR,'report/action/',$this->data['id'] ?>" method="post" class='pure-form'>
                    <fieldset>

                        <?= statusSelectBox($this->data['status']) ?> <button class='pure-button success' type='submit' name='updateStatus' value="1">&rarr;Update Status</button>
                  
                        <button class='pure-button icon-<?= $this->data['closed'] ? 'lock' : 'unlock' ?>' type='submit' name='toggleClosed' value="1"><?=$this->data['closed'] ? 'Open' : 'Close' ?> Report</button>
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
