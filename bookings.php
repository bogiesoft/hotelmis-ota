<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file bookings.php
 * @brief bookings webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup BOOKING_MANAGEMENT Booking/Check-in setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$logofile=Get_LogoFile();
$lang = get_language();
load_language($lang);
date_default_timezone_set(TIMEZONE);
access("booking"); //check if user is allowed to access this page

$res_id = 0;
$book_id = 0;
$guest_id = 0;
$details_id = 0;
$restarget=0;
$act = 0;
$type_id = 0;
$room_id = 0;
$bookroom=array();
$rem_cnt = 0;
$bookings=array();
$offset = 0;

if(isset($_GET['resid'])) { 
	$res_id = $_GET['resid'];
	$_POST["reservation_id"] = $_GET['resid'];
}
if(isset($_GET['id'])) {
	$book_id = $_GET['id'];
}
if(isset($_GET['guestid'])) {
	$guestid = $_GET['guestid'];
}
if(isset($_GET['detailsid'])) {
	$details_id = $_GET['detailsid'];
}
if(isset($_GET['action'])) {
	$act = $_GET['action'];
}
if(isset($_GET['typeid']) ) {
	$type_id = $_GET['typeid'];
	$_POST['roomtypeid'] = $_GET['typeid'];
}
if(isset($_GET['roomid'])) {
	$room_id = $_GET['roomid'];
}
if(isset($_GET['rem'])) {
	$rem_cnt = $_GET['rem'];
}
if(isset($_GET['rateid'])) {
	$_POST["ratesid"] = $_GET['rateid'];
}

//process datablob from XOWeb
if(is_ebridgeCustomer()){ //???
	if (isset($_POST['EXOdatablob']) && !empty($_POST['EXOdatablob'])) { //datablob from MIS //???		
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/EXO_link.php");	
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		$itemidMIS=0;
		$catMIS=0;
		$arrayXO=array();									
		$arrayXO=process_EXO_datablob($_POST['EXOdatablob']);
		$arrayMIS=process_EXO_datablob($_POST['MISdatablob']);
		if (isset($arrayXO['module']) && !empty($arrayXO['module'])) {
			if ($arrayXO['module']==EBRIDGE_HOTEL) {
				$catMIS=HOTEL;
			} elseif ($arrayXO['module']==EBRIDGE_TOUR) {
				$catMIS=TOUR;
			} elseif ($arrayXO['module']==EBRIDGE_GOLF) {
				$catMIS=GOLF;
			} elseif ($arrayXO['module']==EBRIDGE_VEHICLE) {
				$catMIS=TRANSPORT;
			}
		}
		$itemidMIS=get_first_itemID_by_itype($catMIS);
		modify_transaction(0, $arrayMIS['bill_id'],$itemidMIS,'', $arrayXO['datebooked'], $_SESSION['userid'], $arrayXO['amount'],
		0, 0,$arrayXO['amount'], 0, 0,$arrayXO['count'],'','',
		$arrayXO['amount'],$arrayXO['currency'],$arrayXO['XOID']);
	} 
}

// This is a new checkin, so set the reservation and the reservation_room details to Checked In.
if($act && $res_id && !$id && $details_id ) {
	// Only upate the status when last one is checked in.
	if($rem_cnt == 0) {
		update_reservation_status($res_id,RES_CHECKIN);
	}
	get_booking_byresid($res_id, $details_id, $bookings);
	$book_id = $bookings['book_id'];
	update_resDetails_status($details_id,RES_CHECKIN);
}
if(!$guestid && isset($_POST['guestid'])) {
	$guestid = $_POST['guestid'];
}
if($_POST['restarget'] && !$restarget) $restarget = $_POST['restarget'];

$today = date("d/m/Y");
$tomorrow = date("d/m/Y", time() + (24*60*60));
$validationMsgs = "";
if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['REG_register']:
		case $_L['BTN_update']:
		case $_L['REG_checkinbox']:
		case $_L['REG_checkoutbox']:
		case $_L['REG_Rcheckout']:
			//if guest has not been selected exit
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('no_adults',$_L['REG_noperson_err']);
			$fv->validateEmpty('roomid',$_L['REG_noroom_err']);				
	
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			} else {
				$userid=$_SESSION["userid"];
				$book_id=$_POST["book_id"];
				$res_id=$_POST["reservation_id"];
				$bill_id=$_POST["bill_id"];
				$guestid=$_POST["guestid"];
				$guestname=$_POST["guestname"];
				$address=$_POST["address"];
				$email=$_POST["email"];
				$phone=$_POST["phone"];
				$postal_code=$_POST['postal_code'];
				$town=$_POST['town'];
				$countrycode=$_POST['countrycode'];
				$nationality=$_POST['nationality'];
				$localid = $_POST['localid'];
				$no_adults=$_POST["no_adults"];	
				$no_child1_5= !empty($_POST["no_child1_5"]) ? $_POST["no_child1_5"] : 0;
				$no_child6_12= !empty($_POST["no_child6_12"]) ? $_POST["no_child6_12"] : 0;
				$no_babies= !empty($_POST["no_babies"]) ? $_POST["no_babies"] : 0;
				$checkindate= $_POST["checkindate"] ;
				$checkoutdate= $_POST["checkoutdate"] ;
				$roomid= $_POST["roomid"] ;				
				$roomtypeid=$_POST["roomtypeid"];
				$ratesid=$_POST["ratesid"];
				$instr=$_POST["instructions"];
				$checkedin_by=$_POST["checkedin_by"];
				$checkedout_by=$_POST["checkedout_by"];
				$checkedout_date=$_POST["checkedout_date"];
				$checkedin_date=$_POST["checkedin_date"];
				$CCnum=$_POST["CCnum"];
				$cctype=$_POST["cctype"];
				$expiry=$_POST["expiry"];
				$CVV=$_POST["CVV"];
				$details_id = (isset($_POST['res_det_id']))?intval($_POST['res_det_id']):0;
				$voucher_no= get_bookingvoucher($book_id);
				$book_status = get_bookingstatus($book_id);
				if(!empty($book_id)){					
					get_booking($book_id, $bookroom);
				}
				if(!$ratesid && $res_id) $ratesid = get_reservationrate($res_id);
				if(!$ratesid && $roomtypeid) $ratesid = get_RateID_byRoomTypeID($roomtypeid);
				if(!$ratesid && $roomid) $ratesid = get_RateID_byRoomID($roomid);
				if($action == $_L['REG_register']) {
					$book_status = BOOK_REGISTERED;					
				}
				if($action == $_L['REG_checkinbox'] && $roomid) {
					$checkedin_by = $userid;
					$checkedin_date = date("d/m/Y H:i");
					$book_status = BOOK_CHECKEDIN;
				}
				if($action == $_L['REG_checkoutbox']) {
					$checkedout_by=$userid;
					$checkedout_date=date("d/m/Y H:i");
					$book_status = BOOK_CHECKEDOUT;					
//					print "Checked out ".$userid." ".$checkedout_date."<br/>";
				}
				if($action == $_L['REG_Rcheckout']) {
					$book_status = BOOK_CHECKEDIN;
					$checkedout_by=0;
				}
					// If the guest does not exist or was not selected, then create a new guest.
				if(!$guestid && $guestname && $address && $phone ) {
					list($firstname, $middlename, $lastname) = preg_split('/\s+/',$guestname,3);
					if($middlename && !$lastname) {
						$lastname = $middlename;
						$middlename = "";
					}
					if(is_ebridgeCustomer()){
						include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
						$profileid = modify_advProfile(0, 0, 1, $firstname, $middlename, $lastname, '0000-00-00', 'M', 'en', 'en', '', 'Profile auto created from Guest Check-In');
						modify_advPhone(0, 0, $profileid, PTT_VOICE, PUT_CONTACT, PLT_DIRECT, "", "", $phone, "");
						modify_advDocument(0, 0, $profileid, DOC_NID, '0000-00-00', '000-00-00', "", "", $nationality, $guestname, $localid, "");
						modify_advEmail(0, 0, $profileid, 0, EAT_PERSONAL, $email, "");
						modify_advAddress(0, 0, $profileid, AUT_CONTACT, CLT_HOME, "", "", "", "", $address, $town, "", $countrycode, $postal_code);
						$guestid = $profileid;
					}
					else{
						$guestid = modify_guest(0,$lastname,$firstname,$middlename,'',
							$localid,'',$countrycode,$address,$town,$postal_code,$phone,$email,
							'', '', '', $nationality);
					}
				}
					
				if(!$guestid) {
					// break out of the loop if no guest ID set.
					break;
				}
				$book_id=modify_booking($book_id,$res_id,$bill_id,$guestid,
						$no_adults,$no_child6_12,$no_child1_5,$no_babies,$checkindate,
						$checkoutdate,$roomid,$roomtypeid,$ratesid, $instr,
						$checkedin_by, $checkedin_date, $checkedout_by, $checkedout_date,
						$cctype,$CCnum,$expiry,$CVV,$voucher_no,$book_status, $details_id);	
				if(is_ebridgeCustomer()){
					include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
					CustomPagesFormRead( HTL_PAGE_BOOK, $book_id);
				}						
				if(!$book_id){
						echo "<div align=\"center\"><h1>".$_L['REG_error1']."</h1></div>";
				} else {
						if ($action == $_L['REG_checkinbox']) {
							update_booking_status($book_id, BOOK_CHECKEDIN);
						} else if($action == $_L['REG_Rcheckout']) {
							update_booking_status($book_id, BOOK_CHECKEDIN);
							update_room_status($roomid, BOOKED);
							update_reservation_status($res_id,RES_CHECKIN);
							if($details_id){				
								update_resDetails_status($details_id,RES_CHECKIN);
							}

						} else if ($action == $_L['REG_checkoutbox']) {
							update_booking_status($book_id, BOOK_CHECKEDOUT);
							update_reservation_status($res_id,RES_CHECKOUT);
							if($details_id){				
								update_resDetails_status($details_id,RES_CHECKOUT);
							}

						}
						/*if($details_id){
							modify_reservation_details($details_id,$res_id,$roomid,$roomtypeid,$ratesid,0,RES_CHECKIN);							
						}*/
												
					echo "<div align=\" center\"><h1>".$_L['REG_success1']."</h1></div>";
					if(!$bill_id) {
						$bill_id = create_booking_bill($book_id, $res_id, $guestid, $userid);
						if(!$bill_id){
							echo "<div align=\" center\"><h1>".$_L['REG_error2']."</h1></div>";
						} else {
							echo "<div align=\" center\"><h1>".$_L['REG_success2']."</h1></div>";
						}
					} else {
						$bill = array();
						get_bill($bill_id, $bill);
						
					
						modify_bill($bill_id,$bill['billno'],$book_id,$res_id,$bill['date_billed'],$bill['date_checked'],$bill['created_by'],$guestid,$bill['status'], $bill['flags']);
					}
					if($action == ($_L['REG_checkinbox']||$_L['BTN_update']) && $roomid) {
						if( ! update_room_status($roomid, BOOKED)) {
							echo "<div><h1>".$_L['REG_error3']."</h1></div>";
						}else{
							echo "<div align=\"center\"><h1>".$_L['REG_success3']."</h1></div>";
						}
					}
					if($action == $_L['REG_checkoutbox'] && $roomid) {
						if( ! update_room_status($roomid, LOCKED)) {
							echo "<div><h1>".$_L['REG_error3']."</h1></div>";
						}else{
							echo "<div align=\"center\"><h1>".$_L['REG_success4']."</h1></div>";
						}
					}	
					if(($action ==$_L['BTN_update']) && $roomid && $bookroom['roomid'] && ($bookroom['roomid']!=$roomid)){
						update_room_status($bookroom['roomid'], LOCKED);
					}
				}
			}			
			break;
		case 'List':
			break;
		case 'Find':
			break;
		}
	}

if(!$book_id && $_POST['book_id']) {
	$book_id = $_POST['book_id'];
}

if($book_id) {
	get_booking($book_id, $bookings);
} else if(!$book_id && $res_id) {
	get_booking_byresid($res_id, $details_id, $bookings);
	if($room_id) {
		update_room_status($room_id, BOOKED);
	}
						
} else if (!$book_id && $guestid) {
	get_booking_byguest($guestid, $bookings);
}

if($bookings['guestid']) 
	$readonly = "readonly";
else 
	$readonly = "";

	
// ensure that we don't lose the information that has just been entered.
if(! $bookings['reservation_id'] && $_POST['reservation_id']&&!empty($_POST['reservation_id'])) 	$bookings['reservation_id']=$_POST["reservation_id"];
if(! $bookings['bill_id'] && $_POST['bill_id']&&!empty($_POST['bill_id'])) 	$bookings['bill_id']=$_POST["bill_id"];
if(! $bookings['guestid'] && $_POST['guestid']&&!empty($_POST['guestid'])) 	$bookings['guestid']=$_POST["guestid"];
if(! $bookings['guestname'] && $_POST['guestname']&&!empty($_POST['guestname'])) 	$bookings['guestname']=$_POST["guestname"];
if(! $bookings['address'] && $_POST['address']&&!empty($_POST['address'])) 	$bookings['address']=$_POST["address"];
if(! $bookings['email'] && $_POST['email']) 	$bookings['email']=$_POST["email"];
if(! $bookings['phone'] && $_POST['phone']) 	$bookings['phone']=$_POST["phone"];
if(! $bookings['postal_code'] && $_POST['postal_code']) 	$bookings['postal_code']=$_POST['postal_code'];
if(! $bookings['town'] && $_POST['town']) 	$bookings['town']=$_POST['town'];
if(! $bookings['countrycode'] && $_POST['countrycode']) 	$bookings['countrycode']=$_POST['countrycode'];
if(! $bookings['nationality'] && $_POST['nationality']) 	$bookings['nationality']=$_POST['nationality'];
if(! $bookings['no_adults'] && $_POST['no_adults']) 	$bookings['no_adults']=$_POST["no_adults"];	
if(! $bookings['no_child1_5'] && $_POST['no_child1_5']) 	$bookings['no_child1_5']= $_POST["no_child1_5"];
if(! $bookings['no_child6_12'] && $_POST['no_child6_12']) 	$bookings['no_child6_12']= $_POST["no_child6_12"];
if(! $bookings['no_babies'] && $_POST['no_babies']) 	$bookings['no_babies']= $_POST["no_babies"];
if(! $bookings['checkindate'] && $_POST['checkindate']) 	$bookings['checkindate']= $_POST["checkindate"] ;
if(! $bookings['checkoutdate'] && $_POST['checkoutdate']) 	$bookings['checkoutdate']= $_POST["checkoutdate"] ;
if((! $bookings['roomid'] && isset($_POST['roomid'])&& $_POST['roomid']) || (isset($_POST['roomid'])&& $_POST['roomid'] && $bookings['roomid'] != $_POST['roomid'])) 	$bookings['roomid']= $_POST["roomid"] ;
if((! $bookings['roomtypeid'] && isset($_POST['roomtypeid'])&&$_POST['roomtypeid']) || 
   (isset($_POST['roomtypeid'])&&$_POST['roomtypeid'] && $bookings['roomtypeid'] != $_POST['roomtypeid'])) 	
		$bookings['roomtypeid']=$_POST["roomtypeid"];
if((! $bookings['rates_id'] && isset($_POST['rates_id'])&& $_POST['rates_id']) || 
   (isset($_POST['rates_id'])&&$_POST['rates_id'] && $bookings['rates_id'] != $_POST['rates_id'])) 	
		$bookings['rates_id']=$_POST["rates_id"];
if(! $bookings['instructions'] && $_POST['instructions']) 	$bookings['instructions']=$_POST["instructions"];
if(! $bookings['checkedin_by'] && $_POST['checkedin_by']) 	$bookings['checkedin_by']=$_POST["checkedin_by"];
if(! $bookings['checkedout_by'] && $_POST['checkedout_by']) 	$bookings['checkedout_by']=$_POST["checkedout_by"];
if(! $bookings['checkedout_date'] && $_POST['checkedout_date']) 	$bookings['checkedout_date']=$_POST["checkedout_date"];
if(! $bookings['checkedin_date'] && $_POST['checkedin_date']) 	$bookings['checkedin_date']=$_POST["checkedin_date"];
if(! $bookings['CCnum'] && $_POST['CCnum']) 	$bookings['CCnum']=$_POST["CCnum"];
if(! $bookings['cctype'] && $_POST['cctype']) 	$bookings['cctype']=$_POST["cctype"];
if(! $bookings['expiry'] && $_POST['expiry']) 	$bookings['expiry']=$_POST["expiry"];
if(! $bookings['CVV'] && $_POST['CVV']) 	$bookings['CVV']=$_POST["CVV"];

if(! $bookings['res_det_id'] && $_POST['res_det_id']) 	$bookings['res_det_id']=$_POST["res_det_id"];
if(! $bookings['res_det_id'] && $details_id) $bookings['res_det_id']=$details_id;
if(! $bookings['voucher_no'] && $_POST['voucher_no']) 	$bookings['voucher_no']=$_POST["voucher_no"];
// Rates id, no roomtype or room number
if(!$restarget && $bookings['rates_id'] && !$bookings['roomtypeid'] && !$bookings['roomid']) $restarget = 3;
// Roomtype set and no room id 
if (!$restarget && $bookings['roomtypeid'] && !$bookings['roomid']) $restarget = 2;
// have a room id
if(!$restarget && $bookings['roomid']) $restarget = 1;
$total = $bookings['no_adults'] + $bookings['no_child1_5'] + $bookings['no_child6_12'] + $bookings['no_babies'];
//$localid = $bookings['idno'];						// ???
if(!$localid  && $bookings['pp_no']) {
	$localid = $bookings['pp_no'];
}	
// @todo fix this to use defines - not magic value.
if(!$bookings['checkindate']) $bookings['checkindate'] = $today." 14:00";
if(!$bookings['checkoutdate']) $bookings['checkoutdate'] = $tomorrow." 10:00";
if(!$bookings['no_nights']) $bookings['no_nights'] = 1;
if(!$bookings['no_adults']) $bookings['no_adults'] = 1;
if(!$bookings['no_child1_5']) $bookings['no_child1_5'] = 0;
if(!$bookings['no_child6_12']) $bookings['no_child6_12'] = 0;
if(!$bookings['no_babies']) $bookings['no_babies'] = 0;
if(!$bookings['roomtypeid'] && $type_id) $bookings['roomtypeid'] = $type_id;
$mscludge = "";
if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
	$mscludge = 'onfocus="javascript: this.style.width=\'auto\';" onblur="javascript: this.style.width=18;"';
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	  <link href="css/new.css" rel="stylesheet" type="text/css" />
	  <link href="css/style.css" rel="stylesheet" type="text/css" />
	  <link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
	  <script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	  <script type="text/javascript" src="js/datefuncs.js"></script>
	  <title><?php echo $_L['MAIN_Title']." ".$_L['MNU_checkin'];?></title>
	  <script type="text/javascript">
		<!--
		/**
		 * This function clears the roomtypeid from the data selected if the 
		 * room is reselected by room number
		 */
		function fixroomtype() {
			var restarget= document.getElementById('restarget');
			var val = restarget.options[restarget.selectedIndex].value;
			if(val == 1) {
				document.getElementById('roomtypeid').selectedIndex = 0;
			}
		}
		/**
		 * This function sets the guest from the pulldown list
		 * Updates the page by setting the guest name and running the form submit
		 * to retrieve the guest details.
		*/
		function updateguestname() {
		  var guestname=document.getElementById('guestname');
		  var tguestid=document.getElementById('tguestid');
		  var guestid=document.getElementById('guestid');
		  var val;
		  var txt;
		  var i;
		  for (i = tguestid.length - 1; i>=0; i--) {
			if (tguestid.options[i].selected) {
				val = tguestid.options[i].value;
				txt = tguestid.options[i].text;
				break;
			}
		  }
//		  alert("val = " + val);
//		  alert("txt = " + txt);
		  guestid.value = val;
		  guestname.value = txt;
		  document.forms[0].submit();	
		}
		/** if the target for the reservation is a specfic room
			show the room list, if the a room type, show the room type list
			if a promotional rate, show the rates list.
		*/
		function showtarget(dest) {
		  target=dest.value;
		  rate=document.getElementById('targetrate');
		  rtype=document.getElementById('targetroomtype');
		  if(target >= 1 && target <= 3) {
			if(target == 1) {
				rate.style.display = "none";	
				rtype.style.display = "none";
			}
			if(target == 2) {
				rate.style.display = "none";	
				rtype.style.display = "";
			}
			if(target == 3) {
				rate.style.display = "";	
				rtype.style.display = "none";
			}
		  } else {
				rate.style.display = "none";	
				rtype.style.display = "none";
		  }
		}
	  //-->
	  </script>
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
			width:18px;
			font-size:11px;
			-moz-max-content:16px;
			-moz-appearance: menuimage;
		}
		#tguestid {
			width:18px;
			font-size:11px;
		}
		#tguestid option {
			width:auto;
			font-size:14px;
		}
	  </style>
<?php
	$onsubmit = '';
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		CustomPagesOnSubmitFunctionCode(HTL_PAGE_BOOK);
		$onsubmit=CustomPagesOnSubmitFunctionCall(HTL_PAGE_BOOK);
		if($onsubmit) $onsubmit = 'onsubmit="return '.$onsubmit.'"';
	}
?>
	  </head>
	<body>
	  <form action="index.php?menu=booking" method="post" name="bookings" enctype="multipart/form-data" <?php echo $onsubmit; ?> >
		<table width="100%"  class="listing-table" border="0" cellpadding="1" align="center">
		  <tr valign="top" >
			<!-- PRINT Content Header -->
			<?php 
				if ($_GET['menu'] == "booking") {
					print_rightMenu_home();
				}?> 
			<!-- Print the content center -->
			<td  class="c3">
			<table width="100%">
			<tr><td><h2><a href="https://www.youtube.com/watch?v=PwfclcT0__E" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['REG_title']; ?></h2></a></td></tr>
			<tr>
		  		<td><?php echo $validationMsgs;?></td>
		  		</tr>
			<tr><td>
			 <!--  INSERT TAB Here-->
			 <div  id="TabbedPanels1" class="TabbedPanels">
				<ul id="tabgroup" class="TabbedPanelsTabGroup">
					<?php 
					//This tab index cross reference to the spry assets tab javaxcript at the bottom of index.php
					$tabidx = 0; 
					?>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['FRM_reservetitle']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['FRM_guestinfotitle']; ?></li>
					<?php if(is_ebridgeCustomer()){?>
						<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['CST_fields']; ?></li>
					<?php }?>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['REG_payment']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['RSV_summary']; ?></li>
				</ul>
				<div class="TabbedPanelsContentGroup">
				<!-- TAB Registration INFORMATION-->
				<div class="TabbedPanelsContent">
				
				<div class="scrolltab">
				<table  width="100%" border="0" cellpadding="1">
				
				
				<tr>
		  		<td>&nbsp;</td>
		  		</tr>
				<!-- Row for the registration header -->
				<tr >
				
				<td>
				<?php 
				    // print STATUS if have
					if(!$res_id) {
						$res_id = get_reservation_id($book_id);
					}
					?>
				<table  align=center border="0"><tr >
				<td align=left style="padding:7;"><?php echo $_L['REG_no']; ?></td>
				<td>
					<input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/>
					<input type="text" name="book_id" value="<?php echo trim($bookings['book_id']); ?>" size="3" readonly=""/>
					<input name="guestid" type="hidden" id="guestid" value="<?php echo trim($bookings['guestid']); ?>" />
					<input name="reservation_id" type="hidden" value="<?php echo trim($bookings['reservation_id']); ?>" />
					<input name="detailsid" id="detailsid" type="hidden" value="<?php echo $details_id; ?>" />
					<input name="res_det_id" id="res_det_id" type="hidden" value="<?php echo $bookings['res_det_id']; ?>" />
					<?php 
					
						if($res_id > 0) {
					?>
					  <a class="button" href="index.php?menu=reservation&resid=<?php echo $res_id; ?>"><?php echo $_L['REG_gotores']; ?></a>
					<?php } ?>
					
				</td>
				<td>
				<?php 
				    // print STATUS if have
				
					if($res_id) {
						$res_sts = intval(get_reservation_status($res_id));
						$res_txt = get_res_status_text($res_sts);
						
						if ($res_txt == 'Check In' || $res_txt == 'Check Out') {								
							echo $_L['RSV_status'];
							echo "&nbsp;&nbsp;<input type=\"text\" name=\"res_sts\" readonly id=\"res_sts\" size=10 value=\"$res_txt\"/> ";
						}
					}
							?>
				</td>
				
				</tr>
				<tr>
				<td align=left style="padding:7;"><?php echo $_L['REG_invoiceno']; ?> </td>
				<td>
					<input type=hidden name=bill_id value="<?php echo $bookings['bill_id']; ?>" />
					<input type="text" size=10 name="invoice_no" value="<?php echo trim(get_billnumber($bookings['bill_id'])); ?>" readonly="readonly" />&nbsp;&nbsp;<a class="button" href="index.php?menu=invoice&id=<?php echo $bookings['bill_id']; ?>" target='billings' ><?php echo $_L['REG_gotoinvoiceno']; ?></a>
				</td>
				<td>
				<?php //if ebridge customer {print exo link function} 
					if(is_ebridgeCustomer()){	//???	
						include_once(dirname(__FILE__)."/OTA/advancedFeatures/EXO_link.php");
							
						if ($book_id && !empty($book_id)) {
							$statusBookID = get_bookingstatus($book_id);
							if ($statusBookID==BOOK_CHECKEDIN || $statusBookID==BOOK_REGISTERED) {
								create_print_button();	
							}
						}	
					}
				?>	
				</td>
				</tr>
				
				</table>
				</td>
				</tr>
				<tr><td>&nbsp;&nbsp;</td></tr>
				<tr><td>&nbsp;&nbsp;</td></tr>
				<tr><td>&nbsp;&nbsp;</td></tr>
				<tr>
				<td>
				<table border="0">
					<tr>
						<td style="padding:5px;" ><?php echo  $_L['RSV_arrival']; ?></td>
						<td>
						  <img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkindate,'dd/mm/yyyy hh:ii',this, true, 1000)"/>
						  <input type="text" name= "checkindate" id="checkindate"  size=16 maxlength=16 readonly="readonly" value="<?php echo trim($bookings['checkindate']);?>" onchange="addDateDays('checkindate', 'no_nights','checkoutdate','DD/MM/YYYY HH:II');" />
						</td>
						<td><?php echo  $_L['RSV_depart']; ?></td>
						<td>
						  <img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkoutdate,'dd/mm/yyyy hh:ii',this, true, 1000)"/>
						  <input type="text" name= "checkoutdate" id="checkoutdate"  size=16 maxlength=16 readonly="readonly" value="<?php echo trim($bookings['checkoutdate']);?>" onchange="subDates('checkindate','checkoutdate','no_nights', 'DD/MM/YYYY HH:II');" />
						</td>
						<td><?php echo$_L['REG_nights']; ?> </td>
						<td><input type=text size=4 maxlength=4 name=no_nights id=no_nights value="<?php echo $bookings['no_nights']; ?>" onchange="addDateDays('checkindate','no_nights','checkoutdate','DD/MM/YYYY HH:II');"/></td>
					</tr>
					<tr>
						  <td style="padding:5px;" colspan=4>
							<table>
							  <tr>
								<td>
									<?php echo $_L['RSV_adultsno']; ?><br />
									<input type="text" name="no_adults" id="no_adults" size="4"  maxlength="4" value="<?php echo trim($bookings['no_adults']);?>" />
								</td>
								<td>
									<?php echo $_L['RSV_infantsno']; ?><br />
									<input type="text" name="no_child1_5" size="4"  maxlength="4" value="<?php echo trim($bookings['no_child1_5']);?>" />
							</td>
							<td>
								<?php echo $_L['RSV_childno']; ?><br />
								<input type="text" name= "no_child6_12" size="4"  maxlength="4" value="<?php echo trim($bookings['no_child6_12']);?>" />
							</td>
							<td>
								<?php echo $_L['RSV_babyno']; ?><br />
								<input type="text" name="no_babies" size="4" maxlength="4" value="<?php echo trim($bookings['no_babies']);?>" />
							</td>
							<td>
								<b><?php echo $_L['REG_totalppl']; ?></b><br/>
								<input type="text" name="total_guests" id="total_guests" size="4" maxlength="4" value="<?php echo $total; ?>" readonly="readonly" />
							</td>
						  </tr>
						</table>
					  </td>
					</tr>
					<tr>
						  <td style="padding:5px;" >
						  <?php  echo $_L['RTS_code']; 
								$rmstyle="display:none";
								$rtstyle="display:none";
								$rastyle="display:none"; 
						   ?>
						  </td>
						  <td>
							<select name="restarget" id="restarget" onchange="showtarget(this);document.forms[0].submit();" class="plainDropDown" >
								<option value=1 <?php if ($restarget == 1) { echo "selected=selected"; $rmstyle=''; } ?> ><?php echo $_L['RM_roomno']; ?> </option>
								<option value=2 <?php if ($restarget == 2) { echo "selected=selected"; $rtstyle=''; } ?> ><?php echo $_L['RMT_rtype']; ?> </option>
								<option value=3 <?php if ($restarget == 3) { echo "selected=selected"; $rastyle=''; } ?> ><?php echo $_L['RTS_code']; ?> </option>
							</select>
						  </td>
						  <td colspan=2>
							<table width=290px border=0>
							  <tr id=targetrate style="<?php echo $rastyle; ?>">
								<td> <?php echo $_L['RTS_code']; ?></td>
								<td>&nbsp;								
								  <select name=ratesid class="plainDropDown"  onchange="document.forms[0].submit();" >
									<option value=0> </option>
									<?php 
									 $cond = ""; 
									$selected="";
								  	if(isset($_POST['ratesid'])) $selected=$_POST['ratesid']; else $selected=$bookings['rates_id'];
									populate_select("rates","ratesid","ratecode",$selected, $cond);									
									?>
								  </select>
								</td> 
							  </tr>
							  <tr id=targetroomtype style="<?php echo $rtstyle; ?>">
								<td> <?php echo $_L['RMT_rtype']; ?></td>
								<td>&nbsp;
									<select name="roomtypeid" id="roomtypeid" class="plainDropDown" onchange="document.forms[0].submit();" >
									  <option value='0'> </option>
									  <?php 
									  $selected="";
									  $cond="";
								  	  if(isset($_POST['roomtypeid'])) $selected=$_POST['roomtypeid']; else $selected=$bookings['roomtypeid'];
									  populate_select("roomtype","roomtypeid","roomtype",$selected, $cond);?>
									</select>
								</td>

							  </tr>
							</table>
						  </td>
						  
						</tr>
						<tr>
						  <td style="padding:5px;" ><?php echo $_L['RM_roomno'];?><font color="#FF0000">*</font></td>
						  <td>
						    
						    <!-- <option value=0> </option>-->					  
							  <?php 
							  if($restarget==2){
								echo '<select name="roomid" id="roomid" class="plainDropDown" onchange="fixroomtype();document.forms[0].submit();">';
								echo '<option value="0" > </option>';
							  	$room =array();								  						 
							  	if(isset($_POST['roomtypeid'])&&!empty($_POST['roomtypeid'])){						  
							  		get_roombyroomtype(get_roomtype($_POST['roomtypeid']),$room);							  	
								  	foreach($room as $rm){
								  		if(isset($bookings['roomid'])&&$bookings['roomid']==$rm['roomid']){
							  				print "<option value='" . $rm['roomid'] ."' ";
										  	print " selected='selected'" ;
										  	print ">";
											print $rm['roomno'];
											print "</option>";
								  		}else{
									  		if($rm['status']==VACANT){								  			
											  	print "<option value='" . $rm['roomid'] ."'>";
												print $rm['roomno'];
												print "</option>";
									  		}
								  		}
									 }
							  	}
							  }else if($restarget==3){	
								echo '<select name="roomid" id="roomid" class="plainDropDown" onchange="fixroomtype();document.forms[0].submit();">';
								echo '<option value="0" > </option>';
							  	$room =array();								  					 
							  	if(isset($_POST['ratesid'])&&!empty($_POST['ratesid'])){	
							  		get_rooms_byrateid($_POST['ratesid'],$room);
							  		foreach($room as $rm){
							  			if(isset($bookings['roomid'])&&$bookings['roomid']==$rm['roomid']){
							  				print "<option value='" . $rm['roomid'] ."' ";
										  	print " selected='selected'" ;
										  	print ">";
											print $rm['roomno'];
											print "</option>";
							  			}else{
									  		if($rm['status']==VACANT){
											  	print "<option value='" . $rm['roomid'] ."'>";
												print $rm['roomno'];
												print "</option>";
									  		}
							  			}
									 }
							  	}
							  }else if($restarget==0||$restarget==1){	
								echo '<select name="roomid" id="roomid" class="plainDropDown" onchange="document.forms[0].submit();">';
								echo '<option value="0" > </option>';
							    	$criteria = "status='".VACANT."'";								  	
								  	if(isset($_REQUEST['roomid'])) $selected=$_REQUEST['roomid']; else $selected=$bookings['roomid'];
								  	if($bookings['roomno']){
									  	print "<option value='" . $bookings['roomid'] ."' selected='selected'>";
										print $bookings['roomno'];
										print "</option>";
								  	}
									populate_roomselect($bookings['checkindate'], $bookings['checkoutdate'], $selected);
									//populate_select("rooms","roomid","roomno",$selected, $criteria);
							  }
							  ?>
						    </select>
						  </td>
						  <td></td>
						  <td align=left><?php  
								if($restarget==2||$restarget==1 ){ 
							   	  $rmid="";
							   	  $selected=0;
								  $rates=array();
								  if(isset($bookings['roomid'])) $rmid=$bookings['roomid'];
								  if(isset($_REQUEST['roomid'])) $rmid=$_REQUEST['roomid'];
								  if(isset($bookings['rates_id'])) $selected=$bookings['rates_id'];
								  // post variable overrides booking variable
								  if(isset($_POST['ratesid']) && $_POST['ratesid'] > 0) $selected=$_POST['ratesid'];
								  
								  get_ratebyroomid($rmid,$rates,$selected);
								  ?>
								<select id=ratesid name=ratesid class="plainDropDown"  >
									<option value='0'> </option>
								 <?php 
								foreach($rates as $rt){
								  	print "<option value='" . $rt['rateid'] ."' ";
								  	if($selected == $rt['rateid']) print " selected=selected>";
								  	else print ">";
									print $rt['ratecode'];
									print "</option>";
								  }
								  ?>
								</select>
								<?php } ?>
							</td>
						  
						</tr>
						
				</table>
				
				</td>
				</tr>
				</table>
				<table>
				<tr><td>&nbsp;&nbsp;</td></tr>
						<tr><td colspan=4 align=center valign=bottom ><h1><?php echo $_L['REG_notes']; ?></h1></td></tr>
						<tr><td colspan=4 valign=top ><textarea cols=73 rows=3 name="instructions" ><?php echo $bookings['instructions']; ?></textarea></td></tr>
				</table>
				</div>
				</div>
				<!-- DIV FOR Guest DETAILS -->
				<div  class="TabbedPanelsContent">
				
					<table border="0">
						<tr><td>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
						<tr><td>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
						<tr>
						<td>
							<table>
							<tr>
							  <td style="padding:10px" align=left><?php echo $_L['GST_Guest']; ?></td>
							  <td><input type="text" name="guestname" id="guestname" size=15 maxlength=50 value="<?php echo $bookings['guestname']; ?>" /> 
								<?php if(! $bookings['guestid']) { ?>
								<select name=tguestid id=tguestid class=narrowDropDown onchange="updateguestname();" <?php echo $mscludge; ?> >
								  <option value=0 title="Select an Option" > </option>
								  <?php 
								   if(is_ebridgeCustomer()){
										populate_select("advprofile", "profileid", "firstname,lastname", $bookings['guestid'], "");  
									} else {
										populate_select("guests", "guestid", "firstname,lastname", $bookings['guestid'], ""); 
									}
								  ?>
								</select>
								<?php } ?>
							  </td>
							 </tr>
							 <tr>
							 <td style="padding:10px" ><?php echo $_L['GST_id']; ?></td>
							 <td valign=top ><input type=text size=15 maxlength=30 name=localid  value="<?php echo $bookings['pp_no']; ?>" /></td>
							 </tr>
							 <tr>
							 <td style="padding:10px"  align=left><?php echo $_L['GST_phone']; ?> </td>
							 <td><input size=15 maxlength=30 type=text name=phone value="<?php echo $bookings['phone']; ?>" /></td>
							 </tr>
							 <tr>
							 <td style="padding:10px" align=left><?php echo $_L['GST_email']; ?> </td>
							 <td><input type=text name=email value="<?php echo $bookings['email']; ?>" /></td>
							 </tr>
							 </table>
						</td><td>
						<table>
							<tr>
							  <td style="padding:10px" valign=bottom ><?php echo $_L['GST_street']; ?><br/><textarea name=address cols=40 rows=2 ><?php echo $bookings['address']; ?></textarea></td>
							</tr>
							<tr>
							<td >
							
							
							<table>
							<tr>
							<td style="padding:10px" align=left ><?php echo $_L['GST_pcode']; ?><br/><input type=text size=3 name=postal_code value='<?php echo $bookings['postal_code']; ?>' /> </td>
							<td align=left ><?php echo $_L['GST_city']; ?> <br/><input type=text size=15 name=town value="<?php echo $bookings['town']; ?>" /> </td>
							</tr>
							<tr>
							<td style="padding:10px" ><?php echo $_L['GST_country']; ?><br/>
							<select name="countrycode" class=plainDropDown>
							  <option value=""><?php echo $_L['GST_selcountry']; ?></option>
							  <?php populate_select("countries","countrycode","country",$bookings['countrycode'], "");?>
							</select> 
							</td>
							
							<td><?php echo $_L['GST_nationality']; ?><br/>
							<select name="nationality" class=plainDropDown >
							  <option value=""><?php echo $_L['GST_selcountry']; ?></option>
							  <?php populate_select("countries","countrycode","country",$bookings['nationality'], "");?>
							</select>
							</td>
							</tr>
							
							</table>
						
						
						</td>
						</tr>
						
						</table>
						</td>
						</tr>
					</table>
				
				</div>
				<?php if(is_ebridgeCustomer()){?>
				<!-- DIV FOR custom DETAILS -->
				<div  class="TabbedPanelsContent">
				
				<table>
				
				<tr><td>&nbsp;</td></tr>
				 <?php
						if(is_ebridgeCustomer()){
							include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
							$offset = 150;
								
							print "<tr><td colspan=5>\n";
							CustomPagesFormPrint(HTL_PAGE_BOOK, $bookings['book_id'], 500, 350);
							print "</td></tr>\n";		
						}
					  ?>
				</table>
				
				</div>
				<?php }?>
				<!-- DIV FOR custom DETAILS -->
				<div  class="TabbedPanelsContent">
					<table border="0">
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						  <td>
							<select name=cctype id=cctype class=plainDropDown >
								<option value="CA" <?php if($bookings['cctype'] == "CA") echo "selected"; ?> ><?php echo $_L['CC_CA']; ?></option>
								<option value="DC" <?php if($bookings['cctype'] == "DC") echo "selected"; ?> ><?php echo $_L['CC_DC']; ?></option>
								<option value="AX" <?php if($bookings['cctype'] == "AX") echo "selected"; ?> ><?php echo $_L['CC_AX']; ?></option>
								<option value="VI" <?php if($bookings['cctype'] == "VI") echo "selected"; ?> ><?php echo $_L['CC_VI']; ?></option>
								<option value="JCB" <?php if($bookings['cctype'] == "JCB") echo "selected"; ?> ><?php echo $_L['CC_JCB']; ?></option>
								<option value="EC" <?php if($bookings['cctype'] == "EC") echo "selected"; ?> ><?php echo $_L['CC_EC']; ?></option>
							</select>
						  </td>
						  <td><?php echo $_L['RSV_cardno']; ?><input type="text" name="CCnum" id="CCnum" size=16 maxlength=19 onchange="CheckCardNumber('cctype','CCnum','expiry');" value='<?php echo $bookings['CCnum']; ?>' /></td>
						  <td><?php echo $_L['RSV_expiry']; ?> <input type="text" name="expiry" id="expiry" size=4 maxlength=4 onchange="CheckCardNumber('cctype','CCnum','expiry');" value='<?php echo $bookings['expiry']; ?>' /></td>
						  <td><?php echo $_L['RSV_CVV']; ?> <input type="text" name="CVV" size=4 maxlength=4 value='<?php echo $bookings['CVV']; ?>' /></td>
						</tr>
						
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
					  <td align=center valign=bottom ><h2><?php echo $_L['REG_inby']; ?></h2></td>
					  <td></td>
					  <td align=center valign=bottom ><h2><?php echo $_L['REG_outby']; ?></h2></td>
					  <td></td>
					</tr>
					<?php if ( $bookings['book_id'] && !$bookings['checkedin_by'] ) {  ?> 
					<tr>
					  <td></td>
					  <td><input type='submit' name='Submit' value='<?php echo $_L['REG_checkinbox']; ?>' /></td>
					  <td></td>
					  <td></td>
					</tr>
					<?php } else if( $bookings['book_id'] && $bookings['checkedin_by'] ){ ?>
						<tr>
						  <td align=right><?php echo $_L['REG_name']; ?></td>
						  <td><input type="hidden" size=15 name="checkedin_by" value="<?php echo $bookings["checkedin_by"]; ?>"  /> 
							  <input type="text" size=15 name="checkedin_byname" value="<?php echo trim(get_username($bookings["checkedin_by"])); ?>" readonly="readonly" />
						  </td> 
						  <?php if( get_bookingstatus($book_id) != BOOK_CHECKEDIN) { ?>
						  <td align=right><?php echo $_L['REG_name']; ?></td>
						  <td><input type="hidden" size=15 name="checkedout_by" value="<?php echo $bookings["checkedout_by"]; ?>" />
							  <input type="text" size=15 name="checkedout_byname" value="<?php echo trim(get_username($bookings["checkedout_by"])); ?>" readonly="readonly" />

						  </td> 
						  <?php } else { ?>
						 
						  <td><input type='submit' name='Submit' class="button" value='<?php echo $_L['REG_checkoutbox']; ?>' onclick="return confirm('<?php echo $_L['REG_checkoutbox']; ?> ?')"/></td>
						   <td>&nbsp;</td>
						  <?php } ?>
						</tr>
					<?php } ?>
					<?php if( $bookings['book_id'] && $bookings['checkedin_by'] ){ ?>
						<tr>
						  <td align=right><?php echo $_L['REG_date']; ?></td>
						  <td><input type="text" size=15 name="checkedin_date" value="<?php echo trim($bookings["checkedin_date"]); ?>" readonly="readonly" /></td> 
						  <?php if( get_bookingstatus($book_id) != BOOK_CHECKEDIN) { ?>
						  <td align=right><?php echo $_L['REG_date']; ?></td>
						  <td><input type="text" size=15 name="checkedout_date" value="<?php echo trim($bookings["checkedout_date"]); ?>" readonly="readonly" />
						  
						  </td> 
						  <?php } else { ?>
						  <td>&nbsp;</td>
						  <td></td>
						  <?php } ?>
						</tr>
						<?php } ?>
					</table>
				</div>
				
				<!-- TAB Summary FIELD-->
				<div class="TabbedPanelsContent">
						<?php $amp = ""; 
						$uri = "reservation_summary.php?";
						if($bookings['book_id']) { $uri .= "bid=".$bookings['book_id']; $amp="&"; }
						if($bookings['checkindate']) { $uri .= $amp."in=".urlencode($bookings['checkindate']); $amp="&"; }
						if($bookings['checkoutdate']) { $uri .= $amp."out=".urlencode($bookings['checkoutdate']); $amp="&"; }
						if($bookings['guestid']) { $uri .= $amp."guest=".$bookings['guestid']; $amp="&"; }
						if($bookings['roomid']) { $uri .= $amp."room=".$bookings['roomid']; $amp="&"; }
						if($bookings['roomtypeid']) { $uri .= $amp."roomtype=".$bookings['roomtypeid']; $amp="&"; }
						if($bookings['rates_id']) { $uri .= $amp."rate=".$bookings['rates_id']; $amp="&"; }
					  ?>
					 <div class="scroll" id=res_summary src="<?php echo $uri;	?>">
						<iframe frameborder="0"  src="<?php echo $uri; ?>" width="100%" height="380px" >
							<p>Your browser does not support iframes.</p>
							<a href="<?php echo $uri; ?>" > Click this uri <?php echo $uri; ?> </a>
						</iframe>
					 </div>
				</div>				
			</div>
			</div>
			
			<div class="btngroup2" align=right>
						  <?php if( get_bookingstatus($book_id) != BOOK_CHECKEDIN) { 
							  
								if(isset($_SESSION["admin"]) && $_SESSION["admin"]) { ?>
						  <input class="button" type='submit' name='Submit' value='<?php echo  $_L['REG_Rcheckout']; ?>' onclick="return confirm('<?php echo $_L['REG_Rcheckout'];?> ?')"/>
						  <?php } 
						  } else { ?>
						 
						  <input type='submit' name='Submit' class="button" value='<?php echo $_L['REG_checkoutbox']; ?>' onclick="return confirm('<?php echo $_L['REG_checkoutbox']; ?> ?')"/>
						  <?php } ?>				
					<input class="button" type="button" name="Submit" value="<?php echo $_L['BTN_list']; ?>" onclick="self.location='index.php?menu=listbooking'"/>
				 <?php if (!$bookings['book_id']) { ?>
					<input  type="submit" name="Submit" class="button" value="<?php echo $_L['REG_register']; ?>" />
				  <?php } else { 
				  $reguri = "registrationform.php?";
				  if($bookings['book_id']) { $reguri .= "bid=".$bookings['book_id']; $amp="&"; }
				  if($bookings['checkindate']) { $reguri .= $amp."in=".urlencode($bookings['checkindate']); $amp="&"; }
				  if($bookings['checkoutdate']) { $reguri .= $amp."out=".urlencode($bookings['checkoutdate']); $amp="&"; }
				  if($bookings['guestid']) { $reguri .= $amp."guest=".$bookings['guestid']; $amp="&"; }
				  if($bookings['roomid']) { $reguri .= $amp."room=".$bookings['roomid']; $amp="&"; }
				  if($bookings['roomtypeid']) { $reguri .= $amp."roomtype=".$bookings['roomtypeid']; $amp="&"; }
				  if($bookings['rates_id']) { $reguri .= $amp."rate=".$bookings['rates_id']; $amp="&"; }
						  
				  ?>
				 <input class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_update']; ?>"/> <input type ="button" class="button" name="regform" value="<?php echo $_L['BTN_registration']; ?>" onclick="window.open('<?php echo $reguri;?>','Registration Form','');" />
					
				  <?php } ?>
				 
			</div>
			</td>
			</tr>
			</table>
			</td>
			
		  </tr>
		  
		</table>
	  </form>
      <?php 
      if (is_ebridgeCustomer()) { //???
		$ssl="";
		if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }

      	$MISreturnURL = 'http'.$ssl.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?id=".$bookings['book_id'];
      	create_link_form($bookings['guestid'],$bookings['book_id'],$bookings['bill_id'],$bookings['reservation_id'],$bookings['voucher_no'],$MISreturnURL);
      }
      ?>
	</body>
</html>
<?php
/**
 * @}
 * @}
 */
 ?>