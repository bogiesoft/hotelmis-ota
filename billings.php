<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file billings.php
 * @brief billings webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup INVOICE_MANAGEMENT Invoice setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

if(is_ebridgeCustomer()){
	include_once(dirname(__FILE__)."/OTA/advancedFeatures/billings.php");
	return;
}


$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
date_default_timezone_set(TIMEZONE);
access("billing"); //check if user is allowed to access this page
$refline = "";
$totalArr = array();
$payedArr=array();
$payedDisplayArr=array();
$dueArr = array();
$currencyArr = array();
get_CurrencyList($currencyArr);
$refunds = array();
$XOID=0;
$transDetail='';
$flags = 0;
//check if refered exchnge order link & check invoice item id & exchange order id  then set the exchange order id against invoice item???

// Date and time of today (now)
$today = date("d/m/Y");
// Bill id from get first, not post.
if($_GET['id']) {
	$id = $_GET['id'];
}
// now check the posted value
if(!$id && $_POST['billid']) {
	$id = $_POST['billid'];
}
// Not loaded, or different to what is already loaded.
if(isset($_POST['bill_id']) && $id <> $_POST['bill_id']) {
//	echo "reset id to POST<br/>";
	$id=$_POST['bill_id'];
}

if(isset($_POST['bill_search'])) {
	$tmp = get_bill_id($_POST['bill_search']);
	if($tmp > 0) {
		$id = $tmp;
		$_POST['bill_id'] = $tmp;
	}
} 
// Booking id
if($_GET['bid']) {
	$bid = $_GET['bid'];
}
// Reservation id
if($_GET['rid']) {
	$rid = $_GET['rid'];
}
// transaction id
if($_GET['tid']) {
	$tid = $_GET['tid'];
}
// receipt/payment id
if($_GET['pid']) {
	$pid = $_GET['pid'];
}

if(!$CCnum && isset($_POST['CCnum'])) $CCnum = $_POST['CCnum'];
if(!$cvv && isset($_POST['cvv'])) $cvv = $_POST['cvv'];
if(!$rcpt_date && isset($_POST['rcpt_date'])) $rcpt_date = $_POST['rcpt_date'];
if(!$cardname && isset($_POST['cardname'])) $cardname = $_POST['cardname'];
if(!$cctype && isset($_POST['cctype'])) $cctype = $_POST['cctype'];
if(!$auth && isset($_POST['auth'])) $auth = $_POST['auth'];
if(!$expiry && isset($_POST['expiry'])) $expiry = $_POST['expiry'];
if(!$cctype && isset($_POST['cctype'])) $cctype = $_POST['cctype'];
if(!$rcpt_amt && isset($_POST['rcpt_amt'])) $rcpt_amt = $_POST['rcpt_amt'];
if(!$trans_date && isset($_POST['trans_date'])) $trans_date = $_POST['trans_date'];
if(!$std_amount && isset($_POST['std_amount'])) $std_amount = $_POST['std_amount'];
if(!$amount && isset($_POST['amount'])) $amount = $_POST['amount'];
if(!$quantity && isset($_POST['quantity'])) $quantity = $_POST['quantity'];
if(!$svc && isset($_POST['svc'])) $svc = $_POST['svc'];
if(!$tax && isset($_POST['tax'])) $tax = $_POST['tax'];
if(!$gross && isset($_POST['gross'])) {
	$gross = $_POST['gross'];
} elseif (isset($arrayXO['amountAfterTax'])) {
	$gross = $arrayXO['amountAfterTax'];
}

if(!$std_svc && isset($_POST['std_svc'])) $std_svc = $_POST['std_svc'];
if(!$std_tax && isset($_POST['std_tax'])) $std_tax = $_POST['std_tax'];
if(!$rateid && isset($_POST['rateid'])) $rateid = $_POST['rateid'];
if(!$itemid && isset($_POST['itemid'])) $itemid = $_POST['itemid'];
if(!$guestid && isset($_POST['guestid'])) $guestid = $_POST['guestid'];
if(!$transDetail && isset($_POST['transDetail'])) $transDetail = $_POST['transDetail'];
$fop=$_POST["fop"];

$checkin="";
$checkout="";
$zerobalance = 0;
$linecr = '';		
if (isset($_GET['action'])){
	$action=$_GET['action'];                                                                                                                                          
	$search=$_GET['search'];
	switch ($action) {
		case 'void':
//			print "Void".$id."-".$tid."<br/>\n";
			if($id && $tid) transaction_void($id, $tid);
			if($id && $pid) receipt_void($id, $pid);
			break;
		case 'search':
			break;
	}		
}

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['INV_addroomchg']:
			if($id) {
				$checkin = $_POST['checkindate'];
				$checkout = $_POST['checkoutdate'];
				$userid=$_SESSION["userid"];
				$details=array();
				$resid=get_ReservationID_By_BillID($id);
				$resdetailcount = reservation_details_byResID($resid,$details);
				if($resid) {
						
					foreach($details as $dt){
						$rateid = $dt['ratesid'];
						$roomid=$dt['roomid'];
						add_roomcharges($id, $roomid, $rateid, $checkin, $checkout, $userid);
					}
				}
				else 
				{
					add_roomcharges($id, $roomid, $rateid, $checkin, $checkout, $userid);
				}

			}
			break;
		case $_L['BTN_update']:
			//filed validations
			$fv=new formValidator(); //from functions.php				
			if(isset($_POST['modifytrans'])&&$_POST['modifytrans']){				
				$std_itmamnt='std_amt_'.$_POST['modifytrans'];
				$itmamnt='amount_'.$_POST['modifytrans'];
				$itmqty='qty_'.$_POST['modifytrans'];				
			
				if($_POST['std_amount']=="" && $_POST['itemid']!="0"||$_POST[$std_itmamnt]==""){
					$fv->addErrormsg($_L['INV_nostdamnt_err']);
				}
				if($_POST['amount']=="" && $_POST['itemid']!="0"||$_POST[$itmamnt]==""){
					$fv->addErrormsg($_L['INV_noamnt_err']);
				}
				if($_POST['quantity']=="" &&$_POST['itemid']!="0"||$_POST[$itmqty]==""){
					$fv->addErrormsg($_L['INV_noqty_err']);
				}
			
				if($_POST['std_amount']!="" &&$_POST['itemid']!="0"&&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['std_amount']) ||
					$_POST[$std_itmamnt]!="" &&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST[$std_itmamnt])){
					$fv->addErrormsg($_L['INV_validstdamnt_err']);
				}
				if($_POST['amount']!="" &&$_POST['itemid']!="0"&&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['amount']) ||
					$_POST[$itmamnt]!="" &&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST[$itmamnt])){
					$fv->addErrormsg($_L['INV_validamnt_err']);
				}
				
				if($_POST['std_amount']!="" &&$_POST['itemid']!="0"&&preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['std_amount'])){
					if($_POST['std_amount']<=0)
						$fv->addErrormsg($_L['INV_stdamntgrtz_err']);
				}elseif ($_POST[$std_itmamnt]!=0 && preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST[$std_itmamnt])){
					if($_POST[$std_itmamnt]<=0)
						$fv->addErrormsg($_L['INV_stdamntgrtz_err']);
				}
				if($_POST['amount']!="" &&$_POST['itemid']!="0"&&preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['amount'])){
					if($_POST['amount']<=0)
						$fv->addErrormsg($_L['INV_amntgrtz_err']);
				}elseif ($_POST[$itmamnt]!=0 && preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST[$itmamnt])){
					if($_POST[$itmamnt]<=0)
						$fv->addErrormsg($_L['INV_amntgrtz_err']);
				}
				
				if($_POST['quantity']!="" &&$_POST['itemid']!="0"&&!preg_match("/^[0-9]+$/",$_POST['quantity']) || 
					$_POST[$itmqty]!="" &&!preg_match("/^[0-9]+$/",$_POST[$itmqty])){
					$fv->addErrormsg($_L['INV_validqty_err']);
				}
				if($_POST['quantity']!="" &&$_POST['itemid']!="0"&&preg_match("/^[0-9]+$/",$_POST['quantity'])){
					if($_POST['quantity']<=0)
						$fv->addErrormsg($_L['INV_qtygrtz_err']);
				}elseif($_POST[$itmqty]!="" &&preg_match("/^[0-9]+$/",$_POST[$itmqty])){
					if($_POST[$itmqty]<=0)
						$fv->addErrormsg($_L['INV_qtygrtz_err']);
				}
				
			}elseif(isset($_POST['itemid']) && $_POST['itemid']!="0"){						
				$fv->EmptyCheck('std_amount',$_L['INV_nostdamnt_err']);
				$fv->EmptyCheck('quantity',$_L['INV_noqty_err']);
				
				if($_POST['std_amount']!="" &&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['std_amount'])){
					$fv->addErrormsg($_L['INV_validstdamnt_err']);
				}
				if($_POST['amount']!="" &&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['amount'])){
					$fv->addErrormsg($_L['INV_validamnt_err']);
				}
				
				if($_POST['std_amount']!="" &&preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['std_amount'])){
					if($_POST['std_amount']<=0)
						$fv->addErrormsg($_L['INV_stdamntgrtz_err']);
				}
				if($_POST['amount']!="" &&preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['amount'])){
					if($_POST['amount']<=0)
						$fv->addErrormsg($_L['INV_amntgrtz_err']);
				}
				if($_POST['quantity']!=""&&!preg_match("/^[0-9]+$/",$_POST['quantity'])){
					$fv->addErrormsg($_L['INV_validqty_err']);
				}
				if($_POST['quantity']!=""&&preg_match("/^[0-9]+$/",$_POST['quantity'])){
					if($_POST['quantity']<=0)
						$fv->addErrormsg($_L['INV_qtygrtz_err']);
				}
			}		
			
			if($_POST['fop']&&$_POST['fop']!=0 && ($_POST['fop']==FOP_CC  || $_POST['fop']==FOP_CC_DEP) ) {
				$fv->EmptyCheck('CCnum',$_L['CC_noccnum_err']);
				if(empty($_POST['cctype'])){
					$fv->addErrormsg($_L['CC_nocctype_err']);
				}
				$fv->EmptyCheck('expiry',$_L['CC_noexpiry_err']);
				$fv->EmptyCheck('cvv',$_L['CC_nocvv_err']);
				
				if(!empty($_POST['CCnum'])&&!preg_match("/^[0-9]+$/",$_POST['CCnum'])){
					$fv->addErrormsg($_L['CC_ccnum_numeric_err'] );
				}
				if(!empty($_POST['expiry'])&&!preg_match("/^[0-9]+$/",$_POST['expiry'])){
					$fv->addErrormsg($_L['CC_expiry_fmt_err']);
				}elseif(!empty($_POST['expiry'])&&preg_match("/^[0-9]+$/",$_POST['expiry'])&&strlen($_POST['expiry'])<4){
					$fv->addErrormsg($_L['CC_expiry_fmt_err']);
				}							
				if(!empty($_POST['cvv'])&&!preg_match("/^[0-9]+$/",$_POST['cvv'])){
					$fv->addErrormsg($_L['CC_cvv_numeric_err']);
				}elseif(!empty($_POST['cvv'])&&(strlen($_POST['cvv'])<3)){
					$fv->addErrormsg($_L['CC_cvv_numdigit_err']);
				}
				
				$fv->EmptyCheck('cardname',$_L['CC_noname_err']);
				$fv->EmptyCheck('rcpt_amt',$_L['INV_norcptamnt_err']);					
			}
			if($_POST['fop']&&$_POST['fop']!=0) {
				
				if($_POST['fop']&& $_POST['fop']!=2){
//					if($_POST['fop']!= FOP_CASH && $_POST['fop']!= FOP_CASH_DEP)
//						$fv->EmptyCheck('auth',$_L['INV_norcptauth_err']);
					$fv->EmptyCheck('rcpt_amt',$_L['INV_norcptamnt_err']);
					$fv->EmptyCheck('cardname',$_L['CC_noname_err']);
				}
				
				
				if(!empty($_POST['cardname'])&&!preg_match("/^[\sa-zA-Z]+$/",$_POST['cardname'])){
					$fv->addErrormsg($_L['CC_validname_err']);
				}
				if($_POST['rcpt_amt']!="" &&!preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['rcpt_amt'])){
					$fv->addErrormsg($_L['INV_validrcptamnt_err']);
				}
				if($_POST['rcpt_amt']!="" &&preg_match("/^[0-9]*(\.[0-9]+)?+$/",$_POST['rcpt_amt'])){
					if($_POST['rcpt_amt']<=0)
						$fv->addErrormsg($_L['INV_rcptamntgrtz_err']);
				}
			}
			if(isset($_POST['chkPay'])){
				 foreach($_POST['chkPay'] as $idx => $val){
					if(empty($_POST['txtExrate_'.$idx]) || $_POST['txtExrate_'.$idx]==0){
						$fv->addErrormsg($_L['INV_exrategrtz_err']);
					}
				}
			}
			
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			}else{
				if(isset($_POST['AUTH_RCPTID']) && isset($_POST['AUTH_VALUE'])) {
					update_receipt_auth($_POST['AUTH_RCPTID'], $_POST['AUTH_VALUE']);
				}
				$userid=$_SESSION["userid"];
				if(!$bid) $bid = $_POST['bid'];
				if(!$rid) $rid = $_POST['rid'];
				$CCnum = $_POST['CCnum'];
				$cvv = $_POST['cvv'];
				$rcpt_date = $_POST['rcpt_date'];
				$cardname = $_POST['cardname'];
				$cctype = $_POST['cctype'];
				$auth = $_POST['auth'];
				$expiry = $_POST['expiry'];
				$rcpt_amt = $_POST['rcpt_amt'];
				$trans_date = $_POST['trans_date'];
				$std_amount = $_POST['std_amount'];
				$std_svc = $_POST['std_svc'];
				$std_tax = $_POST['std_tax'];
				$amount = $_POST['amount'];
				$quantity = $_POST['quantity'];
				$svc = $_POST['svc'];
				$tax = $_POST['tax'];
				$gross = $_POST['gross'];
				$rateid = $_POST['rateid'];
				$itemid = $_POST['itemid'];
				$billno=$_POST["billno"];
				$fop=$_POST["fop"];
				$roomid=$_POST["roomid"];
				if(!$amount) $amount = $std_amount;
				if(!$tax) $tax = $std_tax;
				if(!$svc) $svc = $std_svc;
				$status = $_POST['status'];
				$totaldue = $_POST['totaldue'];
				$total = $_POST['itemtotal'];
				$date_billed=$_POST['date_billed'];
				$date_verified=$_POST['date_verified'];
				$created_by=$_POST['created_by'];
				if(!$created_by) $created_by = $userid;
				$guestid=$_POST['guestid'];
				$modid = $_POST['modifytrans'];
				$currency = $_POST['selCurrencyGross'];
				$transDetail = $_POST['transDetail'];
				$notes = $_POST['notes'];
				$flags = $_POST['flags'];
				if($status == STATUS_CLOSED && $id && $date_verified == '') {
					$date_verified = $today;
				}
				if($totaldue == 0 && $total <> 0 && $status == STATUS_OPEN && $id) {
					$date_verified = $today;
					$status = STATUS_CLOSED;
				}
				if($totaldue <> 0 && $status == STATUS_CLOSED) {
					$status = STATUS_OPEN;
					$date_verified = '';
				}
		//		echo "Modify bill ".$id." Bill ".$billno." Booking id ".$bid."<br/>\n";
				$id = modify_bill($id,$billno,$bid,$rid,$date_billed,$date_verified,$created_by,$guestid,$status,$notes,$flags);
				// Add a new transaction item
	//			echo "Modify transaction ".$id. " item ".$itemid." amount ".$std_amount." date ".$trans_date." <br/>";
				if($id && $itemid && $std_amount) {
					$gross = get_ratecharges($itemid, $quantity, $roomid, $trans_date, $rateid, $std_amount, $std_tax, $std_svc, $amount, $tax, $svc);
					modify_transaction(0, $id,$itemid,$today, $trans_date, $userid, $std_amount, $std_svc, $std_tax, $amount, $tax, $svc,$quantity,$rateid,$transDetail,$gross,$currency);
				}

				// An item was selected for amendment.
				if($modid) {
					$itm = $_POST['itemid_'.$modid];
					$addby = $_POST['add_by_'.$modid];
					$dat = $_POST['add_date_'.$modid];
					$trdate = $_POST['trans_date_'.$modid];
					$std_amt = $_POST['std_amt_'.$modid];
					$std_tx = $_POST['std_tax_'.$modid];
					$std_sv = $_POST['std_svc_'.$modid];
					$amt = $_POST['amount_'.$modid];
					$tx = $_POST['tax_'.$modid];
					$sv = $_POST['svc_'.$modid];
					$qty = $_POST['qty_'.$modid];
					$rid = $_POST['ratesid_'.$modid];
					$currency = get_Currency_byTransactionID($modid);
					$transDetail = $_POST['transDetail_'.$modid];
					$XOID = $_POST['XOID_'.$modid];
					modify_transaction($modid, $id,$itm,$dat, $trdate, $addby, $std_amt, $std_sv, $std_tx, $amt, $tx, $sv,$qty,$rid,$transDetail,'',$currency,$XOID);
				}
				// add a new payment
				if($id && $fop && $rcpt_amt) {
					if($fop == FOP_CASH_DEP || $fop == FOP_CC_DEP) {
						$tgtCurrency=$_POST['DepCurrency'];
						$srcCurrency=$_POST['DepCurrency'];
						$exrate=1;	
						if($fop == FOP_CASH_DEP) {
							$fop = FOP_CASH;
							$cardname = "Deposit ".$cardname;
						}
						if($fop == FOP_CC_DEP) {
							$fop = FOP_CC;
							$cardname = "Deposit ".$cardname;
						}
//						echo "Add deposit<br/>";
						modify_receipt(0, $id, $bid, $rid, "", $rcpt_date, $fop, $cctype, $CCnum, $expiry, $cvv, $auth,$cardname, $rcpt_amt, $userid, $today,$exrate,$srcCurrency,$tgtCurrency);
					} else {
						foreach ($_POST['chkPay'] as $idx=>$val){						
							$tgtCurrency=$_POST['txtrcptamt_curr'];
							$srcCurrency=$_POST['txtSrcCurr_'.$val];
							$exrate=$_POST['txtExrate_'.$val];	
							$rcptamt=$_POST['txtrcptpay_'.$val];
							if($fop == FOP_CASH_DEP) {
								$fop = FOP_CASH;
								$cardname = "Deposit ".$cardname;
							}
							if($fop == FOP_CC_DEP) {
								$fop = FOP_CC;
								$cardname = "Deposit ".$cardname;
							}
							modify_receipt(0, $id, $bid, $rid, "", $rcpt_date, $fop, $cctype, $CCnum, $expiry, $cvv, $auth, $cardname, $rcptamt, $userid, $today,$exrate,$srcCurrency,$tgtCurrency);
						}
					}
				}
				//Sucessfull save reset varibles
				$CCnum = "";
				$cvv = "";
				$rcpt_date = "";
				$cardname = "";
				$cctype = "";
				$auth = "";
				$expiry = "";
				$cctype = "";
				$rcpt_amt = "";
				$trans_date = "";
				$std_amount = "";
				$amount = "";
				$quantity = "";
				$svc = "";
				$tax = "";
				$gross = "";
				$std_svc = "";
				$std_tax = "";
				$itemid = "";
				$fop="";
			}
			
			break;
		case $_L['INV_refund']:
				if(isset($_POST['refund']) ) {
					$i = $_POST['refund'];
					if(!$bid) $bid = $_POST['bid'];
					if(!$rid) $rid = $_POST['rid'];
					$dt = "ref_date".$i;
					$ref_date = $_POST[$dt];
					$cctp = "refcctype".$i;
					$refcctype = $_POST[$cctp];
					$rfop = "reffop".$i;
					$reffop = $_POST[$rfop];
					$rfamt = "refamount".$i;
					$refamount = $_POST[$rfamt];
					$rfcur = "refcur".$i;
					$refcur = $_POST[$rfcur];
					$userid=$_SESSION["userid"];
					if($refamount > 0) $refamount = 0 - $refamount;
					$linecr .= "Refund ".$i." ".$refamount." ".$reffop." ".$refcur."<br/>";
					if($reffop && $refamount && $refcur) {	
						modify_receipt(0, $id, $bid, $rid, "", $ref_date, $reffop, $refcctype, "", "", "", "Refund", "Refund", $refamount, $userid, $today,1,$refcur,$refcur);
					}
				}
				break;
		case 'Find':
			//check if user is searching using name, payrollno, national id number or other fields
			break;
	}
}

$bill = array();
$book = array();
$res = array();
// If the bill id is present get it and set the booking and reservations ids.
if($id) {
	if(get_bill($id, $bill)) {
		$bid = $bill['book_id'];
		$rid = $bill['reservation_id'];
	}
}
$ssl="";
if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }

$MISreturnURL = 'http'.$ssl.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?id=".$bill['bill_id']; //referer for XO

// If the booking id is present load the booking
if($bid) {
	get_booking($bid, $book);
	// If the bill id is not set, ie get bill by booking
	// retrieve the bill and set the reservation id
	if(!$id && sizeof($bill) == 0) {
		$id= $book['bill_id'];
		get_bill($id, $bill);
		$rid = $bill['reservation_id'];
	}
}
// if the reservation id is found, load the reservation
if($rid) {
	get_reservation($rid, $res);
	// if the bill is not loaded, load the invoice id from the reservation
	// load the bill, then attempt to load the booking detail that goes
	// with it.
	if(!$id && sizeof($bill) == 0) {
		$id= $res['bill_id'];
		get_bill($id, $bill);
		$bid = $bill['book_id'];
		get_booking($bid, $book);
	}
}
$guestid = $bill['guestid'];
if(!$guestid && $book['guestid']) $guestid = $book['guestid'];
if(!$guestid && $res['guestid']) $guestid = $res['guestid'];
if($book['checkindate']) $checkin =  $book['checkindate'];
if($book['checkoutdate']) $checkout =  $book['checkoutdate'];

$guestname = $book['guestname'];

if(!$trans_date) $trans_date=$today;
if(!$rcpt_date) $rcpt_date=$today;
if(!$quantity) $quantity = 1;
if($bid && ! $rateid) $rateid = get_bookingrate($bid);
if(!$guestname) $guestname = $res['guestname'];
if(!$guestname) $guestname = get_guestname($guestid);


$mscludge = "";
if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
	$mscludge = 'onfocus="javascript: this.style.width=\'auto\';" onblur="javascript: this.style.width=18;"';
}
$i=0;
while ($i < $bill['transcount']) {
	if($bill['trans'][$i]['status'] != STATUS_VOID) {
		if(isset($totalArr[$bill['trans'][$i]['currency']]) ){
			$totalArr[$bill['trans'][$i]['currency']] += $bill['trans'][$i]['grossamount'];
		}else{
			$totalArr[$bill['trans'][$i]['currency']]=$bill['trans'][$i]['grossamount'];
		}
	}
	$i++;
}
//calculating balance due for each currency code
					
foreach($totalArr as $idx => $val){
	$balanceDue=0;
	$dueArr[$idx] = $val;
	$linecr .= "Due ".$idx." ".$dueArr[$idx]."<br/>";
}						
for($i=0; $i < $bill['rcptcount']; $i++) {
	$idx = $bill['rcpts'][$i]['srcCurrency'];
	if($bill['rcpts'][$i]['status'] != STATUS_VOID) {
		if(!isset($refunds[$idx])) {
			$linecr .= "Set ".$idx."<br/>";
			$refunds[$idx] = $bill['rcpts'][$i]['amount'];
		} else {
			$refunds[$idx] += $bill['rcpts'][$i]['amount'];
		}
		$linecr .= "Ref ".$idx."-".$refunds[$idx]."<br/>";
	}
}
foreach($dueArr as $idx => $val) {
	if($dueArr[$idx] >= $refunds[$idx]) {
		$linecr .= "Calc ".$dueArr[$idx]." gte ".$refunds[$idx] ."=";
		$dueArr[$idx] -= $refunds[$idx];
		$refunds[$idx] = 0;
		$linecr .= $dueArr[$idx]."<br/>";
	} else {
		$refunds[$idx] -= $dueArr[$idx];
		$dueArr[$idx] = 0;
		$linecr .= "Due ".$idx."=0<br/>";
	}
	$linecr .= "Calc ".$idx." ".$refunds[$idx]."<br/>";
}
$dep_html = "<select name=DepCurrency id=DepCurrency>";
foreach($currencyArr as $idx=>$curr){
	$dep_html .= "<option value=".$curr;				              		
	$dep_html .= ">".$curr."</option>";
}
$dep_html .= "</select>";
						
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<link href="css/new.css" rel="stylesheet" type="text/css" />
	<link href="css/styles2.css" rel="stylesheet" type="text/css" />
	<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
	
	<script type="text/javascript" src="js/dhtmlgoodies_calendar.js" />
	<script type="text/javascript" src="js/datefuncs.js"></script>
	<script type="text/javascript" src="js/ccards.js"></script>
	<title><?php echo $_L['MAIN_Title']." ".$_L['MNU_billing'];?></title>
	<style>
	  .plainDropDown{
		width:130px;
		font-size:11px;
	  }
	  .plainDropDown2{
		width:80px;
		font-size:11px;
	  }
	  .plainSelectList{
		width:250px;
		font-size:11px;
	  }
	  .plainButton {
		font-size:11px;	
	  }
	  .plaintable {
		font-size:11px;
	  }
	  th {
		font-size:10px;
	  }
	  input {
		font-size:10px;
	
	  }
	  .submitLink {
		background-color: transparent;
		text-decoration: underline;
		border: none;
		color: blue;
		cursor: pointer;
	}
	</style>
	<script type="text/javascript">
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
		guestid.value = val;
		guestname.value = txt;
//		document.forms[0].submit();	
	  }
	  /**
	   * This function is to update tax and service fee 
	   * 
	   * @param amt [in] field name for amount
	   * @param tx [in/out] field name for tax
	   * @param sf [in/out] field name for service fee
	   */
	  function update_tax_svc(amt,tx,sf) {
		var val=document.getElementById(amt);
		var tax=document.getElementById(tx);
		var svc=document.getElementById(sf);
		var updtaxsvc = true;
		var inamt = val.value * 1;
		var orig = val.value * 1;
		var taxamt = 0;
		var svcamt = 0;
		var inctaxsvc = confirm("Amount Entered includes Tax & Service? Yes(OK) No(Cancel)");
		if(inctaxsvc == true) {
			updtaxsvc = confirm("Split out Tax and Service? Yes(OK) No(Cancel)");
		} else {
			updtaxsvc = confirm("Calculate Tax and Service? Yes(OK) No(Cancel)");
		}
		/* Just ignore the tax service update */
		if(updtaxsvc == false) {
			tax.value = 0;
			svc.value = 0;
			return;
		}
		if(inctaxsvc == false) {
			tax.value = (inamt * <?php echo TAXPCT; ?>/100);
			svc.value = (inamt * <?php echo SVCPCT; ?>/100);
		} else {
			taxamt = roundNumber(((inamt / (100 + <?php echo SVCPCT." + ".TAXPCT; ?>)*<?php echo TAXPCT; ?>)),2);
			svcamt = roundNumber((inamt / (100 + <?php echo SVCPCT." + ".TAXPCT; ?>)*<?php echo SVCPCT; ?>),2);
			inamt = roundNumber(((inamt / (100 + <?php echo SVCPCT." + ".TAXPCT; ?>))*100),2);
			// in case the rounding is wrong round up.
			if(orig < (inamt + svcamt + taxamt)) {
				taxamt = taxamt - 0.01;
			}
			// check again
			if(orig < (inamt + svcamt + taxamt)) {
				svcamt = svcamt - 0.01;
			}
			// check again
			if(orig < (inamt + svcamt + taxamt)) {
				inamt = inamt - 0.01;
			}
			// now check for round down errors
			if(orig > (inamt + svcamt + taxamt)) {
				inamt = inamt + 0.01;
			}
			// check again
			if(orig > (inamt + svcamt + taxamt)) {
				taxamt = taxamt + 0.01;
			}
			// check again
			if(orig > (inamt + svcamt + taxamt)) {
				svcamt = svcamt + 0.01;
			}
			svc.value = svcamt;
			tax.value = taxamt; 
			val.value = inamt;
		}
	  }

	  function updatePaymentOnchange(){
	  	var chkpayArr = document.getElementsByName('chkPay[]');	
	  	var totAmnt =0;	
	  	
	  	for(var i=0 ;i< chkpayArr.length;i++) {
	  		if(chkpayArr[i].checked) {
	  			var exrate =  document.getElementById("txtExrate_"+i).value*1.0;
	  			var bdue = document.getElementById("txtbalanceDue_"+i).value*1.0;
	  			totAmnt = totAmnt + exrate*bdue;
	  			//only one is checked
	  			//so the user can edit the amount to be pay
	  			var partialamnt = document.getElementById("rcpt_amt").value;
	  			document.getElementById("txtrcptpay_"+i).value = partialamnt/exrate;			
	  		 }
	  	 }
	  }
	  //-->	 
	</script>
	<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
<?php
	$onsubmit = '';
?>
  </head>
  <body>
	<form action="index.php?menu=invoice" method="post" enctype="multipart/form-data" id="billing" name="billing" <?php echo $onsubmit; ?>>
	  <table class="listing-table" height="500" border="0" cellpadding="1" >
		<tr valign="top">
		   <?php 
			if ($_GET['menu'] == "invoice") {
				print_rightMenu_home();
			}?> 
		  <td  class="c3">
			<table width="100%">
			 <tr><td><h2><a href="https://www.youtube.com/watch?v=XASuz7Kw4WQ" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['INV_title']; ?></a></h2></td></tr>
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
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['INV_charge']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['REG_pmt']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['INV_refund']; ?></li>
					<li class="TabbedPanelsTab" tabindex="<?php echo $tabidx;?>" onclick="getTabIndex(<?php echo $tabidx; $tabidx++;?>);"><?php echo $_L['REG_notes']; ?></li>
				</ul>
			  
			  <div class="TabbedPanelsContentGroup">
				<!-- TAB INVOICE INFORMATION-->
				<div class="TabbedPanelsContent">
					<table width="100%">
					<tr><td>&nbsp;
						<input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/>
					</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					  <td Style="padding:10px;"align=left>
						<strong><?php echo $_L['STS_title']; ?> &nbsp;</strong> </td>
					  <td align=left>
						<select name=status id=status onchange="if (this.value=='<?php echo STATUS_CLOSED; ?>') {
							var r = confirm('<?php echo $_L['INV_closemsg']; ?>');
							if(r == false) {
								this.options[0].selected = true;
							};
						}" >
						  <option value="<?php echo STATUS_OPEN; ?>" <?php if($bill['status'] == STATUS_OPEN) echo "selected"; ?> ><?php echo $_L['STS_open']; ?></option>
						  <option value="<?php echo STATUS_CLOSED; ?>" <?php if($bill['status'] == STATUS_CLOSED) echo "selected"; ?> ><?php echo $_L['STS_closed']; ?></option>
						  <option value="<?php echo STATUS_CANCEL; ?>" <?php if($bill['status'] == STATUS_CANCEL) echo "selected"; ?> ><?php echo $_L['STS_cancel']; ?></option>
						  <option value="<?php echo STATUS_VOID; ?>" <?php if($bill['status'] == STATUS_VOID) echo "selected"; ?> ><?php echo $_L['STS_void']; ?></option>
						</select>
						<?php 
						echo "<input type=text name='billid' value='".$id."' />\n";
						if($bill['date_billed']) echo "<input type=hidden name=date_billed value='".$bill['date_billed']."' />\n";
						if($bill['date_verified']) echo "<input type=hidden name=date_verified value='".$bill['date_checked']."' />\n";
						if($bill['created_by']) echo "<input type=hidden name=created_by value='".$bill['created_by']."' />\n";
						if($bill['book_id']) echo "<input type=hidden name=bid value='".$bill['book_id']."' />\n";
						if($bill['reservation_id']) echo "<input type=hidden name=rid value='".$bill['reservation_id']."' />\n";
						echo "<input type=hidden name=guestid id=guestid value='".$guestid."' />\n";
						if($bill['flags']) {
							$flags = $bill['flags'];
						}
						echo "<input type=hidden name=flags id=flags value='".$flags."' />\n";
						?>
					  </td>
					  <td align=left>
					  <strong><?php echo $_L['USR_billlist']; ?></strong>   </td>
					  <td align=left>
						<select name=bill_id id=bill_id onchange="document.forms[0].submit()">
						  <option value="0"> </option>
						  <?php $cond = "status=".STATUS_OPEN;
						  if($id) $cond .= " or bill_id=".$id;
						  populate_select("bills","bill_id","billno",$id,$cond); ?>
						</select>
					    <input type=text id=bill_search name=bill_search size=10 maxlength=15>
						<input type=image src='images/button_view.png' alt='Search' />
						
						
					  </td>
					</tr>
					<tr>
					<td  Style="padding:10px;"><strong><?php echo $_L['INV_voucher']; ?></strong></td>
					<td><input type="text" name="voucher_no" size="10" readonly="readonly" value="<?php echo $res['voucher_no']; ?>"/>
					  <?php if($rid) { ?>
					  <a href="index.php?menu=reservation&resid=<?php echo $rid;?>" target='reservations' class="button"><?php echo $_L['ADM_reservation'];?></a>
					  <?php } 
							if($bid) {
					  ?>
					  <a href="index.php?menu=booking&id=<?php echo $bid;?>" target='bookings' class="button"><?php echo $_L['RGL_book'];?></a>
					  <?php } ?></td>
					<td><strong><?php echo $_L['INV_invoice']; ?></strong></td>
					<td><input type="text" name="billno" size="10" readonly="readonly" value="<?php echo $bill['billno']; ?>" /></td>
					
					</tr>
					<tr>
					<td  Style="padding:10px;"><strong><?php echo $_L['INV_guestname']; ?></strong></td>
					  <td>
						<input type="text" name="guestname" id="guestname" size=15 maxlength=50 readonly="readonly" value="<?php echo $guestname; ?>"/>
						<?php if(! $guestname) { ?>
						<select name=tguestid id=tguestid onchange="updateguestname();" <?php echo $mscludge; ?> >
						  <option value=0 title="Select an Option" > </option>
						  <?php 
						        populate_select("guests", "guestid", "firstname,lastname", $bill['guestid'], "");
						  ?>						  
						</select>
						<?php } else {?>
						<a href="index.php?menu=editprofile&id=<?php echo $guestid;?>" target='guests' class="button"><?php echo $_L['BTN_details'];?></a>
						<?php } ?>
					  </td>
					  <td><strong><?php echo $_L['INV_room']; ?></strong></td>
					  <td><input type="text" name="roomno" size="10" readonly="readonly" value="<?php echo get_roomno($book['roomid']); ?>"/>
						  <input type="hidden" name="roomid" value="<?php echo $book['roomid']; ?>"/>
					  </td>
					
					</tr>
					<tr>
					  <td Style="padding:10px;"><strong><?php echo $_L['INV_datein']; ?></strong></td>
					  <td>
						<img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkindate,'dd/mm/yyyy hh:ii',this, true, 1400)" />
						<input type="text" name="checkindate" id="checkindate" size=16 maxlength=16 readonly="readonly" value="<?php echo trim($book['checkindate']);?>" /> 
					  </td>
					  <td><strong><?php echo $_L['INV_dateout']; ?></strong></td>
					  <td>
						<img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].checkoutdate,'dd/mm/yyyy hh:ii',this, true, 1400)" />
						<input type="text" name="checkoutdate" id="checkoutdate" size=16 maxlength=16 readonly="readonly" value="<?php echo trim($book['checkoutdate']);?>" /> 
					  </td>
					</tr>
					<tr>
					<td>&nbsp;</td>
					</tr>
					
				  </table><br/><br/>
				<table border="1" width=75%>
				
					<tr bgcolor="#CCCCCC">
						<td colspan=9 align=center><b><?php echo $_L['INV_total']." ".$_L['INV_charge']; ?></b></td></tr><tr>
						  <?php if(count($totalArr) == 0) {
							echo" <td><b>0</b></td>";
							$zerobalance = 1;
						  } else {
							echo "</tr>";
							foreach($totalArr as $idx => $val){?>
							<tr>
						 	<td align=right><b id="itmstotal_<?php echo $idx;?>"><?php echo sprintf("%02.2f",$val)." ".$idx; ?></b></td>
							</tr>
						  <?php }
						  }?>
					<tr bgcolor="#CCCCCC">
						<td colspan=9 align=center><b><?php echo $_L['INV_due']; ?></b></td>
					</tr>
					
							<?php 
							//printing balance due for each currency code
							if(count($dueArr)>0){
								$printBalanceTitle=1;
								$numDRs=0;	
								
								//only print the header if at least one DR is there										
								foreach($dueArr as $idx => $balanceDue){
									if($balanceDue>0){
										$numDRs++;	
									}	
									if($printBalanceTitle && $balanceDue>0){
										$printBalanceTitle=0;						
									}	
								}?>
								<?php 	
								$dueIdx=0;											
								foreach($dueArr as $idx => $balanceDue){															
								?>
								<tr align=right>							
								
								<td>
								<b id="balanceDue_<?php print $dueIdx;?>"><?php echo sprintf("%02.2f",$balanceDue); if($balanceDue<0) print " CR"; elseif($balanceDue>0) print " DR";?>
								<?php print $idx;?></b>
								</td>
								
							
								</tr>
								<?php
									if($balanceDue>0){
										$dueIdx = $dueIdx+ 1;
									}								
								}
								?>
								
							<?php 
							}else{
								// receipt and no charges
								if($bill['rcptcount'] > 0 && ($bill['transcount'] == 0 || $zerobalance) && $paytotal > 0) { 
									$br = "";
									foreach ($refunds as $cur => $refamount) {
										if($refamount == 0 )
											continue;
										if($refamount < 0) $refamount = 0 - $refamount;
										print $br. sprintf("%02.2f", $refamount). " ".$cur. " CR";
										$br = "<br/>";
									}
								} else {
									//print "0";
								}
							}?>								
					
					
				</table>
				  
				</div>
				<!-- TAB chages INFORMATION-->
				<div class="TabbedPanelsContent" style="align-content:center;">
				
				 <div class="scrolltabinv">
				
					<table  align=center border="1" cellpadding="1" width="100%" cellspacing="0">
						<tr bgcolor="#3593de">
							<th rowspan=3> </th>
							<th rowspan=3><?php echo $_L['INV_date']; ?></th>
							<th colspan=2><?php echo $_L['INV_item']; ?></th>
							<th><?php echo $_L['INV_rate']; ?></th>
							<th colspan=3 rowspan=2><?php echo $_L['INV_charged']; ?></th>
							<th rowspan=3><?php echo $_L['INV_qty']; ?></th>
							<th rowspan=3 colspan=2><?php echo $_L['INV_total']; ?></th>
						</tr>
						<tr bgcolor="#3593de">
							<th colspan=3><?php echo $_L['INV_std']; ?></th>

						</tr>
						<tr bgcolor="#3593de">
							<th><?php echo $_L['INV_amount']; ?></th>
							<th><?php echo $_L['INV_service']; ?></th>
							<th><?php echo $_L['INV_tax']; ?></th>
							<th><?php echo $_L['INV_amount']; ?></th>
							<th><?php echo $_L['INV_service']; ?></th>
							<th><?php echo $_L['INV_tax']; ?></th>
						</tr>
						<?php
						  $itemstotal = 0;
						  //get data from selected table on the selected fields
						  if($bill['transcount'] > 0) {
							$i = 0;
							while ($i < $bill['transcount']) {
							  //alternate row colour
							  $j++;
							  if($j%2==1){
								echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
							  }else{
								echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
							  }
							  if($bill['trans'][$i]['status'] == STATUS_VOID) {
								echo "<td rowspan=2></td>";
								echo "<td rowspan=2><del>" . $bill['trans'][$i]['trans_date'] . "</del></td>";
								echo "<td colspan=2><del>" . $bill['trans'][$i]['item_id'] . "</del></td>";
								echo "<td><del>" . get_ratecode($bill['trans'][$i]['ratesid']) . "</del></td>";
								echo "<td rowspan=2><del>" . $bill['trans'][$i]['amount'] . "</del></td>"; 
								echo "<td rowspan=2><del>" . $bill['trans'][$i]['svc'] . "</del></td>";
								echo "<td rowspan=2><del>" . $bill['trans'][$i]['tax'] . "</del></td>"; 
								echo "<td rowspan=2><del>" . $bill['trans'][$i]['quantity'] . "</del></td>"; 
								echo "<td rowspan=2 colspan=2><del>" . $bill['trans'][$i]['grossamount'] . "</del></td>"; 
								echo "</tr>"; //end of - data rows
								if($j%2==1){
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
								}else{
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
								}
								echo "<td><del>" . $bill['trans'][$i]['std_amount'] . "</del></td>";
								echo "<td><del>" . $bill['trans'][$i]['std_svc'] . "</del></td>";
								echo "<td><del>" . $bill['trans'][$i]['std_tax'] . "</del></td>"; //when negative don't show
							  } else {
								echo "<td rowspan=2 align=center><input type=radio name=modifytrans title='Update transaction' value='".$bill['trans'][$i]['trans_id']."'>";
								echo "<a href=\"index.php?menu=invoice&id=".$bill['bill_id']."&action=void&tid=".$bill['trans'][$i]['trans_id']."\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"void transaction\"/></a></td>";
								echo "<td rowspan=2>" . $bill['trans'][$i]['trans_date'];
								echo "<input type=hidden name='trans_date_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['trans_date']."' />\n";
								echo "<input type=hidden name='add_by_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['add_by']."' />\n";
								echo "<input type=hidden name='add_date_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['add_date']."' />\n";
								echo "<input type=hidden name='transDetail_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['details']."' />\n";
								echo "<input type=hidden name='XOID_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['XOID']."' />\n";
								echo "</td>";
								echo "<td colspan=2>";
								echo "<select name='itemid_".$bill['trans'][$i]['trans_id']."' class=plainDropDown >";
								populate_select("details","itemid","item",$bill['trans'][$i]['item_id'],""); 
								echo "</select> ";
								
								echo "</td>\n"; 
								//insert link button here trigger javascript to set hidden variables bottom of page and submit 
										//return url is the url for this invoice ???
										//if exchange order id is set then print pop up to go to Exchange order
								echo "<td><select name='ratesid_".$bill['trans'][$i]['trans_id']."' value='".$bill['trans'][$i]['ratesid']."' class='plainDropDown2' >"; 
								echo "<option value='0'> </option>";
								$cond ="";
								//$cond = "rate_type=".PROMORATE;
								// if there is an agent rate select it as well, but once unselected/saved it will be gone.
								//if($rateid) $cond .= " or ratesid = ".$bill['trans'][$i]['ratesid'];
								populate_select("rates","ratesid","ratecode",$bill['trans'][$i]['ratesid'], $cond);
								echo "</select></td>\n";
								echo "<td rowspan=2><input name='amount_".$bill['trans'][$i]['trans_id']."' id='amount_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
										value='";
										if(isset($_POST['amount_'.$bill['trans'][$i]['trans_id']])) print $_POST['amount_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['amount'];
										print "' 
										onkeyup='verify(this.id); '
										onchange=\"update_tax_svc('amount_".$bill['trans'][$i]['trans_id']."','tax_".$bill['trans'][$i]['trans_id']."','svc_".$bill['trans'][$i]['trans_id']."');
										\" /></td>\n"; 
								echo "<td rowspan=2><input name='svc_".$bill['trans'][$i]['trans_id']."' id='svc_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
										value='";
										if(isset($_POST['svc_'.$bill['trans'][$i]['trans_id']])) print $_POST['svc_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['svc'];
										print "' 
										onkeyup='verify(this.id); '
										onchange=\"calc_amnt('amount_".$bill['trans'][$i]['trans_id']."','tax_".$bill['trans'][$i]['trans_id']."','svc_".$bill['trans'][$i]['trans_id']."','','tdgross_".$bill['trans'][$i]['trans_id']."','qty_".$bill['trans'][$i]['trans_id']."');\" /></td>\n";
								echo "<td rowspan=2><input name='tax_".$bill['trans'][$i]['trans_id']."' id='tax_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
										value='";
										if(isset($_POST['tax_'.$bill['trans'][$i]['trans_id']])) print $_POST['tax_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['tax'];
										print "' 
										onkeyup='verify(this.id); '
										onchange=\"calc_amnt('amount_".$bill['trans'][$i]['trans_id']."','tax_".$bill['trans'][$i]['trans_id']."','svc_".$bill['trans'][$i]['trans_id']."','','tdgross_".$bill['trans'][$i]['trans_id']."','qty_".$bill['trans'][$i]['trans_id']."');\" /></td>\n"; 
								echo "<td rowspan=2><input name='qty_".$bill['trans'][$i]['trans_id']."' id='qty_".$bill['trans'][$i]['trans_id']."' size=2 maxlength=3  
										value='";
										if(isset($_POST['qty_'.$bill['trans'][$i]['trans_id']])) print $_POST['qty_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['quantity'];
										print "' 
										onkeyup='verify(this.id); '
										onchange=\"calc_amnt('amount_".$bill['trans'][$i]['trans_id']."','tax_".$bill['trans'][$i]['trans_id']."','svc_".$bill['trans'][$i]['trans_id']."','','tdgross_".$bill['trans'][$i]['trans_id']."','qty_".$bill['trans'][$i]['trans_id']."');\" /></td>\n"; 
								echo "<td rowspan=2 colspan=2 id='tdgross_".$bill['trans'][$i]['trans_id']."'>" . $bill['trans'][$i]['grossamount']; 
								echo " ".$bill['trans'][$i]['currency']."</td>";
								
								$itemstotal += $bill['trans'][$i]['grossamount'];
								
								echo "</tr>"; //end of - data rows
								if($j%2==1){
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
								}else{
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
								}
								echo "<td><input name='std_amt_".$bill['trans'][$i]['trans_id']."' id='std_amt_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
									value='";
									if(isset($_POST['std_amt_'.$bill['trans'][$i]['trans_id']])) print $_POST['std_amt_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['std_amount'];
									print "' 
									onkeyup='verify(this.id); '
									onchange=\"update_tax_svc('std_amt_".$bill['trans'][$i]['trans_id']."','std_tax_".$bill['trans'][$i]['trans_id']."','std_svc_".$bill['trans'][$i]['trans_id']."');
									calc_amnt('amount_".$bill['trans'][$i]['trans_id']."','tax_".$bill['trans'][$i]['trans_id']."','svc_".$bill['trans'][$i]['trans_id']."','','tdgross_".$bill['trans'][$i]['trans_id']."','qty_".$bill['trans'][$i]['trans_id']."');\"  /></td>\n";
								echo "<td><input name='std_svc_".$bill['trans'][$i]['trans_id']."' id='std_svc_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
									value='";
									if(isset($_POST['std_svc_'.$bill['trans'][$i]['trans_id']])) print $_POST['std_svc_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['std_svc'];
									print "' 
									onkeyup='verify(this.id); '/></td>\n";
								echo "<td><input name='std_tax_".$bill['trans'][$i]['trans_id']."' id='std_tax_".$bill['trans'][$i]['trans_id']."' size=5 maxlength=10  
									value='";
									if(isset($_POST['std_tax_'.$bill['trans'][$i]['trans_id']])) print $_POST['std_tax_'.$bill['trans'][$i]['trans_id']]; else print $bill['trans'][$i]['std_tax'];
									print "'
									onkeyup='verify(this.id); '/></td>\n"; //when negative don't show
							  }
							  echo "</tr>";
							  $i++;
							}
						  } //end of while row
						?>
						<tr>
							<td rowspan=2 colspan=2>
								<img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].trans_date,'dd/mm/yyyy',this, false, 1400)" />
								<input type="text" name="trans_date" id="trans_date"  size=10 maxlength=16 readonly value="<?php echo trim($trans_date);?>" /> 
							</td>
							<td colspan=2>
							  <select name=itemid id=itemid class=plainDropDown >
								<option value="0" > </option>
								<?php populate_select("details","itemid","item","$itemid",""); ?>
							  </select>
							</td>
							<td>
							  <select name=rateid class="plainDropDown2" >
								<option value=0> </option>
								<?php 
									$cond ="";
									//$cond = "rate_type=".PROMORATE;
									// if there is an agent rate select it as well, but once unselected/saved it will be gone.
									//if($rateid) $cond .= " or ratesid = ".$rateid;
									populate_select("rates","ratesid","ratecode","", $cond);?>
							  </select>
							</td>
							<td rowspan=2><input name='amount' id='amount' size=5 maxlength=10 value='<?php echo $amount; ?>'  onchange="update_tax_svc('amount','tax','svc');calc_amnt('amount','tax','svc','gross','','quantity');" onkeyup='verify(this.id); '/>  </td>
							<td rowspan=2><input name='svc' id='svc' size=5 maxlength=10 value='<?php echo $svc; ?>' onchange="calc_amnt('amount','tax','svc','gross','','quantity');" onkeyup='verify(this.id); '/>  </td>
							<td rowspan=2><input name='tax 'id='tax' size=5 maxlength=10 value='<?php echo $tax; ?>' onchange="calc_amnt('amount','tax','svc','gross','','quantity'); " onkeyup='verify(this.id); '/> </td>
							<td rowspan=2><input name='quantity' id='quantity' size=3 maxlength=3 value='<?php echo $quantity; ?>' onchange="calc_amnt('amount','tax','svc','gross','','quantity');" onkeyup='verify(this.id); '/></td>
							<td rowspan=2 style='border-right: 0;' ><input name='gross' id='gross' readonly size=5 value='<?php echo $gross; ?>' /></td>
							<td rowspan=2 style='border-left: 0;' >
								<select name=selCurrencyGross id=selCurrencyGross >
								<?php 
								foreach($currencyArr as $idx=>$curr){
									print "<option value='".$curr."'";				              		
				              		print ">".$curr."</option>";
								}
								?>
								</select>
							</td>
						</tr>
						<tr>
							<td><input name='std_amount' id='std_amount' size='5' value='<?php echo $std_amount; ?>'  onchange="update_tax_svc('std_amount','std_tax','std_svc');" onkeyup='verify(this.id); ' />  </td>
							<td><input name='std_svc' id='std_svc' size='5' value='<?php echo $std_svc; ?>' onkeyup='verify(this.id); '/>  </td>
							<td><input name='std_tax' id='std_tax' size='5' value='<?php echo $std_tax; ?>' onkeyup='verify(this.id); '/>  
							<input type="hidden" name='transDetail' id='transDetail' size='5' value='<?php echo $transDetail; ?>' />
							</td>
						</tr>	
					</table>
					
				  </div>
				  <div>
				  <table align=right>
				  <tr>
						 	<td colspan=2>&nbsp;</td>
						  </tr>
				   <tr>
							<td colspan=9 align=right rowspan="<?php echo count($totalArr)+1;?>"><b><?php echo $_L['INV_total']; ?></b></td>
						  <?php if(count($totalArr) == 0) {
							echo" <td colspan=2><b>0</b></td>";
							$zerobalance = 1;
						  } else {
							echo "</tr>";
							foreach($totalArr as $idx => $val){?>
						  <tr>
						 	<td colspan=2><b id="itmstotal_<?php echo $idx;?>"><?php echo sprintf("%02.2f",$val)." ".$idx; ?></b></td>
						  </tr>
						  <?php }
						  }?>
				  </table>
				  </div>
				</div>
				
				<!-- TAB GUEST INFORMATION-->
				<div class="TabbedPanelsContent" style="align-content:center;">
				
				 <div class="scrolltabinv">
					<table  align=center border="1" cellpadding="1" width="100%" cellspacing="0">
						 <tr bgcolor="#3593de">
							<th rowspan=2> </th>
							<th rowspan=2><?php echo $_L['INV_date']; ?></th>
							<th><?php echo $_L['INV_fop']; ?></th>
							<th><?php echo $_L['INV_cctype']; ?></th>
							<th><?php echo $_L['INV_expiry']; ?></th>
							<th rowspan=2><?php echo $_L['INV_auth']; ?></th>
							<th colspan=3 rowspan=2><?php echo $_L['INV_name']; ?></th>
							<th rowspan=2 colspan=2><?php echo $_L['INV_amount']; ?></th>
						  </tr>
						  <tr bgcolor="#3593de">
							<th colspan=2><?php echo $_L['INV_ccnum']; ?></th>
							<th><?php echo $_L['INV_cvv']; ?></th>
						  </tr>
						  <?php 
						  $paytotal = 0;
						  if($bill['rcptcount'] > 0) {
							$i = 0;
							while ($i < $bill['rcptcount']) {
							  //alternate row colour
							  $j++;
							  if($j%2==1){
								echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
							  }else{
								echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
							  }
							  if($bill['rcpts'][$i]['status'] == STATUS_VOID) {
								echo "<td rowspan=2> </td>";
								echo "<td rowspan=2>";
								echo "	<del>".$bill['rcpts'][$i]['rcpt_date']."</del> ";
								echo "</td>";
								echo "<td> <del>";
								print get_foptext($bill['rcpts'][$i]['fop']);
								echo "</del></td>";
								echo "<td>";
								echo "<del>";
								$fop = "";
								if($bill['rcpts'][$i]['cctype'] == "CA") echo $_L['CC_CA'];
								if($bill['rcpts'][$i]['cctype'] == "DC") echo $_L['CC_DC'];
								if($bill['rcpts'][$i]['cctype'] == "AX") echo $_L['CC_AX'];
								if($bill['rcpts'][$i]['cctype'] == "VI") echo $_L['CC_VI'];
								if($bill['rcpts'][$i]['cctype'] == "JCB") echo $_L['CC_JCB'];
								if($bill['rcpts'][$i]['cctype'] == "EC") echo $_L['CC_EC'];
								echo "</del></td>";
								echo "<td> <del>".$bill['rcpts'][$i]['expiry']."</del> </td>";
								echo "<td rowspan=2> <del>".$bill['rcpts'][$i]['auth']."</del> </td>";
								echo "<td colspan=3 rowspan=2> <del>".$bill['rcpts'][$i]['name']."</del> </td>";
								echo "<td rowspan=2> <del>".$bill['rcpts'][$i]['amount']."</del> </td>";
								echo "</tr>";
								if($j%2==1){
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
								}else{
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
								}
								echo "<td colspan=2> <del>".$bill['rcpts'][$i]['CCnum']."</del> </td>";
								echo "<td> <del>".$bill['rcpts'][$i]['CVV']."</del> </td>";
							  } else {
								echo "<td rowspan=2>";
								echo "<a href=\"index.php?menu=invoice&id=".$bill['bill_id']."&action=void&pid=".$bill['rcpts'][$i]['receipt_id']."\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"void transaction\"/></a></td>";
								echo "</td>";
								echo "<td rowspan=2>";
								echo trim($bill['rcpts'][$i]['rcpt_date']);
								echo "</td>";
								echo "<td>";
								print get_foptext($bill['rcpts'][$i]['fop']);
								echo "</td>";
								echo "<td>";								
								print get_creditcardString($bill['rcpts'][$i]['cctype']);
								
								$paytotal += $bill['rcpts'][$i]['amount'];
								

								$displaytot = $bill['rcpts'][$i]['amount']*$bill['rcpts'][$i]['exrate'];
								$payedDisplayArr[$bill['rcpts'][$i]['tgtCurrency']]+=$displaytot;
						
								echo "</td>";
								echo "<td> ".$bill['rcpts'][$i]['expiry']."</td>";
								echo "<td rowspan=2>";
								// only first empty authorization number will be blank, as this will be for cc payments
								// auto submitted. If partial payments, they will need to process 1 at a time anyway.
								//$bill['rcpts'][$i]['auth'];
							  	if( $bill['rcpts'][$i]['auth'] != '' || $add_authid) {
									echo $bill['rcpts'][$i]['auth'];
								} else {
									$add_authid=1;
									echo "<input type='hidden' name='AUTH_RCPTID' id='AUTH_RCPTID' value='".$bill['rcpts'][$i]['receipt_id']."' />";
									echo "<input type='text' size='8' maxlength='200' name='AUTH_VALUE' id='AUTH_VALUE' />";
								}
								echo "</td>";
								echo "<td colspan=3 rowspan=2>".$bill['rcpts'][$i]['name']."</td>";
								echo "<td rowspan=2 colspan=2>".sprintf("%02.2f",$displaytot)." ".$bill['rcpts'][$i]['tgtCurrency']."</td>";
								echo "</tr>";
								if($j%2==1){
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
								}else{
								  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
								}
								echo "<td colspan=2>".$bill['rcpts'][$i]['CCnum']."</td>";
								echo "<td>".$bill['rcpts'][$i]['CVV']."</td>";
							  }
							  echo "</tr>";
							  $i++;
							}
						  }
						  ?>
						  <tr>
							<td rowspan=2 colspan=2>
								<img src="images/ew_calendar.gif" width="16" height="16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].rcpt_date,'dd/mm/yyyy',this, false, 1400)" />
								<input type="text" name="rcpt_date" id="rcpt_date"  size=10 maxlength=16 readonly value="<?php echo trim($rcpt_date);?>" /> 
							</td>
							<td>
							  <input type=hidden id='lastfop' value=''>
							  <select name=fop id=fop class=plainDropDown onchange='					 					  
						  	  var chkpayArr = document.getElementsByName("chkPay[]");
							  if(this.value==0){					  
							  	document.getElementById("cctype").disabled = true;
							  	document.getElementById("expiry").disabled = true;
							  	document.getElementById("cvv").disabled = true;
							  	document.getElementById("CCnum").disabled = true;
							  	document.getElementById("auth").disabled = true;
							  	document.getElementById("cardname").disabled = true;
								document.getElementById("rcpt_amt").disabled = true;
								document.getElementById("rcpt_amt").readOnly=true;
							  	document.getElementById("rcpt_amt").value = "";
							  	document.getElementById("cctype").value = "";
							  	document.getElementById("expiry").value = "";
							  	document.getElementById("cvv").value = "";
							  	document.getElementById("CCnum").value = "";
							  	document.getElementById("auth").value = "";
							  	document.getElementById("cardname").value = "";
							  	document.getElementById("cctype").options[0].selected = true; 	
							  }else if(this.value==<?php echo FOP_CC;?>){
							  	document.getElementById("rcpt_amt").disabled = false;
							  	document.getElementById("cctype").disabled = false;
							  	document.getElementById("expiry").disabled = false;
							  	document.getElementById("cvv").disabled = false;
							  	document.getElementById("CCnum").disabled = false;
							  	document.getElementById("auth").disabled = true;
							  	document.getElementById("cardname").disabled = false;	
							  	document.getElementById("auth").value = "";				  	
							  }else if(this.value==<?php echo FOP_CC_DEP;?>){
							  	document.getElementById("rcpt_amt").disabled = false;
							  	document.getElementById("cctype").disabled = false;
							  	document.getElementById("expiry").disabled = false;
							  	document.getElementById("cvv").disabled = false;
							  	document.getElementById("CCnum").disabled = false;
							  	document.getElementById("auth").disabled = true;
							  	document.getElementById("cardname").disabled = false;	
							  	document.getElementById("auth").value = "";				  	
								document.getElementById("tdrcptamt_curr").innerHTML ="<?php echo $dep_html;?>";
								document.getElementById("rcpt_amt").readOnly=false;
							  } else if(this.value==<?php echo FOP_CASH_DEP;?>){
							  	document.getElementById("rcpt_amt").disabled = false;
							  	document.getElementById("cctype").disabled = true;
							  	document.getElementById("expiry").disabled = true;
							  	document.getElementById("cvv").disabled = true;
							  	document.getElementById("CCnum").disabled = true;
							  	document.getElementById("auth").disabled = true;
							  	document.getElementById("cardname").disabled = false;
							  	document.getElementById("cctype").value = "";
							  	document.getElementById("expiry").value = "";
							  	document.getElementById("cvv").value = "";
							  	document.getElementById("CCnum").value = "";
								document.getElementById("rcpt_amt").readOnly=false;
							  	document.getElementById("cctype").options[0].selected = true;	
								document.getElementById("tdrcptamt_curr").innerHTML ="<?php echo $dep_html; ?>";
							  } else if(this.value==<?php echo FOP_CHEQUE;?> || this.value==<?php echo FOP_COUPON;?> 
								|| this.value==<?php echo FOP_VOUCHER;?> || this.value==<?php echo FOP_REDEMPTION;?>){
							  	document.getElementById("rcpt_amt").disabled = false;
							  	document.getElementById("cctype").disabled = true;
							  	document.getElementById("expiry").disabled = true;
							  	document.getElementById("cvv").disabled = true;
							  	document.getElementById("CCnum").disabled = false;
							  	document.getElementById("auth").disabled = false;
							  	document.getElementById("cardname").disabled = false;
							  	document.getElementById("cctype").value = "";
							  	document.getElementById("expiry").value = "";
							  	document.getElementById("cvv").value = "";
							  	document.getElementById("CCnum").value = "";
							  	document.getElementById("cctype").options[0].selected = true;					  						  
							  } else{
							  	document.getElementById("rcpt_amt").disabled = false;
							  	document.getElementById("cctype").disabled = true;
							  	document.getElementById("expiry").disabled = true;
							  	document.getElementById("cvv").disabled = true;
							  	document.getElementById("CCnum").disabled = true;
							  	document.getElementById("auth").disabled = true;
							  	document.getElementById("cardname").disabled = false;	
							  	document.getElementById("auth").value = "";	
							  };
							  if (this.value == <?php echo FOP_CASH_DEP;?> || this.value ==<?php echo FOP_CC_DEP;?>) {
									for(var i=0; i< chkpayArr.length;i++) {
										if(chkpayArr[i].checked) {
											chkpayArr[i].checked = false;
										}
									}
							  };
							  if((document.getElementById("lastfop").value == <?php echo FOP_CASH_DEP;?> ||
								 document.getElementById("lastfop").value == <?php echo FOP_CC_DEP;?>) && 
								 (this.value != <?php echo FOP_CASH_DEP;?> && this.value != <?php echo FOP_CC_DEP;?>)) {
									document.getElementById("tdrcptamt_curr").innerHTML = "";
							  };
							  document.getElementById("lastfop").value =this.value;

							  '>
								<option value='0' >Select FOP</option>
								<option value='<?php echo FOP_CASH; ?>' <?php if($fop == FOP_CASH) echo "selected"; ?> > <?php echo $_L['FOP_cash']; ?></option>
								<option value='<?php echo FOP_CC; ?>' <?php if($fop == FOP_CC) echo "selected"; ?> > <?php echo $_L['FOP_cc']; ?></option>
								<option value='<?php echo FOP_TT; ?>' <?php if($fop == FOP_TT) echo "selected"; ?> > <?php echo $_L['FOP_tt']; ?></option>
								<option value='<?php echo FOP_DB; ?>' <?php if($fop == FOP_DB) echo "selected"; ?> > <?php echo $_L['FOP_db']; ?></option>
								<option value='<?php echo FOP_CHEQUE; ?>' <?php if($fop == FOP_CHEQUE) echo "selected"; ?> > <?php echo $_L['FOP_chq']; ?></option>
								<option value='<?php echo FOP_COUPON; ?>' <?php if($fop == FOP_COUPON) echo "selected"; ?> > <?php echo $_L['FOP_coupon']; ?></option>
								<option value='<?php echo FOP_VOUCHER; ?>' <?php if($fop == FOP_VOUCHER) echo "selected"; ?> > <?php echo $_L['FOP_voucher']; ?></option>
								<option value='<?php echo FOP_REDEMPTION; ?>' <?php if($fop == FOP_REDEMPTION) echo "selected"; ?> > <?php echo $_L['FOP_redem']; ?></option>
								<option value='<?php echo FOP_CASH_DEP; ?>' <?php if($fop == FOP_CASH_DEP) echo "selected"; ?> > <?php echo $_L['FOP_cash_dep']; ?></option>
								<option value='<?php echo FOP_CC_DEP; ?>' <?php if($fop == FOP_CC_DEP) echo "selected"; ?> > <?php echo $_L['FOP_cc_dep']; ?></option>
							  </select>
							</td>
							<td>
							  <select name=cctype id=cctype class=plainDropDown2 <?php if ($fop!=FOP_CC) echo 'disabled="disabled"';?>>
								<option value='0' >Select Type</option>
								<option value="CA" <?php if($cctype == "CA") echo "selected"; ?>><?php echo $_L['CC_CA']; ?></option>
								<option value="DC" <?php if($cctype == "DC") echo "selected"; ?> ><?php echo $_L['CC_DC']; ?></option>
								<option value="AX" <?php if($cctype == "AX") echo "selected"; ?> ><?php echo $_L['CC_AX']; ?></option>
								<option value="VI" <?php if($cctype == "VI") echo "selected"; ?> ><?php echo $_L['CC_VI']; ?></option>
								<option value="JCB" <?php if($cctype == "JCB") echo "selected"; ?> ><?php echo $_L['CC_JCB']; ?></option>
								<option value="EC" <?php if($cctype == "EC") echo "selected"; ?> ><?php echo $_L['CC_EC']; ?></option>
							  </select>
							  
							</td>
							<td> <input name=expiry id=expiry size=5 maxlength=5 <?php if ($fop!=FOP_CC) echo 'disabled="disabled"';?> value='<?php echo $expiry; ?>' onkeyup='verify(this.id); '/></td>
							<td rowspan=2> <input name=auth id=auth size=5 maxlength=10 <?php if ($fop==FOP_CC || empty($fop) || !$fop) echo 'disabled="disabled"';?> value='<?php echo $auth; ?>' /></td>
							<td colspan=3 rowspan=2> <input name=cardname id=cardname size=30 maxlength=30 <?php if (empty($fop) || !$fop) echo 'disabled="disabled"';?> value='<?php echo $cardname; ?>'/></td>
							<td colspan=1 rowspan=2 style="border: 0;" width="5%" > 
							<input name=rcpt_amt id=rcpt_amt onkeyup="verify(this.id);" readonly="readonly" size=8 maxlength=10 value='' onchange="updatePaymentOnchange();"/>
							<input type="hidden" name=txtrcptamt_curr id=txtrcptamt_curr value=""/>
						  	</td>
							<td colspan=1 rowspan=2 id="tdrcptamt_curr" style="border: 0;" >
							</td>
				
						  </tr>
						  <tr>
							<td colspan=2> <input type="text" name="CCnum" id="CCnum" size=19 maxlength=19 <?php if ($fop!=FOP_CC) echo 'disabled="disabled"';?> onchange="CheckCardNumber('cctype','CCnum','expiry');" value='<?php echo $CCnum; ?>' /></td>
							<td> <input name=cvv id=cvv size=4 maxlength=6 <?php if ($fop!=FOP_CC) echo 'disabled="disabled"';?> value='<?php echo $cvv; ?>' onkeyup='verify(this.id); '/></td>
						  </tr>
						  
					</table>
					<table>
						<tr bgcolor="#CCCCCC">
						<td colspan=9 align=center><b><?php echo $_L['INV_due']; ?></b></td>
					</tr>
					<tr>
						<td colspan=2>	
							<?php 
							//printing balance due for each currency code
							if(count($dueArr)>0){
								$printBalanceTitle=1;
								$numDRs=0;	
							?>					
							<table border="0" width="100%">
							<?php 	
								//only print the header if at least one DR is there										
								foreach($dueArr as $idx => $balanceDue){
									if($balanceDue>0){
										$numDRs++;	
									}	
									if($printBalanceTitle && $balanceDue>0){
										$printBalanceTitle=0;						
									?>
									<tr>
									<td colspan="3">&nbsp;</td><td><?php print $_L['INV_src'];?></td><td><?php print $_L['INV_tgt'];?></td><td><?php print $_L['INV_exrate'];?></td>
									</tr>
									<?php 								
									}	
								}?>
								<?php 	
								$dueIdx=0;											
								foreach($dueArr as $idx => $balanceDue){															
								?>
								<tr>							
								<?php 
								if($balanceDue>0){
								?>
								<td>
								<input id="chkPay" name="chkPay[]" type="checkbox" value="<?php print $dueIdx;?>" onclick="updatePaymentAmount();" <?php if($numDRs==1) print "checked='checked'";?> />
								
								</td>
								<?php 								 
								}else{
									print "<td>&nbsp;</td>";
								}
								?>
								<td>
								<b id="balanceDue_<?php print $dueIdx;?>"><?php echo sprintf("%02.2f",$balanceDue); if($balanceDue<0) print " CR"; elseif($balanceDue>0) print " DR";?></b>
								</td>
								<td>&nbsp;</td>
								<td>
								<?php print $idx;?>
								</td>
								<td>
								<?php 
								if($balanceDue>0){
								?>
								<input type="hidden" name="txtbalanceDue_<?php print $dueIdx;?>" id="txtbalanceDue_<?php print $dueIdx;?>" value="<?php print sprintf("%02.2f",$balanceDue);?>"/>
								<input type="hidden" name="txtrcptpay_<?php print $dueIdx;?>" id="txtrcptpay_<?php print $dueIdx;?>" value=""/>
								<select name=selCurrTarget_<?php print $dueIdx;?> id=selCurrTarget_<?php print $dueIdx;?> onchange="updatePaymentAmount();">
								<?php 
								foreach($totalArr as $idx2 => $val){
									print "<option value='".$idx2."'";
									if($idx==$idx2){
										print " selected='selected'";
									}		              		
					             		print ">".$idx2."</option>";
								}
								?>
								</select>
								<?php }?>
								</td>
								<td> 
								<?php 
								if($balanceDue>0){
								?>
								<input type="text" name=txtExrate_<?php print $dueIdx;?> id=txtExrate_<?php print $dueIdx;?> onkeyup="verify(this.id);"  size=8 maxlength=10 onchange="updatePaymentAmount();" value='1'/><font color="#FF0000">*</font>
								<input type="hidden" name=txtSrcCurr_<?php print $dueIdx;?> id=txtSrcCurr_<?php print $dueIdx;?> value='<?php print $idx;?>'/>
								<?php }
								//if no CR or DR assign totaldue as 0(==0) else totladue as 1(<>0) (used to set only the status)
								if(count($dueArr)>0){
									print '<input type=hidden name="totaldue" id="totaldue" value="'.array_sum($dueArr).'" />';
								}
								?>
							  	<input type=hidden name="itemtotal" id="itemtotal" value="<?php echo sprintf("%02.2f",$itemstotal); ?>" />
							  	<input type=hidden name="rcpttotal" id="rcpttotal" value="<?php echo sprintf("%02.2f",$paytotal); ?>" />
								<?php if($numDRs==1){
									print "<script type='text/javascript'>";
									print '
									document.getElementById("rcpt_amt").readOnly=false;
									document.getElementById("tdrcptamt_curr").innerHTML="'.$idx.'";
				 					document.getElementById("txtrcptamt_curr").value = "'.$idx.'";
				 					document.getElementById("rcpt_amt").value = Number('.$balanceDue.').toFixed(2); 
				 					updatePaymentOnchange();
									';								
									print "</script>";
								}
								?>
								</td>
								</tr>
								<?php
									if($balanceDue>0){
										$dueIdx = $dueIdx+ 1;
									}								
								}
								?>
								</table>
							<?php 
							}else{
								// receipt and no charges
								if($bill['rcptcount'] > 0 && ($bill['transcount'] == 0 || $zerobalance) && $paytotal > 0) { 
									$br = "";
									foreach ($refunds as $cur => $refamount) {
										if($refamount == 0 )
											continue;
										if($refamount < 0) $refamount = 0 - $refamount;
										print $br. sprintf("%02.2f", $refamount). " ".$cur. " CR";
										$br = "<br/>";
									}
								} else {
									print "0";
								}
							}?>								
						</td>
					</tr>
					</table>
				 </div>
				 <div>
				<table align=right>
				  <tr>
						 	<td colspan=2>&nbsp;</td>
						  </tr>
				  <tr>
							<td colspan=9 align=right rowspan="<?php echo count($payedDisplayArr)+1;?>"><b><?php echo $_L['INV_total'] ?></b></td>
						  </tr>						  
						  <?php 
						  foreach($payedDisplayArr as $idx => $val){?>
						  <tr>
						 	<td colspan=2><b id="rcptTot_<?php print $idx;?>"><?php print sprintf("%02.2f",$val)." ".$idx; ?></b>
							</td>
						  </tr>				  
						  <?php 
						  	}
						  ?>	
				 </table>
				 </div>
				</div>
				<!-- TAB GUEST INFORMATION-->
				<div class="TabbedPanelsContent" style="align-content:center;">
				<table>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<?php 
					foreach ($refunds as $cur => $refamount) {
						if($refamount == 0 )
							continue;
						if(!$refline)
							$refline = "<tr bgcolor='#3593de'><th colspan=11>".$_L['INV_refund']."</th></tr>";
							if($refamount > 0) $refamount = 0 - $refamount;
							$refline .= "<tr>";
							$refline .= "<td > <input type='radio' id='refund' name='refund' value='". $i."' />";
							if($refline) {
								$refline .= "<input class='button' type=submit name='Submit' value='".$_L['INV_refund']."' />";
							}
							
							$refline .="</td>";
							$refline .= "<td >";
							$refline .= "<img src='images/ew_calendar.gif' width='16' height='16' border='0' onclick=\"setCalendarLanguage('". $lang."');displayCalendar(document.forms[0].ref_date". $i.",'dd/mm/yyyy',this, false, 1400)\" />";
							$refline .= "<input type='text' name='ref_date". $i."' id='ref_date". $i."'  size=16 maxlength=16 readonly value='". $today."' /> ";
							$refline .= "</td>";
							$refline .= "<td>";
							$refline .= "<select name='reffop".$i."' id='reffop' class=plainDropDown onchange='	";				 					  
							$refline .= "if(this.value==0){";
							$refline .= "document.getElementById(\"refcctype".$i."\").disabled = true;";
							$refline .= "document.getElementById(\"refcctype".$i."\").options[0].selected = true;	";			  	
							$refline .= "}else if(this.value==".FOP_CC."){";
							$refline .= "document.getElementById(\"refcctype".$i."\").disabled = false;";
							$refline .= "}else {";
							$refline .= "document.getElementById(\"refcctype".$i."\").disabled = true;";
							$refline .= "document.getElementById(\"refcctype".$i."\").options[0].selected = true;";
							$refline .= " };' >";
							$refline .= "<option value='0' >Select FOP</option>";
							$refline .= "<option value='".FOP_CASH."' ";
							$refline .= ($reffop == FOP_CASH)? "selected=selected":"";
							$refline .= " > ".$_L['FOP_cash']. "</option>";
							$refline .= "<option value='".FOP_CC."' ";
							$refline .= ($reffop == FOP_CC)? "selected=selected":"";
							$refline .= " > ". $_L['FOP_cc']."</option>";
							$refline .= "<option value='".FOP_TT."' ";
							$refline .= ($reffop == FOP_TT)?  "selected=selected":"";
							$refline .= " > ". $_L['FOP_tt']."</option>";
							$refline .= "<option value='".FOP_DB."' ";
							$refline .= ($reffop == FOP_DB)?  "selected=selected":"" ;
							$refline .= " > ". $_L['FOP_db']."</option>";
							$refline .= "<option value='".FOP_CHEQUE."' ";
							$refline .= ($reffop == FOP_CHEQUE)?  "selected=selected":"";
							$refline .= " > ". $_L['FOP_chq']."</option>";
							$refline .= "<option value='".FOP_COUPON."' ";
							$refline .= ($reffop == FOP_COUPON)?  "selected=selected":"";
							$refline .= " > ". $_L['FOP_coupon']."</option>";
							$refline .= "<option value='".FOP_VOUCHER."' ";
							$refline .= ($reffop == FOP_VOUCHER)?  "selected=selected":"";
							$refline .= " > ". $_L['FOP_voucher']."</option>";
							$refline .= "<option value='".FOP_REDEMPTION."' ";
							$refline .= ($reffop == FOP_REDEMPTION)?"selected=selected":"";
							$refline .= " > ". $_L['FOP_redem']."</option>";
							$refline .= "</select>";
							$refline .= "</td>";
							$refline .= "<td>";
							$refline .= " <select name=refcctype".$i." id=refcctype".$i." class=plainDropDown2 ";
							$refline .= ($reffop!=FOP_CC)? "disabled=\"disabled\"":"" ;
							$refline .= "<option value='0'>Select Type</option>";
							$refline .= "<option value='CA' ";
							$refline .= ($refcctype == "CA") ? "selected":"" .">".$_L['CC_CA'];
							$refline .= "</option>";
							$refline .= "<option value='DC' ";
							$refline .= ($refcctype == "DC") ? "selected":"" .">".$_L['CC_DC'];
							$refline .= "</option>";
							$refline .= "<option value='AX' ";
							$refline .= ($refcctype == "AX") ? "selected":"" .">".$_L['CC_AX'];
							$refline .= "</option>";
							$refline .= "<option value='VI' ";
							$refline .= ($refcctype == "VI") ? "selected":"" .">".$_L['CC_VI'];
							$refline .= "</option>";
							$refline .= "<option value='JCB' ";
							$refline .= ($refcctype == "JCB") ? "selected":"" .">".$_L['CC_JCB'];
							$refline .= "</option>";
							$refline .= "<option value='EC' ";
							$refline .= ($refcctype == "EC") ? "selected":"" .">".$_L['CC_EC'];
							$refline .= "</option>";
							$refline .= "</select>";
							$refline .= "<font color='#FF0000'>*</font>";
							$refline .= "</td>";
							$refline .= "<td> </td>";
							$refline .= "<td> </td>";
							$refline .= "<td colspan=3 ></td>";
							$refline .= "<td colspan=2>".sprintf("%02.2f",0 - $refamount). " ".$cur." ";
							$refline .= "<input type=hidden name='refcur".$i."' id='refcur".$i."' value='".$cur."' />";
							$refline .= "<input type=hidden name='refamount".$i."' id='refamount".$i."' value='".$refamount."' />";
							$refline .= "</td></tr>";
							$i++;
						}
						
						echo $refline; 
					?>
						
				</table>
				</div>
				<div class="TabbedPanelsContent" style="align-content:center;">
				<textarea name="notes" id="notes" style="width: 750px; height: 300px;"><?php echo $bill['notes']; ?></textarea>
				 <div class="scrolltabinv">
				 </div>
				</div>
			  </div>
			  </div>
			  
			  <div class="btngroup" align=right>
				 <?php 

					if( $bill['status'] != STATUS_CLOSED || (isset($_SESSION["admin"]) && $_SESSION["admin"])) {
				  ?>
				  <input type=submit name="Submit" value="<?php echo $_L['BTN_update']; ?>" class="button"/>
				  &nbsp;&nbsp;
				  <?php 
						$transcount = get_transactionsCount_byBillID($id);				 	
						if($transcount>0 && !empty($checkin) && !empty($checkout)){
				  ?>
				  <input type=submit name="Submit" value="<?php echo $_L['INV_addroomchg']; ?>" onclick="return confirmAddRoomCharges();" class="button"/>
				  <?php 
						}
						if($transcount<=0 && !empty($checkin) && !empty($checkout)){
				  ?>
				  	<input type=submit name="Submit" value="<?php echo $_L['INV_addroomchg']; ?>" class="button" />
				  	<?php 
						}
						
					}
				  ?>
			      &nbsp;&nbsp;
				  <input type=button value="<?php echo $_L['BTN_print']; ?>" onclick="window.open('invoice.php?inv=<?php echo $id; ?>','invoice','');" class="button"/>
				  &nbsp;&nbsp;
				  <input type=button value="<?php echo $_L['BTN_receipt']; ?>" onclick="window.open('receipt.php?inv=<?php echo $id; ?>','receipt','');" class="button"/>

			  
			  </div>
			 </td></tr>
			 
			 
			</table>
			
			
			
		  </td>
		</tr>
		
	  </table>
	</form>
<script type="text/javascript">
function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function calc_amnt(amt,tx,sf,gross,grosshtml,quanty){
	var amount=document.getElementById(amt).value;
	var tax=document.getElementById(tx).value;
	var svc=document.getElementById(sf).value;		
	var qty = document.getElementById(quanty).value;
	
	var totAmount=0;
	if(amount==""){
		amount=0;
	}
	if(tax==""){
		tax=0;
	}
	if(svc==""){
		svc=0;
	}
	if(qty==""){
		qty=0;
	}
	var inamt = amount * 1.0;	
	sum = inamt + tax*1.0+svc*1.0;		
	if(grosshtml!=""){
		document.getElementById(grosshtml).innerHTML = roundNumber(sum * qty,2);		
	}else{
		document.getElementById(gross).value = roundNumber(sum * qty,2);	
	}	
  }
function updatePaymentAmount(){
	//onclick
	var chkpayArr = document.getElementsByName('chkPay[]');
	var selFirstTarget;

	var tgtCur ="";
	var totAmnt =0;	
	var numOfChecks=0;
	var lastCheckedIdx=-1;
	var firstCheckedIdx=-1;

	for(var i=0 ;i< chkpayArr.length;i++) {
		if(chkpayArr[i].checked) {
			var exrate =  document.getElementById("txtExrate_"+i).value*1.0;
			var bdue = document.getElementById("txtbalanceDue_"+i).value*1.0;
			totAmnt = totAmnt + exrate*bdue;
			document.getElementById("txtrcptpay_"+i).value = bdue;
			if(numOfChecks==0){
				selFirstTarget = document.getElementById("selCurrTarget_"+i);
			}
			lastCheckedIdx=i;
			numOfChecks++;
		 }
		 if(numOfChecks>1 && firstCheckedIdx == -1){
			 firstCheckedIdx = i;
		 }
	 }	 

	if(lastCheckedIdx == -1){
		lastCheckedIdx=0;
	}
	if(firstCheckedIdx == -1){
		firstCheckedIdx=0;
	}

		
	 if(numOfChecks==0){
		 //more than 1 checked
		 
		 document.getElementById("tdrcptamt_curr").innerHTML="";
		 document.getElementById("rcpt_amt").readOnly=true;
		 document.getElementById("rcpt_amt").value="";
	 }else if(numOfChecks>1){
		//change all checked currencies to a single currency
		firstCurrency = selFirstTarget.options[selFirstTarget.selectedIndex].text;
		
		for(var i=1 ;i< chkpayArr.length;i++) {
			var selCurrTarget = document.getElementById("selCurrTarget_"+i);
			for(var j=0 ;j< selCurrTarget.length;j++) {
				if(chkpayArr[i].checked) {
					if(selCurrTarget.options[j].text == firstCurrency)
						selCurrTarget.selectedIndex = j;
				}
			}
		}

		//===
		 var selCurrTarget = document.getElementById("selCurrTarget_"+lastCheckedIdx);
		 tgtCur = selCurrTarget.options[selCurrTarget.selectedIndex].text;
			
		 document.getElementById("tdrcptamt_curr").innerHTML=tgtCur;
		 document.getElementById("rcpt_amt").readOnly=true;
		 document.getElementById("rcpt_amt").value = Number(totAmnt).toFixed(2);
				
	 }else{
		 //only one is checked
		 var selCurrTarget = document.getElementById("selCurrTarget_"+lastCheckedIdx);
		 tgtCur = selCurrTarget.options[selCurrTarget.selectedIndex].text;
			
		 document.getElementById("tdrcptamt_curr").innerHTML=tgtCur;
		 document.getElementById("rcpt_amt").readOnly=false;
		 document.getElementById("rcpt_amt").value = Number(totAmnt).toFixed(2);			
	 }
	 document.getElementById("txtrcptamt_curr").value = tgtCur;	 	 
}
function verify(frm)
{
    var element = document.getElementById(frm);
    if(isNaN(new Number(element.value)))
            element.value = element.value.substring(0, element.value.length - 1) 
}
function confirmAddRoomCharges(){
	var res=false;
	var msg="<?php echo $_L['RSV_cnfrmAddRoomCharge'];?>";		
	res=confirm(msg);
	return res;
}
</script>
  </body>
</html>
<?php
/**
 * @}
 * @}
 */
?>