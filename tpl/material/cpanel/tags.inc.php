<section id="tags" class="col s12 primary-text secondary-base section">
  
<?php
if(!empty($this->data['tags']))
{
?>  
    <section id="viewTags" class="section primary-text z-depth-3">
        <h1 class="icon-tags">Tags</h1>
            <div class="row">
                <div class="right-align col s2">
                    <span class="hide-on-med-and-down">Usage</span>
                    <i class="icon-chart"></i>
                </div>
                <div class="left-align col s6">
                <i class="icon-tags"></i> Tag <span class="hide-on-med-and-down">Icon, Title, and Color</span></div>
                <div class="right-align col s4"><i class="icon-cog"></i> Actions</div>
            </div>
            
            <div class="divider"></div>
<?php
    $i = 0;
    ($total_reports = selectCount('reports')) || ($total_reports = 1);
    $total_tags = count($this->data['tags']);
    foreach($this->data['tags'] as $p)
    {
        $usage = round(selectCount('tag_joins', 'tag = '.$p['id'])/$total_reports, 2)*100;
?>
            <div class="row">

                <div class="col s1">
                    <a href="<?=BUNZ_HTTP_DIR,'search/tag:',$p['id']?>" 
                               class="right btn btn-flat btn-floating secondary-text" 
                               title="Search for where this tag is used">
                        <i class="icon-search"></i></a>
                </div>
                <div class="col s1 right-align <?= $usage/100 > 1/$total_tags ? 'large' : 'small' ?>"><?= $usage ?>%</div>
                <div class="col s6 input-field" style="position: relative;">
                        <div>&nbsp;
                            <span class="tag-<?= $p['id'] ?> <?= $p['icon'] ?>"
                                  style="padding: 0 0.5em;
                                         max-height: 1.5em; margin-top: .75rem;
                                         opacity: <?= 0.5 + $usage/200 ?>;
                                         display: inline-block; 
                                         height: 100%;
                                         position: absolute;
                                         z-index: 1">
                                <?= $p['title'] ?>
                            </span>
                        </div>
                    <div class="tag-<?=$p['id']?> no-select" 
                         style="width: <?=$usage?>%; 
                                height: 1.5em; margin-top: .75rem;
                                
                                position: absolute; 
                                left: 7px; top: 0; 
                                pointer-events: none"></div>&nbsp;
                </div>
                <div class="col s4"><a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/tag/',$p['id']?>" 
                               class="waves-effect waves-red right btn btn-flat btn-floating danger-text" 
                               title="delete tag"><i class="icon-delete"></i></a>&emsp;
                            <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/tag/',$p['id']?>" 
                               class="waves-effect right btn btn-flat btn-floating success-base" 
                               title="edit tag"><i class="icon-pencil-alt"></i></a>
                </div>
            </div>
<?php
    }
?>
    </section>
<?php
}
?>

        <div class="section">
            <p>Tags do things.</p>
        </div>

    <section id="createTag" class="row section primary-text z-depth-3">
        <form class="secondary-text z-depth-5 section " action="<?= BUNZ_HTTP_DIR ?>cpanel/add/tag" method="post">
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


