<?php
header('Content-Type: application/xml');
echo '<?xml version="1.0"?>',"\n";
?>
<rss version="2.0">
    <channel>
        <title>bunzilla rss feeds!</title>
        <link>http://<?= $_SERVER['HTTP_HOST'] ?></link>
        <description>yay</description>
        <item>
            <title>hi</title>
            <description>/(^_^)\</description>
        </item>
    </channel>
</rss>
