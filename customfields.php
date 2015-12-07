<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file customfields.php
 * @brief document setup webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup ADMIN_MANAGEMENT 
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();




?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!--<html xmlns="http://www.w3.org/1999/xhtml">-->
<!--<head>-->
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
<!--<link href="css/new.css" rel="stylesheet" type="text/css">-->
<!--<link href="css/styles2.css" rel="stylesheet" type="text/css">-->
<!--</head>-->
<!--<body class="tdbgcl">-->
      <table class="tdbgcl" width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">

    
    <td width="85%" class="c4">
	<table class="tdbgcl" width="100%"  border="0" cellpadding="1">
      <tr>
        <td align="center"></td>
      </tr>
      <tr>
        <td><div id="Requests">

				  <?php 
				  	if(file_exists(dirname(__FILE__)."/OTA/advancedFeatures/customfields.php")){
				  		include_once(dirname(__FILE__)."/OTA/advancedFeatures/customfields.php");
				  	} else { ?>
				  		<table>
					  		<tr>
					  			<td><h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3></td>
					  		</tr>
					  		<tr>
						  		<td>
						  			<a href="https://www.e-bridgedirect.com" target="e-Bridge"> <img src="images/Splash_HotelGolf.jpg" width="50%" height="50%" /></a>
						  		</td>
					  		</tr>
				  		</table>				  		
				  <?php 
				  	}
				  ?>


        </div></td>
		
      </tr>
	  <tr bgcolor="#66CCCC" >
        <td align="left"><div id="RequestDetails"></div>
		</td>
      </tr>
    </table></td>
  </tr>
</table>
<!--</body>-->
<!--</html>-->
<?php
/**
 * @}
 * @} 
 */
?>