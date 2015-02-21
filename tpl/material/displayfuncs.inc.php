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

    $ret = [];
    list($ret[], $diff) = ftime($diff, 31536000, 'year');
    list($ret[], $diff) = ftime($diff, 2592000, 'month');
    list($ret[], $diff) = ftime($diff, 604800, 'week');
    list($ret[], $diff) = ftime($diff, 86400, 'day');
    list($ret[], $diff) = ftime($diff, 3600, 'hour');
    list($ret[], $diff) = ftime($diff, 60, 'minute');
    list($ret[], $diff) = ftime($diff, 1, 'second');
    return sprintf('<time title="%s">%s ago</time>',
        date(BUNZ_BUNZILLA_DATE_FORMAT, $time),
        implode(', ',array_filter($ret,function($val){return $val;}))
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
    return '<span class="left badge '.($badge ? 'badge ' : '').'z-depth-5 tag priority-'.$id.'" title="Priority '.$id.' out of '.count($pryor).'"><span class="hide-on-small-only">Priority: </span><strong class="'.$p['icon'].'">'.$p['title'].'</strong></span>';
}


/**
 * ditto for <select> dropdowns */
function dropdown( $fieldName, $values, $selected = false, $disableKey = false )
{
    if($disableKey !== false && isset($values[$disableKey]))
        unset($values[$disableKey]);

    $ret = "<select name='$fieldName'>\n";
    foreach($values as $opt)
    {
        $ret .= "\t<option value='{$opt['id']}' class='{$fieldName}-{$opt['id']}' data-icon='{$opt['icon']}'".($selected === $opt['id'] ? ' selected' : '').">{$opt['title']}</option>\n";
    }
    return "$ret</select>";
}
function statusDropdown( $selected, $disableId = false ) { 
    return dropdown('status',Cache::read('statuses'),$selected, $disableId);
}
function categoryDropdown( $selected, $disableId = false ) { 
    return dropdown('category', Cache::read('categories'), $selected, $disableId);
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