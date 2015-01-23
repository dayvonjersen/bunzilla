<?php
$pageTitle = 'cpanel';

require_once BUNZ_TPL_DIR . 'header.inc.php';
?>
<article class="container pink">
    <header class="row">
        <h1><?= BUNZ_PROJECT_TITLE, ' :: ', $pageTitle ?></h1>

        <section class="col s12">
            <div class="row">
                <ul class="tabs">
                    <li class="tab col s2"><a class="icon-chart" href="#statistics">Statistics</a></li>
                    <li class="tab col s2"><a class="icon-doc-text-inv" href="#changelog">Changelog</a></li>
                    <li class="tab col s2"><a class="icon-cog" href="#cron">Cron Settings</a></li>

                    <li class="tab col s1"><a class="icon-list-dl" href="#categories">Categories</a></li>
                    <li class="tab col s1"><a class="icon-pinboard" href="#statuses">Statuses</a></li>
                    <li class="tab col s1"><a class="icon-tags" href="#tags">Tags</a></li>
                    <li class="tab col s1"><a class="icon-attention" href="#priorities">Priorities</a></li>

                    <li class="tab col s1"><a class="icon-delete" href="#purge">Purge</a></li>
                    <li class="tab col s1"><a class="icon-database" href="#export">Export</a></li>
                </ul>

                <section id="statistics">
<pre><?= print_r($this->data['statistics'],1) ?></pre>
                </section>

                <section id="changelog">
<pre><?= print_r($this->data['changelog'],1) ?></pre>
                </section>

                <section id="categories" class="col s12">
                    <ul class="tabs">
                        <li class="tab col s6"><a href="#viewCategories" class="icon-flashlight">View All</a></li>
                        <li class="tab col s6"><a href="#createCategory" class="icon-plus">Create New Category</a></li>
                    </ul>
                    <section id="viewCategories">
                    <table>
                        <thead>
                            <tr>
                                <th>Title and caption</th>
                                <th># reports</th>
                                <th>last activity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
foreach($this->data['categories'] as $cat)
{
?>
                            <tr>
                                <td><a href="<?= BUNZ_HTTP_DIR ?>report/category/<?= $cat['id'] ?>"
                                        class="category-<?= $cat['id']?>-text"><?=$cat['title']?></a><br>
<?= $cat['caption'] ?>
                                </td>
                                <td>9001</td>
                                <td><?= datef() ?></td>
                                <td>Edit /Move / Delete</td>
                            </tr>
<?php
}
?>
                        </tbody>
                    </table>
                    </section>
                    <section id="createCategory">
                        <form>
                            <div class="input-field">
                                <input>
                                <label>Things</label>
                            </div>
                        </form>
                    </section>
                </section>

                <section id="statuses">
<pre><?= print_r($this->data['statuses'],1) ?></pre>
                </section>

                <section id="tags">
<pre><?= print_r($this->data['tags'],1) ?></pre>
                </section>

                <section id="priorities">
<pre><?= print_r($this->data['priorities'],1) ?></pre>
                </section>
            </div>
        </section>
    </header>
</article>
<?php
require_once BUNZ_TPL_DIR . 'footer.inc.php';
