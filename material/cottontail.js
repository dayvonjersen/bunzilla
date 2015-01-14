/**
 * activate materialize.js components */
$(document).ready(function(){
    $(".dropdown-button").dropdown();
    $(".collapsible").collapsible();
    $('select').material_select();
    $('ul.tabs').tabs();
    $('img').materialbox();
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
        {
            troubleMaker.style.height = 'auto';
            document.querySelector('#mp-pusher > main').style.paddingBottom = navigator.mozApps ? "170px" : "150px"; //ffs
        }

                
    }
    window.addEventListener('resize', holyshit, false);
    holyshit();

    if(gEbI("sorttable_override"))
    {
        var iconSortDESC = 'icon-down-open-mini',
            iconSortASC  = 'icon-up-open-mini',
            iconSortNONE = 'icon-sort';

        gEbI('sorttable_override').addEventListener('click', function(evt){
            var target;
            if(evt.target instanceof HTMLAnchorElement)
                target = evt.target
            else if(evt.target instanceof HTMLSpanElement)
                target = evt.target.parentElement
            else
                return;

            var th = gEbI(target.dataset.th);
            if(th)
            {
                evt.preventDefault();

                sorttable.innerSortFunction.apply(th,[]);

                if(target.className.indexOf('light-blue') === -1)
                    target.className = target.className + " light-blue";

                if(th.className.indexOf('sorttable_sorted_reverse') !== -1)
                    gEbI("ico-"+target.dataset.th).className = iconSortASC;
                else if(th.className.indexOf('sorttable_sorted') !== -1)
                    gEbI("ico-"+target.dataset.th).className = iconSortDESC;

                var nodeList = document.querySelectorAll("#sorttable_override a span");
                for(var i = 0; i < nodeList.length; i++)
                {
                    if(nodeList.item(i).id != "ico-"+target.dataset.th)
                    {
                        nodeList.item(i).parentElement.className = nodeList.item(i).parentElement.className.replace('light-blue','');
                        nodeList.item(i).className = iconSortNONE;
                    }
                }
            }
        }, false);
   }     
});
function gEbI(id)
{
    return document.getElementById(id);
}

