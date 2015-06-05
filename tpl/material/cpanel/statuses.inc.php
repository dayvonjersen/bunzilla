<section id="statuses" class="col s12 primary-text shade-base">
     <section class="section shade-text">
        <h1 class="icon-pinboard">Statuses</h1>
        <p>Statuses are what you can use to mark the status or progress of a report. All reports will start off with a (configurable) default status. There is no limit to the number of statuses you can have, though they are limited to 25 characters for visual purposes.</p>
    </section> 
<?php
if(!empty($this->data['statuses']))
{
?> 
    <section id="viewStatuses" class="section row z-depth-3">
        
        <form action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/status#statuses" method="post" class="primary-text section z-depth-3"  id='statusList'>
            <div class="row">
                <div class="right-align col s2">
                    <a href="#viewStatuses" 
                       class="btn-flat sort icon-chart waves-effect" 
                       data-sort="status_usage">Usage<i class="icon-sort"></i>
                    </a>
                </div>
                <div class="left-align col s6">
                    <small>&nbsp;<i class="icon-ok"></i></small>
                    <a href="#viewStatuses"
                       class="btn-flat sort icon-pinboard waves-effect"
                       data-sort="status_title">Title<i class="icon-sort"></i>
                    </a>
                    <a href="#viewStatuses"
                       class="btn-flat sort icon-emo-happy waves-effect"
                       data-sort="status_icon">Icon<i class="icon-sort"></i>
                    </a>
                    <a href="#viewStatuses"
                       class="btn-flat sort icon-css waves-effect"
                       data-sort="status_color">Color<i class="icon-sort"></i>
                    </a></div>
                <div class="right-align col s4"><i class="icon-cog"></i> Actions</div>
            </div>
            
            <div class="divider"></div>
<ul class="list">
<?php
    $i = 0;
    ($total_reports = selectCount('reports')) || ($total_reports = 1);
    $total_statuses = count($this->data['statuses']);
    foreach($this->data['statuses'] as $p)
    {
        $usage = round(selectCount('reports', 'status = '.$p['id'])/$total_reports, 2)*100;
?>
            <li class="row hoverfx">
                <span class="status_icon gone"><?=$p['icon']?></span>
                <span class="status_color gone"><?=strtolower($p['color'])?></span>
                <div class="status_usage col s2 right-align <?= $usage/100 > 1/$total_statuses ? 'h4' : 'h5' ?>"><?= $usage ?>%</div>
                <div class="col s6 input-field" style="position: relative;">
                    <p style="margin: 0">
                        <input type="radio" 
                               name="set_default" 
                               value="<?= $p['id'] ?>" 
                               id="default_status_<?= $p['id'] ?>"
                               <?= $p['default'] ? 'checked' : ''?>>
                        <label for="default_status_<?= $p['id'] ?>" 
                               style="position: static; transform: none; z-index: 1000;">
                            <span class="status_title status-<?= $p['id'] ?> <?= $p['icon'] ?>"
                                  style="padding: 0 0.5em; 
                                         opacity: <?= 0.5 + $usage/200 ?>;
                                         display: inline-block; 
                                         height: 100%;
                                         position: absolute;
                                         z-index: 1">
                                <?= $p['title'] ?>
                            </span>
                        </label>
                    </p>
                    <div class="status-<?=$p['id']?> no-select" 
                         style="width: <?=$usage?>%; 
                                height: 100%; 
                                position: absolute; 
                                left: 35px; top: 0; 
                                pointer-events: none"></div>
                </div>
                <div class="col s4"><a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/status/',$p['id']?>#statuses" 
                               class="waves-effect waves-red right btn-small btn btn-flat btn-floating danger-text" 
                               title="delete status"
onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this status?')){ evt.stopPropagation(); evt.preventDefault();}})(event);"><i class="icon-delete"></i></a>&emsp;
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/status/',$p['id']?>" 
                               class="waves-effect right  btn-small btn btn-flat btn-floating success-base" 
                               title="edit status"><i class="icon-pencil-alt"></i></a>

                    <a href="<?=BUNZ_HTTP_DIR,'search/status:',$p['id']?>" 
                               class="right btn-small btn btn-flat btn-floating secondary-text" 
                               title="Search for where this status is used">
                        <i class="icon-search"></i></a>
                </div>
            </li>
<?php
    }
?></ul><?php
}
?>
            <div class="row">
                <div  class="col offset-s2 s10">
                    <button type="submit" class="waves-effect btn btn-flat secondary-text"><i class="icon-ok"></i>Change Default</button>
                    <button type="reset" class="waves-effect waves-light btn btn-flat icon-cancel secondary-text transparent">Reset</button>
                </div>
            </div>
        </form>
    </section>

    <section id="createStatus" class="row section primary-text z-depth-3">
        <form class="secondary-text z-depth-5 section " action="<?= BUNZ_HTTP_DIR ?>cpanel/add/status#statuses" method="post">
            <h1 class="icon-plus">Create New Status</h1>
            <div class="input-field">
                <input id="add-status-title" type="text" name="title" maxlength="255"/>
                <span class="material-input"></span>
                <label for="add-status-title">Title</label>
            </div> 
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="add-status-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color' value='ffffff' maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList() ) ?>
                </div>
            </div>
            <div class="row">
            <div class="input-field col s12">
                <button type="submit" class="btn secondary-base icon-plus waves-effect">Create!</button>
                <button type="reset" class="btn btn-flat icon-cancel waves-effect transparent secondary-text">Clear Form</button>
            </div>
            </div>
        </form>
    </section>
</section>


