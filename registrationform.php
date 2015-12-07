<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file guests.php
 * @brief guests webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup GUEST_MANAGEMENT Guest setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("admin"); //check if user is allowed to access this page

if(isset($_GET['bid'])) $bid=$_GET['bid'];
if(isset($_GET['in'])) $checkin=urldecode($_GET['in']);
if(isset($_GET['out'])) $checkout=urldecode($_GET['out']);
if(isset($_GET['room'])) $roomid=$_GET['room'];
if(isset($_GET['roomtype'])) $roomtypeid=$_GET['roomtype'];
if(isset($_GET['rate'])) $ratesid=$_GET['rate'];
if(isset($_GET['guest'])) $guestid=$_GET['guest'];


$reguri = "registrationform.php?";
if($bid) { $reguri .= "bid=".$bid; $amp="&"; }
if($checkin) { $reguri .= $amp."in=".urlencode($checkin); $amp="&"; }
if($checkout) { $reguri .= $amp."out=".urlencode($checkout); $amp="&"; }
if($guestid) { $reguri .= $amp."guest=".$guestid; $amp="&"; }
if($roomid) { $reguri .= $amp."room=".$roomid; $amp="&"; }
if($roomtypeid) { $reguri .= $amp."roomtype=".$roomtypeid; $amp="&"; }
if($ratesid) { $reguri .= $amp."rate=".$ratesid; $amp="&"; }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link href="css/new.css" rel="stylesheet" type="text/css"></link>
  <title><?php echo $_L['MAIN_Title'];?></title>
</head>
<body>
  <form action="<?php echo $reguri;?>" method="post" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="1" align="center">
      <tr valign="top">
      
        <td class="c4" width="85%">
		<div class='PageNext'>
		  <table style="width: 7.5in;"  border="0" cellpadding="1">
			<tr><td width="13%" align="center"></td></tr>
			<tr><td colspan="2"><h2><?php echo $_L['MNU_Regform']; ?></h2></td></tr>
			<?php 
			//display the advanced user profile page with indispensable features 
			//Only availble for ebridge customers
			if(is_ebridgeCustomer()){
				include_once(dirname(__FILE__)."/OTA/advancedFeatures/regform.php");
				
				//header("emailconfig.php");
				//return 1;

			}else{
				?>
				<tr>
				
			  		<td><h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3></td>
					
				
			  	</tr>
			  	<tr>
					<td>
					<a href="https://www.e-bridgedirect.com" target="e-Bridge"> <img src="images/Splash_HotelGolf.jpg" width="50%" height="50%" /></a>
					</td>
			  	</tr>		  		
				<?php 
			}
			?>
		</table>
		</div>
		<div class='showprint'>
	<p><?php
	$terms = "";
	$terms = file_get_contents('./custom/terms.txt');
	print $terms;
	
	?></p>
	</div>
		</td>
       </tr> 
        	  <tr>
		<td colspan=3>
		<div class="noprint">
		  <table>
			<tr></tr>
		  </table>
		 </div>
		</td>
	  </tr>
	</table>
  </form>
  <style rel="stylesheet" type="text/css" media="print">
	@media print
	{
		.noprint {display:none;}
		.PageNext{page-break-after: always;}
		.showprint {display:block;}
	}
	
	
	</style>
	 
</body>
</html>
<?php
/**
 * @}
 * @}
 */
 ?>        