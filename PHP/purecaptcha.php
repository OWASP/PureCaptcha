<?php
/**
 * OWASP PureCaptcha
 * Generates simple CAPTCHAs without requiring any third party library.
 * @version 1.1
 */
class PureCaptcha 
{
    protected $charWidth=6;
    protected $charHeight=13;
    protected $chars="2346789ABDHKLMNPRTWXYZ"; //do not modify!
    protected $ascii="eNrtW0FuwyAQ/NIANjbOa3LMG6r8vWrrSonkkt0xDWDvIcGXEYHM7O6s4b
    p4v3zcFlyuiwu/T/Hn4ba4h49fx7COwzqO3+P964tF+i0kViRWJLaQ4RGJF3PiETnQyFGzzidklC
    A31znlkMjt7Zzb2+y/kjQ79MwEbEH/+kOftsjxLHKehK7cbYT/qu0KNBcHmosjzcVI63yi1znTyE
    RHCAd6oZX479uL/yIuBho5SFgs5z8kc0YaOUm4iJfxXxVbEr23Djy0Dv+D1T/iXzvSyKjhop7/r+
    M/LP5v83+w+qdM/aOP/6LKqXr9r0Lm+Z+H1uH/aPGf87/Y7X8t/jfA/2j8b43/MP/7Pv5P7frfbH
    YPtKOszn8VcqIrxPrxXwetw/+5n/pfz395/Y8L2//HG+sf1ZwzjUw0Utf/byH+J+N/bf7L47/xv/
    z7L7QnAFG+7NgAgDYAnRngHgog50wAXAcU9BtgaBqDEz3nTK/zVALw/QiAbwHxFvg/X4G53QJwuw
    VgR4CCZYBc9wh0BpD3gKDuAVkJVE4Aw5EEgGICcF0JgG+C4vQCGI/QBTqWCT5cCSSDVhJANAH0Kw
    AzwfsFMB3xHBzEJliFLHwPoMY5OBEy0cj8OWi0mAFmM8G1D0LwHgC0CYbaBIvaBB1mgHRuAajOEB
    W+CcNnANGcM408UwnkYQLoTwBWApUTgN0F7vAuDNRdILsLvymA+ycgmwSd";
    function __construct()
    {
        $this->ascii=unserialize(gzuncompress(base64_decode(
                                                  preg_replace('/\s+/', '', $this->ascii))));
        $this->text=$this->randomText();

    }

    /**
     * Contains the captcha text.
     * @var string
     */
    public $text;

    /**
     * Generates random text for use in captcha
     * @param  integer $length
     * @return string
     */
    protected function randomText($length=4)
    {
        $res="";
        for ($i=0;$i<$length;++$i)
            $res.=$this->chars[mt_rand(0,strlen($this->chars)-1)];
        return $res;
    }
    /**
     * returns the index of a char in $chars array
     */
    protected function asciiEntry($char)
    {
        for ($i=0;$i<strlen($this->chars);++$i)
            if ($this->chars[$i]==$char) return $i;
        return -1;

    }
    /**
     * converts a text to a bitmap
     * which is a 2D array of ones and zeroes denoting the text
     */
    protected function textBitmap($text,$spacing=2)
    {
        $width=$this->charWidth;
        $height=$this->charHeight;
        $result=array();
        $baseY=$baseX=0;

        for ($index=0;$index<strlen($text);++$index)
        {
            for ($j=0;$j<$height;++$j)
            {
                for ($i=0;$i<$width;++$i)
                    $result[$baseY+$j][$baseX+$i]=
                        1-$this->ascii[$this->asciiEntry($text[$index])][$j][$i];
                for ($i=0;$i<$spacing;++$i)
                    $result[$baseY+$j][$baseX+$width+$i]=0;
            }
            $baseX+=$width+$spacing;
        }
        return $result;
    }
    /**
     * displays a bitmap string on the browser screen
     */
    protected function displayBitmap($bitmap)
    {
        header("Content-Type: image/bmp");
        echo $this->bitmap2bmp($bitmap);
    }
    /**
     * generates a monochrome BMP file
     * a bitmap needs to be sent to this function
     * i.e a 2D array with every element being either 1 or 0
     * @param  integer $width
     * @param  integer $height
     * @param  array $bitmap
     * @return string
     */
    protected function bitmap2bmp($bitmap)
    {
        $width=count($bitmap[0]);
        $height=count($bitmap);
        $bytemap=$this->bitmap2bytemap($bitmap);

        $rowSize=floor(($width+31)/32)*4;
        $size=$rowSize*$height + 62; //62 metadata size
        #bitmap header
        $data= "BM"; //header
        $data.= (pack('V',$size)); //bitmap size ,4 bytes unsigned little endian
        $data.= "RRRR";
        $data.= (pack('V',14+40+8)); //bitmap data start offset ,
        //4 bytes unsigned little endian, 14 forced, 40 header, 8 colors

        #info header
        $data.= pack('V',40); //bitmap header size (min 40),
        //4 bytes unsigned little-endian
        $data.= (pack('V',$width)); //bitmap width , 4 bytes signed integer
        $data.= (pack('V',$height)); //bitmap height , 4 bytes signed integer
        $data.= (pack('v',1)); //number of colored plains , 2 bytes
        $data.= (pack('v',1)); //color depth , 2 bytes
        $data.= (pack('V',0)); //compression algorithm , 4 bytes (0=none, RGB)
        $data.= (pack('V',0)); //size of raw data, 0 is fine for no compression
        $data.= (pack('V',11808)); //horizontal resolution (dpi), 4 bytes
        $data.= (pack('V',11808)); //vertical resolution (dpi), 4 bytes
        $data.= (pack('V',0)); //number of colors in pallette (0 = all), 4 bytes
        $data.= (pack('V',0)); //number of important colors (0 = all), 4 bytes

        #color palette
        $data.= (pack('V',0x00FFFFFF)); //next color, white
        $data.= (pack('V',0)); //first color, black

        for ($j=$height-1;$j>=0;--$j)
            for ($i=0;$i<$rowSize/4;++$i)
                for ($k=0;$k<4;++$k)
                    if (isset($bytemap[$j][$i*4+$k]))
                        $data.= pack('C',$bytemap[$j][$i*4+$k]);
                    else
                        $data.= pack('C',0);
        return $data;
    }
    /**
     * Converts a bitmap to a bytemap, which is necessary for outputting it
     *
     */
    protected function bitmap2bytemap($bitmap)
    {
        $width=count($bitmap[0]);
        $height=count($bitmap);
        $bytemap=array();
        for ($j=0;$j<$height;++$j)
        {
            for ($i=0;$i<$width/8;++$i)
            {
                $bitstring="";
                for ($k=0;$k<8;++$k)
                    if (isset($bitmap[$j][$i*8+$k]))
                        $bitstring.=$bitmap[$j][$i*8+$k];
                    else
                        $bitstring.="0";
                $bytemap[$j][]=bindec($bitstring);
            }
        }
        return $bytemap;
    }
    /**
     * rotates a bitmap, returning new dimensions with the bitmap
     * return bitmap
     */
    protected function rotateBitmap($bitmap, $degree)
    {
        $c=cos(deg2rad($degree));
        $s=sin(deg2rad($degree));

        $width=count($bitmap[0]);
        $height=count($bitmap);
        $newHeight=round(abs($width*$s)+abs($height*$c));
        $newWidth=round(abs($width*$c) + abs($height*$s))+1;
        $x0 = $width/2 - $c*$newWidth/2 - $s*$newHeight/2;
        $y0 = $height/2 - $c*$newHeight/2 + $s*$newWidth/2;
        $result=array_fill(0, $newHeight, array_fill(0, $newWidth, 0));
        for ($j=0;$j<$newHeight;++$j)
            for ($i=1;$i<$newWidth;++$i)
            {
                $y=(int)(-$s*$i+$c*$j+$y0);
                $x=(int)($c*$i+$s*$j+$x0);
                if (isset($bitmap[$y][$x]))
                    $result[$j][$i]=$bitmap[$y][$x];
            }
        return $result;
    }
    /**
     * scales a bitmap to be bigger
     */
    protected function scaleBitmap($bitmap,$scaleX,$scaleY)
    {
        $width=count($bitmap[0]);
        $height=count($bitmap);
        $newHeight=$height*$scaleY;
        $newWidth=$width*$scaleX;
        $result=array_fill(0, $newHeight, array_fill(0, $newWidth, 0));
        for ($j=0;$j<$newHeight;++$j)
            for ($i=0;$i<$newWidth;++$i)
                $result[$j][$i]=$bitmap[(int)($j/$scaleY)]
                [(int)($i/$scaleX)];
        return $result;
    }
    /**
     * adds random noise to the captcha
     */
    protected function distort($bitmap,$noisePercent=5)
    {
        for ($j=0;$j<count($bitmap);++$j)
            for ($i=0;$i<count($bitmap[0]);++$i)
                if (isset($bitmap[$j][$i]) && mt_rand()%100<$noisePercent)
                    $bitmap[$j][$i]=1;
        return $bitmap;
    }

    /**
     * draw a captcha to the screen, returning its value
     */
    public function show($distort=true,$scale=2.3)
    {
        $bitmap=$this->textBitmap($this->text);
        $degree=mt_rand(2,4);
        if (mt_rand()%100<50)
            $degree=-$degree;
        $bitmap=$this->rotateBitmap($bitmap,$degree);
        $bitmap=$this->scaleBitmap($bitmap,$scale,$scale);
        if ($distort) $bitmap=$this->distort($bitmap);
        $this->displayBitmap($bitmap);
        return $this->text;
    }

}
