<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file configsetup.php
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
include_once(dirname(__FILE__)."/../validate_email.php");

/**< logo file $logofile */
$logofile = Get_LogoFile();
/**< reset data $resetDefaultData */
$resetDefaultData=0; 
/**< valid email flag $validEmailDB */
$validEmailDB=1; 

// If the POST forms are set, override the retrieved settings
/**< validation message $validationMsgs */
$validationMsgs = '';
/**< e-Bridge ID $ebridgeid */
$ebridgeid = "";
/**< Hotel name $hotel */
$hotel = "";
/**< alternate name $altname */
$altname = "";
/**< company name $company */
$company = "";
/**< Business registration number $register */
$register = "";
/**< tax ID 1 $tax1 */
$tax1 = "";
/**< tax ID 2 $tax2 */
$tax2 = "";
/**< phone number $phone */
$phone = "";
/**< fax number $fax */
$fax = "";
/**< Intant Messeger $IM */
$IM = "";
/**< street address $street */
$street = "";
/**< city $city */
$city = "";
/**< city code $citycode */
$citycode = "";
/**< state $state */
$state = "";
/**< post code $postcode */
$postcode = "";
/**< country code $countrycode */
$countrycode = "";
/**< country $country */
$country = "";
/**< logo $logo */
$logo = "";
/**< latitude $latitude */
$latitude = "";
/**< longitude $longitude */
$longitude = "";
/**< language $language */
$language = "";
/**< email $email */
$email = "";
/**< web $web */
$web = "";
/**< ota $ota */
$ota = "";
/**< chain code $chaincode */
$chaincode = "";
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);

if(!empty($email)){
	if (!validateEmail($email,true,true,"",$email)){
		$validEmailDB=0;	
		$errmsg = "Please enter a valid email address.<br/> If valid address is keyed and problem still persists, kindly contact us at<br/>support@e-novate.asia
					<br/>Problem may be caused by your mail server as it does not accept validation requests.";
		$validationMsgs = $errmsg;	
	}
}
if ((strcasecmp($company, "E-Novate Pte Ltd")==0 || strcasecmp($street, "163 Geylang Road #03-01 The Grandplus")==0 
		|| stripos($email, "@e-novate.com") !== false || strcasecmp($register, "201010533E")==0 || strcasecmp($phone, "6567470497")==0)){
			
	$resetDefaultData=1;
		
	if (strcasecmp($company, "E-Novate Pte Ltd")==0 )
		$company="";
	if (strcasecmp($street, "163 Geylang Road #03-01 The Grandplus")==0 )
		$street="";
	if(stripos($email, "@e-novate.com") !== false)
		$email="";
	if (strcasecmp($register, "201010533E")==0 )
		$register="";
	if (strcasecmp($phone, "6567470497")==0)
		$phone="";
			
	//reset default data
	Save_HotelSettings($hotel, $altname, $company, $register,$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,$city, $citycode,
						 $state, $postcode, $countrycode, $country,	$logo, $latitude, $longitude, $language, $email, $web, $ota,$chaincode);
}	
		
/*Data for the table `hotelsetup` */	

if (!empty($hotel) && !empty($register) && !empty($email) && $resetDefaultData==0 && $validEmailDB==1){
	//For registration email
	$datatopost = 'PRODUCTNAME=Hotel&ACT=Update&OPERATOR_NAME='.$hotel.'&COMPANY_NAME='.$company.'&BUSINESS_NUMBER='.$register.'&TAXID1='.$tax1.'&TAXID2='.$tax2;
	$datatopost.= '&EMAIL='.$email.'&TELEPHONE='.$phone.'&STREET='.$street.'&CITY='.$city.'&CITY_CODE='.$citycode.'&COUNTRY_CODE='.$countrycode.'&POST_CODE='.$postcode;
	$url = "http://www.e-bridgedirect.com/Installer/register_email.php";
	post_Data($datatopost, $url);
	
	//For thankyou email
	$datatopost = 'PRODUCTNAME=Hotel&&EMAIL='.$email;
	$url = "http://www.e-bridgedirect.com/Installer/complete_setup.php";
	post_Data($datatopost, $url);
	
	header("Location:index.php?action=thankyou");
}

if ($_POST['submit']){
	$prod = '';
	$hotel = '';
	$company = '';
	$register = '';
	$taxid1 = '';
	$taxid2 = '';
	$email = '';
	$phone = '';
	$street = '';
	$city = '';
	$citycode = '';
	$countrycode = '';
	$postcode = '';
	$taxpct = ''; 
	$svcpct = '';
	$ebridgeid="";
	
	if($_POST['hotel']) {
		$hotel = $_POST['hotel'];
	}
	if($_POST['ebridgeid']) {
		$ebridgeid = $_POST['ebridgeid'];
	}
	if($_POST['company']) {
		$company = $_POST['company'];
	}
	if($_POST['register']) {
		$register = $_POST['register'];
	}
	if($_POST['tax1']) {
		$taxid1 = $_POST['tax1'];
	}
	if($_POST['tax2']) {
		$taxid2 = $_POST['tax2'];
	}
	if($_POST['phone']) {
		$phone = $_POST['phone'];
	}
	if($_POST['email']) {
		$email = $_POST['email'];
	}
	if($_POST['street']) {
		$street = $_POST['street'];
	}
	if($_POST['city']) {
		$city = $_POST['city'];
	}
	if($_POST['citycode']) {
		$citycode = $_POST['citycode'];
	}
	if($_POST['countrycode']) {
		$countrycode = $_POST['countrycode'];
		$newcountrycode = $_POST['countrycode'];
	}
	if($_POST['postcode']) {
		$postcode = $_POST['postcode'];
	}
	if($_POST['taxpct']) {
		$taxpct = $_POST['taxpct'];
	}
	if (empty($taxpct)){
		$taxpct = 7;
	}
	if($_POST['svcpct']) {
		$svcpct = $_POST['svcpct'];
	}
	if (empty($svcpct)){
		$svcpct = 10;
	}
	
	if(empty($company)){
		$company = $hotel;
	}
	$country = Get_Country($countrycode);
	$timezone = "";
	
	if (validateEmail($email,true,true,"",$email)){
		//get the timezone from the country code
		if(empty($countrycode)){
			$countrycode = "SG";	
		}
		$timezones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countrycode);
		
		foreach( $timezones as $key => $zone )
		{
			 $cityArr = explode("/",$zone);
			 $tempCity = $cityArr[1];
			 if(!empty($city)&& strcasecmp($tempCity,$city)==0){
				  $timezone = $zone;
				  break;
			 }
		}
		
		if(empty($timezone)){
			$timezone = $timezones[0];	
		}
		
		//Operator Setup
		Save_HotelSettings($hotel, $hotel, $company, $register,$ebridgeid, $taxid1, $taxid2, $phone, $fax, $IM, $street,	$city, $citycode, $city, $postcode, $countrycode, $country,
		'images/enovate_symbol_sm.JPG', '103.52.28E', '1.18.42N', 'en', $email, 'www.e-novate.asia', 'http://localhost', 'Chain Code');
		//End of Operator Setup
		
		//Write the confguration.inc file
		$data= "<?php\n";
		$data.= "/**\n";
		$data.= " * @package OTA Hotel Management\n";
		$data.= " * @file admin.php\n";
		$data.= " * @brief admin web page called by OTA Hotel Management\n";
		$data.= " * see readme.txt for credits and references\n";
		$data.= " *\n";
		$data.= " * @addtogroup CODE_MANAGEMENT\n";
		$data.= " * @{\n";
		$data.= " * @defgroup DATABASE_MANAGEMENT Database setup and management page\n";
		$data.= " * @{\n";
		$data.= " * This documentation is for code maintenance, not a user guide.\n";
		$data.= " */\n";
		$data.= "/** MySQL hostname or IP address */\n";
		$data.= "define(\"HOST\", \"127.0.0.1\");\n";
		$data.= "/** MySQL Database port number */\n";
		$data.= "define(\"PORT\", 3306);\n";
		$data.= "/** MySQL database user name for database - default hotelmis */\n";
		$data.= "define(\"USER\", \"hotelmis\");\n";
		$data.= "/** MySQL database password for user name - default hotelmis */\n";
		$data.= "define(\"PASS\", \"hotelmis\");\n";
		$data.= "/** MySQL database name - default hotelmis */\n";
		$data.= "define(\"DB\", \"hotelmis\");\n";
		$data.= "/** Print out debug information */\n";
		$data.= "define(\"DEBUG\", 0);\n";
		$data.= "/** Default Tax percentage */\n";
		$data.= "define(\"TAXPCT\", ".$taxpct.");\n";
		$data.= "/** Default service charge percentage */\n";
		$data.= "define(\"SVCPCT\", ".$svcpct.");\n";
		$data.= "/** TimeZone Information for local hotel\n";
		$data.= " * @see http://www.php.net/manual/en/timezones.php\n";
		$data.= " */\n";
		$data.= "define (\"TIMEZONE\", \"".$timezone."\");\n";
		$data.= "/** Validate IATA against the ebridge id. 1-Validate 0-No Validation */\n";
		$data.= "define(\"IATAEBRIDGE\", 0);\n";
		$data.= "/**\n";
 		$data.= " * Auto processing cuttoff time for next day charges \n";
		$data.= " */\n";
		$data.= "define(\"NEXTDAY_CUTOFF\", \"20:00:00\");\n";
		$data.= "/** \n";
		$data.= " * Group the same rooms in the voice as a single line by room type and rate code\n";
		$data.= " * Individual room charges 0 or group charge 1 \n";
		$data.= " */\n";
		$data.= "define(\"GROUP_BY_ROOMTYPERATE\", 1);\n";
		$data.= "/** Default room item id for auto pricing */\n";
		$data.= "define('DEFAULT_ROOMCODE', 1);\n";
		$data.= "/** Default Check In Time */\n";
		$data.= "define(\"CHECKIN\", \"14:00:00\");\n";
		$data.= "/** Default Check In Time */\n";
		$data.= "define(\"CHECKOUT\", \"12:00:00\");\n";
		$data.= "/**\n";
		$data.= " * @}\n";
		$data.= " * @}\n";
		$data.= " */\n";
		$data.= " ?>";
		
		$fh = fopen('../configuration.inc.php', 'w');
		fwrite($fh, $data);
		fclose($fh);
		
		//For registration email
		$datatopost = 'PRODUCTNAME=Hotel&ACT=Install&OPERATOR_NAME='.$hotel.'&COMPANY_NAME='.$company.'&BUSINESS_NUMBER='.$register.'&TAXID1='.$tax1.'&TAXID2='.$tax2;
		$datatopost.= '&EMAIL='.$email.'&TELEPHONE='.$phone.'&STREET='.$street.'&CITY='.$city.'&CITY_CODE='.$citycode.'&COUNTRY_CODE='.$countrycode.'&POST_CODE='.$postcode;
		$url = "http://www.e-bridgedirect.com/Installer/register_email.php";
		post_Data($datatopost, $url);
		
		//For thankyou email
		$datatopost = 'PRODUCTNAME=Hotel&&EMAIL='.$email;
		$url = "http://www.e-bridgedirect.com/Installer/complete_setup.php";
		post_Data($datatopost, $url);
		
		header("Location:index.php?action=initialsetup");
	} else {
		$errmsg = "Please enter a valid email address.<br/> If valid address is keyed and problem still persists, kindly contact us at <br/>support@e-novate.asia
					<br/>Problem may be caused by your mail server as it does not accept validation requests.";
		$validationMsgs = $errmsg;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../css/new.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/gen_validatorv4.js"></script>
<title><?php echo $_L['MAIN_Title']; ?></title>
</head>
<body>


<form action="index.php?action=configsetup" name="configsetup" id="configsetup" method="post" enctype="multipart/form-data">
      <table width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">           
    <td class="c4" width="80%"  align="center">
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
  		<td></td>
  	  </tr>
	<tr>
        <td align="center"><div id="Requests">
		<table width="80%"  border="0" cellpadding="1">		
	      <tr>
	        <td align="left" colspan="4"><h2><?php echo $_L['MNU_hotel']; ?></h2></td>
	      </tr>
	      <tr>
		    <td align="left" colspan="4" ><font color="#FF0000">*</font><?php echo $_L['PR_AllMandatory']; ?></td>
		  </tr>
	  	  <tr>
          	<td align="left" colspan="4"><span style="color:red"><div id="configsetup_errorloc" class="error_strings"></div>
          	<?php if(!empty($validationMsgs)){?>
          		<ul><li><?php echo $validationMsgs; ?></li></ul>
          	<?php }?>
          	</span></td>
          </tr> 
	      <tr>
		    <td align="left" colspan="4" >&nbsp;</td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left" ><?php echo $_L['HTL_name']; ?><font color="#FF0000">*</font></td>
		    <td align="left" ><input type="text" name="hotel" id="hotel" value="<?php echo $hotel; ?>" maxlength=250 /></td>
		    <td align="left" ><?php echo $_L['HTL_ebridge']; ?></td>
		    <td align="left" ><input type="text" name="ebridgeid" id="ebridgeid" value="<?php echo $ebridgeid; ?>" maxlength=250 /></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_company']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="company" id="company" maxlength=250 value="<?php echo $company; ?>"/></td>
		    <td align="left"><?php echo $_L['HTL_register']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="register" id="register" maxlength=100 value="<?php echo $register; ?>"/></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_tax1']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="tax1" id="tax1" maxlength=100 value="<?php echo $tax1; ?>"/></td>
		    <td align="left"><?php echo $_L['HTL_tax2']; ?></td>
		    <td align="left"><input type="text" name="tax2" id="tax2" maxlength=100 value="<?php echo $tax2; ?>"/></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_phone']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="phone" id="phone" maxlength=100 value="<?php echo $phone; ?>"/></td>
		    <td align="left"><?php echo $_L['HTL_email']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="email" id="email" maxlength=200 value="<?php echo $email; ?>"/></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_street']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="street" maxlength=250 value="<?php echo $street; ?>"/></td>
		    <td align="left"><?php echo $_L['HTL_countrycode']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><select name="countrycode" id="countrycode" >
				<?php populate_select("countries", "countrycode", "countrycode", $countrycode, ""); ?>
				</select>
			</td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_city']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="city" id="city" maxlength=250 value="<?php echo $city; ?>"/></td>
		    <td align="left"><?php echo $_L['HTL_citycode']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="citycode"  id="citycode" size=4 maxlength=3 value="<?php echo $citycode; ?>"/></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['HTL_state']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="state" id="state" value="<?php echo $state; ?>" maxlength=250 /></td>
		    <td align="left"><?php echo $_L['HTL_postcode']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="postcode" id="postcode" size=10 maxlength=50 value="<?php echo $postcode; ?>"/></td>
		  </tr>
		  <tr>
		    <td style="padding:5px;" align="left"><?php echo $_L['ITM_service']." ".$_L['RTS_percent']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="taxpct" id="taxpct" value="<?php echo $taxpct; ?>" maxlength=2 /></td>
		    <td align="left"><?php echo $_L['ITM_tax']." ".$_L['RTS_percent']; ?><font color="#FF0000">*</font></td>
		    <td align="left"><input type="text" name="svcpct" id="svcpct" size=10 maxlength=2 value="<?php echo $svcpct; ?>"/></td>
		  </tr>
		  <tr>
			<td></td>
			<td></td>
			<td></td>
			<td style="padding:5px;" align=right ><input class="button" type="submit" name="submit" id="submit" value="<?php echo $_L['BTN_save']; ?>"/></td>
		  </tr>
		</table>
		</div>
		</td>
		<td class="c4" width="5%"> </td>
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
</form>



<script language="JavaScript" type="text/javascript" xml:space="preserve">//<![CDATA[
 //Form validations
 var frmvalidator  = new Validator("configsetup");
 frmvalidator.EnableOnPageErrorDisplaySingleBox();
 frmvalidator.EnableMsgsTogether();

 frmvalidator.addValidation("hotel","req","Please enter the Hotel Name");

 frmvalidator.addValidation("company","req","Please enter the Company Name");

 frmvalidator.addValidation("register","req","Please enter the Registration Number");

 frmvalidator.addValidation("tax1","req","Please enter Tax ID 1");

 frmvalidator.addValidation("phone","req","Please enter the Phone Number");

 frmvalidator.addValidation("street","req","Please enter the Street");

 frmvalidator.addValidation("city","req","Please enter the City");

 frmvalidator.addValidation("citycode","req","Please enter the 3 letter city code of the nearest airport");
 frmvalidator.addValidation("citycode","alphabetic","Please enter a valid 3 letter city code of the nearest airport");
 frmvalidator.addValidation("citycode","minlength=3","Please enter a valid 3 letter city code of the nearest airport");

 frmvalidator.addValidation("email","req","Please enter the email");
 frmvalidator.addValidation("email","email","Please enter a valid email");

 frmvalidator.addValidation("phone","req","Please enter the phone number");
 frmvalidator.addValidation("phone","numeric","Please enter a valid phone number");

 frmvalidator.addValidation("state","req","Please enter the State");

 frmvalidator.addValidation("postcode","req","Please enter the Postal Code");

 frmvalidator.addValidation("taxpct","req","Please enter the tax percentage");
 frmvalidator.addValidation("taxpct","numeric","Please enter a valid tax percentage");

 frmvalidator.addValidation("svcpct","req","Please enter the service percentage");
 frmvalidator.addValidation("svcpct","numeric","Please enter a valid service percentage");
  
  
//]]>
</script>
</body>
</html>
<?php
/**
 * Function to post the data to e-bridge
 * @param $datatopost [in] The data to be posted
 * @param $urltopost [in] The URL to which the data is to be posted
 */
function post_Data($datatopost,$urltopost){	
	//initialize the connection
	$ch = curl_init ();
	//Set the URL and post variables
	curl_setopt($ch,CURLOPT_URL, $urltopost);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $datatopost);
	curl_setopt($ch, CURLOPT_HEADER, true);
	//curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
	//Setting CURLOPT_RETURNTRANSFER to 0 so that the page content is not returned
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
	//execute the post
	$result = curl_exec ($ch);
	//close the connection
	curl_close($ch);
}
/**
 * @}
 * @}
 */
 ?>