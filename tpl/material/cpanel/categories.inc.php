<section id="categories" class="col s12 primary-base">
  
<?php
if(!empty($this->data['categories']))
{
?>  
    <section id="viewCategories">
        <section class="section shade-text">
            <h1 class='icon-list-dl'>Categories</h1>
            <p>Merely how reports are grouped. You can create as many categories as you like. Whatever applies to your project and your workflow can be set up.</p>
        </section>
<?php
    $i = 0;
    foreach($this->data['categories'] as $cat)
    {
        echo $i == 0 ? '<div class="row">' : '';
?>
    <div class="col s12 m6">
        <div class="section no-pad-top">
            <section class='section col s12 z-depth-5 category-<?= $cat['id'] ?>-base waves-effect' onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>'; }})(event);">
            <!--
                actions
            -->

                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/delete/category/',$cat['id']?>#categories" 
                       class="right btn-small btn btn-floating z-depth-5 danger-base" 
                       title="delete category"
                        onclick="(function(evt){if(!window.confirm('Are you sure you want to PERMANENTLY(!) DELETE this category and all associated reports and comments?')){ evt.stopPropagation(); evt.preventDefault();}})(event);"><i class="icon-delete"></i></a>

                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/category/',$cat['id']?>" 
                       class="right btn-small btn btn-floating z-depth-5 success-base" 
                       title="edit category"><i class="icon-pencil-alt"></i></a>

            <!-- 
                title 
            -->
                <h2><a href="<?=BUNZ_HTTP_DIR,'report/category/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
                <h6><?= $cat['caption'] ?></h6>
            </section>

        </div>
    </div>
<?php
        if($i++ >= 1 || end($this->data['categories']) === $cat)
        {
            echo '</div>';
            $i = 0;
        } else
            $i++;
    }
}
?>
    </section>

    <section id="moveCategory" class="section alert-base">
        <form class=" z-depth-5 shade-text section" action="<?= BUNZ_HTTP_DIR ?>cpanel/edit/category#categories" method="post">
            <h3 class="icon-move">Move All Reports...</h3>
            <div class="input-field col s12 m6 section">
            <p class="h3">From &darr;</p>
                <?= categoryDropdown(null, null, null, 'from') ?>
            </div>
            <div class="input-field col s12 m6 section">
                <p class="h3">To &rarr;</p>
                <?= categoryDropdown(null, null, null, 'to') ?>
            </div><div class="row">
            <div class="input-field col s12 center">
                <button type="submit" name="move" class="btn alert-base icon-move waves-effect">Move</button>
            </div></div>
        </form>
    </section>        

    <section id="createCategory" class="secondary-base section">
        <form class=" z-depth-5 section shade-text" action="<?= BUNZ_HTTP_DIR ?>cpanel/add/category#categories" method="post">
            <h1 class="icon-plus secondary-text">Create New Category</h1>
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
            <div class="divider" style="margin: 0 0 2em;"></div>
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
            <div class="divider" style="margin-top: -0.5em;"></div>
            <div class="row">
                <div class="input-field col s6">
                    <h5>Color</h5>
                    <input id="add-category-color" type="text" class="color {pickerMode:'HVS',pickerPosition:'bottom',pickerFaceColor:'transparent',pickerFace:3,pickerBorder:0,pickerInsetColor:'transparent'}" name='color' value='ffffff' maxlength='6' />
                </div>
                <div class="input-field col s6">
                    <h5>Icon</h5>
                    <?= dropdown('icon', Cache::getIconList() ) ?>
                </div>
            </div><div class="row">
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-plus waves-effect">Create Category!</button>
                <button type="reset" class="btn btn-flat icon-cancel shade-text waves-effect">Clear Form</button>
            </div></div>
        </form>
    </section>
</section>

