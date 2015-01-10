<?php
class Color {
    /**
     * might be useful to someone out there */
    public static function hex2rgb( $hex )
    {
        if(!preg_match('/^[0-9a-f]{6}$/i',$hex))
            throw new InvalidArgumentException("Invalid hex: $hex");

        for($rgb = [], $i = 0; $i < 6; $i+=2)
            $rgb[] = hexdec(substr($hex,$i,2));
        return $rgb;
    }

    public static function hsl2rgb( $hsl )
    {
        list($h,$s,$l) = $hsl;
        if($s == 0)
            $r = $g = $b = $l * 255;
        else
        {
            $var2 = $l < 0.5 ? $l * (1 + $s) : ($l + $s) - ($s * $l);
            $var1 = 2 * $l - $var2;

            $hue2rgb = function($var1,$var2,$h) {
                if($h < 0) $h += 1;
                if($h > 1) $h -= 1;
                if((6*$h) < 1) return $var1 + ($var2 - $var1) * 6 * $h;
                if((2*$h) < 1) return $var2;
                if((3*$h) < 2) return $var1 + ($var2 - $var1) * ((2/3) - $h) * 6;
                return $var1;
            };

            $r = 255 * $hue2rgb($var1,$var2,$h+(1/3));
            $g = 255 * $hue2rgb($var1,$var2,$h);
            $b = 255 * $hue2rgb($var1,$var2,$h - (1/3));
        }

        return [$r,$g,$b];
    }

    public static function rgb2hsl( $rgb )
    {
        $rgb = array_map(function($c){return $c/255;},$rgb);
        $min = min($rgb);
        $max = max($rgb);
        $delta = $max - $min;

        $l = ($max + $min) / 2;

        if($delta == 0)
            $h = $s = 0;
        else
        {
            list($r,$g,$b) = $rgb;

            $s = $l < 0.5 ? $delta/($max + $min) : $delta/(2 - $max - $min);
            $deltaR = ((($max - $r) / 6) + ($delta/2))/$delta;
            $deltaG = ((($max - $g) / 6) + ($delta/2))/$delta;
            $deltaB = ((($max - $b) / 6) + ($delta/2))/$delta;

            switch($max)
            {
                case $r:
                    $h = $deltaB - $deltaG;
                    break;
                case $g:
                    $h = (1/3) + $deltaR - $deltaB;
                    break;
                case $b:
                    $h = (2/3) + $deltaG - $deltaR;
                    break;
            }

            if($h < 0)
                $h += 1;
            if($h > 1)
                $h -= 1;
        }

        return [$h,$s,$l];
    }

    /**
     * http://stackoverflow.com/a/9733420 */
    const CONTRAST_RATIO = 3.5;

    public static function luminanace( $rgb )
    {
        list($r,$g,$b) = array_map(function($v){
            $v/=255;
            return ($v <= 0.03928) ? $v/12.92 : pow(($v+0.055)/1.055,2.4);
        },$rgb);
        return ($r*0.2126+$g*0.7152+$b*0.0722) + 0.05;
    }

    public static function fstop( $values, $format = null )
    {
        if(count($values) !== 3)
            throw new InvalidArgumentException('Not enough/too many colors.');

        switch($format)
        {
            case 'array':
                return $values;
            case null:
                $string = '#%02x%02x%02x';
                break;
            case 'rgba':
                $string = 'rgba(%d,%d,%d,1)';
                break;
            case 'hsla':
                $string = 'hsla(%d,%.4f,%.4f,1)';
                break;
            default:
                throw new InvalidArgumentException('Unrecognized format');
        }

        return vsprintf($string,$values);
    }

    /**
     * instance stuff
     * @usage: $color = new Color('ffcc00');
     *         echo '.custom-color-lite-1 { background: '.$color->lighten() }
       or something
     */
    protected $original = '';
    protected $rgb = [];
    protected $hsl = [];

    public function __construct($hex)
    {
        $this->original = $hex;
        $this->rgb = self::hex2rgb($hex);
        $this->hsl = self::rgb2hsl($this->rgb);
    }

    public function undo()
    {
        $this->__construct($this->original);
    }

    protected function adjust($param,$amount)
    {
        list($h,$s,$l) = $this->hsl;
        switch($param)
        {
            case 'hue':
            case 'saturation':
            case 'red':
            case 'green':
            case 'blue':
                throw new LogicException("$param adjust Not yet implemented.");
                break;
            case 'lightness':
                $l += $amount/100;
                if($l < 0)
                    $l = 0;
                if($l > 1)
                    $l = 1;
                break;
        }
        //return self::fstop(self::hsl2rgb([$h,$s,$l]));
        $this->hsl = [$h,$s,$l];
        $this->rgb = self::hsl2rgb($this->hsl);
    }

    public function lighten( $percent )
    {
        if($percent <= 0 || $percent > 100)
            throw new InvalidArgumentException('Use a number 1-100 (percent)');

        return $this->adjust('lightness',$percent);
    }

    public function darken( $percent )
    {
        if($percent <= 0 || $percent > 100)
            throw new InvalidArgumentException('Use a number 1-100 (percent)');

        return $this->adjust('lightness',-1 * $percent);
    }

    public function getTextColor()
    {
        return self::fstop(
            self::luminanace([255,255,255])/self::luminanace($this->rgb) 
                >= self::CONTRAST_RATIO ? [255,255,255] : [0,0,0]
        );
    }

    public function __toString()
    {
        return self::fstop($this->rgb);
    }
}// I want to cry.
