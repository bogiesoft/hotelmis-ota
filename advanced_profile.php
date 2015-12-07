<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file advanced_profile.php
 * @brief advanced profile webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @note The advanced profile is only available for 
 * 		OTA Hotel Management users registered with e-Bridge
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup GUEST_MANAGEMENT Guest setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("guest");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link href="css/new.css" rel="stylesheet" type="text/css" />
  <link href="css/styles.css" rel="stylesheet" type="text/css" />
  <link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen" />
  <script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
  <title><?php echo $_L['MAIN_Title']." ".$_L['MNU_guest'];?></title>
</head>
<body>
    <table height="500" class="listing-table" width="100%" border="0" cellpadding="1" align="center">
     
	 <tr valign="top" style="padding:5;">
	   <?php 
		if ($_GET['menu'] == "editprofile") {
			print_rightMenu_home();
		}?>
	 <td  style="padding:10;" height="430">
		<table width="100%"  border="0" cellpadding="1">
		<tr><td width="13%" align="center"></td></tr>
		<tr><td colspan="2"><h2><a href="https://www.youtube.com/watch?v=0QCETX2ggds" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['ADP_title']; ?></a></h2></td></tr>	
			
		<tr><td colspan="2">&nbsp;</td></tr>	
		<tr>
		  <td colspan="2">
			<div id="Requests">
			  <?php 
			  	if(file_exists(dirname(__FILE__)."/OTA/advancedFeatures/adv_Profile.php")){
			  		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_Profile.php");
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
				   
		  	</div>
		  </td>
		</tr>
		
				
		</table>
	 
	  </td>
	 </tr>
  </table>
</body>
</html>
<?php
/**
 * @}
 * @}
 */
?>