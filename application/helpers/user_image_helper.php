<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('getUserImageSrc'))
{
    function getUserImage($sender_controller, $imgSrc)
    {
        if (!isset($imgSrc) || strlen($imgSrc) == 0)
        {
            return '/img/no-photo.jpeg';
        }
        else
        {
            return $imgSrc;
        }
    }
}

if( !function_exists('checkUserUploadedPic'))
{
    function checkUserUploadedPic($sender_controller, $user_id)
    {
        //Must check if a user uploaded a pic with png, gif, jpg, jpeg
        
        $png_version = $user_id .'.png';
        $jpg_version = $user_id .'.jpg';
        $gif_version = $user_id .'.gif';
        $jpeg_version = $user_id .'.jpeg';
        
        
        if(file_exists('./img/'. $png_version)) 
            return '/img/'.$png_version;
        else if (file_exists('./img/'.$jpg_version))
            return '/img/'.$jpg_version;
        else if (file_exists('./img/'.$gif_version))
            return '/img/'.$gif_version;
        else if (file_exists('./img/'.$jpeg_version))
            return '/img/'.$jpeg_version;
        else
            return null;          
    }
    
}

?>