<section id="purge" class="col s12 danger-base">
    <div class="section">
        <h1 class="icon-delete tab">Purge ALL Reports &mdash; NO UNDO <a href="<?= BUNZ_HTTP_DIR?>cpanel/export">BACKUP FIRST</a></h1>
        <h4>(and associated comments and diffs)</h4>
    </div>
    <div class="section">
    <form id="purge__form" class=""
          action="<?= BUNZ_HTTP_DIR ?>cpanel/purge"
          method="post">
        <div class="row section shade-text">
            <div class="col s12">made...</div>
            <div class="col s6">
                <input type="radio" id="before" name="before" value="1" checked>
                <label for="before"><h2>BEFORE</h2><small>(older than)</small></label>
            </div>
            <div class="col s6">
                <input type="radio" id="during" name="before" value="0">
                <label for="during"><h2>DURING</h2>(only in the past few, <small><small>useful for ongoing attacks...</small></small>)</label>
            </div>


            <div class="col s12">the past...</div>
            <div class="col s2 section no-pad-top">
            <select name="year">
<?php
for($i = 0; $i < 11; $i++) { // XXX adjust as needed
?>
                <option value="<?=$i?>"><?=$i?></option>
<?php
}
?>
            </select> years
            </div>
            <div class="col s2 section no-pad-top">
            <select name="month">
<?php
for($i = 0; $i <= 12; $i++) { // XXX adjust as needed
?>
                <option value="<?=$i?>"><?=$i?></option>
<?php
}
?>
            </select> months
            </div>
            <div class="col offset-s1 s2 section no-pad-top">
            <select name="day">
<?php
for($i = 0; $i <= 12; $i++) { // XXX adjust as needed
?>
                <option value="<?=$i?>"><?=$i?></option>
<?php
}
?>
            </select> days
            </div>
            <div class="col offset-s1 s2 section no-pad-top">
            <select name="hour">
<?php
for($i = 0; $i <= 12; $i++) { // XXX adjust as needed
?>
                <option value="<?=$i?>"><?=$i?></option>
<?php
}
?>
            </select> hours
            </div>
            <div class="col s2 section no-pad-top">
            <select name="year">
<?php
for($i = 0; $i < 60; $i++) { // XXX adjust as needed
?>
                <option value="<?=$i?>"><?=$i?></option>
<?php
}
?>
            </select> minutes
            </div>
            <div class="col s12"><small>yes I know a date picker would be really useful right about now <a href="https://github.com/generaltso/bunzilla">help me out</a></small></div>
        </div>&nbsp;
        <div class="row section shade-text">
            <div class="col s6">
                <input type="radio" id="open" name="include_open" value="1">
                <label for="open">INCLUDING OPEN REPORTS</label>
            </div>
            <div class="col s6">
                <input type="radio" id="close" name="include_open" value="0" checked>
                <label for="close">which are closed</label>
            </div>
        </div>&nbsp;
        <div class="row section shade-text">
            <div class="col s12 section">from ANY of the following categories:<br>&nbsp;</div>
<?php
foreach($this->data['categories'] as $id => $cat) {
?>          <div class="input-field col s12 m6 l3">
                <input type="checkbox" id="purge__category<?= $id ?>" name="categories[]" value="<?=$id?>" checked>
                <label for="purge__category<?= $id ?>"><span class="<?= $cat['icon'] ?>"></span><?=$cat['title']?></label>
            </div>
<?php
}
?>
        </div>&nbsp;
        <div class="row section shade-text">
            <div class="col s12 section">with ANY of the following statuses:<br>&nbsp;</div>
<?php
foreach($this->data['statuses'] as $id => $stat) {
?>          <div class="input-field col s12 m6 l3">
                <input type="checkbox" id="purge__status<?= $id ?>" name="statuses[]" value="<?=$id?>" checked>
                <label for="purge__status<?= $id ?>"><span class="<?= $stat['icon'] ?>"></span><?=$stat['title']?></label>
            </div>
<?php
}
?>
        </div>&nbsp;
        <div class="row section shade-text">
            <div class="col s12 section center">
                <button onclick="event.preventDefault(); event.stopPropagation(); [].forEach.call(document.forms['purge__form'], function(el){el.checked=false})"
                        class="btn btn-flat white shade-text icon-cancel waves-effect">Clear All!</button>
                <button type="reset"
                        class="btn btn-flat white secondary-text icon-cancel waves-effect waves-effect-red">Defaults Please!</button>
                <br>&nbsp;<br>
                <button type="submit"
                        onclick="(function(evt){if(!window.confirm('FOR GREAT JUSTICE')){ evt.stopPropagation(); evt.preventDefault(); alert('MOVE ZIG'); }})(event)"
                        class="btn icon-delete waves-effect btn-raised danger-base">YOU KNOW WHAT YOU DOING<i>!!</i></button>
            </div>
        </div>
    </form> 
    </div>
</section>
