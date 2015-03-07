<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title><?= BUNZ_PROJECT_TITLE ?> changelog</title>
    </head>
    <body>
        <header>
            <h1><?= BUNZ_PROJECT_TITLE ?> changelog</h1>
            <h4>generated at <?= date(BUNZ_BUNZILLA_DATE_FORMAT) ?> | current version: <?= BUNZ_PROJECT_VERSION ?></h4>
        </header>
<?php
foreach($this->data['versions'] as $ver)
{
    echo "\t\t<h2>version {$ver}</h2>\n\t\t<ul>\n";
    foreach($this->data['messages'] as $msg)
        echo $msg['version'] == $ver ? "\t\t\t<li>{$msg['message']}</li>\n" : '';
    echo "\t\t</ul>\n";
}
?>
        <footer>
            <?= BUNZ_PROJECT_TITLE ?>
            <q><?= BUNZ_PROJECT_MISSION_STATEMENT ?></q>: 
            <a href="<?= BUNZ_PROJECT_WEBSITE ?>"><?= BUNZ_PROJECT_WEBSITE ?></a>
            <address><?= BUNZ_SIGNATURE, ' ', BUNZ_VERSION ?></address>
        </footer>
    </body>
</html>
