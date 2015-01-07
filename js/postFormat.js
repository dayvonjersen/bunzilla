/**
 * everything went better than expected */
var postFormat = (function()
{
    var validFields = [], // things to apply this widget to
        textbox,          // target <textarea>
        widgetctn,        // container div#postFormat-ctn
        widgetbox,        // the actual toolbar fieldset#postFormat
        widgetbtn,        // toggle button a#postFormat-btn
        parentElement;    // because insertBefore() is awful

    /**
     * update stuff on :focus */
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
        parentElement.insertBefore(widgetctn,textbox.parentElement);
    }

    /**
     * housekeeping */
    function blurField()
    {
        textbox = null;
    }

    /**
     * aka __construct() */
    function init( form )
    {
        widgetctn = document.getElementById('postFormat-ctn');
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
          // TODO: figure out how to blur correctly;
          // you can see what happens if you uncomment this:
          //    form[i].addEventListener('blur',function(evt){
          //        evt.preventDefault();
          //        deactivate();
          //    },false);

          // probably a better way to do this:
            } else if(form[i] instanceof HTMLFieldSetElement && form[i].id == 'postFormat-parent') {
                parentElement = form[i];
            }
        }
        //deactivate(); unnecessary with proper initial styling
    }

    /**
     * wax on */
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

    /**
     * wax off */
    function deactivate()
    {
        widgetbox.style.display = "none";
        widgetbtn.style.display = "inline-block";
        widgetbox.removeEventListener('click',insertHandler,false);
        blurField();
    }

    /**
     * called a few times 
     * muh abstraction */
    function promptURL()
    {
        var url = prompt('Enter a full URL, e.g. http://www.example.com/stuff.whatever');
        return (url ? url : window.location.origin);
    }

    /**
     * what this actually does 
     * thanks to developer.mozilla.org */
    function insertHandler(evt)
    {
        if(!textbox || (!(evt.target instanceof HTMLSpanElement) && !(evt.target instanceof HTMLDivElement)))
            return;

        //textbox.focus();

        var markup = evt.target.dataset.markup,
            url, title,
            codelang = document.getElementById('codelang'),

  
            oldText = textbox.value,
            selectionStart = textbox.selectionStart,
            selectionEnd = textbox.selectionEnd, 
            selectionEmpty = (selectionStart === selectionEnd),
   
            startTag, endTag;

        switch(markup)
        {
            case undefined:
            case null:
                console.log("Called insertHandler on an element with no data-markup!");
                return;

            case "ul":
            case "ol":
                document.getElementById('disable_nlbr').checked = true;
                startTag = (selectionStart == 0 ? "" : "\n") + "<" + markup + ">" + "\n\t<li>";
                endTag = "</li>\n</" + markup + ">";
                break;

            case "link":
                if(selectionEmpty)
                {
                    url   = promptURL();
                    title = prompt('Enter a text for the link (optional), e.g. Click here!');
                } else if(!(/^(f|ht)tp(s)?:\/\//.exec(oldText.substr(selectionStart,selectionEnd)))) {
                    url = promptURL();
                    title = oldText.substr(selectionStart,selectionEnd);
                    oldText = "";
                } else {
                    url = "";
                    title = "";
                }
                startTag = "<link>" + url + (title ? '{' + title + '}' : '');
                endTag = "</link>";

                url = title = undefined;
                break;

            case "image":
                if(selectionEmpty)
                {
                    oldText = promptURL();
                }
                startTag = "<image>";
                endTag = "</image>";
                break;

            case "delins":
                startTag = "<del>"
                endTag = "</del><ins>[InsertYourEditHere]</ins>";
                break;

            case "code":
            default:
                startTag = "<" + markup + (markup == "code" && codelang.value && codelang.value !== "pick a language" ? " " + codelang.value : "") + ">";
                endTag =  "</" + markup + ">";
        }

        // I don't know why this works but it does :D
        textbox.value = oldText.substring(0,selectionStart) + startTag + oldText.substring(selectionStart,selectionEnd) + endTag + oldText.substring(selectionEnd);
        textbox.setSelectionRange(selectionEmpty ? selectionStart + startTag.length : selectionStart, selectionEnd + startTag.length);
        textbox.focus();
    }

    return {
        init: init,
        activate: activate,
        deactivate: deactivate
    };
})(); // because all good js files end with that
