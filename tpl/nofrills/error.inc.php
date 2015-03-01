<!DOCTYPE HTML>
<html>
    <head>
        <meta charset='utf-8'>
        <title>ERROR</title>
    </head>
    <body>
        <h1>ERROR(s)</h1>
        <ul>
<?php
if(isset($_ERROR))
    foreach($_ERROR as $thing => $problem)
        echo '<li>',$thing,'<ul><li>',$problem,'</li></ul></li>';
?>
        </ul>
    </body>
</html>
