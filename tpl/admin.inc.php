<?php
$pageTitle = 'bunzilla settings';

require BUNZ_TPL_DIR . 'header.inc.php';
?>
        <article>
            <h1>edit categories</h1>
            <table>
                <thead>
                    <tr>
                        <th>title</th>
                        <th># reports</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>bug reports</td>
                        <td>9001</td>
                        <td><a href="#">View</a><a href="#">Edit</a><a href="#">Delete</a>
                    </tr>
                </tbody>
            </table>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <fieldset>
                    <legend>create new</legend>
                    <p>
                        <label>title</label>
                        <input type="text">
                    </p>
                    <p>
                        <label>caption</label>
                        <input type="text">
                    </p>
                    <p>
                        <label>requires description</label>
                        <input type="checkbox">
                    </p>
                    <p>
                        <label>requires reproduce</label>
                        <input type="checkbox">
                    </p>
                    <p>
                        <label>requires expected</label>
                        <input type="checkbox">
                    </p>
                    <p>
                        <label>requires actual</label>
                        <input type="checkbox">
                    </p>
                    <p>
                        <label>pick a color</label>
                        <input type="text">
                    </p>

                    <p>
                        <label>pick an icon</label>
                        <select>
                            <option class='icon-bomb'>.</option>
                        </select>
                    </p>
                    <button>create new category</button>
                </fieldset>
            </form>
        </article>        
        <article>
            <h1>edit statuses</h1>
            <table>
                <thead>
                    <tr>
                        <th>title</th>
                        <th># reports</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>unassigned</td>
                        <td>9001</td>
                        <td><a href="#">View</a><a href="#">Edit</a><a href="#">Delete</a>
                    </tr>
                </tbody>
            </table>
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
                <fieldset>
                    <legend>create new</legend>
                    <p>
                        <label>title</label>
                        <input type="text">
                    </p>
                    <p>
                        <label>pick a color</label>
                        <input type="text">
                    </p>

                    <p>
                        <label>pick an icon</label>
                        <select>
                            <option class='icon-bomb'>.</option>
                        </select>
                    </p>
                    <button>create new category</button>
                </fieldset>
            </form>
        </article>
<?php
require BUNZ_TPL_DIR . 'footer.inc.php';
