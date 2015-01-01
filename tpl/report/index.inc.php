<?php
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='box'>
            <header>
                <h1><?= BUNZ_PROJECT_TITLE ?></h1>
                <h6>version <?= BUNZ_PROJECT_VERSION ?></h6>
            </header>
            
            <p><?= BUNZ_PROJECT_MISSION_STATEMENT ?></p>
        </article>

        
<?php /*<pre><?= print_r($this->data,1); ?></pre>*/ ?>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
?>
