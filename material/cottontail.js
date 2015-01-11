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
/**
 * [hax intensify] */
    function holyshit() {
        var htmlHeight = document.documentElement.scrollHeight,
            bodyHeight = null,//document.body.scrollHeight,
            viewHeight = window.screen.availHeight,
            troubleMaker = document.getElementById('mp-pusher');

        bodyHeight = troubleMaker.scrollHeight;

        if(htmlHeight == bodyHeight && htmlHeight < viewHeight)
            troubleMaker.style.height = '100%';

        else if(bodyHeight > viewHeight)
            troubleMaker.style.height = 'auto';

                
    }
    window.addEventListener('resize', holyshit, false);
    holyshit();
});
