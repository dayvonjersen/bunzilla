<?php
$pageTitle = 'Advanced Search';
$background = 'secondary-base';
require BUNZ_TPL_DIR . 'header.inc.php';
?>
<header class="row">
    <form action="<?= BUNZ_HTTP_DIR ?>search" method="get">
        <section class="section col s12 shade-text z-depth-5">
            <div class="input-field">
                <input type="text" id="searchBox" name="q" />
                <label for="searchBox">Search Terms...</label>
                <span class="material-input"></span>
            </div>
            <ol class="collapsible">
                <li>
                    <div class="section no-pad icon-cog-alt collapsible-header secondary-text no-select waves-effect">
                        Advanced...</div>

                    <div class="collapsible-body section">
                        <div class="divider" style="margin-bottom: 1em"></div>
                        <div class="row section">
                            <div class="input-field col s4">
                                <input type="checkbox">
                                <label>loadsa optionz</label>
                            </div>
                            <?= categoryDropdown('category') ?>
                            <?= statusDropdown('status') ?>
                        </div>
                    </div>
                </li>
            </ol>
            <div class="center ">
                <button type="submit"
                        class="btn secondary-darken-1">
                    <i class="icon-search"></i>
                    Search
                </button>
            </div>
        </section>
            
    </form>
</header>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
