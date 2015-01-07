<?php
$pageTitle = 'Whoops!';

require BUNZ_TPL_DIR . 'header.inc.php';

?>
<script>
    function additionalHax()
    {
        clearInterval(window.bunnyFooFoo);
        setTimeout(function(){
            document.querySelector('#bunny-bar a.pure-menu-heading').style.opacity = 0;
        },2500);
    }
</script>
<style>
    #bunny-bar a.pure-menu-heading {
        animation-name: hinge !important;
        animation-duration: 2s;
        animation-delay: 1s;
        animation-iteration-count: 1;
        transition: opacity 500ms ease-out;
    }
</style>
        <article class='box'>
            <h1>HTTP/1.1 404: Duck Season</h1>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
