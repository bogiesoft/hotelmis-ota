<?php
if(!isset($_SESSION)){
    session_start();
}
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file login_check.inc.php
 * @brief generic login session checking for OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
include_once(dirname(__FILE__)."/functions.php");

//error_reporting(E_ALL & ~E_NOTICE);

$lang = get_language();
load_language($lang);


//if user clicked on logout
if (isset($_POST['login']) && $_POST['login']==$_L['PR_logout']){
	LogoutHotel();
	header("Location: index.php");
}

// if the cookie doesn't exsist means the user hasn't been verified by the login page so send them
// back to the login page.
if(!isset($_COOKIE['data_login'])){
	LogoutHotel();
	header("Location: index.php");
}


//function to check if user has access to the page

?>
