<section id="priorities" class="col s12 primary-text alert-base">
      <section class="section shade-text">
            <h1 class="icon-attention">Priorities</h1>
            <p>Like statuses, a default priority will be assigned to all new reports submitted. Unlike statuses, the ID determines the importance of the priority.</p>
            <p>Priorities should be of limited quantity. A maximum of 128 are allowed.</p>
      </section>
<?php
if(!empty($this->data['priorities']))
{
?>  
    <section id="viewPriorities" class="section row">
        
        <form action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/priority#priorities" method="post" class="section z-depth-3 primary-text" id="priorityList">
            <div class="row">
                <div class="right-align col s2">
                    <a href="#viewPriorities" 
                       class="btn-flat sort icon-chart waves-effect"
                       data-sort="priority_usage">Usage<i class="icon-sort"></i>
                    </a>
                </div>
                <div class="left-align col s6">
                    <small>&nbsp;<i class="icon-ok"></i></small>
                    <a href="#viewPriorities"
                       class="btn-flat sort icon-ol waves-effect"
                       data-sort="priority_id">ID#<i class="icon-sort"></i>
                    </a>
                    <a href="#viewPriorities"
                       class="btn-flat sort icon-pinboard waves-effect"
                       data-sort="priority_title">Title<i class="icon-sort"></i>
                    </a>
                    <a href="#viewPriorities"
                       class="btn-flat sort icon-emo-happy waves-effect"
                       data-sort="priority_icon">Icon<i class="icon-sort"></i>
                    </a>
                    <a href="#viewPriorities"
                       class="btn-flat sort icon-css waves-effect"
                       data-sort="priority_color">Color<i class="icon-sort"></i>
                    </a></div>
                <div class="right-align col s4"><i class="icon-cog"></i> Actions</div>
            </div>
            
            <div class="divider"></div>
<ul class="list">
<?php
    $i = 0;
    ($total_reports = selectCount('reports')) || ($total_reports = 1);
    $total_priorities = count($this->data['priorities']);
    foreach($this->data['priorities'] as $p)
    {
        $usage = round(selectCount('reports', 'priority = '.$p['id'])/$total_reports, 2)*100;
?>
            <li class="row hoverfx">
                <span class="priority_color gone"><?=strtolower($p['color'])?></span>
                <span class="priority_icon gone"><?=$p['icon']?></span>
                <span class="priority_title gone"><?=$p['title']?></span>
                <span class="priority_id gone"><?=$p['id']?></span>
                <div class="priority_usage col s2 right-align <?= $usage/100 > 1/$total_priorities ? 'h4' : 'h5' ?>"><?= $usage ?>%</div>
                <div class="col s6 input-field" style="position: relative;">
                    <p style="margin: 0">
                        <input type="radio" 
                               name="set_default" 
                               value="<?= $p['id'] ?>" 
                               id="default_priority_<?= $p['id'] ?>"
                               <?= $p['default'] ? 'checked' : ''?>>
                        <label for="default_priority_<?= $p['id'] ?>" 
                               style="position: static; transform: none; z-index: 1000;">
                            <span class="priority-<?= $p['id'] ?> <?= $p['icon'] ?>"
                                  style="padding: 0 0.5em; 
                                         opacity: <?= 0.5 + $usage/200 ?>;
                                         display: inline-block; 
                                         height: 100%;
                                         position: absolute;
                                         z-index: 1">
                                <?= $p['title'], ' (id: ',$p['id'],')' ?>
                            </span>
                        </label>
                    </p>
                    <div class="priority-<?=$p['id']?> no-select" 
                         style="width: <?=$usage?>%; 
                                height: 100%; 
                                position: absolute; 
                                left: 35px; top: 0; 
                                pointer-events: none"></div>
                </div>
                <div class="col s4">
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/priority/',$p['id']?>#priorities" 
                               class="waves-effect waves-red right btn-small btn btn-flat btn-floating danger-text" 
                               title="delete priority"
onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this priority?')){ evt.stopPropagation(); evt.preventDefault();}})(event);"><i class="icon-delete"></i></a>&emsp;
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/priority/',$p['id']?>" 
                               class="waves-effect right btn-small btn btn-flat btn-floating success-base" 
                               title="edit priority"><i class="icon-pencil-alt"></i></a>
                    <a href="<?=BUNZ_HTTP_DIR,'search/priority:',$p['id']?>" 
                               class="right btn btn-small btn-flat btn-floating secondary-text" 
                               title="Search for where this priority is used">
                        <i class="icon-search"></i></a>
                </div>
            </li>
<?php
    }
?></ul>
            <div class="row">
                <div  class="col offset-s2 s10">
                    <button type="submit" class="waves-effect btn btn-flat secondary-text"><i class="icon-ok"></i>Change Default</button>
                    <button type="reset" class="waves-effect waves-light btn btn-flat icon-cancel secondary-text transparent">Reset</button>
                </div>
            </div>
        </form>
    </section>
<?php
}
?>
    <section id="createPriority" class="row section primary-text z-depth-3">
        <form class="secondary-text z-depth-5 section " action="<?= BUNZ_HTTP_DIR ?>cpanel/add/priority#priorities" method="post">
            <h1 class="icon-plus">Create New Priority</h1>
            <div class="input-field">
                <input id="add-priority-id" type="text" name="id" maxlength="3"/>
                <span class="material-input"></span>
                <label for="add-priority-id">ID</label>
            </div> 
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
            <div class="row">
            <div class="input-field col s12">
                <button type="submit" class="btn secondary-base icon-plus waves-effect">Create!</button>
                <button type="reset" class="btn btn-flat icon-cancel waves-effect transparent secondary-text">Clear Form</button>
            </div>
            </div>
        </form>
    </section>
</section>

