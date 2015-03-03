<?php
if(isset($_ERROR) && $_ERROR['severity'] != 'DEPRECATED.')
{
    header('Content-Type: text/plain');
    print_r($_ERROR);
    exit;
}

header('HTTP/1.1 503 Service Unavailable');
?>
<h1>There is no RSS feed available for the requested URL.</h1>
<h2>Sorry about that.</h2>

<p>If you feel you have received this page in error, please report it!</p>
