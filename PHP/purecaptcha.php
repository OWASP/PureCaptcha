<?php
/**
 * OWASP PureCaptcha
 * Generates simple CAPTCHAs without requiring any third party library.
 * @version 1.1
 */
class PureCaptcha 
{
    /**
     * The width of a character
     *
     * @var number
     */
    protected $charWidth = 6;

    /**
     * The height of the character
     *
     * @var number
     */
    protected $charHeight = 13;

    /**
     * The string from where the characters are chosen for Captcha
     *
     * @var string
     */
    protected $chars = "2346789ABDHKLMNPRTWXYZ"; //do not modify!

    /**
     * The encoded form of the bitmap of all characters
     *
     * @var string
     */
    protected $ascii = "eNrtW0FuwyAQ/NIANjbOa3LMG6r8vWrrSonkkt0xDWDvIcGXEYHM7O6s
    4bp4v3zcFlyuiwu/T/Hn4ba4h49fx7COwzqO3+P964tF+i0kViRWJLaQ4RGJF3PiETnQyFGzzidk
    lCA31znlkMjt7Zzb2+y/kjQ79MwEbEH/+kOftsjxLHKehK7cbYT/qu0KNBcHmosjzcVI63yi1znT
    yERHCAd6oZX479uL/yIuBho5SFgs5z8kc0YaOUm4iJfxXxVbEr23Djy0Dv+D1T/iXzvSyKjhop7/
    r+M/LP5v83+w+qdM/aOP/6LKqXr9r0Lm+Z+H1uH/aPGf87/Y7X8t/jfA/2j8b43/MP/7Pv5P7frf
    bHYPtKOszn8VcqIrxPrxXwetw/+5n/pfz395/Y8L2//HG+sf1ZwzjUw0Utf/byH+J+N/bf7L47/x
    v/z7L7QnAFG+7NgAgDYAnRngHgog50wAXAcU9BtgaBqDEz3nTK/zVALw/QiAbwHxFvg/X4G53QJw
    uwVgR4CCZYBc9wh0BpD3gKDuAVkJVE4Aw5EEgGICcF0JgG+C4vQCGI/QBTqWCT5cCSSDVhJANAH0
    KwAzwfsFMB3xHBzEJliFLHwPoMY5OBEy0cj8OWi0mAFmM8G1D0LwHgC0CYbaBIvaBB1mgHRuAajO
    EBW+CcNnANGcM408UwnkYQLoTwBWApUTgN0F7vAuDNRdILsLvymA+ycgmwSd";

    /**
     * The final bitmap of all 22 characters
     * The bitmap of each character is a matrix of 13 rows and 6 columns
     *
     * @var array
     */
    protected $bitmap;

    /**
     * The text to be displayed in captcha
     *
     * @var string
     */
    protected $captcha;

    /**
     * The length of the captcha text
     *
     * @var integer
     */
    protected $length;

    /**
     * The spacing between two characters in the captcha
     *
     * @var number
     */
    protected $spacing;

    /**
     * The degree of rotation of captcha text clockwise
     *
     * @var number
     */
    protected $degree;

    /**
     * Amount to be scaled in x direction
     *
     * @var number
     */
    protected $scaleX;

    /**
     * Amount to be scaled in y direction
     *
     * @var number
     */
    protected $scaleY;

    /**
     * Constructor to intialize bitmap
     */
    function __construct()
    {
        $this->bitmap = unserialize(gzuncompress(base64_decode(
            preg_replace('/\s+/', '', $this->ascii))));

        // Setting up default values
        $this->length = 4;
        $this->captcha = $this->randomText();
        $this->spacing = 2;
        $this->degree = mt_rand(2, 4);
        if (mt_rand() % 100 < 50)
            $this->degree =- $this->degree;
        $this->scaleX = 2.3;
        $this->scaleY = 2.3;
    }

    /**
     * Returns the value of the captcha
     *
     * @return string
     */
    public function getCaptcha()
    {
        return $this->captcha;
    }

    /**
     * Sets the captcha text
     *
     * @param string $captcha
     * @return bool whether the $captcha was valid for not
     */
    public function setCaptcha($captcha)
    {
        $captcha = strtoupper($captcha);
        // Checking if $captcha contains characters in the dataset
        for ($i = 0; $i < strlen($captcha); ++$i)
            if ($this->asciiEntry($captcha[$i]) == -1)
                return false;

        $this->captcha = $captcha;
        $this->length = strlen($this->captcha);
        return true;
    }

    /**
     * Returns the length of the captcha
     *
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Sets the length of the captcha text and generates a new captcha
     *
     * @param integer $length
     */
    public function setLength($length)
    {
        $this->length = $length;
        $this->captcha = $this->randomText();
    }

    /**
     * Returns the spacing between two characters in the captcha
     *
     * @return number
     */
    public function getSpacing()
    {
        return $this->spacing;
    }

    /**
     * Sets the spacing between two characters in the captcha
     *
     * @param number $spacing
     */
    public function setSpacing($spacing)
    {
        $this->spacing = $spacing;
    }

    /**
     * Returns the degree of rotation of captcha text
     *
     * @return number
     */
    public function getDegree()
    {
        return $this->degree;
    }

    /**
     * Sets the degree of rotation of captcha text
     *
     * @param number $degree
     */
    public function setDegree($degree)
    {
        $this->degree = $degree;
    }

    /**
     * Returns the horizontal scale of the image
     *
     * @return number
     */
    public function getScaleX()
    {
        return $this->scaleX;
    }

    /**
     * Sets the horizontal scale of the image
     *
     * @param number $scaleX
     */
    public function setScaleX($scaleX)
    {
        $this->scaleX = $scaleX;
    }

    /**
     * Returns the vertical scale of the image
     *
     * @return number
     */
    public function getScaleY()
    {
        return $this->scaleY;
    }

    /**
     * Sets the vertical scale of the image
     *
     * @param number $scaleY
     */
    public function setScaleY($scaleY)
    {
        $this->scaleY = $scaleY;
    }

    /**
     * Generates random text for use in captcha
     *
     * @return string          
     */
    protected function randomText()
    {
        $res = "";
        for ($i = 0; $i < $this->length; ++$i)
            $res .= $this->chars[mt_rand(0, strlen($this->chars) - 1)];
        return $res;
    }

    /**
     * Returns the index of a char in $chars array
     *
     * @param character $char
     */
    protected function asciiEntry($char)
    {
        for ($i = 0; $i < strlen($this->chars); ++$i)
            if ($this->chars[$i] == $char)
                return $i;
        return -1;
        
    }

    /**
     * Converts a text to a bitmap
     * which is a 2D array of ones and zeroes denoting the text
     *
     * @param string $text 
     * @return array the final bitmap of the text
     */
    protected function textBitmap($text)
    {
        $width = $this->charWidth;
        $height = $this->charHeight;
        $spacing = $this->spacing;
        $result = array();
        $baseY = 0;
        $baseX = 0;

        for ($index = 0; $index < strlen($text); ++$index) {
            for ($j = 0; $j < $height; ++$j) {
                for ($i = 0; $i < $width; ++$i)
                    $result[$baseY + $j][$baseX + $i] = 1 - 
                        $this->bitmap[$this->asciiEntry(
                        $text[$index])][$j][$i];
                for ($i = 0; $i < $spacing; ++$i)
                    $result[$baseY + $j][$baseX + $width + $i] = 0;
            }
            $baseX += $width + $spacing;
        }
        return $result;
    }

    /**
     * Displays a bitmap string on the browser screen
     *
     * @param array $bitmap the bitmap to be printed
     */
    protected function displayBitmap($bitmap)
    {
        header("Content-Type: image/bmp");
        echo $this->bitmap2bmp($bitmap);
    }

    /**
     * Generates a monochrome BMP file 
     * a bitmap needs to be sent to this function
     * i.e a 2D array with every element being either 1 or 0
     *
     * @param  array $bitmap
     * @return string
     */
    protected function bitmap2bmp($bitmap)
    {
        $width = count($bitmap[0]);
        $height = count($bitmap);
        $bytemap = $this->bitmap2bytemap($bitmap);

        $rowSize = floor(($width + 31) / 32) * 4;
        $size = $rowSize * $height + 62; //62 metadata size
        #bitmap header
        $data = "BM"; //header
        $data .= (pack('V', $size)); //bitmap size
        //4 bytes unsigned little endian
        $data .= "RRRR";
        $data .= (pack('V', 14 + 40 + 8)); //bitmap data start offset ,
        //4 bytes unsigned little endian, 14 forced, 40 header, 8 colors

        #info header
        $data .= pack('V', 40); //bitmap header size (min 40),
        //4 bytes unsigned little-endian
        $data .= (pack('V', $width)); //bitmap width , 4 bytes signed integer
        $data .= (pack('V', $height)); //bitmap height , 4 bytes signed integer
        $data .= (pack('v', 1)); //number of colored plains , 2 bytes
        $data .= (pack('v', 1)); //color depth , 2 bytes
        $data .= (pack('V', 0)); //compression algorithm
        //4 bytes (0=none, RGB)
        $data .= (pack('V', 0)); //size of raw data
        //0 is fine for no compression
        $data .= (pack('V', 11808)); //horizontal resolution (dpi), 4 bytes
        $data .= (pack('V', 11808)); //vertical resolution (dpi), 4 bytes
        $data .= (pack('V', 0)); //number of colors in pallette (0 = all)
        //4 bytes
        $data .= (pack('V', 0)); //number of important colors (0 = all)
        //4 bytes 

        #color palette
        $data .= (pack('V', 0x00FFFFFF)); //next color, white
        $data .= (pack('V', 0)); //first color, black

        for ($j = $height - 1; $j >= 0; --$j)
            for ($i = 0; $i < $rowSize / 4; ++$i)
                for ($k = 0; $k < 4; ++$k)
                    if (isset($bytemap[$j][$i * 4 + $k]))
                        $data .= pack('C', $bytemap[$j][$i * 4 + $k]);
                    else
                        $data .= pack('C', 0);
        return $data;
    }

    /**
     * Converts a bitmap to a bytemap, which is necessary for outputting it
     *
     * @param array $bitmap
     * @return array
     */
    protected function bitmap2bytemap($bitmap)
    {
        $width = count($bitmap[0]);
        $height = count($bitmap);
        $bytemap = array();
        for ($j = 0; $j < $height; ++$j) {
            for ($i = 0; $i < $width / 8; ++$i) {
                $bitstring = "";
                for ($k = 0; $k < 8; ++$k)
                    if (isset($bitmap[$j][$i * 8 + $k]))
                        $bitstring .= $bitmap[$j][$i * 8 + $k];
                    else
                        $bitstring .= "0";
                $bytemap[$j][] = bindec($bitstring);
            }
        }
        return $bytemap;
    }

    /**
     * Rotates a bitmap, returning new dimensions with the bitmap
     *
     * @param array $bitmap
     * @return array
     */
    protected function rotateBitmap($bitmap)
    {
        $c = cos(deg2rad($this->degree));
        $s = sin(deg2rad($this->degree));

        $width = count($bitmap[0]);
        $height = count($bitmap);
        $newHeight = round(abs($width * $s) + abs($height * $c));
        $newWidth = round(abs($width * $c) + abs($height * $s)) + 1;
        $x0 = $width / 2 - $c * $newWidth / 2 - $s * $newHeight / 2;
        $y0 = $height / 2 - $c * $newHeight / 2 + $s * $newWidth / 2;
        $result = array_fill(0, $newHeight, array_fill(0, $newWidth, 0));
        for ($j = 0; $j < $newHeight; ++$j) 
            for ($i = 1; $i < $newWidth; ++$i) {
                $y = (int)(-$s * $i + $c * $j + $y0);
                $x = (int)($c * $i + $s * $j + $x0);
                if (isset($bitmap[$y][$x]))
                    $result[$j][$i] = $bitmap[$y][$x];
            }
        return $result;
    }

    /**
     * Scales a bitmap to be bigger
     *
     * @param array $bitmap
     * @return array
     */
    protected function scaleBitmap($bitmap)
    {
        $width = count($bitmap[0]);
        $height = count($bitmap);
        $newHeight = $height * $this->scaleY;
        $newWidth = $width * $this->scaleX;
        $result = array_fill(0, $newHeight, array_fill(0, $newWidth, 0));
        for ($j = 0; $j < $newHeight; ++$j)
            for ($i = 0; $i < $newWidth; ++$i)
                $result[$j][$i] = $bitmap[(int)($j / $this->scaleY)]
            [(int)($i / $this->scaleX)];
        return $result;
    }

    /**
     * Adds random noise to the captcha
     *
     * @param array $bitmap
     * @param numver @noisePercent the percentage of noise to be added
     * @return array
     */
    protected function distort($bitmap, $noisePercent = 5)
    {
        for ($j = 0; $j < count($bitmap); ++$j)
            for ($i = 0; $i < count($bitmap[0]); ++$i)
                if (isset($bitmap[$j][$i]) && mt_rand() % 100 < $noisePercent)
                    $bitmap[$j][$i] = 1;
        return $bitmap;
    }

    /**
     * Draw a captcha to the screen, returning its value
     *
     * @param bool $distort
     * @return string
     */
    public function show($distort = true)
    {
        $bitmap = $this->textBitmap($this->captcha);
        $bitmap = $this->rotateBitmap($bitmap, $degree);
        $bitmap = $this->scaleBitmap($bitmap);
        if ($distort)
            $bitmap = $this->distort($bitmap);
        $this->displayBitmap($bitmap);
        return $this->captcha;
    }

}
