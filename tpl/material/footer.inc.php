        </main>
</div>
        <footer class="footer shade-darken-4">
            <a style="position: fixed; bottom: 2em; right: 2em; " class="btn-floating black white-text" title="helo r u lost" href="#"><i class="icon-up-open-mini"></i></a>
                <div class="row">
                    <div class="section col s12 m4">
                        <h1><?=$_BUNNIES[array_rand($_BUNNIES)]?></h1>
                        <h6><?= BUNZ_SIGNATURE ?> version <?= BUNZ_VERSION ?></h6>
                    </div>
                    <div class="section col s12 m4 right-align hide-on-small-only">
                        <ul class="">
                            <li class='icon-github'>
                                <a href="https://github.com/generaltso/bunzilla">on github</a>
                            </li>
                            <li class='icon-person'>
                                <a href="https://var.abl.cl/">at home</a>
                            </li>
                            <li class='icon-emo-happy'>
                                <a href="http://japaneseemoticons.net/rabbit-emoticons">bunny emoticons</a>
                            </li>
                        </ul>
                    </div>
                    <div class="section col s12 m4 hide-on-small-only">
                        <ul class="">
                            <li class='icon-mail'><a href="mailto:țšō@țėķńĭķ.ı0?subject=remove+accents">tell me what you think</a></li>
                            <li class='icon-bug'>x_x</li>
                            <li class='icon-magic'><?= round(microtime(1) - BUNZ_START_TIME,4) ?>s</li>
                        </ul>
                    </div>
                </div>
        </footer>
        <script src="<?= BUNZ_HTTP_DIR,'tpl/material/' ?>jquery-2.1.3.min.js"></script>
        <script src="<?= BUNZ_HTTP_DIR,'tpl/material/' ?>materialize.js"></script>
        <script src="<?= BUNZ_HTTP_DIR,'tpl/material/' ?>gnmenu.js"></script>
        <script src="<?= BUNZ_HTTP_DIR,'tpl/material/' ?>headroom.js"></script>
        <script src="<?= BUNZ_HTTP_DIR,'tpl/material/' ?>cottontail.js"></script>
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
