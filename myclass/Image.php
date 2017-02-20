<?php

namespace app\myclass;

class Image 
{
    public $nativeimg;
    public $image;
    public $image_type;
 
    public function load($filename) 
    {
        $image_info = getimagesize($filename);
        $this->image_type = $image_info[2];
 
        if( $this->image_type == IMAGETYPE_JPEG ) 
        {
            $this->nativeimg = imagecreatefromjpeg($filename);
            if($this->getHeight() < $this->getWidth())
                $this->exifrotate($filename);
            return true;
        } 
        elseif( $this->image_type == IMAGETYPE_GIF ) 
        {
            $this->nativeimg = imagecreatefromgif($filename);
            return true;
        } 
        elseif( $this->image_type == IMAGETYPE_PNG ) 
        {
            $this->nativeimg = imagecreatefrompng($filename);
            return true;
        }
        else
            return false;
 
    }
 
    public function save($filename, $image_type=IMAGETYPE_JPEG, $compression=90, $owner=null, $permissions=null) 
    {
       if( $image_type == IMAGETYPE_JPEG ) 
        {
            imagejpeg($this->image,$filename,$compression);
        } 
 
        elseif( $image_type == IMAGETYPE_GIF ) 
        {
            imagegif($this->image,$filename);
        } 
        elseif( $image_type == IMAGETYPE_PNG ) 
        {
            imagepng($this->image,$filename);
        }
 
        if( $permissions != null) 
        {
            chown($filename, $permissions);
        }
 
        if( $permissions != null) 
        {
            chmod($filename, $owner);
        }
    }
 
    public function output($image_type=IMAGETYPE_JPEG) 
    {
        if( $image_type == IMAGETYPE_JPEG ) 
        {
            imagejpeg($this->image);
        } 
        elseif( $image_type == IMAGETYPE_GIF ) 
        {
            imagegif($this->image);
        } 
        elseif( $image_type == IMAGETYPE_PNG ) 
        {
            imagepng($this->image);
        }
    }
 
    public function getWidth() 
    {
        return imagesx($this->nativeimg);
    }
 
    public function getHeight() 
    {
        return imagesy($this->nativeimg);
    }
 
    public function resizeToHeight($height) 
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }
 
    public function resizeToWidth($width) 
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }
 
    public function resampleToHeight($height) 
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resample($width,$height);
    }
 
    public function resampleToWidth($width) 
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resample($width,$height);
    }
 
 
    public function scale($scale) 
    {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }
 
    public function resize($width,$height) 
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresized($new_image, $this->nativeimg, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }      
 
    public function resample($width,$height) 
    {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->nativeimg, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }      
 
    public function sharpen($strenght = 18)
    {
        /*Less value in the middle means more sharpen*/
        $sharpen_matrix=array(  array(0, -1, 0),
                                array(-1, $strenght, -1),
                                array(0, -1, 0));
 
        $divisor=array_sum(array_map('array_sum', $sharpen_matrix));
        imageconvolution($this->image, $sharpen_matrix, $divisor, 0);
   }
 
    public function exifrotate($path)
    {
        if(function_exists('exif_read_data'))
        {
            $exif = exif_read_data($path);
            if(!empty($exif['Orientation'])) 
            {
                switch($exif['Orientation']) 
                {
                    case 8:
                        $this->nativeimg = imagerotate($this->nativeimg, 90, 0);
                        break;
                    case 3:
                        $this->nativeimg = imagerotate($this->nativeimg, 180, 0);
                        break;
                    case 6:
                        $this->nativeimg = imagerotate($this->nativeimg, -90, 0);
                        break;
                }
            }
        }
    }
}