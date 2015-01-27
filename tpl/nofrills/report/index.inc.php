<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title><?= BUNZ_PROJECT_TITLE, ': ', BUNZ_SIGNATURE ?></title>
    </head>
    <body>
        <header>
            <h1><?= BUNZ_PROJECT_TITLE ?> version <?= BUNZ_PROJECT_VERSION ?></h1>
            <h2><?= BUNZ_SIGNATURE ?></h2>
            <h3><?= BUNZ_PROJECT_MISSION_STATEMENT ?></h3>
        </header>
        <main>
            <h4>Category Listing</h4>
<?php
if(empty($this->data['categories']))
{
?>
            <p>No categories have been created yet. <a href="<?= BUNZ_HTTP_DIR ?>cpanel">Go to the control panel and make one</a>.</p>
<?php
} else {
    echo "\t\t\t<ol>\n";

    foreach($this->data['categories'] as $cat)
    {
/**
 * other available variables in $this->data
   ['category' => 'color' => a 6 digit hex for colors
                  'icon'  => a className for webfont icons
    'stats'    => 'total_issues' => number of reports in category
                  'unique_posters' => number of unique emails in category
                  'last_activity' => latest time a report was touched in cat.
                  'open_issues' => number of open reports in category
**/
?>
                <li>
                    <a href="<?= 
BUNZ_HTTP_DIR ?>report/category/<?= $cat['id'] ?>"><?= $cat['title'] ?></a>
                    <p><?= $cat['caption'] ?></p>
                </li>
<?php
    }
    echo "\t\t\t</ol>\n";
}
?>
        </main>
    </body>
</html>
