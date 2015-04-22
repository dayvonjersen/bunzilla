window.onload = function() {
    function mapRange(from, to, s) {
      return Math.round(to[0] + (s - from[0]) * (to[1] - to[0]) / (from[1] - from[0]));
    }

    var xhr  = new XMLHttpRequest(),
        dest = document.getElementById('tagCloud');
    xhr.open('GET',dest.dataset.uri + '/tagcloud?json');
    xhr.onreadystatechange = function() {
        if(xhr.readyState == 4)
        {
            dest.innerHTML = "";
            var cloud = JSON.parse(xhr.responseText);
            for(var i = 0; i < cloud.tags.length; i++)
            {
                var tag  = cloud.tags[i];
                if(tag.percent == 0)
                    continue;
                var anchor = document.createElement('a');

                anchor.setAttribute('href',dest.dataset.uri+'?q=tag:'+tag.id);
                anchor.setAttribute('class', 'tooltipped '+tag.icon+' h'+mapRange([cloud.min_percent,cloud.max_percent],[1,6],cloud.max_percent - tag.percent));
                anchor.setAttribute('data-tooltip', tag.count + ' report' + (tag.count === 1 ? '' : 's'));
                anchor.setAttribute('style','white-space: nowrap');
                anchor.appendChild(document.createTextNode(tag.title));
                dest.appendChild(anchor);
                dest.appendChild(document.createTextNode(" "));
            }
            $('.tooltipped').tooltip();
        }
    };
    xhr.send();
};
