/**
 * activate materialize.js components */
$(document).ready(function(){
//$(window).load(function(){
    $(".dropdown-button").dropdown(
{
    hover: false,
    constrain_width: true,
    belowOrigin: true
});
    $(".collapsible").collapsible(
    {accordion: !($(".collapsible").data("collapsible") == "expandable")});
    $('select').material_select();
    $('ul.tabs').tabs();
    $('img').materialbox();
    $('.tooltipped').tooltip({"delay": 50});

/**
 * let me tell you why ... */
    function bullshit(that){
        var text_val = $(that).val();
        if(text_val === '')
            $(that).siblings('label').removeClass('active');
        else
            $(that).siblings('label').addClass('active');
    }
    $('.input-field input').each(function(){bullshit(this)});
    $('.input-field textarea').each(function(){bullshit(this)}); 
    $('.input-field input').change(function(){bullshit(this)});
    $('.input-field textarea').change(function(){bullshit(this)});
    $('.input-field input').focusout(function(){bullshit(this)});
    $('.input-field textarea').focusout(function(){bullshit(this)});
    $('.input-field input').keypress(function(){bullshit(this)});
    $('.input-field textarea').keypress(function(){bullshit(this)});
    $('.input-field input').keydown(function(){bullshit(this)});
    $('.input-field textarea').keydown(function(){bullshit(this)});
    $('.input-field input').keyup(function(){bullshit(this)});
    $('.input-field textarea').keyup(function(){bullshit(this)});
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


    if(window.scrollY != 0)
        headroom.unpin();

    setTimeout(function(){
    if(window.scrollY != 0)
        headroom.unpin();
    }, 400);

    if(typeof diffModal !== "undefined"){
    var regex = /\/diff\/(reports|comments)\/(\d+)/,
        matches;
    for(var i = 0; i < document.links.length; i++)
    {
        matches = regex.exec(document.links[i].pathname);
        if(matches)
        {
            (function(anchor,url,type,id) {
                anchor.addEventListener('click',function(evt){
                    evt.preventDefault();
                    diffModal(url,type,id);
                });
            })(document.links[i],document.links[i].href,matches[1],matches[2]);
        }
    }}
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

