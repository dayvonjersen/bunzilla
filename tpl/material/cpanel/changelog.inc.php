
                <section id="changelog">
                    <nav>
                        <a href="#" class="dropdown-button btn" data-activates="changelog-exports">Export as</a>
                        <ul id="changelog-exports" class="dropdown-content">
                            <li><a href="#" class="icon-html">HTML</a></li>
                            <li><a href="#" class="icon-list-dl">Markdown</a></li>
                            <li><a href="#" class="icon-ol">Plain text</a></li>
                        </ul>
                        <a href="#" class="dropdown-button btn" data-activates="changelog-versions">View version</a>
                        <ul id="changelog-versions" class="dropdown-content">
                            <li><a href="#">All</a></li>
                            <li><a href="#">1.0</a></li>
                            <li><a href="#">2.0</a></li>
                            <li><a href="#">...</a></li>
                        </ul>
                    </nav>
<form>
<h1 class="icon-pencil-alt">Edit changelog</h1>
<ol>
<?php
foreach($this->data['changelog'] as $ln)
{
    $ln = current($ln);
    echo '<li class="input-field"><input type="text" value="',htmlspecialchars($ln),'"/><span class="material-input"></span></li>';
}
?>
</ol>
                    <p class="input-field"><input type="text" placeholder="Add a line..."/><span class="material-input"></span><button class="btn btn-floating left" type="submit"><i class="icon-plus"></i></button></p>
</form>
                </section> 
