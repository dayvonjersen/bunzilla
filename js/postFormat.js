var postFormat = (function()
{
    var validFields = [],
        textbox,
        widgetbox,
        widgetbtn,
        parentElement;

    function focusField( element )
    {
        if(element === textbox)
            return;

        blurField();
        for(var i = 0; i < validFields.length; i++)
        {
            if(element === validFields[i])
            {
                textbox = element;
                break;
            }
        }
        parentElement.insertBefore(widgetbox,textbox.parentElement);
    }

    function blurField()
    {
        textbox = null;
    }

    function init( form )
    {
        widgetbox = document.getElementById('postFormat');
        widgetbtn = document.getElementById('postFormat-on');
        for(var i = 0, j = 0; i < form.length; i++)
        {
            if(form[i] instanceof HTMLTextAreaElement)
            {
                validFields[j++] = form[i];
                form[i].addEventListener('focus',function(evt){
                    activate();
                },false);
          //    form[i].addEventListener('blur',function(evt){
          //        evt.preventDefault();
          //        deactivate();
          //    },false);
            } else if(form[i] instanceof HTMLFieldSetElement && form[i].id !== 'postFormat') {
                parentElement = form[i];
            }
        }
        deactivate();    
    }

    function activate()
    {
        widgetbox.style.display = "block";
        widgetbtn.style.display = 'none';
        widgetbox.addEventListener('click',insertHandler,false);
        
        if(document.activeElement instanceof HTMLTextAreaElement)
            focusField(document.activeElement);
        else if(textbox === null)
            focusField(validFields[0]);
    }

    function deactivate()
    {
        widgetbox.style.display = "none";
        widgetbtn.style.display = "block";
        widgetbox.removeEventListener('click',insertHandler,false);
        blurField();
    }

    function insertHandler(evt)
    {
        if(!textbox || (!(evt.target instanceof HTMLSpanElement) && !(evt.target instanceof HTMLDivElement)))
            return;

        textbox.focus();

        var markup = evt.target.dataset.markup,
            url, title,
            codelang = document.getElementById('codelang');

        switch(markup)
        {
            case undefined:
            case null:
                console.log("Called insertHandler on an element with no data-markup!");
                return;

            case "ul":
            case "ol":
                markup = "<" + markup + "><li>item 1</li><li>item 2</li><li>item 3</li></" + markup + ">";
                break;

            case "link":
                url = prompt('Enter the full URL for the link, e.g. http://www.example.com/');
                title = prompt('Enter a text for the link (optional), e.g. Click here!');
                markup = "<link>" + (url ? url : window.location.origin) + (title ? '{' + title + '}' : '') + '</link>';
                url = title = undefined;
                break;

            case "image":
                url = prompt('Enter the full URL for the image, e.g. http://goatse.cx/hello.jpg');
                markup = "<image>" + (url ? url : window.location.origin) + "</image>";
                url = undefined;
                break;

            case "delins":
                markup = "<del>oldstuff</del><ins>newstuff</ins>";
                break;

            case "code":
            default:
                markup = "<" + markup + (markup == "code" && codelang.value ? " " + codelang.value : "") + ">" + "something" + "</" + markup + ">";
        }

        textbox.value = markup;
    }

    return {
        init: init,
        activate: activate,
        deactivate: deactivate
    };
})();
