<?php
$pageTitle = $this->data['category']['title'];
$bread = [
    $pageTitle => ['href' => BUNZ_HTTP_DIR.$_GET['url'],
        'icon' => $this->data['category']['icon'],
        'color' => $this->data['category']['color']
    ],
];
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<div class="row"><div class="col s12">
        <article class='card small' style='background: #<?= $this->data['category']['color'] ?>'>
            <header class='card-image'>
                <span class="card-title <?= $this->data['category']['icon'] ?>"><?= $pageTitle ?></span>
            </header>
            <section class="card-content">
                <p class="white" title='caption'><?= $this->data['category']['caption'] ?></p>
            </section>
            <section class="card-action">
            <p class='box'><a href="<?= BUNZ_HTTP_DIR, 'post/category/', $this->data['category']['id'] ?>" class='icon-plus'>Submit New <?= $this->data['category']['title'] ?></a></p>
            </section>
        </article>
</div></div>
<div class="container" style="background: #<?= $this->data['category']['color'] ?>">
            <table class='striped hoverable'>
                <thead>
                    <tr>
                        <th>subject</th>
                        <th>status</th>
                        <th>submitted at</th>
                    </tr>
                </thead>
                <tbody>
<?php

function statusButton($id,&$status)
{
    
    return '<span class="badge tag" style="background-color: #'.$status[$id]['color'].' !important;" data-title="'.$status[$id]['title'].'"><i class="'.$status[$id]['icon'].'"></i></span>';
}

foreach($this->data['reports'] as $i => $report)
{
?>
                    <tr<?= $i&1 ? ' class="pure-table-odd"' : ''?>>
                        <td><a href="<?= BUNZ_HTTP_DIR, 'report/view/', $report['id'] ?>"><?= $report['subject'], $report['closed'] ? '<i class="icon-lock" title="Closed"></i>' : '' ?></a></td>
                        <td><?= statusButton($report['status'],$this->data['statuses']) ?></td>
                        <td><?= date(BUNZ_BUNZILLA_DATE_FORMAT,$report['time']) ?></td>
                    </tr>
<?php
}
?>
                </tbody>
            </table>
</div>
<?php
require BUNZ_TPL_DIR .'footer.inc.php';
