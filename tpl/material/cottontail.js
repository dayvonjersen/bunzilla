/**
 * activate materialize.js components */
//$(document).ready(function(){
$(window).load(function(){
    $(".dropdown-button").dropdown(
{
    constrain_width: true,
    belowOrigin: false
});
    $(".collapsible").collapsible();
    $('select').material_select();
    $('ul.tabs').tabs();
    $('img').materialbox();
    $('.tooltipped').tooltip({"delay": 50});
/**
 * codrops google nexus 7 menu 
 * http://tympanus.net/codrops/?p=16030
 *
 * codrops example is based off of this:
 * http://web.archive.org/web/20130731035203/http://www.google.com/nexus/
 *
 * additional fuckery c/o headroom.js 
 * http://wicky.nillia.ms/headroom.js/
 */

    function clozure(yes) {
        setTimeout(function(){
            nexus.style.overflow = yes ? "visible" : "hidden";
        },500);
    }

    var nexus = document.getElementById("gn-menu"),
        seven = document.querySelector('li.gn-trigger a'),
        headroom = (new Headroom(nexus,{onPin: function(){clozure(true);}, onUnpin: function(){clozure(false);}}));

    (new gnMenu(nexus));

    headroom.init();

    function muhClosures(evt){
        headroom.pin()
    }

    seven.addEventListener('mouseover',muhClosures,false);
    seven.addEventListener('click',muhClosures,false);
    seven.addEventListener('touchstart',muhClosures,false);
    
});
/*
***************
 * todo : list.js **
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
   }     */
/** xxx
function gEbI(id)
{
    return document.getElementById(id);
} **/

