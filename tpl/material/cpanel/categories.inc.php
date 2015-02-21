<section id="categories" class="col s12">
    <ul class="tabs">
<?php
if(!empty($this->data['categories']))
{
?>
        <li class="tab col s6">
            <a href="#viewCategories" class="icon-flashlight">View All</a>
        </li>
<?php
}
?>
        <li class="tab col s6">
            <a href="#createCategory" class="icon-plus">Create New Category</a>
        </li>
    </ul>
  
<?php
if(!empty($this->data['categories']))
{
?>  
    <section id="viewCategories">
<?php
    $i = 0;
    foreach($this->data['categories'] as $cat)
    {
        echo $i == 0 ? '<div class="row">' : '';
?>
    <div class="col s12 m6 l3">
        <div class="section no-pad-top">
            <section class='section col s12 z-depth-5 category-<?= $cat['id'] ?>-base waves-effect' onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>'; }})(event);">
            <!--
                actions
            -->
                <a href="<?=BUNZ_HTTP_DIR,'post/category/',$cat['id']?>" class="waves-effect btn btn-floating z-depth-5 right category-<?= $cat['id'] ?>-base" title="submit new"><i class="icon-plus"></i></a>
            <!-- 
                title 
            -->
                <h2><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
                <h6><?= $cat['caption'] ?></h6>
            </section>

        </div>
    </div>
<?php
        if($i++ >= 5 || end($this->data['categories']) === $cat)
        {
            echo '</div>';
            $i = 0;
        } else
            $i++;
    }
}
?>
    </section>

    <section id="createCategory" class="secondary-base section no-pad-top no-pad-bot">
        <form class="secondary-text z-depth-5 section no-pad" action="<?= BUNZ_HTTP_DIR ?>cpanel/add/category" method="post">
            <h1 class="icon-plus">Create New Category</h1>
            <div class="input-field">
                <input id="add-category-title" type="text" name="title" maxlength="255"/>
                <span class="material-input"></span>
                <label for="add-category-title">Title</label>
            </div>
            <div class="input-field">
                <input id="add-category-caption" type="text" name="caption" maxlength="255"/>
                <span class="material-input"></span>
                <label for="add-category-caption">Caption</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="add-category-desc" name="description" value="1"/>
                <label for="add-category-desc">Require Description</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="add-category-repr" name="reproduce" value="1"/>
                <label for="add-category-repr">Require Reproduce</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="add-category-expe" name="expected" value="1"/>
                <label for="add-category-expe">Require Expected</label>
            </div>
            <div class="input-field">
                <input type="checkbox" id="add-category-actu" name="actual" value="1"/>
                <label for="add-category-actu">Require Actual</label>
            </div>
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="add-category-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color' value='ffffff' maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList() ) ?>
                </div>
            </div>
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-plus">Create Category!</button>
                <button type="reset" class="btn btn-flat icon-cancel secondary-text">Clear Form</button>
            </div>
        </form>
    </section>
</section>

