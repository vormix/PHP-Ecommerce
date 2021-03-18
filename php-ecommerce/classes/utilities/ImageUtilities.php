<?php

class ImageUtilities {

  public static function thumbnail($file) 
  {
    return self::resizeImage($file, 320, 320, '_thumbnail');
  }

  public static function wallpaper($file) 
  {
    return self::resizeImage($file, 1280, 1280, '');
  }

  private static function resizeImage($file, $w, $h, $newName = '', $crop=FALSE) 
  {
    list($width, $height) = getimagesize($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) 
        {
            $width = ceil($width-($width*abs($r-$w/$h)));
        } else 
        {
            $height = ceil($height-($height*abs($r-$w/$h)));
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = $h*$r;
            $newheight = $h;
        } else {
            $newheight = $w/$r;
            $newwidth = $w;
        }
    }
    $src = imagecreatefromjpeg($file);
    $dst = imagecreatetruecolor($newwidth, $newheight);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagejpeg($dst, str_replace('.jpg', $newName.'.jpg', $file) );
  
    return $dst;
  }
}