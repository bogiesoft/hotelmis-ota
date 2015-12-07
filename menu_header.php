<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file menu_header.php
 * @brief setup for menus used webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once (dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);

$access = array();
$loginname=$_SESSION["loginname"];
get_useraccess($loginname, $access);
$msg[0]="";
$msg[1]="";

if($access['reservation']==1) echo "<tr><td><a href=\"reservations.php\">".$_L['MNU_reservations']."</a></td></tr>";
if($access['booking']==1) echo "<tr><td><a href=\"bookings.php\">".$_L['MNU_checkin']."</a></td></tr>";
if($access['guest']==1) echo "<tr><td><a href=\"guests.php\">".$_L['MNU_guest']."</a></td></tr>";
if($access['billing']==1){
	echo "<tr><td><a href=\"billings.php\">".$_L['MNU_billing']."</a></td></tr>";
	echo "<tr><td><a href=\"invoice_export.php\">".$_L['MNU_InvoiceExport']."</a></td></tr>";
}
if($access['lookup']==1) echo "<tr><td><a href=\"lookup.php\">".$_L['MNU_lookup']."</a></td></tr>";
if($access['reports']==1) echo "<tr><td><a href=\"reports.php\">".$_L['MNU_reports']."</a></td></tr>";
if($access['rates']==1) echo "<tr><td><a href=\"rates.php\">".$_L['MNU_rates']."</a></td></tr>";
if($access['agents']==1) echo "<tr><td><a href=\"agents.php\">".$_L['MNU_agents']."</a></td></tr>";
if($access['rooms']==1) echo "<tr><td><a href=\"rooms.php\">".$_L['MNU_rooms']."</a></td></tr>";
if($access['admin']==1) {
	echo "<tr><td><a href=\"admin.php\">".$_L['MNU_admin']."</a></td></tr>";
	echo "<tr><td><a href=\"adminhotel.php\">".$_L['MNU_hotel']."</a></td></tr>";
	echo "<tr><td><a href=\"hotelweb_setup.php\">".$_L['MNU_websetup']."</a></td></tr>";
	echo "<tr><td><a href=\"currencySetup.php\">".$_L['MNU_currencysetup']."</a></td></tr>";
	echo "<tr><td><a href=\"emailsetup.php\">".$_L['MNU_emailsetup']."</a></td></tr>";	
	echo "<tr><td><a href=\"holidays.php\">".$_L['MNU_holidaysetup']."</a></td></tr>";
}
if($access['policy']==1) echo "<tr><td><a href=\"policy.php\">".$_L['MNU_policy']."</a></td></tr>";	
?>
