<?php
//
// functions for to displaying the things
//

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

    /**
     * todo: maybe expand this to days/months/years
     * maybe that's overkill */
    if($diff < 86400)
    {
        if($diff < 60)
            $unit =  'second';
        elseif($diff < 3600)
        {
            $diff = (int)$diff/60;
            $unit = 'minute';
        } else {
            $diff = (int)$diff/3600;
            $unit = 'hour';
        }
        return sprintf('%d %s%s ago', $diff, $unit, ($diff == 1 ? '' : 's'));
    }

    return date(BUNZ_BUNZILLA_DATE_FORMAT, $time);
}

/**
 * lets create the tag and status buttons with the same html
 * and use CSS to differentiate them */
function badge( $type, $id, $short = false )
{
    if(!in_array($type,['tag','status'],true))
        throw new InvalidArgumentException(__FUNCTION__);

    static $tags = null;
    static $status = null;
    if($$type === null)
        $$type = Cache::read($type.($type == 'status' ? 'e':'').'s');

    return '
<span class="z-depth-3 '.$type.'-'.$id.' '.${$type}[$id]['icon'].'" 
      title="'.${$type}[$id]['title'].'">'
.($short ? '': ${$type}[$id]['title']).'</span>'."\n";
}
function status( $id ) { return badge('status',(int)$id); }
function tag( $id, $short = true ) { return badge('tag',(int)$id,$short); }
