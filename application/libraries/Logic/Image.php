<?php
namespace Library\Logic;

use LogicAbstract;

class Image extends \Library\Logic\LogicAbstract
{
    private $CI;
    private $Email;
    
    function __construct() { 
        parent::__construct();
        $this->CI =& get_instance();
    }
    
    /**
     * @param string $fn Image filename source
     */
    static function Resize($fn, $width=500, $height=500, $default_format="png", $overwrite=true) {
        $size = getimagesize($fn);
        $ratio = $size[0]/$size[1]; // width/height
        
        if ($size[0] <= $width & $size[1] <= $height) {
            return $fn;
        }
        
        if( $size[0] > $width) {
            $height = $width/$ratio;
        }
        elseif ($size[1] > $height) {
            $width = $width*$ratio;
        }
        
        $target = $fn;
        if (!$overwrite) {
            $targe .= "." . time();
        }

        $src = imagecreatefromstring(file_get_contents($fn));
        $dst = imagecreatetruecolor($width, $height);
        
        imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
        imagedestroy($src);
        imagepng($dst, $target); // adjust format as needed
        imagedestroy($dst);
        
        return $target;
    }
}