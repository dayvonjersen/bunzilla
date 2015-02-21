<section id="tags" class="col s12">
    <ul class="tabs">
<?php
if(!empty($this->data['tags']))
{
?>
        <li class="tab col s6">
            <a href="#viewTags" class="icon-flashlight">View All</a>
        </li>
<?php
}
?>
        <li class="tab col s6">
            <a href="#createTag" class="icon-plus">Create New Status</a>
        </li>
    </ul>
  
<?php
if(!empty($this->data['tags']))
{
?>  
    <section id="viewTags">
<?php
    $i = 0;
    foreach($this->data['tags'] as $cat)
    {
        echo $i == 0 ? '<div class="row">' : '';
?>
    <div class="col s12 m6 l3">
        <div class="section no-pad-top">
            <section class='section col s12 z-depth-5 tag-<?= $cat['id'] ?>-base waves-effect' onclick="(function(evt){ if(!(evt.target instanceof HTMLAnchorElement)){ window.location='<?=BUNZ_HTTP_DIR,'report/status/',$cat['id']?>'; }})(event);">
            <!--
                actions
            -->

                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/tag/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 danger-base" 
                       title="delete tag"><i class="icon-delete"></i></a>


                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/tag/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 alert-base" 
                       title="merge tag"><i class="icon-move"></i></a>

                    <a href="<?=BUNZ_HTTP_DIR,'cpanel/edit/tag/',$cat['id']?>" 
                       class="right btn btn-floating z-depth-5 success-base" 
                       title="edit tag"><i class="icon-pencil-alt"></i></a>

            <!-- 
                title 
            -->
                <h2><a href="<?=BUNZ_HTTP_DIR,'report/tag/',$cat['id']?>" class="<?= $cat['icon'] ?>"><?= $cat['title'] ?></a></h2>
            </section>

        </div>
    </div>
<?php
        if($i++ >= 5 || end($this->data['tags']) === $cat)
        {
            echo '</div>';
            $i = 0;
        } else
            $i++;
    }
}
?>
    </section>

    <section id="createTag" class="secondary-base section no-pad-top no-pad-bot">
        <form class="secondary-text z-depth-5 section no-pad" action="<?= BUNZ_HTTP_DIR ?>cpanel/add/tag" method="post">
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
            <div class="input-field col s12 center">
                <button type="submit" class="btn secondary-base icon-plus">Create Tag!</button>
                <button type="reset" class="btn btn-flat icon-cancel secondary-text">Clear Form</button>
            </div>
        </form>
    </section>
</section>

