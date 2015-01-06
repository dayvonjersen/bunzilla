<?php
$pageTitle = 'Whoops!';

require BUNZ_TPL_DIR . 'header.inc.php';

?>
<script>
    clearInterval(window.bunnyFooFoo);
</script>
<style>
    #bunny-bar a.pure-menu-heading {
        animation-name: hinge !important;
        animation-duration: 2s;
        animation-delay: 5s;
        animation-iteration-count: 1;
    }
</style>
        <article class='box'>
            <h1>HTTP/1.1 404: Duck Season</h1>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
