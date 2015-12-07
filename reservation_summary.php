<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file reservations.php
 * @brief Reservations webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup RES_MANAGEMENT Reservation setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */
//error_reporting(E_ALL&~ E_NOTICE);
ob_start();
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/MPDF57/mpdf.php");

$logofile=Get_LogoFile();
//access("reservation");
$lang = get_language();
load_language($lang);
date_default_timezone_set(TIMEZONE);
$today = date("d/m/Y");
$tomorrow = date("d/m/Y", time() + (24*60*60));
$res = array();
$guestid=0;
$pdf = 0;
$MPDF57=0;
$showToPDF=1;

$bid = 0;
$resid = 0;
$checkin = "";
$checkout= "";
$roomid = 0;
$roomtypeid = 0;
$ratesid = 0;
$fid=0;
if(isset($_GET['bid'])) {
	$tag = "bid";
	$bid=$_GET['bid'];
	$fid = $bid;
}
if(isset($_REQUEST['rid'])) {
	$resid=$_REQUEST['rid'];
	$tag = "rid";
	$fid = $resid;
}
if(isset($_GET['in'])) $checkin=urldecode($_GET['in']);
if(isset($_GET['out'])) $checkout=urldecode($_GET['out']);
if(isset($_GET['room'])) $roomid=$_GET['room'];
if(isset($_GET['roomtype'])) $roomtypeid=$_GET['roomtype'];
if(isset($_GET['rate'])) $ratesid=$_GET['rate'];
if(isset($_GET['guest'])) $guestid=$_GET['guest'];
if(isset($_GET['PR'])) $pdf=$_GET['PR'];
if(isset($_GET['MPDF57'])) $MPDF57=$_GET['MPDF57'];
if(isset($_GET['showToPDF'])) $showToPDF=$_GET['showToPDF'];

$pdffilename = "TMP/".$tag.$fid.".pdf";

//echo "DEBUG bid:".$bid."-rid".$resid."-in:".$checkin."-out:".$checkout."-room:".$roomid."-roomtype:".$roomtypeid."-rate:".$ratesid."<br/>";
$vch = "";
if($resid) {
	if(get_reservation($resid, $res)) {
		$vch = $res['voucher_no'];
		if(!$guestid) $guestid = $res['guestid'];
		if(!$checkin) $checkin = $res['checkindate'];
		if(!$checkout) $checkout = $res['checkoutdate'];
		if(!$roomid) $roomid = $res['roomid'];
		if(!$roomtypeid) $roomtypeid = $res['roomtypeid'];
		if(!$ratesid) $ratesid = $res['ratesid'];
	}	
	$input = "/hotelmis/webtoPDF.php?TMPL=reservation_summary.php&PR=1&rid=".$resid;
}
if($bid) {
	if(get_booking($bid, $res)) {
		$vch = $res['voucher_no'];
		if(!$guestid) $guestid = $res['guestid'];
		if(!$checkin) $checkin = $res['checkindate'];
		if(!$checkout) $checkout = $res['checkoutdate'];
		if(!$roomid) $roomid = $res['roomid'];
		if(!$roomtypeid) $roomtypeid = $res['roomtypeid'];
		if(!$ratesid) $ratesid = $res['rates_id'];
		$resid = $res['reservation_id'];
	}	
	$input = "/hotelmis/webtoPDF.php?TMPL=reservation_summary.php&PR=1&bid=".$bid;
}

 if($guestid > 0) {
	$guest = array();
	findguestbyid($guestid, $guest);
	$guestname = $guest['guest'];
}



$rate=array();
if($ratesid && !$resid) {
	get_ratebyratesid($ratesid, $rate);
}

if($roomid && !$roomtypeid && !$ratesid && !$resid) get_ratebyratesid(get_RateID_byRoomID($roomid),$rate);
if($roomtypeid && !$ratesid && !$resid) get_ratebyratesid(get_RateID_byRoomTypeID($roomtypeid),$rate);
if($resid){
	$details=array();
	$total=0;
	reservation_details_byResID($resid,$details);
	foreach ($details as $resdetail){
		$rate = array();
		$rate_id = $resdetail['ratesid'];
		get_ratebyratesid($rate_id,$rate);
		$total = $total +$rate['price'];
	}
} else {
	$total = $rate['price'];
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta name="generator" content= "HTML Tidy for Linux/x86 (vers 11 February 2007), see www.w3.org" />
    <meta http-equiv="Content-Type" content= "text/html; charset=us-ascii" />
	<link href="css/new.css" rel="stylesheet" type="text/css">
	<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></LINK>
	<SCRIPT type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	<SCRIPT type="text/javascript" src="js/datefuncs.js"></script>
	<SCRIPT type="text/javascript" src="js/ccards.js"></script>
    <title>
      <?php echo $_L['MAIN_Title'];?>
    </title>


   <style>
	.plainDropDown{
		width:130px;
		font-size:11px;
	}
	.plainDropDown2{
		width:75px;
		font-size:11px;
	}
	.plainSelectList{
		width:250px;
		font-size:11px;
	}
	.plainButton {
		font-size:11px;	
	}
	.narrowDropDown{
		width:16px;
		font-size:11px;
		-moz-max-content:16px;
		-moz-appearance: menuimage;
	}
  </style>

  </head>
  <body >
  <div class="layer1">
	<h1> <?php echo $_L['RSV_summary']; ?> </h1><br/>
	<?php 
		if(file_exists($pdffilename)) {
			print "<small><a href='#' onclick='window.open(\"".$pdffilename."\")' >";
			print "<img src='images/PDF_Icon.gif'/>";
			print "</a></small><br/>"; 
		} else {
			if(!$pdf) {
	 ?>
		<small><a href="<?php echo $input; ?>" >To PDF</a><br/><br/></small>
		<?php  
			}
		}
    	?>
	
	<b> <?php echo $_L['RSV_voucherno']; ?> </b> :  <?php echo $vch; ?> <br/>
	<b> <?php echo $_L['RSV_guest']; ?> </b> : <?php echo $guestname; ?> <br/>
	<b> <?php echo $_L['RSV_arrival']; ?> </b> :  <?php echo $checkin; ?> <br/>
	<b> <?php echo $_L['RSV_depart']; ?> </b> :  <?php echo $checkout; ?> <br/>
	<b> <?php echo $_L['INV_total']; ?> </b> : <?php echo $rate['currency']." ".$total;?><br/>
	<br/>
	<b> <?php echo $_L['RSV_instructions']; ?> </b> <br/>
	<p>
	<?php echo $res['instructions']; ?>
	</p>
	<?php 
	if($resid) {
		foreach ($details as $resdetail){
			$rate = array();
			$rate_id = $resdetail['ratesid'];
			get_ratebyratesid($rate_id,$rate);
			echo "<b> ". $_L['RTS_code']." </b> : ". $rate['code']. " : ". $rate['name']." <br/>";
			echo "<b> ". $_L['RTS_inclusions']." </b><p>";
			echo $rate['inclusions']; 
			echo "</p>";
			echo "<b> ". $_L['RTS_condtions']." </b><p>";
			echo $rate['requirements']; 
			echo "</p>";
		}
	}
	if($bid) {
		echo "<b> ".$_L['RTS_code']." </b> : ". $rate['code']. " : ". $rate['name']." <br/>";
		echo "<b> ". $_L['RTS_inclusions']." </b>";
		echo "<p>";
		echo $rate['inclusions']; 
		echo "</p>";
		echo "<b> ". $_L['RTS_condtions']."</b>";
		echo "<p>";
		echo $rate['requirements']; 
		echo "</p>"; 
	}
	?></div>
  </body>
</html>
<?php

/**
 * @}
 * @}
 */
?>
