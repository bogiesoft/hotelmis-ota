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
error_reporting(E_ALL&~ E_NOTICE);
ob_start();
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
//print_r($_POST);
$logofile=Get_LogoFile();
access("reservation");
$lang = get_language();
load_language($lang);
date_default_timezone_set(TIMEZONE);
$today = date("d/m/Y");
$tomorrow = date("d/m/Y", time() + (24*60*60));
$reservation = array();
$details = array();
$resdetailcount=0;
$guestid=0;
$booked_by_ebridgeid="";
$bookref = "";
$bookrefid = 0;
$offset = 0;
if(isset($_GET["resid"])&&!empty($_GET["resid"])) {
  $resid = $_GET['resid'];
}
if(isset($_POST["reservation_id"])&&!empty($_POST["reservation_id"])) {
  $resid = $_POST['reservation_id'];
}
if(isset($_POST['guestid']) && $_POST['guestid'] > 0) {
	$guestid=$_POST['guestid'];
	$reservation['guestid'] = $guestid;
}
if(!$guestid && isset($_GET['guestid']) && $_GET['guestid'] > 0) {
	$guestid=$_GET['guestid'];
	$reservation['guestid'] = $guestid;
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

if(!$guestid && isset($_POST['NewLastName']) && $_POST['NewLastName'] != '') {
    $firstname = (isset($_POST['NewFirstName'])) ?$_POST['NewFirstName']:"";
	$lastname = (isset($_POST['NewLastName'])) ?$_POST['NewLastName']:"";
    $countrycode = (isset($_POST['NewCountryCode'])) ?$_POST['NewCountryCode']:"";
    $areacode = (isset($_POST['NewAreaCode']))? $_POST['NewAreaCode']:"";
    $phone = (isset($_POST['NewTelephone'])) ?$_POST['NewTelephone']:"";
    $pp_no = (isset($_POST['NewDocID")'])) ? $_POST['NewDocID']:"";
    $email = (isset($_POST['NewEmail'])) ? $_POST['NewEmail']:"";
	if(is_ebridgeCustomer()){
		//echo "add new guest";
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		//function modify_advProfile($id,$parentid,$salutation,$firstname ,$middlename ,
  		//			$lastname ,$dob ,$gender,$lang ,$altlang ,$ebridgeid,$comments
		$profid = modify_advProfile(0,0,'1',$firstname ,'' ,$lastname ,'1970-01-01' ,'M','en' ,'en' ,'','');
		if($profid) {
			modify_advPhone(0,0,$profid,0,0,0,$countrycode,$areacode,$telephone,'');
			modify_advEmail(0,0,$profid,0,0,$email,'');
			modify_advDocument($docid,0,$profid,$doctype,$validfrom,$validto,$issuer,$issuecountry,$nationality,$nameondoc,$docnum,$issuelocation);
		}
		$guestid=$profid;
	} else {	
		$guestid= modify_guest(0,$lastname,$firstname,'',1,
							$pp_no,'','','','','','','',$areacode,$phone,$email,
							'', '', '', '');
	}
	$reservation['guestid'] = $guestid;
}

$bill_id = get_billID_byResID($resid);

$booked_by_ebridgeid = get_ebridgeID_fromOperatorSetup();
if(isset($_POST['Submit']) || isset($_POST['remove_id'])&&$_POST['remove_id']!=0 || isset($_POST['add_id'])&&$_POST['add_id']!=0){
	if(isset($_POST['remove_id'])) $action="remove_rate";
	if(isset($_POST['add_id'])) $action="add_rate";
	if(isset($_POST['Submit'])){
		$action=$_POST['Submit'];
	}
	
	switch($action) {
    case $_L['BTN_add']:
    case $_L['BTN_update']:
    case "remove_rate":
    case "add_rate":
    	
	$fv=new formValidator(); //from functions.php
	if(isset($_POST['add_id'])&&$_POST['add_id']!=0){
		$opnumber =$_POST['add_id'];
		
		if (isset($_POST["ratesid_".$opnumber])&&empty($_POST["ratesid_".$opnumber]))
			$fv->addErrormsg($_L['RSV_ratecode_err']);
		if (isset($_POST["roomid_".$opnumber])&&empty($_POST["roomid_".$opnumber]))
			$fv->addErrormsg($_L['RSV_roomnum_err']);
		if (isset($_POST["roomtypeid_".$opnumber])&&empty($_POST["roomtypeid_".$opnumber]))
			$fv->addErrormsg($_L['RSV_roomtype_err']);
	}
	if($fv->checkErrors()){
		$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
	}
	else {
		
	
    //if guest has not been selected exit
    // instantiate form validator object
		$userid=$_SESSION["userid"];
		$deposit_made = !empty($_POST["deposit_made"])?$_POST["deposit_made"]:0;
		$resid=!empty($_POST["reservation_id"])?$_POST["reservation_id"]:0;
		$guestid=!empty($_POST["guestid"]) ?  $_POST["guestid"] : '';
		$res_by=!empty($_POST["reservation_by"])? $_POST["reservation_by"] :'';
		$rsvd_by=!empty($_POST["reserved_by"])? $_POST['reserved_by'] : $userid;
		$phone=!empty($_POST["phone"])? $_POST['phone'] : '';
		$res_by_phone=!empty($_POST["reservation_by_phone"])? $_POST["reservation_by_phone"] :'';
		$checkindate=!empty($_POST["checkindate"])?$_POST["checkindate"]:$today;
		$checkoutdate=!empty($_POST["checkoutdate"])?$_POST["checkoutdate"]:$tomorrow;
		$no_adults=!empty($_POST["no_adults"])?$_POST["no_adults"]:0;
		$no_child1_5=!empty($_POST["no_child1_5"])?$_POST["no_child1_5"]:0;
		$no_child6_12=!empty($_POST["no_child6_12"])?$_POST["no_child6_12"]:0;
		$no_babies=!empty($_POST["no_babies"])?$_POST["no_babies"]:0;
		$instructions=!empty($_POST["instructions"])?$_POST["instructions"]:'';
		$ccnum=!empty($_POST["CCnum"])?$_POST["CCnum"]:'';
		$cctype=!empty($_POST["cctype"])?$_POST["cctype"]:'';
		$expiry=!empty($_POST["expiry"])?$_POST["expiry"]:'';
		$cvv=!empty($_POST["cvv"])?$_POST["cvv"]:'';
		$agentid=!empty($_POST["agentid"])?$_POST["agentid"]:'';
		$vchr=(!empty($_POST["voucher_no"]) && $_POST["voucher_no"] <> "NEW" )?$_POST["voucher_no"]:'';
		//a need to check if the session value exists
		$reserved_date=!empty($_POST["reserved_date"])?$_POST["reserved_date"]:$today;
		$confirmed_by=!empty($_POST["confirmed_by"])?$_POST["confirmed_by"]:0;
		$confirmed_date=!empty($_POST["confirmed_date"])?$_POST["confirmed_date"]:$today;
		$resdtime = !empty($_POST["reserve_time"])?$_POST["reserve_time"]:0;
		$bill_id = !empty($_POST["bill_id"])?$_POST["bill_id"]:0;
		$book_id = !empty($_POST["book_id"])?$_POST["book_id"]:0;
		$src = !empty($_POST["src"])?$_POST["src"]:'';
		$status = !empty($_POST['status'])?$_POST['status']:0;
		if($confirmed_by && $status == RES_QUOTE) $status = RES_ACTIVE;
		$no_nights = !empty($_POST['no_nights'])?$_POST['no_nights']:0;
		$bookref = !empty($_POST['bookref'])?$_POST['bookref']:0;
		$bookrefid = !empty($_POST['bookrefid'])?$_POST['bookrefid']:0;
	
		$roomid=0;
		$roomtypeid=0;
		$ratesid=0;
			
		if(isset($_POST['add_id'])&&$_POST['add_id']!=0){
			$opnumber =$_POST['add_id'];
			$roomid=isset($_POST["roomid_".$opnumber])?$_POST["roomid_".$opnumber]:0;
			$roomtypeid=isset($_POST["roomtypeid_".$opnumber])?$_POST["roomtypeid_".$opnumber]:0;
			$ratesid=isset($_POST["ratesid_".$opnumber])?$_POST["ratesid_".$opnumber]:0;
		}
		
		
		// New reservation reservation id = 0.
		$fop = !empty($_POST['fop'])?$_POST['fop']:FOP_CASH;
		$amt = !empty($_POST['amt'])?$_POST['amt']:0;
		$svc = 0;
		$tax = 0;
		$totamt = $amt;
		$auth = '';
		$name = $_POST['guestname'];
		if (($amt>0)&&($status==RES_QUOTE)){
			$status = RES_ACTIVE;
		}
		$resid=modify_reservation($resid, $src, $guestid, $phone, $vchr, $agentid,
		$res_by,$res_by_phone, $checkindate, $checkoutdate, 
		$no_adults, $no_child1_5, $no_child6_12, $no_babies,
		$instructions,$ccnum, $cctype, $expiry, $cvv,
		$rsvd_by,$reserved_date,$confirmed_by, $confirmed_date, 
		$roomid, $roomtypeid, $ratesid, $resdtime, $book_id, $status, $bill_id, $fop, $amt,$booked_by_ebridgeid,"","");
		if(is_ebridgeCustomer()){
			include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
			CustomPagesFormRead( HTL_PAGE_RES, $resid);
		}
		$resdetailID=0;
		if(!$ratesid&&$resid){
			$reservation = array();
			get_reservation($resid, $reservation,0);
			$currency=get_Currency_byRateID($reservation['ratesid']);
		}else{
			$currency=get_Currency_byRateID($ratesid);
		}		
		if(!$currency){
			$currency = get_defaultcurrencycode();
		}
		modify_agent_bookingref($bookrefid, $resid, $agentid, $bookref);
		if(!$resid) {
			echo "<div align=\"center\"><h1>".$_L['RSV_resvnotmade_err']."</h1></div>";
		} else {
			if(isset($_POST['add_id'])&&$_POST['add_id']!=0){
				$quantity=1;
				$resdetailID = modify_reservation_details(0,$resid,$roomid,$roomtypeid,$ratesid,$quantity,$status);
			}else if(isset($_POST['remove_id'])&&$_POST['remove_id']!=0){
				//delete
				delete_resdetails($_POST['remove_id']);
			}			
			$details=array();
			$resdetailcount = reservation_details_byResID($resid,$details);	
			if($status==RES_CHECKIN){				
				foreach($details as $dt) {
					update_resDetails_status($dt['id'], RES_CHECKIN);
				}
			}	
			$bill_id = get_billID_byResID($resid);
					
			echo "<div align=\"center\"><h1>".$_L['RSV_resv_sccss']." ID = ".$resid."</h1></div>";
			if($status == RES_ACTIVE && $bill_id){
				//recording the deposit
				if ($amt>0){
					if(!$deposit_made)	{
						modify_receipt(0, $bill_id, 0, $resid, 0, $resdtime, $fop, $cctype, $ccnum, $expiry, $cvv, $auth, 'Reservation Deposit', $amt, $userid, '',1,$currency,$currency);
					}

					if(isset($_POST['payinadvance'])&& $resdetailID){

						if(is_ebridgeCustomer()){
							include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
							
							process_advancebilling($bill_id, $ratesid, $checkindate, $checkoutdate,$userid, $roomtypeid );

						} else { 

							$ratedetails = array();
							if($ratesid) get_rateitems($ratesid,$ratedetails);
							$curr = get_Currency_byRateID($ratesid);
							//print_r($ratedetails);
							$roomamt = 0;
							$totamt = $roomamt;
							$totsvc = 0;
							$tottax = 0;
							$stdsvc=0;
							$stdtax=0;
							foreach ($ratedetails as $ratedetail){
								$roomamt = $roomamt + $ratedetail['discountvalue'];
								$svc = 0;
								if (!$ratedetail['service'])
									$svc = $svc + ($ratedetail['discountvalue'] * SVCPCT/100);								
								$tax = 0;
								if (!$ratedetail['tax'])
									$tax = $tax + ($ratedetail['discountvalue'] * TAXPCT/100);
								$newamt = $ratedetail['discountvalue'] + $svc + $tax;
								$totamt = $totamt + $newamt;
								$totsvc = $totsvc + $svc;
								$tottax = $tottax + $tax;
							}
							$newCheckinDate = $checkindate;
							for($dc=0;$dc<$no_nights;$dc++){
								modify_transaction(0, $bill_id, 1, $reserved_date, $newCheckinDate, $userid, $roomamt, $totsvc, $tottax, $roomamt, $totsvc, $tottax, 1, $ratesid, '', $totamt,$curr);
								$newCheckinDate = date("Y-m-d", strtotime('+1 day', strtotime($newCheckinDate)));
							}
						}
					}else if(isset($_POST['payinadvance'])&&!$resdetailID &&($resdetailcount>0)){
						if(is_ebridgeCustomer()){
							include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");

							foreach($details as $dt) {
								process_advancebilling($bill_id, $dt['ratesid'], $checkindate, $checkoutdate,$userid,$roomtypeid);
							}
						} else { 
							foreach($details as $dt){
								$rtid = $dt['ratesid'];
								$ratedetails = array();
								if($rtid) get_rateitems($rtid,$ratedetails);
								$curr = get_Currency_byRateID($rtid);
								//print_r($ratedetails);
								$roomamt = 0;
								$totamt = $roomamt;
								$totsvc = 0;
								$tottax = 0;
								$stdsvc=0;
								$stdtax=0;
								foreach ($ratedetails as $ratedetail){
									$roomamt = $roomamt + $ratedetail['discountvalue'];
									$svc = 0;
									if (!$ratedetail['service'])
										$svc = $svc + ($ratedetail['discountvalue'] * SVCPCT/100);
									$tax = 0;
									if (!$ratedetail['tax'])
										$tax = $tax + ($ratedetail['discountvalue'] * TAXPCT/100);
									$newamt = $ratedetail['discountvalue'] + $svc + $tax;
									$totamt = $totamt + $newamt;
									$totsvc = $totsvc + $svc;
									$tottax = $tottax + $tax;
								}
								$newCheckinDate = $checkindate;
								for($dc=0;$dc<$no_nights;$dc++){
									modify_transaction(0, $bill_id, 1, $reserved_date, $newCheckinDate, $userid, $roomamt, $totsvc, $tottax, $roomamt, $totsvc, $tottax, 1, $rtid, '', $totamt,$curr);
									$newCheckinDate = date("Y-m-d", strtotime('+1 day', strtotime($newCheckinDate)));
								}
							}
						}
					}
				}
			}else if($status == RES_ACTIVE && !$bill_id){
				// Must create a Bill before recording the deposit
				$bill_id=create_reservation_bill($resid, $userid, $guestid);
				if ($bill_id){
					if ($amt>0){ 
						if(!$deposit_made) {
							modify_receipt(0, $bill_id, 0, $resid, 0, $resdtime, $fop, $cctype, $ccnum, $expiry, $cvv, $auth, 'Reservation Deposit', $amt, $userid, '',1,$currency,$currency);
						}
						if(isset($_POST['payinadvance'])&& $resdetailID){
							$ratedetails = array();
							if($ratesid) get_rateitems($ratesid,$ratedetails);
							$curr = get_Currency_byRateID($ratesid);
							//print_r($ratedetails);
							$roomamt = 0;
							$totamt = $roomamt;
							$totsvc = 0;
							$tottax = 0;
							$stdsvc=0;
							$stdtax=0;
							foreach ($ratedetails as $ratedetail){
								$roomamt = $roomamt + $ratedetail['discountvalue'];
								$svc = 0;
								if (!$ratedetail['service'])
									$svc = $svc + ($ratedetail['discountvalue'] * SVCPCT/100);
								$tax = 0;
								if (!$ratedetail['tax'])
									$tax = $tax + ($ratedetail['discountvalue'] * TAXPCT/100);
								$newamt = $ratedetail['discountvalue'] + $svc + $tax;
								$totamt = $totamt + $newamt;
								$totsvc = $totsvc + $svc;
								$tottax = $tottax + $tax;
							}
							$newCheckinDate = $reserved_date;
							for($dc=0;$dc<$no_nights;$dc++){
								modify_transaction(0, $bill_id, 1, $reserved_date, $newCheckinDate, $userid, $roomamt, $totsvc, $tottax, $roomamt, $totsvc, $tottax, 1, $ratesid, '', $totamt,$curr);
								$newCheckinDate = date("Y-m-d", strtotime('+1 day', strtotime($newCheckinDate)));
							}
						}else if(isset($_POST['payinadvance'])&&!$resdetailID &&($resdetailcount>0)){
							foreach($details as $dt){
								$rtid = $dt['ratesid'];
								$ratedetails = array();
								if($rtid) get_rateitems($rtid,$ratedetails);
								$curr = get_Currency_byRateID($rtid);
								//print_r($ratedetails);
								$roomamt = 0;
								$totamt = $roomamt;
								$totsvc = 0;
								$tottax = 0;
								$stdsvc=0;
								$stdtax=0;
								foreach ($ratedetails as $ratedetail){
									$roomamt = $roomamt + $ratedetail['discountvalue'];
									$svc = 0;
									if (!$ratedetail['service'])
										$svc = $svc + ($ratedetail['discountvalue'] * SVCPCT/100);
									$tax = 0;
									if (!$ratedetail['tax'])
										$tax = $tax + ($ratedetail['discountvalue'] * TAXPCT/100);
									$newamt = $ratedetail['discountvalue'] + $svc + $tax;
									$totamt = $totamt + $newamt;
									$totsvc = $totsvc + $svc;
									$tottax = $tottax + $tax;
								}
								$newCheckinDate = $checkindate;
								for($dc=0;$dc<$no_nights;$dc++){
									modify_transaction(0, $bill_id, 1, $reserved_date, $newCheckinDate, $userid, $roomamt, $totsvc, $tottax, $roomamt, $totsvc, $tottax, 1, $rtid, '', $totamt,$curr);
									$newCheckinDate = date("Y-m-d", strtotime('+1 day', strtotime($newCheckinDate)));
								}
							}
						}
					}
				}
				if(!$bill_id) {
					echo "<div align=\"center\"><h1>".$_L['RSV_bill_err']."</h1></div>";
				}else {
					echo "<div align=\"center\"><h1>".$_L['RSV_bill_sccss']."</h1></div>";
				}
			}else {
				echo "<div align=\"center\"><h1>".$_L['RSV_billater_err']."</h1></div>";
			}
		}
		break;
	}
    case 'List':
		break;
	}
}
$reservation['reserved_by'] = $_SESSION["userid"];
$reservation['reserved_name'] = $_SESSION["employee"];
$vch = "NEW";
$reservation['no_adults'] = 1;
$reservation['no_child1_5'] = 0;
$reservation['no_child6_12'] = 0;
$reservation['no_babies'] = 0;
$paydetail = array();
if($resid) {
	$reservation = array();
	if(get_reservation($resid, $reservation,0)) {
		$vch = $reservation['voucher_no'];
		$guestid = $reservation['guestid'];
	}
}
 
// Just pull up the guest detail.
if($guestid > 0) {
	$guest = array();
	findguestbyid($guestid, $guest);
	if(!$reservation['guestname']) {
		if(strncmp($guest['salutation'],  $guest['guest'], strlen($guest['salutation']))==0) {
			$reservation['guestname'] = $guest['guest'];
		} else {
			$reservation['guestname'] = $guest['salutation']. " ". $guest['guest'];
		}
	}
	if(!$reservation['address']) $reservation['address'] = $guest['address'];
	if(!$reservation['town']) $reservation['town'] = $guest['town'];
	if(!$reservation['postal_code']) $reservation['postal_code'] = $guest['postal_code'];
	if(!$reservation['phone']) $reservation['phone'] = $guest['phone'];
	if(!$reservation['email']) $reservation['email'] = $guest['email'];
	if(!$reservation['IM']) $reservation['IM'] = $guest['IM'];
	if(!$reservation['pp_no']) $reservation['pp_no'] = $guest['pp_no'];			// ???
 }
 

if($_POST['voucher_no'] && !$vch) $vch = $_POST['voucher_no'];
if($_POST['reservation_id'] && !$reservation['reservation_id']) $reservation['reservation_id'] = $_POST['reservation_id'];
if($_POST['guestid'] && !$reservation['guestid']) $reservation['guestid'] = $_POST['guestid'];
if($_POST['src'] && !$reservation['src']) $reservation['src'] = $_POST['src'];
if($_POST['agentid'] && !$reservation['agentid']) $reservation['agentid'] = $_POST['agentid'];
if($_POST['guestname'] && !$reservation['guestname']) $reservation['guestname'] = $_POST['guestname'];
if($_POST['phone'] && !$reservation['phone']) $reservation['phone'] = $_POST['phone'];
if($_POST['reservation_by'] && !$reservation['reservation_by']) $reservation['reservation_by'] = $_POST['reservation_by'];
if($_POST['reservation_by_phone'] && !$reservation['reservation_by_phone']) $reservation['reservation_by_phone'] = $_POST['reservation_by_phone'];
if($_POST['checkindate'] && (!$reservation['checkindate'] || $reservation['checkindate'] != $_POST['checkindate'])) $reservation['checkindate'] = $_POST['checkindate'];
if($_POST['checkoutdate'] && (!$reservation['checkoutdate'] || $reservation['checkoutdate'] != $_POST['checkoutdate'])) $reservation['checkoutdate'] = $_POST['checkoutdate'];
//if($_POST['checkoutdate'] && !$reservation['checkoutdate']) $reservation['checkoutdate'] = $_POST['checkoutdate'];
if($_POST['no_nights'] && !$reservation['no_nights']) $reservation['no_nights'] = $_POST['no_nights'];
if($_POST['no_adults'] && !$reservation['no_adults']) $reservation['no_adults'] = $_POST['no_adults'];
if($_POST['no_child1_5'] && !$reservation['no_child1_5']) $reservation['no_child1_5'] = $_POST['no_child1_5'];
if($_POST['no_child6_12'] && !$reservation['no_child6_12']) $reservation['no_child6_12'] = $_POST['no_child6_12'];
if($_POST['no_babies'] && !$reservation['no_babies']) $reservation['no_babies'] = $_POST['no_babies'];
if($_POST['restarget'] && !$restarget) $restarget = $_POST['restarget'];
if($_POST['ratesid'] && !$reservation['ratesid']) $reservation['ratesid'] = $_POST['ratesid'];
if($_POST['roomid'] && !$reservation['roomid']) $reservation['roomid'] = $_POST['roomid'];
if($_POST['roomtypeid'] && !$reservation['roomtypeid']) $reservation['roomtypeid'] = $_POST['roomtypeid'];
if($_POST['ratesid'] && !$reservation['ratesid']) $reservation['ratesid'] = $_POST['ratesid'];
if($_POST['cctype']) $reservation['cctype'] = $_POST['cctype'];
if($_POST['CCnum'] && !$reservation['CCnum']) $reservation['CCnum'] = $_POST['CCnum'];
if($_POST['CVV'] && !$reservation['CVV']) $reservation['CVV'] = $_POST['CVV'];
if($_POST['reserved_by'] && !$reservation['reserved_by']) $reservation['reserved_by'] = $_POST['reserved_by'];
if($_POST['reserved_date'] && !$reservation['reserved_date']) $reservation['reserved_date'] = $_POST['reserved_date'];
if($_POST['email'] && !$reservation['email']) $reservation['email'] = $_POST['email'];
if($_POST['IM'] && !$reservation['IM']) $reservation['IM'] = $_POST['IM'];
if($_POST['instructions'] && !$reservation['instructions']) $reservation['instructions'] = $_POST['instructions'];
if($_POST['confirmed_by'] && !$reservation['confirmed_by']) $reservation['confirmed_by'] = $_POST['confirmed_by'];
if($_POST['confirmed_date'] && !$reservation['confirmed_date']) $reservation['confirmed_date'] = $_POST['confirmed_date'];
if($_POST['bill_id'] && !$reservation['bill_id']) $reservation['bill_id'] = $_POST['bill_id'];
if($_POST['fop'] && !$reservation['fop']) $reservation['fop'] = $_POST['fop'];
if($_POST['amt'] && !$reservation['amt']) $reservation['amt'] = $_POST['amt'];

if(!$reservation['checkindate']) $reservation['checkindate'] = $today." ".CHECKIN;
if(!$reservation['checkoutdate']) $reservation['checkoutdate'] = $tomorrow." ".CHECKOUT;
if(!$reservation['no_nights']) $reservation['no_nights'] = 1;
if(!$reservation['status']) $reservation['status'] = RES_QUOTE;
if($reservation['status'] == RES_QUOTE && $reservation['confirmed_by'] ) $reservation['status'] = RES_ACTIVE;
// Get what room types are reserved
$resdetailcount = reservation_details_byResID($resid,$details);							
$notcheckedincount=0;
// Count the number of checkings
foreach($details as $idx=>$value) {
	if($details[$idx]['status']!=RES_CHECKIN){
		$notcheckedincount++;
	}
}
// Calculate the remainder after the checkin
$remcheckin = 0;
if($notcheckedincount > 0) {
	$remcheckin = $notcheckedincount - 1;
}
if($reservation['agentid'] && $reservation['reservation_id']) {
	get_agent_bookingref($reservation['reservation_id'], $reservation['agentid'], $bookref, $bookrefid);
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
			<?php echo $_L['MAIN_Title']." ".$_L['MNU_reservations'];?>
		</title>
		<script type="text/javascript">
		//<![CDATA[
		<!--
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
		
			for (i = tguestid.length - 1; i>=0; i--) {
				if (tguestid.options[i].selected) {
					val = tguestid.options[i].value;
					txt = tguestid.options[i].text;
					break;
				}
			}
//			alert("val = " + val);
//			alert("txt = " + txt);
			guestid.value = val;
			guestname.value = txt;
			document.forms["reservation"].submit();
		}

		/**
		* This function sets the guest from the New Guest details entered
		* Updates the page by setting the guest name and running the form submit
		* to retrieve the guest details.
		*/
		function updateNEWguestname() {		
			document.getElementById('guestname').value = document.getElementById('NewFirstName').value + ' ' + document.getElementById('NewLastName').value;
		}

		/**
		* This function sets the telephone from the New Guest details entered
		* Updates the page by setting the guest name and running the form submit
		* to retrieve the guest details.
		*/
		function updateNEWphone() {		
			var txt = document.getElementById('NewCountryCode').value;
			var ac = document.getElementById('NewAreaCode').value
			if(ac.length > 0) {
				txt = txt+'-'+ ac ;
			}
			txt = txt+'-'+document.getElementById('NewTelephone').value;
			document.getElementById('phone').value = txt;
		}

		/**
		* This function display numbers of nights in web page
		*/
		function nights(){
			date2=(document.getElementById('checkoutdate').value);
			date1=(document.getElementById('checkindate').value);
			date=(date2-date1).value;
			document.getElementById('no_nights').value=parseInt(date);
		}

		/** if the source is an agent booking, then show the 
			agent list */
		function showagentlist(ckb) {
			src  = ckb.value;
			agent = document.getElementById('agentrow');
			if(src == 'A') {
				agent.style.display="";
			} else {
				agent.style.display="none";
			}
		}

		/** if the target for the reservation is a specfic room
			show the room list, if the a room type, show the room type list
			if a promotional rate, show the rates list.
		*/
		function showtarget(dest) {
			target=dest.value;
			rate=document.getElementById('targetrate');
			room=document.getElementById('targetroom');
			rtype=document.getElementById('targetroomtype');
			if(target >= 1 && target <= 3) {
				if(target == 1) {
					room.style.display = "";	
					rate.style.display = "none";	
					rtype.style.display = "none";
				}
				if(target == 2) {
					room.style.display = "none";	
					rate.style.display = "none";	
					rtype.style.display = "";
				}
				if(target == 3) {
					room.style.display = "none";	
					rate.style.display = "";	
					rtype.style.display = "none";
				}
			}else {
				room.style.display = "none";	
				rate.style.display = "none";	
				rtype.style.display = "none";
			}
		}
		//Remove a rate from the list
		function remove_rate(id){
			document.getElementById('remove_id').value = id;
			document.forms[0].submit();
		}
		//add a passenger to the list
		function add_rate(id){	
			document.getElementById('add_id').value = id;		
			document.forms[0].submit();
		}
		function enable_checkin(idx){
			document.getElementById('checkin').disabled = false;
			document.getElementById('selectedIdx').value=idx;
		}
		function redirect_to_checkin(){
			var id=document.getElementById('resbook_id').value;
			var resid=document.getElementById('resreservation_id').value;
			var idx = document.getElementById('selectedIdx').value;
			
			var rmn = 'detailroomid'+idx;
			var rtn = 'detailtypeid'+idx;
			var raten = 'detailrateid'+idx;
			var rdn = 'detailsid'+idx;
			var roomid=document.getElementById(rmn).value;
			var typeid=document.getElementById(rtn).value;
			var rateid=document.getElementById(raten).value;
			var detailsid=document.getElementById(rdn).value;
			if(id!=0){
				location.href="index.php?menu=booking&id="+id+"&resid="+resid+"&roomid="+roomid+"&typeid="+typeid+"&rateid="+rateid+"&detailsid="+detailsid+"&action=checkin&rem=<?php echo $remcheckin;?>";
			}else{
				location.href="index.php?menu=booking&resid="+resid+"&roomid="+roomid+"&typeid="+typeid+"&rateid="+rateid+"&detailsid="+detailsid+"&action=checkin&rem=<?php echo $remcheckin;?>";
			}
			
		}
		//-->    
		//]]>
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
			width:16px;
			font-size:11px;
			-moz-max-content:16px;
			-moz-appearance: menuimage;
		}
		</style>
		
<?php
	$onsubmit = '';
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		CustomPagesOnSubmitFunctionCode(HTL_PAGE_RES);
		$onsubmit=CustomPagesOnSubmitFunctionCall(HTL_PAGE_RES);
		if($onsubmit) $onsubmit = 'onsubmit="return '.$onsubmit.'"';
	}
?>
	</head>
	<body>
	   <form action="index.php?menu=reservation" method="post" name= "reservation" enctype="multipart/form-data" id="reservation" <?php echo $onsubmit; ?>>
		
		<table class="listing-table" height="500" border="0" cellpadding="1" align="center">
		  <tr valign="top">
				  <?php 
					if ($_GET['menu'] == "reservation") {
						print_rightMenu_home();
					}?> 		
			<!-- Print the content on the page -->
			
			
			<td  class="c3">
			<table width="100%">
			<tr><td><h2><a href="https://www.youtube.com/watch?v=1H02poKwc3k" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['RSV_title']; ?></h2></a></td></tr>
			<tr><td>
		     <!--  INSERT TAB Here-->
			 <div  id="TabbedPanels1" class="TabbedPanels">
				<ul id="tabgroup" class="TabbedPanelsTabGroup">
					<?php 
					//This tab index cross reference to the spry assets tab javaxcript at the bottom of index.php
					$tabidx = 0; 
					?>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['FRM_guestinfotitle']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['FRM_reservetitle']; ?></li>
					<?php if(is_ebridgeCustomer()){?>
						<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['CST_fields']; ?></li>
					<?php }?>	
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['REG_payment']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['RSV_summary']; ?></li>				
				</ul>
				<div class="TabbedPanelsContentGroup">
				<!-- TAB GUEST INFORMATION-->
					<div class="TabbedPanelsContent">
						 <table width="100%" border="0" cellpadding="1" height="430" class="tdbgcl">
						 	<tr>
								<td width="60%" valign="top">
									<table width="100%">
									<tr><td>&nbsp;</td><td>&nbsp;<input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/>	</td></tr>
									<tr><td>&nbsp;</td><td>&nbsp;	</td></tr>
									<tr><td>&nbsp;</td><td>&nbsp;	</td></tr>
										<tr>
											<td width="30%"  style="padding:10px;"><b><?php echo $_L['RSV_guest']; ?></b></td>
											<td  style="padding:10px;">
											  <input type="hidden" name="Setguestname" id="Setguestname" value="<?php echo $reservation['guestname']; ?>" /> 
											  <input type="text" name="guestname" id="guestname" size=15 maxlength=50 value="<?php echo $reservation['guestname']; ?>" readonly /> 
											  <select name=tguestid id=tguestid class=narrowDropDown  onchange='
												  if(this.value==0){
													document.getElementById("NewProfile").style.display = "none";
													document.getElementById("NewFirstName").value="";
													document.getElementById("NewLastName").value="";
													document.getElementById("NewCountryCode").value="";
													document.getElementById("NewAreaCode").value="";
													document.getElementById("NewTelephone").value="";
													document.getElementById("NewDocID").value="";
													document.getElementById("NewEmail").value="";
													document.getElementById("guestname").value=document.getElementById("Setguestname").value;
												  } else if (this.value=="New") {
													document.getElementById("guestname").value="";
													document.getElementById("NewProfile").style.display = "";
												  }else{
													document.getElementById("NewProfile").style.display = "none";
													document.getElementById("NewFirstName").value="";
													document.getElementById("NewLastName").value="";
													document.getElementById("NewCountryCode").value="";
													document.getElementById("NewAreaCode").value="";
													document.getElementById("NewTelephone").value="";
													document.getElementById("NewDocID").value="";
													document.getElementById("NewEmail").value="";
													updateguestname();
												  };' >
													  <option value="0"> </option>
												 <?php if(! $resid) { ?>
													  <option value="New"><?php echo $_L['BTN_new']; ?></option>
												 <?php 
													}
													if(is_ebridgeCustomer()){
														populate_select("advprofile", "profileid", "firstname,lastname", $reservation['guestid'], "");  
													} else {
														populate_select("guests", "guestid", "firstname,lastname", $reservation['guestid'], "");
													}
												 ?>
											  </select>
											  <?php if($guestid) { ?>
												<a href="index.php?menu=editprofile&id=<?php echo $guestid;?>" target="guests" class="button"><?php echo $_L['BTN_details'];?></a>
											  <?php } ?>
											</td>
										</tr>
										<tr>
											<td align=left  style="padding:5px;"><b><?php echo $_L['RSV_guestsno']; ?></b></td>
											 <td  style="padding:5px;"><table width="100%"><tr>
												<td>
												<?php echo $_L['RSV_adultsno']; ?><br />
												<input type="text" name="no_adults" id="no_adults" size="4"  maxlength="4" value="<?php echo trim($reservation['no_adults']);?>" />
												</td>
												<td>
													<?php echo $_L['RSV_infantsno']; ?><br />
													<input type="text" name="no_child1_5" size="4"  maxlength="4" value="<?php echo trim($reservation['no_child1_5']);?>" />
												</td>
												<td>
													<?php echo $_L['RSV_childno']; ?><br />
													<input type="text" name= "no_child6_12" size="4"  maxlength="4" value="<?php echo trim($reservation['no_child6_12']);?>" />
												</td>
												<td>
													<?php echo $_L['RSV_babyno']; ?><br />
													<input type="text" name="no_babies" size="4" maxlength="4" value="<?php echo trim($reservation['no_babies']);?>" />
												</td>
												</tr></table></td>
										</tr>
										<tr>
											<td  style="padding:10px;"><b><?php echo $_L['RSV_phone']; ?></b></td>
											<td  style="padding:10px;"><input type="text" id="phone" name="phone" size=12 maxlength=20 value="<?php echo trim($reservation['phone']);?>" /></td>
										</tr>
										<tr>
										<td>&nbsp;</td>
										<td>
											
										
										
										
										</td>
										</tr>
									</table>
									<table  frame="box" id='NewProfile' name='NewProfile' style="display:none">
											<tr  >
											  <td colspan=10 >
											  <br/>
												<table width='100%'>
													<tr >
													<td align=left style="padding:3;"><?php echo  $_L['ADM_first']; ?> <br/><input type="text" name="NewFirstName" id="NewFirstName" size=12 maxlength=20 onchange="updateNEWguestname();" /> </td>
													
													<td align=left style="padding:3;"><?php echo  $_L['ADP_lastname']; ?><br/> <input type="text" name="NewLastName" id="NewLastName" size=12 maxlength=20 onchange="updateNEWguestname();" /> 
													</td></tr>	<tr>
													<td align=left style="padding:3;"><?php echo  $_L['ADM_email']; ?><br/> <input type="text" name="NewEmail" id="NewEmail" size=15 maxlength=50 /> </td>
													<td align=left style="padding:3;"><?php echo  $_L['GSL_passport']; ?><br/><input type="text" name="NewDocID" id="NewDocID" size=12 maxlength=20 /> </td><td></td></tr><tr>
													<td align=left style="padding:3;"><?php echo  $_L['ADM_phone']; ?> <br/> <input type="text" name="NewCountryCode" id="NewCountryCode" size=2 maxlength=6 onchange="updateNEWphone();" />&nbsp;<input type="text" name="NewAreaCode" id="NewAreaCode" size=2 maxlength=6  onchange="updateNEWphone();" />&nbsp;<input type="text" name="NewTelephone" id="NewTelephone" size=12 maxlength=20  onchange="updateNEWphone();" /> </td>
													</tr>
												</table><br/>
											  </td>
											</tr>
											 
											</table>
								</td>
								<td valign="top" style="padding:10px;">
									<table align=center width="100%" border="0" bgcolor="#eff7fe">
									<tr><td style="padding:5px;">&nbsp;</td>
										<td><b><?php echo  $_L['RSV_profDetails']; ?> </b></td></tr>
									<tr><td style="padding:5px;" align=left ><?php echo $_L['RSV_email']; ?></td><td style="padding:5px;"><input type="text" readonly value='<?php if($reservation['email']) echo trim($reservation['email']); ?>' />
										<input type=hidden name=email value="<?php echo $reservation['email']; ?>" />
										<input type=hidden name=IM value="<?php echo $reservation['IM']; ?>" />
									</td></tr>	
									<tr>
									<td style="padding:5px;" align=left ><?php echo $_L['RSV_IM']; ?></td>
									<td style="padding:5px;" ><input type="text" readonly value='<?php if($reservation['IM']) echo trim($reservation['IM']); ?>' />
									</td></tr>
									<tr>
									<td style="padding:5px;" align=left ><?php echo  $_L['RSV_docnum']; ?></td>
									<td style="padding:5px;" ><input type="text" readonly value='<?php if($reservation['pp_no']) echo trim($reservation['pp_no']); ?>' /></td>
									</tr>
									<tr>
									<td style="padding:5px;" align=left ><?php echo $_L['RSV_address']; ?></td>
									<td style="padding:5px;"><textarea style="resize:none;" readonly rows="3" cols="25" >
									  <?php
										if($reservation['address']) {
										  echo trim($reservation['address']);
										 // echo trim($reservation['town'])."-".trim($reservation['postal_code']);					// ???
										}
									  ?></textarea>
									  <input type=hidden name=address value="<?php echo $reservation['address']; ?>" />
									  <input type=hidden name=town value="<?php echo $reservation['town']; ?>" />
									  <input type=hidden name=postal_code value="<?php echo $reservation['postal_code']; ?>" />
									  <input type=hidden name=pp_no value="<?php echo $reservation['pp_no']; ?>" />
									</td></tr>
									</table>
								</td>
							</tr>
							<tr>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
					      </tr>
						  <tr>
						  <td>&nbsp;</td>
						  <td>&nbsp;</td>
					      </tr>
						 </table>
					</div>
					<!-- DIV FOR RESERVATION DETAILS -->
					<div  class="TabbedPanelsContent">
						<div class="scrolltab">
							<table width="100%" class="tdbgcl">
							<tr>
							<td>
							  <input type="hidden" name="reservation_id" value= "<?php echo trim($reservation['reservation_id']);?>" size="10" readonly="readonly" />
							  <input type="hidden" name="reserve_time" value="<?php echo trim($reservation['reserve_time']);?>" />
							  <input type="hidden" name="guestid" id="guestid"  value= "<?php echo trim($reservation['guestid']);?>" />
							</td>
							<td> </td>
							<td> </td>
							<td> </td>
							</tr>
							
							<tr>
							<td>&nbsp;</td>
							</tr>
							<tr>
							<td>&nbsp;</td>
							</tr>
							<tr >
								<td align=left style="padding:5">
								  <table width="100%" ><tr>
								   <td><b><?php echo $_L['RSV_status'];?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								   <select name=status class=plainDropDown2 >					
									<option value="<?php echo RES_QUOTE; ?>" <?php if($reservation['status'] == RES_QUOTE) echo "selected"; ?> > <?php echo $_L['RSV_quote']; ?></option>
									<option value="<?php echo RES_ACTIVE; ?>" <?php if($reservation['status'] == RES_ACTIVE) echo "selected"; ?> > <?php echo $_L['RSV_active']; ?></option>
									<option value="<?php echo RES_CANCEL; ?>" <?php if($reservation['status'] == RES_CANCEL) echo "selected"; ?> > <?php echo $_L['RSV_cancelled']; ?></option>
									<option value="<?php echo RES_EXPIRE; ?>" <?php if($reservation['status'] == RES_EXPIRE) echo "selected"; ?> > <?php echo $_L['RSV_expired']; ?></option>
									<option value="<?php echo RES_VOID; ?>" <?php if($reservation['status'] == RES_VOID) echo "selected"; ?> > <?php echo $_L['RSV_void']; ?></option>
									<option value="<?php echo RES_CHECKIN; ?>" <?php if($reservation['status'] == RES_CHECKIN) echo "selected"; ?> > <?php echo $_L['RSV_checkin']; ?></option>
									<option value="<?php echo RES_CHECKOUT; ?>" <?php if($reservation['status'] == RES_CHECKOUT) echo "selected"; ?> > <?php echo $_L['RSV_checkout']; ?></option>
									<option value="<?php echo RES_CANCELREQUESTED; ?>" <?php if($reservation['status'] == RES_CANCELREQUESTED) echo "selected"; ?> > <?php echo $_L['RSV_canselreq']; ?></option>
								   </select>
								   </td>
								   
								   <td>
									<?php //if ebridge customer {print exo link function} 
										if(is_ebridgeCustomer()){	//???	
											include_once(dirname(__FILE__)."/OTA/advancedFeatures/EXO_link.php");
											if (($reservation['status'] == RES_ACTIVE) || ($reservation['status'] == RES_CHECKIN)) {
												create_print_button();		
											}
										}
									?>						
								   </td>
								   </tr></table>
								</td>
							</tr>
							<tr><td style="padding:5"><table width="100%"><tr>
							  <td align=left ><b><?php echo $_L['RSV_voucherno']; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="voucher_no" size=10 maxlength=15 readonly value='<?php echo $vch; ?>' /></td>
							   <td align=left><b> <?php if ($reservation['bill_id']) { echo $_L['RSV_invoiceno']; } ?></b>
								<?php if ($reservation['bill_id']) { ?>
								<a href="?menu=invoice&id=<?php echo $reservation['bill_id'];?>" class="button"> 
								<!--<input type=text name="bill_id" size=10 readonly value="<?php echo $reservation['bill_id']; ?>" /> -->
								<b><?php echo $reservation['bill_id']; ?></b>
								</a>
								<?php } ?>			  
							   </td>
							   
								<?php // below 2 cells do not show on any case ?>
							   <td align=right> <?php if ($reservation['book_id']) { echo $_L['RSV_bookingno']; } ?></td>
							   <td>
							   <?php if ($reservation['book_id']) { ?>
							   <a href="?menu=booking&id=<?php echo $reservation['book_id'];?>" > 
							   <input type=text size=10 readonly value="<?php echo $reservation['book_id']; ?>" /> 
							   </a>
							   <?php } ?>
							   </td></tr></table></td>
							</tr>
							<tr>
							   <td style="padding:5"><table><tr><td>
							   <input type="hidden" name="reservation_id" value= "<?php echo trim($reservation['reservation_id']);?>" size="10" readonly="readonly" />
							   <input type="hidden" name="reserve_time" value="<?php echo trim($reservation['reserve_time']);?>" />
								</td></tr></table>
							   </td>
							</tr>
							<tr>
						   <td  ><table width="100%" border="0"><tr><td style="padding:5;" width="62%"><b>
						   <?php echo $_L['RSV_src']; ?>
						   </b><br/>
						   <?php $style = "display:none"; ?>
						   <label><input type="radio" name="src" value="T" onclick="showagentlist(this);" <?php if ($reservation['src'] == "T") echo "checked"; ?> /> <?php echo $_L['RSV_phone']; ?></label> 
						   <label><input type="radio" name="src" value="D" onclick="showagentlist(this);" <?php if ($reservation['src'] == "D") echo "checked"; ?> /> <?php echo $_L['RSV_desk']; ?></label>
						   <label><input type="radio" name="src" value="L" onclick="showagentlist(this);" <?php if ($reservation['src'] == "L") echo "checked"; ?> /> <?php echo $_L['RSV_letter']; ?></label> 
						   <label><input type="radio" name="src" value="O" onclick="showagentlist(this);" <?php if ($reservation['src'] == "O") echo "checked"; ?> /> <?php echo $_L['RSV_online']; ?></label>
						   <label><input type="radio" name="src" value="A" onclick="showagentlist(this);" <?php if ($reservation['src'] == "A") { echo "checked"; $style = ""; } ?> /> <?php echo $_L['ADM_agent']; ?></label></td><td width="38%">
						   <table>
						    <tr>
							  <td  id=agentrow   style="<?php echo $style; ?>">
								<select name=agentid class=plainDropDown>
								  <option value='0'> </option>
								  <?php populate_select('agents','agentID', 'agentname', $reservation['agentid'], ""); ?>
								</select>
								&nbsp;
								<input type=text size=10 id=bookref name=bookref value="<?php echo $bookref;?>" />
								<input type=hidden name=bookrefid id=bookrefid value="<?php echo $bookrefid;?>" />
							  </td>
						    </tr>
						   </table>
						   </td></tr></table>
						  </td>
						  </tr>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
							<td >
							<table border="0" align=left>
								<tr >
								 <td>
									<table border="0" >
									<tr >
									<td align=left style="padding:5;"><b><?php echo $_L['RSV_arrival']; ?></b>
									  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									  <input type="text" name="checkindate" id="checkindate" onchange='addDateDays("checkindate","no_nights","checkoutdate","dd/mm/yyyy hh:ii");document.forms[0].submit()' size=16 maxlength=16 readonly value="<?php echo trim($reservation['checkindate']);?>" />
									  <img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkindate,'dd/mm/yyyy hh:ii',this, true, 1400)" />
									</td>
									
									</tr>
									<tr>
									<td style="padding:5;" align=left ><b><?php echo $_L['RSV_depart']; ?></b>&nbsp;&nbsp;
									  <input type="text" name= "checkoutdate" id="checkoutdate" onchange='subDates("checkindate","checkoutdate","no_nights","dd/mm/yyyy hh:ii");' size=16 maxlength=16 readonly value="<?php echo trim($reservation['checkoutdate']);?>" />
									  <img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkoutdate,'dd/mm/yyyy hh:ii',this, true, 1000)"/>
									</td>
									
								  </tr>
									</table>
								 </td>
								 <td style="padding:10;" ><b><?php echo $_L['RSV_nightsno']; ?></b> <input type="text" name="no_nights" id= "no_nights" size="3" value= "<?php echo trim($reservation['no_nights']);?>" onchange='addDateDays("checkindate","no_nights","checkoutdate","dd/mm/yyyy hh:ii");' /></td>
								</tr>
							</table>
							</td>
						  </tr>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  
						  <tr>
						  <td><table  align=left>
						  <tr>
						<td style="padding:4;" align=left ><b>
                          <?php  echo $_L['RSV_selrateby']; 
						  $rmstyle="display:block"; // rooms - 1
						  $rtstyle="display:none"; // room type - 2
						  $rastyle="display:none"; // rate code - 3
						  
						  if (isset($restarget)&& $restarget == 1) {
						  	  	$rmstyle="display:block"; 
						  		$rtstyle="display:none"; 
						  		$rastyle="display:none"; 
						  } 
						  if (isset($restarget)&& $restarget == 2) {
							  $rmstyle="display:none"; 
						  	  $rtstyle="display:block"; 
						  	  $rastyle="display:none"; 
						  }
						  if (isset($restarget)&& $restarget == 3) {
							 $rmstyle="display:none"; 
						  	 $rtstyle="display:none"; 
						  	 $rastyle="display:block"; 
						  } 
						  ?></b>
						</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>
						  <select id=restarget name=restarget onchange="showtarget(this);" class="plainDropDown" >
							<option value=1 <?php if (isset($restarget)&& $restarget == 1) { echo "selected=selected"; } ?> ><?php echo $_L['RM_roomno']; ?> </option>
							<option value=2 <?php if (isset($restarget)&& $restarget == 2) { echo "selected=selected"; } ?> ><?php echo $_L['RMT_rtype']; ?> </option>
							<option value=3 <?php if (isset($restarget)&& $restarget == 3) { echo "selected=selected";} ?> ><?php echo $_L['RTS_code']; ?> </option>
						  </select>
						</td>
						<td colspan=2>
						  <table width="100%" border=0>
						  	<input type="hidden" name="add_id" id="add_id" value="0" />
							<input type="hidden" name="remove_id" id="remove_id" value="0" />
								
							<tr id=targetrate style="<?php echo $rastyle; ?>">
							  <td>
							  <?php 
							  	 $cond = "";
								 $selected="";
								 if(isset($_POST['ratesid_1'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$_POST['ratesid_1'];
							  ?>
								<select id=ratesid_1 name=ratesid_1 class="plainDropDown" onchange="document.forms[0].submit();">
								  <option value=0> </option>
								  <?php 
								//  $cond = "rate_type=".PROMORATE;							
								 populate_select("rates","ratesid","ratecode",$selected, $cond);?>
								</select>
							  </td> 
							  <td>
							  <?php  
							   	  $rid="";
							   	  $selected="";
								  $rmtypes=array();
								  if(isset($_POST['ratesid_1'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $rid=$_POST['ratesid_1'];								 
								  if(isset($_POST['ratesid_1'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$_POST['ratesid_1'];
								  get_roomtype_byrateid($rid,$rmtypes);
								  ?>
								<select name=roomtypeid_1 class="plainDropDown" >
								 <?php 
								  foreach($rmtypes as $rt){
								  	print "<option value='" . $rt['roomtypeid'] ."' ";
								  	if($selected) print " selected=". $selected . ">";
								  	else print ">";
									print $rt['roomtype'];
									print "</option>";
								  }
								  ?>
								</select>
							  </td>
							   <td colspan="4" align='right'>
								<?php if($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_QUOTE ||!$reservation['status']){
											print "<img id=\"img_addRate\" height='14px' width='14px' src=\"images/addbutton.png\" 											
											 onclick=\"add_rate(1);\" style=\"cursor:pointer;\">";	
								
								} 
								?>
								</td>
							</tr>
							<tr id=targetroom style="<?php echo $rmstyle; ?>" >
							  <td>
								<select name=roomid_2 class="plainDropDown" onchange="document.forms[0].submit();">
								  <option value=0> </option>
								  <?php 
								  $selected="";
								  if(isset($_POST['roomid_2'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$_POST['roomid_2'];							 
								  populate_roomselect($reservation['checkindate'],$reservation['checkoutdate'], $selected);
								  ?>
								</select>
							  </td>
							   <td><?php  
							   	  $rmid="";
							   	  $selected="";
								  $rates=array();
								  if(isset($_POST['roomid_2'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $rmid=$_POST['roomid_2'];							 
								  if(isset($_POST['ratesid_2'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$reservation['ratesid'];
								  get_ratebyroomid($rmid,$rates);
								  ?>
								<select id=ratesid_2 name=ratesid_2 class="plainDropDown"  >
								 <?php 
								foreach($rates as $rt){
								  	print "<option value='" . $rt['rateid'] ."' ";
								  	if($selected) print " selected=". $selected . ">";
								  	else print ">";
									print $rt['ratecode'];
									print "</option>";
								  }
								  ?>
								</select>
							  </td>
							  <td colspan="4" align='right'>
								<?php if($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_QUOTE ||!$reservation['status']){
											print "<img id=\"img_addRate\" height='14px' width='14px' src=\"images/addbutton.png\" 											
											 onclick=\"add_rate(2);\" style=\"cursor:pointer;\">";	
								
								} 
								?>
								</td>
							</tr>
							<tr id=targetroomtype style="<?php echo $rtstyle; ?>">
							  <td>
								<select name=roomtypeid_3 class="plainDropDown" onchange="document.forms[0].submit();">
								  <option value=0> </option>
								  <?php 
								  $selected="";
								  if(isset($_POST['roomtypeid_3'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$_POST['roomtypeid_3'];
								  populate_select("roomtype","roomtypeid","roomtype",$selected, "");?>
								</select>
							  </td>
							   <td>
							   	<?php  
							   	  $rmtid="";
								  $selected="";
								  $rates=array();
								  if(isset($_POST['roomtypeid_3'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $rmtid=$_POST['roomtypeid_3'];								 
								  if(isset($_POST['ratesid_3'])&&isset($_POST['add_id'])&&$_POST['add_id']==0) $selected=$reservation['ratesid'];
								  get_ratebyroomtypeid($rmtid,$rates);								  
								  ?>
								<select id=ratesid_3 name=ratesid_3 class="plainDropDown"  >
								  <?php 
								  foreach($rates as $rt){
								  	print "<option value='" . $rt['rateid'] ."' ";
								  	if($selected) print " selected=". $selected . ">";
								  	else print ">";
									print $rt['ratecode'];
									print "</option>";
								  }
								  ?>
								</select>
							  </td>
							  <td colspan="4" align='right'>
								<?php if($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_QUOTE ||!$reservation['status']){
											print "<img id=\"img_addRate\" height='14px' width='14px' src=\"images/addbutton.png\" 											
											 onclick=\"add_rate(3);\" style=\"cursor:pointer;\">";	
								
								} 
								?>
								</td>
							</tr>
						  </table>
						</td>
						<td></td>
					  </tr>
						  
						  
						  </table></td>
						  
					      </tr>
						  
						  <?php 
						//display already added rates
						$j=0;
						$disabled=" disabled='disabled '";
						$notcheckedinIDx=0;
						if($resdetailcount>0){
							$i=0;
							print "<tr>";
							print "<td><table align=left ><tr>";
							print "<td>
							<input type='hidden' name='resbook_id' id='resbook_id' value='".$reservation['book_id']."' />
							<input type='hidden' name='resreservation_id' id='resreservation_id' value='".$reservation['reservation_id']."' />
							<input type='hidden' name='selectedIdx' id='selectedIdx' value='0' />
							";
							print "</td><td style='padding-left:120;padding-top:10;'><table width='100%'><tr bgcolor='#2e71a7'>";
							if($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_QUOTE ){
								print "<th width='5px'></th>";
								print "<th width='5px'></th>";
							}
						  	print "<th style='padding:5;' align='center'>".$_L['RTS_code']."</th>";	
							print "<th style='padding:5;' align='center'>".$_L['RMT_rtype']." </th>";			
							print "<th style='padding:5;' align='center'>".$_L['RM_roomno']." </th>";
							print "<th style='padding:5;' align='center'>".$_L['REG_registered']." </th>";												
							print "</tr>";										
	
							foreach($details as $idx=>$value) {
								$j++;
							  //display existing records	
								if($j%2==1){
								  print "<tr bgcolor=\"#CCCCCC\">";
								}else{
								  print "<tr bgcolor=\"#EEEEF8\">";
								}
								
								 if($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_QUOTE ){
									if($details[$idx]['status']!=RES_CHECKIN){
										print "<td style='padding:5;' width='5px'>";
										print '&nbsp;<img id="img_removerate" height="14px" width="14px" src="images/remove.png" style="cursor:pointer;" onclick="remove_rate('.$details[$idx]['id'].');" />';
										print "</td>";
										print "<td style='padding:5;' width='5px'>";
										if($notcheckedincount==1){
											$disabled="";
											$notcheckedinIDx=$i;
											print "<input type='radio' id='checkinroom' name='checkinroom' value='".$i."' checked='checked' onclick='enable_checkin(".$i.");' />";
										}else{
											print "<input type='radio' id='checkinroom' name='checkinroom' value='".$i."' onclick='enable_checkin(".$i.");' />";
										}
										print "</td>";
									}else{
										print "<td style='padding:5;' width='5px'>&nbsp;</td>";
										print "<td style='padding:5;' width='5px'>&nbsp;</td>";
									}
								}									
																										
								print "<td style='padding:5;' align='center'>";				
								print trim($details[$idx]['ratecode']);	
								print "<input type='hidden' name='detailsid".$i."' id='detailsid".$i."' value='".$details[$idx]['id']."' />		
								<input type='hidden' name='detailrateid".$i."' id='detailrateid".$i."' value='".$details[$idx]['ratesid']."' />
								<input type='hidden' name='detailroomid".$i."' id='detailroomid".$i."' value='".$details[$idx]['roomid']."' />
								<input type='hidden' name='detailtypeid".$i."' id='detailtypeid".$i."' value='".$details[$idx]['roomtypeid']."' />
								";	
								print "</td>";
								
								print "<td style='padding:5;' align='center'>";
								print trim($details[$idx]['roomtype']);
								print "</td>";
								
								print "<td style='padding:5;' align='center'>";				
								print trim($details[$idx]['roomno']);				
								print "</td>";
								print "<td style='padding:5;' align='center'>";
								$bid = get_bookid_by_resDetail_id($details[$idx]['id']);
								if($bid) {
									print "<a href='index.php?menu=booking&id=".$bid."' target='bookings' class='button'>". $_L['RGL_book']."</a>";
								} else {
									print "&nbsp;";
								}
								print "</td>";
								print "</tr>";								
								$i++;
							}
							$j++;
							if($j%2==1){
							  print "<tr bgcolor=\"#CCCCCC\">";
							}else{
							  print "<tr bgcolor=\"#EEEEF8\">";
							}	
							print "</table></td></tr></table></td></tr>";		
						}
						
						?>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
						  <td><table border="0" width="100%"><tr>
							<td style="padding:5;" align=left colspan="4"><b><?php echo $_L['RSV_instructions']; ?></b></td>
							<td colspan="4"><textarea name="instructions" cols=60 rows=6 ><?php echo $reservation['instructions']; ?></textarea></td>
							</tr></table>
						  </td>
						  </tr>
						  <tr>
						  <td>&nbsp;</td>
						  </tr>
						  <tr><td><table  align=right ><tr>
							<td align=right ><?php echo $_L['RSV_resby']; ?></td>
							<td><input type="text" name="reservation_by" value="<?php echo trim($reservation['reservation_by']);?>"  /></td>
							</tr><tr>
							<td align=right ><?php echo $_L['RSV_byphone']; ?></td>
							<td><input type="text" name= "reservation_by_phone"  maxlength=20 value= "<?php echo trim($reservation['reservation_by_phone']);?>"  /></td></tr></table></td>
						  </tr>
						  <tr>
						  <td>&nbsp;</td>
						  </tr>
							</table>
						</div>
					</div>
					<?php if(is_ebridgeCustomer()){?>
					<!-- TAB CUSTOME FIELD-->
					<div class="TabbedPanelsContent">
					<div class="scrolltab">
						<?php
						if(is_ebridgeCustomer()){
							$offset = 150;
							include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
							print "<table class='tdbgcl' width='100%'>";
							print "<tr><td colspan=5>&nbsp;</td></tr>\n";
							print "<tr><td colspan=5>&nbsp;</td></tr>\n";
							print "<tr><td style='padding:10'>\n";
							CustomPagesFormPrint(HTL_PAGE_RES, $reservation['reservation_id'], 700, 300);
							print "</td></tr>\n";		
							print "</table>";
						}
					  ?>
					</div>
					</div>
					<?php }?>
					
					<!-- TAB GUARANTEE FIELD-->
					<div class="TabbedPanelsContent">
						<table width="100%" class="tdbgcl">
						<tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
								<td colspan="4" align=left>
								  <input type=checkbox name="payinadvance" id="payinadvance" value="1" <?php echo $disabledPayment;?><?php if(isset($_POST['payinadvance'])) print "checked='checked'";?> /> <?php echo $_L['RSV_payadvance']; ?>
							</tr>
						<tr>
						  <td><table><tr>
							<td colspan="4" align=left><?php 
							if($rcptcount>0){
								echo $_L['RSV_depositcollected'];
								echo "<input type=hidden name='deposit_made' id='deposit_made' value='1'/>";
							}else if($resid){
								echo $_L['RSV_nodeposit'];
							}						
							?></td></tr></table></td>
						  </tr>
						  <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  <tr>
						  <td>
						  <table  width="100%">
						  <tr>
							  <td colspan=0>
							  <table border="0"width="60%"><tr>
							  <td style="padding:5;" align=left>
							  <?php echo $_L['INV_fop']; ?>
							  </td>
							  <td align=left>
							  <select name=fop id=fop class=plainDropDown2 <?php echo $disabledPayment;?>>
								<option value='<?php echo FOP_CASH; ?>' <?php if($reservation['fop'] == FOP_CASH) echo "selected"; ?>><?php echo $_L['FOP_cash']; ?></option>
								<option value='<?php echo FOP_CC; ?>' <?php if($reservation['fop'] == FOP_CC) echo "selected"; ?>><?php echo $_L['FOP_cc']; ?></option>
								<option value='<?php echo FOP_DB; ?>' <?php if($reservation['fop'] == FOP_DB) echo "selected"; ?>><?php echo $_L['FOP_db']; ?></option>
								<option value='<?php echo FOP_TT; ?>' <?php if($reservation['fop'] == FOP_TT) echo "selected"; ?>><?php echo $_L['FOP_tt']; ?></option>
								<option value='<?php echo FOP_CHEQUE; ?>' <?php if($reservation['fop'] == FOP_CHEQUE) echo "selected"; ?>><?php echo $_L['FOP_chq']; ?></option>
								<option value='<?php echo FOP_COUPON; ?>' <?php if($reservation['fop'] == FOP_COUPON) echo "selected"; ?>><?php echo $_L['FOP_coupon']; ?></option>
								<option value='<?php echo FOP_VOUCHER; ?>' <?php if($reservation['fop'] == FOP_VOUCHER) echo "selected"; ?>><?php echo $_L['FOP_voucher']; ?></option>
								<option value='<?php echo FOP_PP; ?>' <?php if($reservation['fop'] == FOP_PP) echo "selected"; ?>><?php echo $_L['FOP_voucher']; ?></option>
								<option value='<?php echo FOP_REDEMPTION; ?>' <?php if($reservation['fop'] == FOP_REDEMPTION) echo "selected"; ?>><?php echo $_L['FOP_redem']; ?></option>
							  </select>
							  <?php if(!empty($disabledPayment)){
								print "<input type='hidden' id='fop' name='fop' value='".$reservation['fop']."' />";
								print "<input type='hidden' id='cctype' name='cctype' value='".$reservation['cctype']."' />";
								print "<input type='hidden' id='CVV' name='CVV' value='".$reservation['CVV']."' />";
								print "<input type='hidden' id='amt' name='amt' value='".$reservation['amt']."' />";
								print "<input type='hidden' id='CCnum' name='CCnum' value='".$reservation['CCnum']."' />";
								print "<input type='hidden' id='expiry' name='expiry' value='".$reservation['expiry']."' />";
							  }?>
							  </td>
								
								<td align=left >
								  <select name=cctype id=cctype class=plainDropDown <?php echo $disabledPayment;?>>
									<option value='0' >Select Type</option>
									<option value="CA" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "CA") echo "selected"; ?> ><?php echo $_L['CC_CA']; ?></option>
									<option value="DC" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "DC") echo "selected"; ?> ><?php echo $_L['CC_DC']; ?></option>
									<option value="AX" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "AX") echo "selected"; ?> ><?php echo $_L['CC_AX']; ?></option>
									<option value="VI" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "VI") echo "selected"; ?> ><?php echo $_L['CC_VI']; ?></option>
									<option value="JCB" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "JCB") echo "selected"; ?> ><?php echo $_L['CC_JCB']; ?></option>
									<option value="EC" <?php if($reservation['fop'] == FOP_CC && $reservation['cctype'] == "EC") echo "selected"; ?> ><?php echo $_L['CC_EC']; ?></option>
								  </select>
								</td></tr><tr>
								<td style="padding:5;" align=left > <?php echo $_L['RSV_CVV']; ?></td><td> <input type="text" name="CVV" size=4 maxlength=4 <?php echo $disabledPayment;?> value='<?php echo $reservation['CVV']; ?>' /></td>
								<td align=left >
								  <?php echo $_L['RSV_expiry']; ?> <input type="text" name="expiry" id="expiry" size=4 maxlength=4 <?php echo $disabledPayment;?> OnChange="CheckCardNumber('cctype','CCnum','expiry');" value='<?php echo $reservation['expiry']; ?>' />
								</td>
							  </tr>
							  <tr>
							  <td style="padding:5;" align=left >
								  <?php echo $_L['RSV_cardno']; ?></td>
								  <td><input type="text" name="CCnum" id="CCnum" size=16 maxlength=19 <?php echo $disabledPayment;?> OnChange="CheckCardNumber('cctype','CCnum','expiry');" value='<?php echo $reservation['CCnum']; ?>' />
								</td></tr><tr>
								<td align=left ><?php echo $_L['RTS_amount']; ?></td>
								<td> <input type="text" name="amt" id="amt" size=4 maxlength=10 <?php echo $disabledPayment;?> value='<?php echo $reservation['amt']; ?>' /></td>
								
								
								
							  </tr>
                      </table></td>
					  
						  </tr>
						  </table>
						  </td>
						  </tr>
						   <tr>
						  <td>&nbsp;</td>
						  
					      </tr><tr>
						  <td>&nbsp;</td>
						  
					      </tr><tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						   <tr>
						  <td><table>
						  <tr>
							<td colspan="2" align=center ><h2><?php echo $_L['RSV_reserved_by']; ?>:</h2></td>
							<td colspan="2" align=center ><h2><?php echo $_L['RSV_confirmed_by']; ?>:</h2></td>
						  </tr>
						  <tr>
							<td align=right ><?php echo $_L['RSV_name']; ?></td>
							<td>
							  <input type="hidden" name="reserved_by" value="<?php echo $reservation['reserved_by'];?>" />
							  <input type="text" name="reserved_name" size=15  value="<?php echo $reservation['reserved_name'];?>" readonly />
							</td>
							<td align=right >
							  <?php if($reservation['confirmed_by']) echo $_L['RSV_name'];?>
							</td>
							<td>
							  <?php if($reservation['confirmed_by']) { 
								$confby = array();											// ???
								get_userbyid($reservation['confirmed_by'], $confby);
							  ?>
							  <input type="hidden" name="confirmed_by" value="<?php echo $reservation['confirmed_by']; ?>" />
							  <input type="text" name="confirmed_by_name" size=15 value="<?php echo $confby['loginname'];?>" readonly />
							  <?php } else { ?>
							  <input type=checkbox name="confirmed_by"  value="<?php echo $userid; ?>" /> <?php echo $_L['RSV_confirm']; ?>
							  <?php } ?>
							</td>
						  </tr>
						   <tr>
							<td align=right ><?php echo $_L['RSV_date']; ?></td>
							<td>
							  <?php if($reservation['reserved_date'] == $today || $reservation['reserved_date'] == '' ) { ?>
							  <img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].reserved_date,'dd/mm/yyyy',this, false, 1400)" />
							  <?php } ?>
							  <input type="text" name="reserved_date" id="reserved_date" size=10 maxlength=10 readonly="readonly" value="<?php echo trim($reservation['reserved_date']);?>" />
							</td>
							<td align=right ><?php echo $_L['RSV_date']; ?></td>
							<td>
							  <?php if(! $reservation['confirmed_by']) {  ?>
								<img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].confirmed_date,'dd/mm/yyyy',this, false, 1400)" />
							  <?php } ?>
								<input type="text" name="confirmed_date" id="confirmed_date" size=10 maxlength=10 readonly="readonly" value="<?php echo trim($reservation['confirmed_date']);?>" />
							</td>
						  </tr>
						    <tr>
						  <td>&nbsp;</td>
						  
					      </tr>
						  </table></td>
						  
					      </tr>
						  
						</table>
					</div>
					
					<!-- TAB Summary FIELD-->
					<div class="TabbedPanelsContent">
						
						
						  <?php $amp = ""; 
						  $uri = "reservation_summary.php?";
						  if($reservation['reservation_id']) { $uri .= "rid=".$reservation['reservation_id']; $amp="&"; }
						  if($reservation['checkindate']) { $uri .= $amp."in=".urlencode($reservation['checkindate']); $amp="&"; }
						  if($reservation['checkoutdate']) { $uri .= $amp."out=".urlencode($reservation['checkoutdate']); $amp="&"; }
						  if($reservation['guestid']) { $uri .= $amp."guest=".$reservation['guestid']; $amp="&"; }
						  if($reservation['roomid'] && $restarget == 1) { $uri .= $amp."room=".$reservation['roomid']; $amp="&"; }
						  if($reservation['roomtypeid'] && $restarget == 2) { $uri .= $amp."roomtype=".$reservation['roomtypeid']; $amp="&"; }
						  if($reservation['ratesid'] && $restarget == 3) { $uri .= $amp."rate=".$reservation['ratesid']; $amp="&"; }
						  ?>
						  <div class="scroll" id=res_summary src="<?php echo $uri;	?>">
							<iframe src="<?php echo $uri; ?>" frameborder="0" width="100%" height="380px">
							  <p>Your browser does not support iframes.</p>
							  <a href="<?php echo $uri; ?>" > Click this uri <?php echo $uri; ?> </a>
							</iframe>
						  </div>
						
					
					</div>
					
					
				</div>
			 </div>
			 
			 <div class="btngroup" align=right>
			
			 <input class="button" type="button" name="Submit" value= "<?php echo $_L['BTN_rsvlist']; ?>" onclick= "self.location='index.php?menu=reservationlist'" />
				<?php 
				 if($reservation['status'] != RES_CHECKIN) {
					if(!$reservation['reservation_id']) { ?>
					 <input class="button" type="submit" name="Submit" value= "<?php echo $_L['BTN_add']; ?>" />
					  <?php } else if (!$reservation['book_id']) { ?>
					 <input class="button" type="submit" name="Submit" value= "<?php echo $_L['BTN_update']; ?>" />
					 <?php } 
					}
					?>
				
				  <?php if($reservation['status'] == RES_ACTIVE  ) { 
						if($notcheckedincount==1) {						  
						 print "<input class='button' type='button' id='checkin' name='checkin' ".$disabled." onclick='enable_checkin(".$notcheckedinIDx.");redirect_to_checkin();' value= '".$_L['RSV_checkin']."' />";
				 	} else { 
						 print "<input class='button' type='button' id='checkin' name='checkin' ".$disabled." onclick='redirect_to_checkin();' value= '". $_L['RSV_checkin']."' />";
					}
						  		
				  }
						  ?>
				
				 <?php if($reservation['bill_id'] && ($reservation['status'] == RES_ACTIVE || $reservation['status'] == RES_CANCEL || $reservation['status'] == RES_EXPIRE)) { ?>
				  <a href="index.php?menu=invoice&id=<?php echo $reservation['bill_id']; ?>" target="billings" class="button" ><?php echo $_L['RSV_invoice']; ?></a>
				 <?php } ?>
				
			 </div>
			 </td></tr>
			 <!-- print the button at the bottom pf the page-->
			 <tr >
				<td align=right >	
				
				</td>
				</tr>
			 </table>
			</td>
			</tr>
			 
			 </td>
			</tr>
		  
		</table>
		
		
		
		
		
		
      </form>
      <?php 
      if (is_ebridgeCustomer()) { //???
	if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }
	else { $ssl = ""; }
      	$MISreturnURL = 'http'.$ssl.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?resid=".$reservation['reservation_id'];
      	create_link_form($reservation['guestid'],$reservation['book_id'],$reservation['bill_id'],$reservation['reservation_id'],$reservation['voucher_no'],$MISreturnURL);
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
