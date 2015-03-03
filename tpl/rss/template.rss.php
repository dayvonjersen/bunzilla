<?php
header('Content-Type: text/plain');
header('Content-Type: application/rss+xml; charset=utf-8');

echo '<?xml version="1.0" encoding="utf-8"?>',"\n";
?>
<rss version="2.0">
    <channel>
        <!--required-->
        <title><?= $pageTitle ?></title>
        <link><?= SITE_URL, $thisPage ?></link>
        <description><?= BUNZ_HTTP_DIR ?></description>

        <!--optional-->
        <language>en-US</language>
        <copyright>WTFPL <?= date('Y') ?></copyright>
        <managingEditor>tso@teknik.io</managingEditor>
        <webMaster>tso@teknik.io</webMaster>
        <pubDate><?= date(RSS_DATE_FORMAT) ?></pubDate>
        <lastBuildDate><?= date(RSS_DATE_FORMAT) ?></lastBuildDate>
        <category>bug reports</category>
        <generator>Bunzilla v<?= BUNZ_VERSION ?></generator>
        <docs>https://bunzilla.ga</docs>
<?php
/**
 * unused right now, might be useful down the line
        <!-- N/A: cloud, rating, textInput -->
        <ttl>60</ttl> <!-- time to live in minutes -->
        <image>
            <url>http://localhost</url>
            <title>siteTitle</title>
            <link>http://localhost</link>
            <!--def=88,max=144-->
            <width>90</width>
            <!--def=31,max=400-->
            <height>32</height>
        </image>
        <skipHours>
            <hour>0</hour>
            <hour>23</hour>
        </skipHours>
        <skipDays>
            <day>Monday</day>
            <day>Sunday</day>
        </skipDays>
        <!--all subtags optional-->

**/?>

<?php
if(!isset($rss))
    throw new Exception;

foreach($rss as $item)
{
    echo "\t\t<item>\n";
    foreach($item as $tag => $text)
        echo "\t\t\t<$tag",
            $tag=='guid'?' isPermaLink="true"':'',
            ">$text</$tag>\n";
    echo "\t\t</item>\n";
}
?>
   </channel>
</rss>
