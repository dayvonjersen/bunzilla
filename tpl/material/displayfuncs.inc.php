<?php
//
// functions for to displaying the things
//

function ftime( $time, $amount, $unit )
{
    if($time >= $amount)
        return [
            sprintf("%d $unit%s", 
                $time/$amount, 
                (int)($time/$amount) == 1 ? '':'s'
            ),
            $time%$amount
        ];
    return [false, $time];
}
/**
 * formatted date for web 2.0 
 * relative > exact according to the kids
 */
function datef( $time = -1 )
{
    if($time == 0)
        return '<em>never</em>';

    if($time == -1)
        $time = time();

    $diff = time() - $time;

    if($diff < 1)
        return '<time><em>just now!</em></time>';

    $ret = [];
    list($ret[], $diff) = ftime($diff, 31536000, 'year');
    list($ret[], $diff) = ftime($diff, 2592000, 'month');
    list($ret[], $diff) = ftime($diff, 604800, 'week');
    list($ret[], $diff) = ftime($diff, 86400, 'day');
    list($ret[], $diff) = ftime($diff, 3600, 'hour');
    list($ret[], $diff) = ftime($diff, 60, 'minute');
    list($ret[], $diff) = ftime($diff, 1, 'second');

    $text = implode(', ',array_filter($ret,function($val){return $val;}));
    return sprintf('<time class="tooltipped" data-tooltip="%s ago">%s</time>',
        $text,
        date(BUNZ_BUNZILLA_DATE_FORMAT, $time)
    );
    //return ;
}

/**
 * lets create the tag and status buttons with the same html
 * and use CSS to differentiate them */
function badge( $type, $id, $short = false, $z = 1 )
{
    if(!in_array($type,['tag','status','priority'],true))
        throw new InvalidArgumentException(__FUNCTION__);

    static $tag = null;
    static $status = null;
    static $priorities = null;
    if($$type === null)
    {
        switch($type)
        {
            case 'tag': $tbl = 'tags'; break;
            case 'status': $tbl = 'statuses'; break;
            case 'priority': $tbl = 'priorities'; break;
        }
        $$type = Cache::read($tbl);
    }

    return '
<span class="badge right z-depth-'.$z.' '.$type.' '.$type.'-'.$id.' '.${$type}[$id]['icon'].'" 
      title="'.ucfirst($type).': '.${$type}[$id]['title'].'">'
.($short ? '': '<span class="hide-on-small-only">').${$type}[$id]['title'].'</span></span>'."\n";
}
function status( $id, $short = false ) { return badge('status',(int)$id,$short,5); }
function tag( $id, $short = true ) { return badge('tag',(int)$id,$short); }
function priority( $id, $badge = true ) { 

//    if(!$justIcon)
//        return badge('priority',(int)$id); 


    static $pryor = null;
    $pryor === null && $pryor = Cache::read('priorities');
    if(empty($pryor))
        return '<!-- no priorities defined -->';

    $p = isset($pryor[$id]) ? $pryor[$id] : ['id'=>0,'title'=>'Well, it can\'t be THAT important','icon'=>'icon-emo-displeased'];

//    return sprintf('<i class="%s priority-%d" title="%s"></i>', $p['icon'],$p['id'],$p['title']);

    //$z = round(($id/count($pryor))*5);
    return '<span class="left '.($badge ? 'badge ' : '').'z-depth-5 priority priority-'.$id.'" title="Priority '.$id.' out of '.count($pryor).'"><strong class="'.$p['icon'].'">'.$p['title'].'</strong></span>';
}


/**
 * ditto for <select> dropdowns */
function dropdown( $fieldName, $values, $selected = false, $disableKey = false, $onchange = null )
{
    if($disableKey !== false && isset($values[$disableKey]))
        unset($values[$disableKey]);

    $onchange = isset($onchange) ? " onchange='$onchange'" : '';

    $ret = "<select$onchange name='$fieldName'>\n";
    foreach($values as $opt)
    {
        $ret .= "\t<option value='".($fieldName == 'icon' ? $opt['icon'] : $opt['id'])."' class='{$fieldName}-{$opt['id']}' data-icon='{$opt['icon']}'".($selected === $opt['id'] ? ' selected' : '').">{$opt['title']}</option>\n";
    }
    return "$ret</select>";
}
function statusDropdown( $selected, $disableId = false ) { 
    return dropdown('status',Cache::read('statuses'),$selected, $disableId);
}
function categoryDropdown( $selected, $disableId = false, $onchange = null, $spec_field = false ) { 
    return dropdown($spec_field ? $spec_field : 'category', Cache::read('categories'), $selected, $disableId, $onchange);
}

/**
 * and my input[type="range"] hack */
function rangeOptions( $values )
{
    $ret = "<dl class='gone'>\n";
    foreach($values as $opt)
    {
        $ret .= sprintf(
            "\t<dd data-class='priority-%d' data-icon='%s' data-value='%d'>%s</dd>\n",
            $opt['id'],$opt['icon'],$opt['id'],$opt['title']
        );
    }
    return "$ret</dl>\n";
}

/**
 * still */
function unfiltermessage($msg)
{
    // FUCK
    $msg = str_replace('<br />','',$msg);

    preg_match_all('/\<pre\>\<code( class="language-(\w+)")?/ims', $msg, $codes,PREG_SET_ORDER);
    foreach($codes as $code)
        $msg = str_replace($code[0],'<code'.(isset($code[2]) ? ' '.$code[2] : ''),$msg);
    $msg = str_replace('</code></pre>','</code>',$msg);

    preg_match_all('/\<a href="(.*?)" .+\>(.*)\<\/a\>/im',$msg,$links,PREG_SET_ORDER);
    foreach($links as $link)
        $msg = str_replace($link[0],'<link>'.$link[1].($link[1]!=$link[2]?'{'.$link[2].'}':'').'</link>',$msg);
    preg_match_all('/\<img src="(.*?)" .+\>/im',$msg, $images,PREG_SET_ORDER);
    foreach($images as $image)
        $msg = str_replace($image[0], '<image>'.$image[1].'</image>',$msg);

    $msg = str_replace('<','&lt;',$msg);
    $msg = str_replace('>','&gt;',$msg);

    return $msg;
}

/**
 * absolutely disgusting */
function pagination( $url, $total, $curPage )
{
    if($total < 50)
        return '';

    $curPage += 1;
    $pages = ceil($total/50);

    $return = '<article class="center section no-pad-bot bz-paginate" >
        <div class="section no-pad-top no-pad-bot" data-textlabel="Page '."$curPage of $pages".'">';
    $classList = 'btn btn-floating z-depth-2 waves-effect';

    $omit    = $pages > 6  ? array_keys(array_fill(min([$curPage+3,$pages-3]),$pages-6,null)) : [];
    $started = false;
    for($i = 1; $i <= $pages; $i++)
    {
        switch($i)
        {
            case 1:
                $text = '<span style="font-variant: small-caps">1<sup>ST</sup></span>'; break;
            case $pages:
                $text = '<span style="font-variant: small-caps">END</span>';
                break;
            case ($curPage - 1):
                $text = '<i class="icon-left-open-mini"></i>';
                break;
            case ($curPage + 1):
                $text = '<i class="icon-right-open-mini"></i>';
                break;
            default: 
                $text = $i;
        }
        if($pages > 6 && in_array($i,$omit))
        {
            if(!$started)
            {
                $return .= '<select style="display: inline-block; width: 2.5rem; height: 2.5rem;"  onchange="window.location=\''.$url.'/\' + this.value" class="circle z-depth-5 shade-text browser-default">';
                $return .= '<option disabled selected>...</option>';
                $started = true;
            }
            $return .= '<option value='.($i-1).'>'.$text.'</option>';
        } else {
        if($started)
        {
            $return .= '</select>';
            $started = false;
        }           
        $return .= '<'.($i == $curPage ? 'em' : "a href='$url/".($i-1)."'").' style="margin: 0 0.5rem"'
                    ." class='$classList".($i == $curPage ? ' secondary-darken-4 z-depth-5' : ' shade-lighten-5 secondary-text')."'>$text</"
                    .($i == $curPage ? 'em' : 'a')
                    .'>';
        }
    }
    return "$return</div></article>";
}

// XXX: not sure if this is used anymore
function breadcrumb($crumbs, $youarehere, $categoryId)
{   
    if(!count($crumbs))
        return;
    $style = ' style="padding: 0 1em;width: '.round(66.6667/count($crumbs), 4).'%" ';
    foreach($crumbs as $crumb)
    {
        if(strstr($crumb['href'],'report/category'))
        {
            echo '<li class="hide-overflow-text hide-on-small-only',strstr($crumb['href'],$youarehere) ? ' secondary-text"' : '"',$style,'>',categoryDropdown($categoryId,false,'window.location="'.BUNZ_HTTP_DIR.'report/category/" + this.value'),'</li>';
        } elseif(strstr($crumb['href'],$youarehere)) {
            echo '<li',$style,' class="secondary-text gn-multiline hide-overflow-text hide-on-small-only"><i class="',$crumb['icon'],'"></i><em>',$crumb['title'],'</em><small>&rarr;you are here&larr;</small></li>';
        } else {
            echo '<li',$style,' class="hide-overflow-text hide-on-small-only"><a href="',BUNZ_HTTP_DIR,$crumb['href'],'"', isset($crumb['icon']) ? " class='{$crumb['icon']}'" : '','>',$crumb['title'],'</a></li>',"\n";
        }
    }
}

// "e-penis" is an old online euphamism referring to
// one's status, reputation, or clout especially as it relates to online
// communities or an online identity and is actually meant to be 
// disparaging against stereotypical, egotistical oftentimes teenaged males 
//
// this is the badge that shows up next to authorized users
function epenis($epenis)
{
    switch($epenis)
    {
        default: return '';
        case 1:
            $q = ['secondary-base', 'icon-person', 'Developer'];
            break;
        case 2:
            $q = ['alert-base', 'icon-terminal', 'System'];
            break;
    }
    return vsprintf('<span class="badge %s left %s">%s</span>',$q);
}
