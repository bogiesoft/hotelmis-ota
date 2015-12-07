<?php
session_start();
/**
 * @package OTA Hotel Management
 * @file admin.php
 * @brief admin web page called by OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file index.php
 * @brief called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @defgroup CODE_MANAGEMENT Source Code documentation
 * @{
 * This documentation is for code maintenance, not a user guide.
 * @defgroup FORM_MANAGEMENT Form management function documentation
 * @{
 */
//error_reporting(E_ALL&~ E_NOTICE);
ob_start();
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/validate_email.php");

include_once(dirname(__FILE__)."/temporary.php");

$lang = get_language();
load_language($lang);
//$logofile=Get_LogoFile();
$username = "";
date_default_timezone_set(TIMEZONE);
$operatorExists = 0;


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
$redirectToSetup=0;

$menu="";
$tabvar = 0;
if(isset($_REQUEST['activeTab']) && is_numeric($_REQUEST['activeTab'])) {
	$tabvar = $_REQUEST['activeTab'];
}
//check the operator setup has valid data if not redirect to setup page
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);

if(!empty($email)){
	if (!validateEmail($email,true,true,"",$email)){
		$redirectToSetup=1;
	}
}
if ((strcasecmp($company, "E-Novate Pte Ltd")==0 
		|| stripos($email, "@e-novate.com") !== false || strcasecmp($register, "201010533E")==0 || strcasecmp($phone, "6567470497")==0)){

	$redirectToSetup=1;
}

//check the operator setup is already done, if not redirect to the initial setup page
$operatorExists= Is_OperatorsetupExists();
if(!$operatorExists || $redirectToSetup){
	header("Location: setup/index.php?action=configsetup");
	return 0;
}

if(!empty($_POST["login"])) {
  $username = $_POST["username"];
  switch($_POST["login"]) {
    case $_L['PR_login']:
    if(!LoginHotelNew($_POST['username'], $_POST['password'])) {
      return;
    }
    break;
//    case $_L['PR_logout']:
//    echo "<center><font color=\"#0033CC\"><b>Session successful ended.</b></font></center>";
//    LogoutHotel();
//    break;
  }
}
if(isset($_GET['menu']) && $_GET['menu']=="logout") {
	LogoutHotel();
}
if(!empty($_POST["request"])&&$_POST["request"]=="submit") {
  header("Location: index.php");
}
?>

<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
<title><?php echo $_L['MAIN_Title'];?></title>
<link href="css/styles2.css" rel="stylesheet" type="text/css">
<script src="SpryAssets/SpryTabbedPanels.js" type="text/javascript"></script>
<link href="SpryAssets/SpryTabbedPanels.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
<link href="css/new.css" rel="stylesheet" type="text/css" />
<script type='text/javascript' src='js/urlpost.js'></script>
<link href='js/fullcalendar.css' rel='stylesheet' />
<link href='js/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='js/lib/moment.min.js'></script>
<script src='js/lib/jquery.min.js'></script>
<script src='js/fullcalendar.min.js'></script>
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
</head>

<body>
<div class="wrapper">
	<div class="boundary">
   	  <div class="header">
        	<div class="logo"><a href="http://www.e-novate.asia" target="_blank"><img src="images/enovate_logoWEB2.jpg" alt=""></a></div>
      </div>
      <div class="clr"></div>

     <div class="wrapper">

	<?php

	if(!$_SESSION['userid']) {
		include_once(dirname(__FILE__)."/login.php");
		echo "</div>";
 	} else {
		print_std_menus($_SESSION["loginname"]);
		echo "</div>  "
	?>
     <div class="clr"></div>
     <div class="wrapper">

     <?php
		if(($_GET['menu'] == 'home') || ($_GET['menu'] == '')) {
			include("home.php");
		} else if($_GET['menu'] == 'mysettings' || $_GET['menu'] == 'myProfile' ) {
			include("admin.php");
		} else if($_GET['menu'] == 'exportInvoice' ) {
			include("invoice_export.php");
		} else if($_GET['menu'] == 'reports' ) {
			include("reportsSummary.php");
		} else if($_GET['menu'] == 'admin') {
			if (accessNew('admin')) {
				include("hotelSetupTabs.php");
			} elseif (accessNew('rates')) {
				include("rates.php");
			} elseif (accessNew('rooms')) {
				include("rooms.php");
			} elseif (accessNew('agents')) {
				include("agents.php");
			}
		} else if($_GET['menu'] == 'myShift' ) {
			$menu='myShift';
			include("reports/shift_rpt.php");
		} else if($_GET['menu'] == 'userSetup' ) {
			include("admin.php");
		} else if($_GET['menu'] == 'usersList' ) {
			include("users_list.php");
		} else if($_GET['menu'] == 'websiteSetup' ) {
			include("hotelweb_setup.php");
		} else if($_GET['menu'] == 'emailSetup' ) {
			include("emailsetup.php");
		} else if($_GET['menu'] == 'policySetup' ) {
			include("policy.php");
		} else if($_GET['menu'] == 'policyList' ) {
			include("policy_list.php");
		} else if($_GET['menu'] == 'currencySetup' ) {
			include("currencySetup.php");
		} else if($_GET['menu'] == 'holidaySetup' ) {
			include("holidays.php");
		} else if($_GET['menu'] == 'agentSetup' ) {
			include("agents.php");
		} else if($_GET['menu'] == 'agentsList' ) {
			include("agents_list.php");
		} else if($_GET['menu'] == 'rateSetup' ) {
			include("rates.php");
		} else if($_GET['menu'] == 'ratesList' ) {
			include("rates_list.php");
		} else if($_GET['menu'] == 'roomSetup' ) {
			include("rooms.php");
		} else if($_GET['menu'] == 'roomsList' ) {
			include("rooms_list.php");
		} else if($_GET['menu'] == 'reservation' ) {
			include("reservations.php");
		} else if($_GET['menu'] == 'roomTypeSetup' ) {
			include("roomtypes.php");
		} else if($_GET['menu'] == 'holidayReport' ) {
			include("reports/hotel_holiday_rpt.php");
		} else if($_GET['menu'] == 'guestReport' ) {
			include("reports/hotel_res_rpt.php");
		} else if($_GET['menu'] == 'onlineBookingReport' ) {
			include("reports/hotel_olbook_rpt.php");
		} else if($_GET['menu'] == 'roomUsabilityReport' ) {
			include("reports/hotel_roomusability_rpt.php");
		}else if($_GET['menu'] == 'agodaReport' ) {
			include("reports/hotel_agodaBooking_rpt.php");
		} else if($_GET['menu'] == 'receiptReport' ) {
			include("reports/hotel_receipt_rpt.php");
		} else if($_GET['menu'] == 'roomStatusReport' ) {
			include("reports/hotel_roomstatus_rpt.php");
		} else if($_GET['menu'] == 'receiptDailyReport' ) {
			include("reports/hotel_receiptdaily_rpt.php");
		} else if($_GET['menu'] == 'taxReport' ) {
			include("reports/hotel_tax_rpt.php");
		} else if($_GET['menu'] == 'tourismReport' ) {
			include("reports/hotel_tourism_rpt.php");
		} else if($_GET['menu'] == 'shiftReport' ) {
			$menu='shiftReport';
			include("reports/shift_rpt.php");
		} else if($_GET['menu'] == 'profile' ) {
			include("guests_list.php");
		} else if($_GET['menu'] == 'editprofile' ) {
			if(is_ebridgeCustomer()){
				include("advanced_profile.php");
			}else{
				include("guests.php");
			}
		} else if($_GET['menu'] == 'booking' ) {
			include("bookings.php");
		} else if($_GET['menu'] == 'listbooking' ) {
			include("bookings_list.php");
		} else if($_GET['menu'] == 'reservationlist' ) {			
			include("reservation_list.php");
		} else if($_GET['menu'] == 'invoicelist' ) {			
			include("invoice_list.php");
		} else if($_GET['menu'] == 'invoice' ) {
			include("billings.php");
		} else if($_GET['menu'] == 'advancebooking' ) {
			include("adv_bookinglist.php");
		} else if($_GET['menu'] == 'roomsview' ) {
			include("rooms_view.php");
		} else if($_GET['menu'] == 'closeout' ) {
			include("closeout.php");
		} else if($_GET['menu'] == 'test' ) {
			include("test.php");
		}
		




     ?>




     </div>


	<?php }	?>


    </div>
    <div>
	    <table>
	    	<tr><?php print_footerNew(); ?>	</tr>
	    </table>
    </div>
</div>
<script type="text/javascript">
function getTabIndex(indexTab) {
	var formsCollection;
	var r;

	formsCollection=document.forms;
	for(r=0;r<document.forms.length;r++)
	{
	    document.forms[r].elements["activeTab"].value=indexTab;
	    //document.getElementById('activeTab').value=indexTab;	
	}
	  		  
}
var tabindexVar = <?php echo $tabvar;?>;
var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1", { defaultTab: tabindexVar });

</script>
</body>
</html>