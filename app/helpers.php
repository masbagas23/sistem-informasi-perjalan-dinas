<?php

if(!function_exists('base64ToImage')){
    function base64ToImage($base64, $folder){
        $folderPath = "../storage/app/public/".$folder."/";
        $base64Image = explode(";base64,", $base64);
        $explodeImage = explode("image/", $base64Image[0]);
        $imageType = $explodeImage[1];
        $image_base64 = base64_decode($base64Image[1]);
        $file_name = uniqid();
        $file = $folderPath . $file_name . '.'.$imageType;
        file_put_contents($file, $image_base64);
        return '/storage/'.$folder.'/'.$file_name . '.'.$imageType;
    }
}

if(!function_exists('monthRomawi')){
    function monthRomawi($month)
    {
        switch ($month){
            case 1: 
                return "I";
                break;
            case 2:
                return "II";
                break;
            case 3:
                return "III";
                break;
            case 4:
                return "IV";
                break;
            case 5:
                return "V";
                break;
            case 6:
                return "VI";
                break;
            case 7:
                return "VII";
                break;
            case 8:
                return "VIII";
                break;
            case 9:
                return "IX";
                break;
            case 10:
                return "X";
                break;
            case 11:
                return "XI";
                break;
            case 12:
                return "XII";
                break;
        }
    }
}