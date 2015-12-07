<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file otasync.php
 * @brief configuration of agent for OTA Sync
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

	<table width="100%"  border="0" cellpadding="0">
      <tr>
        <td align="center"></td>
      </tr>
      <tr>
        <td><div id="Requests">

				  <?php 
				  	if(file_exists(dirname(__FILE__)."/OTA/advancedFeatures/otasync.php")){
				  		include_once(dirname(__FILE__)."/OTA/advancedFeatures/otasync.php");
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

    </table>
<?php
/**
 * @}
 * @} 
 */
?>