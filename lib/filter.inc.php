<?php
/**
 */
class Filter
{
    // get flag name
    protected static function _filterFlag($f)
    {
        return constant(
            'FILTER_'
            .($f==='null_on_failure'||$f==='require_array'?'':'FLAG_')
            .strtoupper($f)
        );
    }

    // get full filter config array for a variable
    public static function _filterOptions($validate, $id, $flag = null, $opts = null)
    {
        $const = constant('FILTER'.
            (stripos($id,'callback')===0?'':'_'.($validate?'VALIDATE':'SANITIZE'))
            .'_'.strtoupper($id)
        );
        if($flag === null && $opts === null)
            return $const;

        if($flag||$opts)
        {       
            $return = ['filter'=>$const];
            if($flag)
            {   $flags = 0;
                if(is_array($flag))
                    foreach($flag as $f)
                        $flags |= self::_filterFlag($f);
                else
                    $flags = self::_filterFlag($flag);
                $return['flags'] = $flags;
            }
            if(is_array($opts)||is_scalar($opts))
                $return['options'] = $opts;
            return $return;
        }
        return $const; // no options exist without flags
    }

    protected $options = [];

    public function __construct(){}

    public function add( $key, $opts )
    {
        $this->options[$key] = $opts;
    }

    public function addEmail( $key = 'email' )
    {
        $this->add($key, self::_filterOptions(1, 'email'));
    }

    public function addCallback($key, $callback)
    {
        $this->add($key, self::_filterOptions(0,'callback',null,$callback));
    }

    public function addString($key)
    {
        $this->add($key, self::_filterOptions(0,'full_special_chars'));
    }

    public function addInt($key)
    {
        $this->add($key, self::_filterOptions(0,'number_int'));
    }

    // all this so I can get an empty, keyed array for blank forms
    // ._.;
    public function input_array()
    {
        $result = filter_input_array(INPUT_POST,$this->options);
        if(count($result) < count($this->options))
        {
            $justkeys = array_combine(
                array_keys($this->options),
                array_fill(0,count($this->options),'')
            );
            return $result ? array_merge($result, $justkeys) : $justkeys;
        }
        return $result;
    }
}
