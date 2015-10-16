<style>
#purge :target {
    /* .z-depth-5 */
    box-shadow: 0 8px 10px 0 rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.22);
}
</style>
<section id="purge" class="col s12 danger-base">
    <div class="section">
        <h1 class="icon-delete">THERE IS NO UNDO FOR THIS <a href="<?= BUNZ_HTTP_DIR?>cpanel/export">BACKUP FIRST</a></h1>
        <h5>hint: read this form like a sentence</h5>
        <h1 class="icon-delete">Purge ALL Reports...</h1>
        <h4>...and associated comments and diffs...</h4>
    </div>
    <div class="section">
    <form id="purge__form" class=""
          action="<?= BUNZ_HTTP_DIR ?>cpanel/purge"
          method="post">
        <div class="row section shade-text" id="purge__time">
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
            <select name="year" class="browser-default">
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
            <select name="month" class="browser-default">
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
            <select name="day" class="browser-default">
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
            <select name="hour" class="browser-default">
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
            <select name="minute" class="browser-default">
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
                <label for="open" class="danger-text"><span class="icon-unlock"></span>INCLUDING OPEN REPORTS</label>
            </div>
            <div class="col s6">
                <input type="radio" id="close" name="include_open" value="0" checked>
                <label for="close"><span class="icon-lock"></span>which are closed</label>
            </div>
        </div>&nbsp;
        <div class="row">
            <div class="col s12 section secondary-base"><strong>PROTIP:</strong> leaving the subsequent sections blank will exclude them from the request, so leave <strong>nothing below checked</strong> if you want to just clear <strong>all</strong> reports matching <strong>above criteria</strong>.</div>&nbsp;
            <div class="col s12 section secondary-lighten-5">Actually you know what here have some presets:
                <ul>
                    <li><a onclick="
var form=document.forms['purge__form'];
form.reset();
[].forEach.call(form, function(el) {
    el.checked = (el.id === 'close' || el.id === 'before');
});" href="#purge__submit">Delete All Closed Reports</a></li>
                    <li><a onclick="
var form=document.forms['purge__form'];
form.reset();
[].forEach.call(form, function(el) {
    el.checked = (el.id === 'open' || el.id === 'before');
});" href="#purge__ips">An attacker/spambot/skid/troll posted a bunch of shit sometime in the past</a></li>
                    <li><a onclick="
var form=document.forms['purge__form'];
form.reset();
[].forEach.call(form, function(el) {
    el.checked = (el.id === 'open' || el.id === 'during');
});" href="#purge__time">An attacker/spambot/skid/troll is posting a bunch of shit RIGHT NOW</a>, then <a href="#purge__ips">Select their IP and/or email <strong>ONLY</strong></a></li>
                    <li>todo: think of more common purge scenarios</li>
                    </li>
                </ul>
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
            <div class="col s12 section">with ANY of the following priorities:<br>&nbsp;</div>
<?php
foreach($this->data['priorities'] as $id => $priority) {
?>          <div class="input-field col s12 m6 l3">
                <input type="checkbox" id="purge__priority<?= $id ?>" name="priorities[]" value="<?=$id?>" checked>
                <label for="purge__priority<?= $id ?>"><span class="<?= $priority['icon'] ?>"></span><?=$priority['title']?></label>
            </div>
<?php
}
?>
        </div>&nbsp;
        <div class="row section shade-text" id="purge__ips">
            <div class="col s12 section">made by users with ANY of the following IPs:<br>&nbsp;</div>
<?php
foreach($this->data['users']['ips'] as $id => $ip) {
?>          <div class="input-field col s12 m6 l3">
                <input type="checkbox" id="purge__ip<?= $id ?>" name="ips[]" value="<?=$ip?>" checked>
                <label for="purge__ip<?= $id ?>"><?=$ip?></label>
            </div>
<?php
}
?>
        </div>&nbsp;
        <div class="row section shade-text" id="purge__emails">
            <div class="col s12 section">made by users with ANY of the following Email Addresses:<br>&nbsp;</div>
<?php
foreach($this->data['users']['emails'] as $id => $email) {
?>          <div class="input-field col s12 m6 l3">
                <input type="checkbox" id="purge__email<?= $id ?>" name="emails[]" value="<?=$email?>" checked>
                <label for="purge__email<?= $id ?>"><?=$email?></label>
            </div>
<?php
}
?>
        </div>&nbsp;
        <div class="row section shade-text" id="purge__submit">
            <div class="col s12 section center">
                <button onclick="event.preventDefault(); event.stopPropagation(); document.forms['purge__form'].reset(); [].forEach.call(document.forms['purge__form'], function(el){el.checked=false})"
                        class="btn btn-flat white shade-text icon-cancel waves-effect">Clear All!</button>
                <button type="reset"
                        class="btn btn-flat white secondary-text icon-cancel waves-effect waves-effect-red">Defaults Please!</button>
                <br>&nbsp;<br>
                <button type="submit"
                        onclick="(function(evt){if(!window.confirm('FOR GREAT JUSTICE')){ evt.stopPropagation(); evt.preventDefault(); alert('MOVE ZIG'); }})(event)"
                        class="btn btn-large large icon-delete waves-effect btn-raised danger-base">YOU KNOW WHAT YOU DOING<i>!!</i></button>
            </div>
        </div>
    </form> 
    </div>
</section>
