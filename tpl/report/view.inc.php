<?php
$pageTitle = $this->data['subject'];
require BUNZ_TPL_DIR . 'header.inc.php';
?>

<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
<script>hljs.initHighlightingOnLoad();</script>
        <article class='box'>
            <header class='msginfo box'>
                <h2><?= $this->data['subject'] ?></h2>
                <p>Submitted by <a href="mailto:<?= $this->data['email'] ?>"><?= $this->data['email'] ?></a> at <strong><?= date('r',$this->data['time']) ?></strong></p>
                <p>Status: <?= statusButton($this->data['status'])?> | <span class="icon-<?= $this->data['closed'] ? 'lock inactive' : 'unlock info' ?> pure-button"><?=$this->data['closed'] ? 'CLOSED' : 'OPEN'?></span></p>
            </header>
            <section>
                <form action="<?= BUNZ_HTTP_DIR,'report/action' ?>" method="post" class='pure-form'>
                    <fieldset class='is-center'>
                        <legend>actions</legend>
                    
                        <?= statusSelectBox($this->data['status']) ?> <button class='pure-button' type='submit' name='updateStatus'>Update Status</button>
                        <button class='pure-button' type='submit' name='toggleClosed'><?=$this->data['closed'] ? 'Open' : 'Close' ?> Report</button>
                        <button class='pure-button' type='submit' name='delete'>Delete Report</button>
                    </fieldset>
                </form>
            </section>
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if(isset($this->data[$field]))
    {
?>
            <fieldset class='no-border'>
                <legend class='msginfo'><?= $field ?></legend>
                <blockquote class='box'><?= $this->data[$field] ?></blockquote>
            </fieldset>
<?php
    }
}
?>
            <section>insert comments here</section>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
