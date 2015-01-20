<?php
ob_clean();

$pageTitle = 'Whoops!';

require BUNZ_TPL_DIR .'header.inc.php';

if(isset($_ERROR))
{
?>
    <article class="yellow container">
        <header><h1>PHP Error</h1></header>
<?php
    foreach($_ERROR as $thing => $problem)
        echo '<section class="section white"><strong>',$thing,':</strong> <pre>',$problem,'</pre></section>',"\n";
?>
    </article>
<?php
    exit;
}
?>
<h1>HTTP/1.1 404: Duck Season</h1>
<h2>That's an error</h2>
<h3>That's all we know</h3>
<h4>I hope that isn't copyrighted</h4>

<?php
require BUNZ_TPL_DIR .'footer.inc.php';
