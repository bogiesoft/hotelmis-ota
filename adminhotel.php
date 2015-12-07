<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file admin.php
 * @brief admin web page called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Hotel setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */

session_start();
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
access("admin"); //check if user is allowed to access this page

$logofile = Get_LogoFile();

// If the POST forms are set, override the retrieved settings
$ebridgeid = "";
$hotel = "";
$altname = "";
$company = "";
$register = "";
$tax1 = "";
$tax2 = "";
$phone = "";
$fax = "";
$IM = "";
$street = "";
$city = "";
$citycode = "";
$state = "";
$postcode = "";
$countrycode = "";
$country = "";
$logo = "";
$latitude = "";
$longitude = "";
$language = "";
$email = "";
$web = "";
$ota = "";
$chaincode = "";
if($_POST['ebridgeid']) {
	$ebridgeid = $_POST['ebridgeid'];
}
if($_POST['hotel']) {
	$hotel = $_POST['hotel'];
}
if($_POST['altname']) {
	$altname = $_POST['altname'];
}
if($_POST['company']) {
	$company = $_POST['company'];
}
if($_POST['register']) {
	$register = $_POST['register'];
}
if($_POST['tax1']) {
	$tax1 = $_POST['tax1'];
}
if($_POST['tax2']) {
	$tax2 = $_POST['tax2'];
}
if($_POST['phone']) {
	$phone = $_POST['phone'];
}
if($_POST['IM']) {
	$IM = $_POST['IM'];
}
if($_POST['fax']) {
	$fax = $_POST['fax'];
}
if($_POST['street']) {
	$street = $_POST['street'];
}
if($_POST['countrycode']) {
	$countrycode = $_POST['countrycode'];
	$newcountrycode = $_POST['countrycode'];
}
if($_POST['city']) {
	$city = $_POST['city'];
}
if($_POST['citycode']) {
	$citycode = $_POST['citycode'];
}
if($_POST['state']) {
	$state = $_POST['state'];
}
if($_POST['postcode']) {
	$postcode = $_POST['postcode'];
}
if($_POST['logo']) {
	$logo = $_POST['logo'];
}
if($_POST['londeg']) {
	$londeg = $_POST['londeg'];
}
if($_POST['lonmin']) {
	$lonmin = $_POST['lonmin'];
}
if($_POST['lonsec']) {
	$lonsec = $_POST['lonsec'];
}
if($_POST['londir']) {
	$londir = $_POST['londir'];
}
if($_POST['latdeg']) {
	$latdeg = $_POST['latdeg'];
}
if($_POST['latmin']) {
	$latmin = $_POST['latmin'];
}
if($_POST['latsec']) {
	$latsec = $_POST['latsec'];
}
if($_POST['latdir']) {
	$latdir = $_POST['latdir'];
}
if($_POST['language']) {
	$language = $_POST['language'];
}
if($_POST['email']) {
	$email = $_POST['email'];
}
if($_POST['ota']) {
	$ota = $_POST['ota'];
}
if($_POST['web']) {
	$web = $_POST['web'];
}
if($_POST['chaincode']) {
	$chaincode = $_POST['chaincode'];
}


if($_POST['Save'] && $_POST['tab']=='adminhotel') {
	$longitude = $londeg.".".$lonmin.".".$lonsec.$londir;
	$latitude = $latdeg.".".$latmin.".".$latsec.$latdir;

	Save_HotelSettings($hotel, $altname, $company, $register,
			$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
			$city, $citycode, $state, $postcode, $countrycode, $country,
			$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
}
// Get the current settings
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);

if($latitude) {
	list($latdeg,$latmin,$latsec,$latdir) = sscanf($latitude, "%d.%d.%d%s");
}
if($longitude) {
	list($londeg,$lonmin,$lonsec,$londir) = sscanf($longitude, "%d.%d.%d%s");
}
/** If not saved and the country code changed, then the change back to the new countrycode */
if(!$_POST['Save'] && $countrycode <> $newcountrycode && $newcountrycode) {
	$countrycode = $newcountrycode;
}
// Retrieve the country name for the country code currently set.
$country = Get_Country($countrycode);

?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<!--<html xmlns="http://www.w3.org/1999/xhtml">-->
<!--<head>-->
<!--<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />-->
<!--<link href="css/new.css" rel="stylesheet" type="text/css" />-->
<!--<link href="css/styles2.css" rel="stylesheet" type="text/css">-->
<!--</head>-->
<!--<body class="tdbgcl">-->
					<form action="<?php $_SERVER['REQUEST_URI']?>" name=adminhotel id=adminhotel method="post" enctype="multipart/form-data">
					      <table class="tdbgcl" width="100%" border="0" cellpadding="1" align="center">
					        <tr valign="top">
					        <td class="c4" width="100%">
								<table class="tdbgcl" width="100%"  border="0" cellpadding="1">
								      <tr>
								        &nbsp;<input type="hidden" name="tab" id="tab" value="<?php echo "adminhotel"; ?>"/>
								        <input type="hidden" name="activeTab" id="activeTab" value="<?php $tabvar;?>"/></td>
								      </tr>
									  <tr><!--
									  <td> <a href='items.php'> <?php echo $_L['ADM_items']; ?> </a> &nbsp; <a href='documents.php' > <?php echo $_L['ADM_documentnos']; ?> </a> &nbsp; <?php if(is_ebridgeCustomer()) echo "<a href='customfields.php'>". $_L['CST_fields']."</a>&nbsp;<a href='otasync.php'>". $_L['OTA_title']."</a>"; ?> &nbsp; <a href='uploadExcel.php' > <?php echo "Upload Spreadsheet"; ?> </a></td>
									  --></tr>
									<tr>
								        <td><div id="Requests">
								<table width="90%"  border="0" cellpadding="1">
								  <tr>
								    <td ><?php echo $_L['HTL_name']; ?></td>
								    <td ><input type="text" name="hotel" id="hotel" value="<?php echo $hotel; ?>" maxlength=250 /></td>
								    <td ><?php echo $_L['HTL_ebridge']; ?> <?php if(is_ebridgeCustomer()) echo "<font color='#FF0000'>*</font>";?></td>
								    <td ><input type="text" name="ebridgeid" id="ebridgeid" value="<?php echo $ebridgeid; ?>" maxlength=250 /></td>
								  </tr>								 
								  <tr>
								    <td><?php echo $_L['HTL_altname']; ?></td>
								    <td><input type="text" name="altname" id="altname" value="<?php echo $altname; ?>" maxlength=250 /></td>
								    <td><?php echo $_L['HTL_ebridgeURL']; ?></td>
								    <td><input type="text" name="ota" id="ota" value="<?php echo $ota; ?>" maxlength=200/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_company']; ?></td>
								    <td><input type="text" name="company" id="company" maxlength=250 value="<?php echo $company; ?>"/></td>
								    <td><?php echo $_L['HTL_register']; ?></td>
								    <td><input type="text" name="register" id="register" maxlength=100 value="<?php echo $register; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_tax1']; ?></td>
								    <td><input type="text" name="tax1" id="tax1" maxlength=100 value="<?php echo $tax1; ?>"/></td>
								    <td><?php echo $_L['HTL_tax2']; ?></td>
								    <td><input type="text" name="tax2" id="tax2" maxlength=100 value="<?php echo $tax2; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_phone']; ?></td>
								    <td><input type="text" name="phone" maxlength=100 value="<?php echo $phone; ?>"/></td>
								    <td><?php echo $_L['HTL_im']; ?></td>
								    <td><input type="text" name="IM" maxlength=250 value="<?php echo $IM; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_fax']; ?></td>
								    <td><input type="text" name="fax" id="fax" maxlength=100 value="<?php echo $fax; ?>"/></td>
								    <td><?php echo $_L['HTL_email']; ?></td>
								    <td><input type="text" name="email" id="email" maxlength=200 value="<?php echo $email; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_web']; ?></td>
								    <td><input type="text" name="web" id="web" maxlength=250 value="<?php echo $web; ?>"/></td>
								    <td><?php echo $_L['HTL_chaincode']; ?></td>
								    <td><input type="text" name="chaincode" id="chaincode" maxlength=200 value="<?php echo $chaincode; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_street']; ?></td>
								    <td><input type="text" name="street" maxlength=250 value="<?php echo $street; ?>"/></td>
								    <td><?php echo $_L['HTL_countrycode']; ?><select name="countrycode" id="countrycode"  onchange="document.adminhotel.submit()">
										<?php populate_select("countries", "countrycode", "countrycode", $countrycode, ""); ?>
										</select>
									</td>
								    <td><?php echo $_L['HTL_country']; ?><input type="text" name="country" id="country" readonly="readonly" value="<?php echo $country; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_city']; ?></td>
								    <td><input type="text" name="city" id="city" maxlength=250 value="<?php echo $city; ?>"/></td>
								    <td><?php echo $_L['HTL_citycode']; ?></td>
								    <td><input type="text" name="citycode"  id="citycode" size=4 maxlength=4 value="<?php echo $citycode; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_state']; ?></td>
								    <td><input type="text" name="state" id="state" value="<?php echo $state; ?>" maxlength=250 /></td>
								    <td><?php echo $_L['HTL_postcode']; ?></td>
								    <td><input type="text" name="postcode" id="postcode" size=10 maxlength=50 value="<?php echo $postcode; ?>"/></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_lang']; ?></td>
								    <td><select name="language" id="language">
										<?php populate_select("languages", "lang", "LocalDescription", $language, "active=1"); ?>
										</select>
									</td>
								    <td><?php echo $_L['HTL_logo']; ?></td>
								    <td><input type="text" name="logo" id="logo" value="<?php echo $logo; ?>" maxlength=500 /></td>
								  </tr>
								  <tr>
								    <td><?php echo $_L['HTL_longitude']; ?></td>
								    <td><small><input type="text" name="londeg" id="londeg" size=3 maxlength=3 style='width: 30px;' value="<?php echo $londeg; ?>"/>&deg;
									<input type="text" name="lonmin" id="lonmin" size=2 maxlength=3 style='width: 30px;' value="<?php echo $lonmin; ?>"/> &#39;
									<input type="text" name="lonsec" id="lonsec" size=5 maxlength=6 style='width: 40px;' value="<?php echo $lonsec; ?>"/> &quot;
									<select name=londir id=londir>
										<option value="N" <?php if($londir == 'N') echo "selected" ?> > N </option>
										<option value="S" <?php if($londir == 'S') echo "selected" ?> > S </option>
									</select>
									</small>
									</td>
								    <td><?php echo $_L['HTL_latitude']; ?></td>
								    <td><small><input type="text" name="latdeg" id="latdeg" size=3 maxlength=3 style='width: 30px;' value="<?php echo $latdeg; ?>"/>&deg;
									<input type="text" name="latmin" id="latmin" size=2 maxlength=3 style='width: 30px;' value="<?php echo $latmin; ?>"/> &#39;
									<input type="text" name="latsec" id="latsec" size=5 maxlength=6 style='width: 40px;' value="<?php echo $latsec; ?>"/> &quot;
									<select name=latdir id=latdir>
										<option value="E" <?php if($latdir == 'E') echo "selected" ?> > E </option>
										<option value="W" <?php if($latdir == 'W') echo "selected" ?> > W </option>
									</select>
									</small>
									</td>
								  </tr>
							</table>
							</div>
							<div>
								<table align=right >
									<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
									<tr>
									<td colspan="4" align=right ><input class="button" type="submit" name="Save" id="Save" value="<?php echo $_L['BTN_save']; ?>"/></td>
									</tr>								
								</table>							
							</div>   
								    
								    </td>
								  </tr>
								</table>
							</td>
						</tr>
					   </table>
					</form>  
<!--</body>-->
<!--</html>-->
<?php 
/**
 * @}
 * @}
 */?>

