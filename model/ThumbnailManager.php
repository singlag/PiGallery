<?php
/**
 * Created by IntelliJ IDEA.
 * User: VirtualPc-Win7x64
 * Date: 2014.04.28.
 * Time: 20:38
 */

namespace piGallery\model;

use piGallery\db\entities\ThumbnailInfo;
use piGallery\Properties;

require_once __DIR__."./../config.php";
require_once __DIR__."./../db/entities/ThumbnailInfo.php";

class ThumbnailManager {

    private static function getThumbnailFileName($pathToImage, $size){
        return md5($pathToImage.$size);
    }

    private static function createThumbnail($pathToImage, $size){//TODO: support png and gif too

        Logger::v("ThumbnailManager::createThumbnail","creating thumbnail: " . $pathToImage);
        $pixelCount = $size * $size;

        $outFileName = ThumbnailManager::getThumbnailFileName($pathToImage,$size);

        // load image and get image size
        $img = imagecreatefromjpeg($pathToImage);
        $width = imagesx( $img );
        $height = imagesy( $img );

        $scale = sqrt($pixelCount / ($width * $height));
        if($scale > 1) //do not scale up
            $scale = 1;

        // calculate thumbnail size
        $new_width  = floor( $width  * $scale );
        $new_height = floor( $height * $scale );
        // create a new temporary image
        $tmp_img = imagecreatetruecolor( $new_width, $new_height );

        // copy and resize old image into new image
        //imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

        imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height ); //better quality than simple resize

        // save thumbnail into a file
        imagejpeg( $tmp_img, Helper::concatPath($_SERVER['DOCUMENT_ROOT'],Properties::$thumbnailFolder) . "/" .$outFileName.".jpg", Properties::$thumbnailJPEGQuality  );
        //freeup
        imagedestroy($tmp_img);
        sleep(1);
    }

    /**
     * Checks if the thumbnail to the given image exist
     * @param $pathToImage
     * @param $size
     * @return bool
     */
    private static function isThumbnailExist($pathToImage,$size){
        $fileName = ThumbnailManager::getThumbnailFileName($pathToImage,$size);
        return file_exists(Helper::concatPath($_SERVER['DOCUMENT_ROOT'],Properties::$thumbnailFolder) . "/" .$fileName.".jpg");
    }

    /**
     * @param $pathToImage
     * @return ThumbnailInfo[]
     */
    public static function getAvailableThumbanils($pathToImage){
        $pathToImage = Helper::concatPath(Properties::$imageFolder, $pathToImage);
        $availableThumbnails = array();
        foreach(Properties::$thumbnailSizes as $size){
            $exist = ThumbnailManager::isThumbnailExist($pathToImage,$size);
            $availableThumbnails[]= new ThumbnailInfo($size,$exist);
        }

        return $availableThumbnails;
    }

    public static function requestThumbnail($pathToImage, $size){
        $pathToImage = Helper::concatPath(Properties::$imageFolder, $pathToImage);

       /* $requestedPixelCount = $width * $height;
        $sizeFound = 150;
        foreach(Properties::$thumbnailSizes as $size){
            $sizeFound = $size;
            if($sizeFound * $sizeFound >= $requestedPixelCount)
                break;
        }*/

        if (!ThumbnailManager::isThumbnailExist($pathToImage, $size)){
            ThumbnailManager::createThumbnail($pathToImage,$size);
        }

        $fileName = ThumbnailManager::getThumbnailFileName($pathToImage,$size);
        $thumbnailPath = Helper::concatPath($_SERVER['DOCUMENT_ROOT'],Properties::$thumbnailFolder) . "/" .$fileName.".jpg";
        $image = file_get_contents($thumbnailPath);

        if($image !== false){ //touching files -> means it was used
            touch ($thumbnailPath );
        }

        return array("image" => $image, "filesSze" => filesize($thumbnailPath));
    }

} 