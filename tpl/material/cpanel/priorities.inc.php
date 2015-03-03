<section id="priorities" class="col s12 primary-text">
    <ul class="tabs">
<?php
if(!empty($this->data['priorities']))
{
?>
        <li class="tab col s6">
            <a href="#viewPriorities" class="secondary-text icon-flashlight">View All</a>
        </li>
<?php
}
?>
        <li class="tab col s6">
            <a href="#createPriority" class="success-text icon-plus">Create New Priority</a>
        </li>
    </ul>
  
<?php
if(!empty($this->data['priorities']))
{
?>  
    <section id="viewPriorities" class="section">
        <div class="section z-depth-5 alert-base">
            <p>Priorities should be limited. A maximum of 128 are allowed.</p>
            <p> Their ids determine their urgency (greater = more important)</p>
            <p>Say something about default...<p>
        </div>
        <form action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/priority" method="post" class="section z-depth-3">
            <div class="row">
                <div class="right-align col s1"><i class="icon-chart"></i><span class="hide-on-med-and-down">Usage</span></div>
                <div class="center col s1"><i class="icon-ok"></i><span class="hide-on-med-and-down">Default</span></div>
                <div class="center col s4"><i class="icon-attention"></i> Priority</div>
                <div class="right-align col s4"><i class="icon-cog"></i> Actions</div>
            </div>
            <div class="divider"></div>
<?php
    $i = 0;
    ($total_reports = selectCount('reports')) || ($total_reports = 1);
    foreach($this->data['priorities'] as $p)
    {
        $usage = round(selectCount('reports', 'priority = '.$p['id'])/$total_reports, 2)*100;
?>
            <div class="row">
                <div class="col s1 right-align large" style="padding-top: 7px"><?= $usage ?>%</div>
                <div class="col s5 input-field" style="position: relative; z-index: 10000;">
                    <p style="margin: 0">
                        <input type="radio" name="default_priority" value="<?= $p['id'] ?>" id="default_priority_<?= $p['id'] ?>" <?= $p['default'] ? 'checked' : ''?>>
                        <label for="default_priority_<?= $p['id'] ?>" style="position: relative; z-index: 10000"><?= priority($p['id']) ?><!--: <?= $p['title'] ?>--></label>
                    </p>
                    <div class="priority-<?=$p['id']?> no-select" style="width: <?=$usage?>%; height: 100%; position: absolute; left: 0; top: 12px; z-index: 0">
                        <div class="align-right small" style="position: relative"><?= $usage ?>%</div>
                    </div>
                </div>
                <div class="col s1">
                    <a href="<?=BUNZ_HTTP_DIR,'search/priority:',$p['id']?>" 
                               class="btn btn-flat btn-floating large secondary-text" 
                               title="search priority"><i class="icon-search"></i></a>
                </div>
                <div class="col s5"><a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/priority/',$p['id']?>" 
                               class="right btn btn-flat z-depth-5 danger-text" 
                               title="delete priority"><i class="icon-delete"></i><span class="hide-on-med-and-down"> Delete</span></a>&emsp;
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/priority/',$p['id']?>" 
                               class="right btn btn-flat z-depth-5 success-text" 
                               title="edit priority"><i class="icon-pencil-alt"></i><span class="hide-on-med-and-down"> Edit</span></a>
                </div>
            </div>
<?php
    }
}
?>
            <div class="row">
                <div  class="col offset-s1 s11">
                    <button type="submit" class="btn btn-flat secondary-text"><i class="icon-ok"></i>Change Default Priority</button>
                    <button type="reset" class="btn btn-flat icon-cancel secondary-text">Reset</button>
                </div>
            </div>
        </form>
    </section>

    <section id="createPriority" class="secondary-base section no-pad-top no-pad-bot">
        <form class="secondary-text z-depth-5 section no-pad" action="<?= BUNZ_HTTP_DIR ?>cpanel/add/priority" method="post">
            <h1 class="icon-plus">Create New Priority</h1>
            <div class="input-field">
                <input id="add-priority-title" type="text" name="title" maxlength="255"/>
                <span class="material-input"></span>
                <label for="add-priority-title">Title</label>
            </div> 
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="add-priority-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color' value='ffffff' maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList() ) ?>
                </div>
            </div>
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-plus">Create Priority!</button>
                <button type="reset" class="btn btn-flat icon-cancel secondary-text">Clear Form</button>
            </div>
        </form>
    </section>
</section>

