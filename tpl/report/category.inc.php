<?php
$pageTitle = $this->data['category']['title'];
$bread = [
    $pageTitle => BUNZ_HTTP_DIR.$_GET['url']
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article class='card' style='background: #<?= $this->data['category']['color'] ?>'>
            <header class='box'>
                <h1 class="<?= $this->data['category']['icon'] ?>" style="color: #<?=$this->data['category']['color']?> !important;"><?= $pageTitle ?></h1>
            <h6 title='caption'><?= $this->data['category']['caption'] ?></h2>
            </header>
            <p class='box'><a href="<?= BUNZ_HTTP_DIR, 'post/category/', $this->data['category']['id'] ?>" class='pure-button info icon-plus pure-u-1'>Submit New <?= $this->data['category']['title'] ?></a></p>
            
            <table class='pure-table pure-table-horizontal'>
                <thead>
                    <tr>
                        <th>subject</th>
                        <th>status</th>
                        <th>submitted at</th>
                    </tr>
                </thead>
                <tbody>
<?php
foreach($this->data['reports'] as $i => $report)
{
?>
                    <tr<?= $i&1 ? ' class="pure-table-odd"' : ''?>>
                        <td><a href="<?= BUNZ_HTTP_DIR, 'report/view/', $report['id'] ?>"><?= $report['subject'], $report['closed'] ? '<i class="icon-lock" title="Closed"></i>' : '' ?></a></td>
                        <td><?= statusButton($report['status']) ?></td>
                        <td><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$report['time']) ?></td>
                    </tr>
<?php
}
?>
                </tbody>
            </table>
        </article>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
