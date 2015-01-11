/**
 * activate materialize.js components */
$(document).ready(function(){
    $(".dropdown-button").dropdown();
    $(".collapsible").collapsible();
    $('select').material_select();
/**
 * codrops MultiLevelPushMenu initialization */
    (new mlPushMenu( document.getElementById("mp-menu"), 
                     document.getElementById('mp-trigger') 
        )
    );
});
