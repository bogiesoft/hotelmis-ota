<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file thankyou.php
 * @brief Hotel Management System Initial setup page called by OTA Hotel Management Installer
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Hotel setup and management page
 * @{
 */
error_reporting(0);
include_once(dirname(__FILE__).'/../functions.php');
include_once(dirname(__FILE__)."/../lang/lang_en.php");

$logofile = Get_LogoFile();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="REFRESH" content="3;url=https://www.e-bridgedirect.com" />
<link href="../css/new.css" rel="stylesheet" type="text/css" />
<title><?php echo $_L['MAIN_Title']; ?></title>
</head>
<body>
<table width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">           
    <td class="c4" align="center">
	<table width="80%"  border="0" cellpadding="1">
      <tr>
        <td align="center"></td>
      </tr>
      <tr>
        <td valign="middle" align="center">
        <h4><br/>
		<?php echo $_L['MAIN_Title']; ?><br/>Setup Wizard</h4> </td>
      </tr>
	<tr>
        <td  align="left"><div id="Requests">
        
        <p>Thank you for Installing OTA Hotel Management Software by e-Novate.</p>

		<p>Register with the e-Bridge and get your Hotel online now.<br/>
		Check our website for promotions, you may even be eligible for a free connection and usage period.</p>
		
		<p><a href="https://www.e-bridgedirect.com"> Register @ https://www.e-bridgedirect.com</a></p>
		
		<p>e-Novate has developed this free open source reservation management software to help SME travel and tourism operators get their processes automated 
		at low cost and get their content online with the e-Bridge.</p>
		
		<p>Thank you for installing the free version of OTA Hotel Management.</p>

		<p>If you would like a real time online booking engine and Facebook booking engine for your website, then you can join the e-Bridge and connect your website directly to OTA Hotel Management, so you can manage your inventory online and with your channels without copying data to multiple websites.<br/> 
		Just a fixed low transaction fee from USD 50c per room night - no expensive commissions.  Connected directly to the payment gateway of your choice, so you get paid immediately by the guest at time of booking. </p>

		<p><a href="https://www.e-bridgedirect.com/demos/hotel"><b>Demo white label website </b></a></p>
		<p><a href="https://www.facebook.com/eNovate"><b>Facebook online booking engine(Book now)</b></a></p>

		<p>If online is not for you, but you would like to enhance the version you have with reporting, advanced features, OTA channel management and support please register at <a href="https://www.e-bridgedirect.com"> Register @ https://www.e-bridgedirect.com</a> use the promo code - <b>STANDALONE</b></p>.

		<p>Try our fully functional online demo, account and password are "admin"/"password"<br/>
		<a href="http://www.e-novate.asia/hotelmis"> OTA Hotel Management full features </a></p>
		
		<p>Join today.</p>
        
        </div>
		</td>
		<td class="c4" width="5%"> </td>
		</tr>
		  <tr>
		  <td colspan=3>
		  &nbsp;
		  </td>
		  </tr>
		  <tr>
				  <td colspan=3>
				  &nbsp;
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