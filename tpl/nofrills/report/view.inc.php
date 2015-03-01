<?php
$category = $this->data['categories'][$this->data['category_id']];
$report = $this->data['report'];
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <title><?= BUNZ_PROJECT_TITLE, ': ', $report['subject'] ?></title>
    </head>
    <body>
        <article>
            <header>
                <h1><?= $report['subject'], $report['closed'] ? ' [CLOSED]' : '' ?></h1>
                <h2><?= $report['email'] ?></h2>
                <h3><?= date(BUNZ_BUNZILLA_DATE_FORMAT, $report['time']) ?></h3>
            </header>
            <hr/>
            <p>status priority whatever</p>
            <p><button>actions</button><button>or</button><button>something</button></p>
            <main>
<?php
foreach(['description','reproduce','expected','actual'] as $field)
{
    if($category[$field])
    {
?>
                <section><?= $report[$field] ?></section>
<?php
    }
}
?>
            </main>
            <hr/>
            <p>comments or something</p>
        </article>
    </body>
</html>
