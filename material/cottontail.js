/**
 * activate materialize.js components */
$(document).ready(function(){
    $(".button-collapse").sideNav();
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
