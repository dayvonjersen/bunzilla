<?php
$pageTitle = $this->data['subject'];
require BUNZ_TPL_DIR . 'header.inc.php';
?>

<script src="<?= BUNZ_JS_DIR,'highlight.js' ?>"></script>
        <article>
            <h1><?= $this->data['subject'] ?></h1>
            <h2>Submitted by <?= $this->data['email'] ?> at <?= date('r',$this->data['time']) ?></h2>
            <h3>Status: <?= statusButton($this->data['status'])?></h3>
            <div><button>admin actions or something</button></div>
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if(isset($this->data[$field]))
    {
?>
            <fieldset>
                <legend><?= $field ?></legend>
                <blockquote><?= $this->data[$field] ?></blockquote>
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
