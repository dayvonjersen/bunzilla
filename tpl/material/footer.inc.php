        </main>
</div>
<div class="md-overlay"></div>
        <footer class="footer shade-darken-4">
                <div class="row">
                    <div class="section col s6 m4">
                        <h1><?=$_BUNNIES[array_rand($_BUNNIES)]?></h1>
                        <h6><?= BUNZ_SIGNATURE ?> version <?= BUNZ_VERSION ?></h6>
                    </div>
                    <div class="section col s6 m4 right-align">
                        <ul class="">
                            <li class='icon-github'>
                                <a href="https://github.com/generaltso/bunzilla">on github</a>
                            </li>
                            <li class='icon-person'>
                                <a href="https://bunzilla.ga">at home</a>
                            </li>
                            <li class='hide-on-small-only icon-emo-happy'>
                                <a href="http://japaneseemoticons.net/rabbit-emoticons">bunny emoticons</a>
                            </li>
                            <li class='hide-on-small-only icon-emo-shoot'>
                                <a href="http://textcaptcha.com/">textcaptcha.com</a>
                            </li>
                            <li class='hide-on-med-and-up icon-mail'><a href="mailto:țšō@țėķńĭķ.ı0?subject=remove+accents"><span class="hide-on-small-only">tell me what you think</span><span class="hide-on-med-and-up">e-mail me</span></a></li>
                            <li class='hide-on-med-and-up icon-magic'><?= round(microtime(1) - BUNZ_START_TIME,4) ?>s</li>
                        </ul>
                    </div>
                    <div class="section col m4 hide-on-small-only">
                        <ul class="">
                            <li class='icon-mail'><a href="mailto:țšō@țėķńĭķ.ı0?subject=remove+accents">tell me what you think</a></li>
                            <li class='icon-bug'>x_x</li>
                            <li class='icon-magic'><?= round(microtime(1) - BUNZ_START_TIME,4) ?>s</li>
                        </ul>
                    </div>
                </div>
        </footer>
        <script src="<?= BUNZ_JS_DIR ?>jquery-2.1.3.min.js"></script>
        <script src="<?= BUNZ_JS_DIR ?>materialize.js"></script>
        <script src="<?= BUNZ_JS_DIR ?>gnmenu.js"></script>
        <script src="<?= BUNZ_JS_DIR ?>headroom.js"></script>
        <script src="<?= BUNZ_JS_DIR ?>cottontail.js"></script>
<?php
if(!empty($this->flash))
{
?>
        <script>
document.body.onload = function() {
<?php
foreach($this->flash as $msg)
    echo "\t",'toast("',htmlentities($msg,ENT_QUOTES),'",30000);',"\n";
?>
}
        </script>
<?php
}
?>
    </body>
</html>
