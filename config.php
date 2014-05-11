<?php
namespace piGallery;

require_once __DIR__ ."./db/entities/User.php";
require_once __DIR__ ."./db/entities/Role.php";

use piGallery\db\entities\Role;
use piGallery\db\entities\User;


class Properties{
	public static $documentRoot  = "PiGallery";

    public static $imageFolder = "./testimages";

    /*Thumbnail settings*/
    public static $thumbnailFolder = "./thumbnails";
    public static $thumbnailSizes = array(300, 600);
    public static $thumbnailJPEGQuality = 75;
    public static $enableImageCaching = false;


    public static $databaseEnabled = false;

    /*Database settings*/
    public static $databaseAddress = "localhost";
    public static $databseUserName = "root";
    public static $databsePassword = "root";
    public static $databseName = "pigallery";

    /*No-Database settings*/
    public static $users = array(
        array("userName" => "test", "password" => "test", "role" => 1)
    );

}