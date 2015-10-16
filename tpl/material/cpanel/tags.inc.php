<section id="tags" class="col s12 primary-text secondary-base">
        <section class="section shade-text">
            <h1 class="icon-tags">Tags</h1>
            <p>Tags describe issues at a glance when such descriptions pan many categories. They can also be used as convenient search terms.</p>
        </section>
  
<?php
if(!empty($this->data['tags']))
{
?> 
    <style>
.hoverfx:hover {
    background: #eee;
}
#viewTags .label {
    padding: 5px 0.5em ;
    max-height: 1.5em; 
    display: inline-block; 
    height: 100%;
    position: absolute; 
    z-index: 1;
}
#viewTags .graph {
    height: 1.5em; 
    position: absolute; 
    left: 7px; top: 0;
    pointer-events: none
}
</style>
    <section id="viewTags" class="section row z-depth-3">
        <div class="primary-text section" id="tagList">
            <div class="row">
                <div class="right-align col s2">
                    <a href="#viewTags" 
                       class="btn-flat sort icon-chart waves-effect"
                       data-sort="tag_usage">Usage<i class="icon-sort"></i>
                    </a>
                </div>
                <div class="left-align col s6">
                    <a href="#viewTags"
                       class="btn-flat sort icon-pinboard waves-effect"
                       data-sort="tag_title">Title<i class="icon-sort"></i>
                    </a>
                    <a href="#viewTags"
                       class="btn-flat sort icon-emo-happy waves-effect"
                       data-sort="tag_icon">Icon<i class="icon-sort"></i>
                    </a>
                    <a href="#viewTags"
                       class="btn-flat sort icon-css waves-effect"
                       data-sort="tag_color">Color<i class="icon-sort"></i>
                    </a></div>
                
                <div class="right-align col s4"><i class="icon-cog"></i> Actions</div>
            </div>
            
            <div class="divider"></div>
<ul class="list">
<?php
    $i = 0;
    ($total_reports = selectCount('reports')) || ($total_reports = 1);
    $total_tags = count($this->data['tags']);
    foreach($this->data['tags'] as $p)
    {
        $usage = round(selectCount('tag_joins', 'tag = '.$p['id'])/$total_reports, 2)*100;
?>
            <li class="row hoverfx">
                <span class="tag_title gone"><?=$p['title']?></span>
                <span class="tag_icon gone"><?=$p['icon']?></span>
                <span class="tag_color gone"><?=strtolower($p['color'])?></span>

                <div class="tag_usage col s2 right-align <?= $usage/100 > 1/$total_tags ? 'h4' : 'h6' ?>"><?= $usage ?>%</div>
                <div class="col s6 input-field" style="position: relative; line-height: 1;">
                        <div>&nbsp;
                            <span class="tag-<?= $p['id'] ?> <?= $p['icon'] ?> label"
                                  style="opacity: <?= 0.5 + $usage/200 ?>;">
                                <?= $p['title'] ?>
                            </span>
                        </div>
                    <div class="tag-<?=$p['id']?> no-select graph" 
                         style="width: <?=$usage?>%;"></div>&nbsp;
                </div>
                <div class="col s4"><a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/tag/',$p['id']?>#tags" 
                               class="waves-effect waves-red right btn-small btn btn-flat btn-floating danger-text" 
                               title="delete tag"
                                onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this tag?')){ evt.stopPropagation(); evt.preventDefault();}})(event);"><i class="icon-delete"></i></a>&emsp;
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/tag/',$p['id']?>" 
                               class="waves-effect right btn btn-flat btn-small btn-floating success-base" 
                               title="edit tag"><i class="icon-pencil-alt"></i></a>

                    <a href="<?=BUNZ_HTTP_DIR,'search/tag:',$p['id']?>" 
                               class="right btn btn-small btn-flat btn-floating secondary-text" 
                               title="Search for where this tag is used">
                        <i class="icon-search"></i></a>
                </div>
            </li>
<?php
    }
?>
</ul>
        </div>
    </section>

<?php
}
?>
    <section id="createTag" class="row section primary-text z-depth-3">
        <form class="secondary-text z-depth-5 section " action="<?= BUNZ_HTTP_DIR ?>cpanel/add/tag#tags" method="post">
            <h1 class="icon-plus">Create New Tag</h1>
            <div class="input-field">
                <input id="add-tag-title" type="text" name="title" maxlength="255"/>
                <span class="material-input"></span>
                <label for="add-tag-title">Title</label>
            </div> 
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="add-tag-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color' value='ffffff' maxlength='6' />
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


