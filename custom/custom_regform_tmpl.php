<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file regform.php
 * @brief Registration form to be fill up by customer
 * @note The printing of registration form is only available for 
 * 		OTA Hotel Management users registered with e-Bridge
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Guest setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */

error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/../functions.php");
include_once(dirname(__FILE__)."/../dailyfunc.php");
include_once(dirname(__FILE__)."/../lang/lang_en.php");
include_once(dirname(__FILE__)."/../OTA/advancedFeatures/adv_functions.php");



/**< language $lang */
$lang = get_language();
load_language($lang);
/**< logo file $logofile */
$logofile=Get_LogoFile();
/**< pixel $px */
$px = 14;
/**< line count $lc */
$lc = 43;


$hotel = "";
$altname = "";
$company = "";
$register = "";
$ebridgeid = "";
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
$lang = "";
$email = "";
$web = "";
$ota = "";
$chaincode = "";
$bid = 0;
$checkin = "";
$checkout= "";
$roomid = 0;
$roomtypeid = 0;
$ratesid = 0;
$guestid =0;
$resid =0;

Get_HotelSettings($hotel, $altname, $company, $register,
	$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
	$city, $citycode, $state, $postcode, $countrycode, $country,
	$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
	
	
	
	
	
if(isset($_GET['bid'])) $bid=$_GET['bid'];
if(isset($_GET['in'])) $checkin=urldecode($_GET['in']);
if(isset($_GET['out'])) $checkout=urldecode($_GET['out']);
if(isset($_GET['room'])) $roomid=$_GET['room'];
if(isset($_GET['roomtype'])) $roomtypeid=$_GET['roomtype'];
if(isset($_GET['rate'])) $ratesid=$_GET['rate'];
if(isset($_GET['guest'])) $guestid=$_GET['guest'];

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
		$numguest = $res['no_adults']+$res['no_child1_5']+$res['no_child6_12']+$res['no_babies'];
	}	
	
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


 if($guestid > 0) {
	$guest = array();
	findguestbyid($guestid, $guest);
	$guestname = $guest['guest'];
}

//get the last index of the address for the profile
get_addresses_by_profileID($guestid, $addr);

for($i=0;$i<count($addr);$i++){
$addStr = "";
$postal="";
	$comma="";
	//echo $addr[$i]['addressid'];
	
	if($addr[$i]['floor']){
		
		$addStr .=	$addr[$i]['floor']."Flr";
		$comma=", ";
	}
	if($addr[$i]['unit']){
		
		$addStr .=	"-".$addr[$i]['unit'];
		$comma=", ";
	}
	if($addr[$i]['building']){
		
		$addStr .=	$comma .$addr[$i]['building'];
		$comma=", ";
	}
	if($addr[$i]['blk']){
		
		$addStr .= $comma .$addr[$i]['blk'];
		$comma=", ";
	}
	if($addr[$i]['street']){
		
		$addStr .= $comma .$addr[$i]['street'];
		$comma=", ";
	}
	if($addr[$i]['city']){
		
		$addStr .= $comma .$addr[$i]['city'];
		$comma=", ";
	}
	if($addr[$i]['state']){
		
		$addStr .= $comma .$addr[$i]['state'];
		$comma=", ";
	}
	if($addr[$i]['countrycode']){
		
		$addStr .= $comma .$addr[$i]['countrycode'];
		$comma=", ";
	}
	$postal = $addr[$i]['postcode'];
}

//Get the phone details
get_phones_by_profileID($guestid, $phones);



for($i=0;$i<count($phones);$i++){
	$phonenum = "";
	$dash="";
	if($phones[$i]['countrycode']){
		$phonenum .= $dash .$phones[$i]['countrycode'];
		$dash="-";
	}
	if($phones[$i]['areacode']){
		$phonenum .= $dash .$phones[$i]['areacode'];
		$dash="-";
	}
	if($phones[$i]['phonenumber']){
		$phonenum .= $dash .$phones[$i]['phonenumber'];
		$dash="-";
	}
	if($phones[$i]['ext']){
		$phonenum .= "EXT - ".$phones[$i]['ext'];
		$dash="-";
	}
	
}

//Get the email address
get_emails_by_profileID($guestid, $emails);
for($i=0;$i<count($emails);$i++){
	$emailadd = "";
	if($emails[$i]['addr']){
		$emailadd .= $emails[$i]['addr'];
		
	}
}

//Get the document
get_advDocuments_by_profileID($guestid, $documents);
for($i=0;$i<count($documents);$i++){
	$doctype = "";
	$docid="";
	$nationality = "";
	if($documents[$i]['doctype']){
		if($documents[$i]['doctype']==DOC_PASSPORT)
    		$doctype = $_L['ADP_passport'];
    	elseif($documents[$i]['doctype']==DOC_DL)
    		$doctype = $_L['ADP_driverlicense'];
    	elseif($documents[$i]['doctype']==DOC_MILITARY)
    		$doctype = $_L['ADP_military'];
    	elseif($documents[$i]['doctype']==DOC_NID)
    		$doctype = $_L['ADP_nid'];
    	elseif($documents[$i]['doctype']==DOC_VISA)
    		$doctype = $_L['ADP_visa']; 
	}
	$docid = $documents[$i]['docnumber'];
	$nationality = $documents[$i]['nationality'];	
}

//Get the booking details
get_booking($bid, $res);

echo "<tr >";
echo "<td align=right>";
echo "<table width=100%><tr><td width='33%'></td><td width='33%' align=center><h1>".$hotel."</h1></td>";
echo "<td align=right rowspan=2><img src='".$logofile."' title='".$logofile."' width='70' height='60mm' border='0' /></td></tr>";
echo "</table>";
echo "</tr>";
	




?>


	<tr><td align=center>CUSTOM FORM<br/></td></tr>
	<tr><td align=center><h3><?php echo $_L['FRM_guestinfotitle'];?></h3></td></tr>
	<tr><td>
		<div>
	<?php echo $_L['FRM_guestname'];?> <span style="padding: 0 5px;"><input type="text" readonly=readonly class="box" style="width:220px" value="<?php echo $guestname;?>"></span><br/><br/>
	<?php echo $_L['FRM_address'];?> <span style="padding: 0 30px;"><textarea readonly=readonly rows="4" cols="50" class="box" style="overflow:auto;resize:none"><?php echo $addStr;?></textarea></span><span style="padding: 0 10px;"><?php echo $_L['FRM_postal'];?></span><span style="padding: 0 10px;"><input type="text"  readonly=readonly class="box" style="width:100px" value="<?php echo $postal;?>"></span><br/><br/>
	<?php echo $_L['FRM_phone'];?><span style="padding: 0 20px;"><input  readonly=readonly type="text" class="box" style="width:200px" value="<?php echo $phonenum;?>"></span><span style="padding: 0 5px;"><?php echo $_L['FRM_email'];?></span><span style="padding: 0 30px;"><input type="text"  readonly=readonly class="box" style="width:200px" value="<?php echo $emailadd;?>"></span>
	<br/><br/>
	<?php echo $_L['FRM_document'];?><span style="padding: 0 23px;"><input type="text"  readonly=readonly class="box" style="width:200px" value="<?php echo $doctype;?>"></span><?php echo $_L['FRM_documentno'];?><span style="padding: 0 10px;"><input readonly=readonly type="text" class="box" style="width:200px" value="<?php echo $docid;?>"></span><br/><br/>
	<?php echo $_L['FRM_natioality'];?><span style="padding: 0 23px;"><input type="text" readonly=readonly  class="box" style="width:200px"value="<?php echo $nationality;?>"></span>
	
	</div>
	</td></tr>
	<tr><td align=center></td></tr>
	<tr><td align=center><h3><?php echo $_L['FRM_reservetitle'];?></h3></td></tr>
	<tr><td>
	<div>
	<?php echo $_L['FRM_roomno'];?><span style="padding: 0 5px;"><input  readonly=readonly type="text" class="box" style="width:50px" value="<?php echo $res['roomno'];?>"></span>
	<span style="padding: 0 10px;"><?php echo $_L['FRM_price'];?></span><span style="padding: 0 10px;"><input  readonly=readonly type="text" class="box" style="width:100px" value="<?php echo $rate['currency']." ".$total;?>"></span><br/><br/>
	<?php echo $_L['FRM_start'];?><span style="padding: 0 40px;"><input  readonly=readonly type="text" class="box" style="width:200px" value="<?php echo $checkin;?>"></span>
	<span style="padding: 0 5px;"><?php echo $_L['FRM_end'];?></span><span style="padding: 0 20px;"><input  readonly=readonly type="text" class="box" style="width:200px" value="<?php echo $checkout;?>"></span><br/><br/>
	<?php echo $_L['FRM_numday'];?><span style="padding: 0 5px;"><input  readonly=readonly type="text" class="box" style="width:50px" value="<?php echo $res['no_nights'];?>"></span>
	<span style="padding: 0 30px;"><?php echo $_L['FRM_numguest'];?></span><span style="padding: 0 10px;"><input  readonly=readonly type="text" class="box" style="width:50px" value="<?php echo $numguest;?>"></span><br/><br/>
	
	
	</div>
	</td></tr>
	
	
	<?php 
	echo "<tr><td><br/><br/><br/>";
	echo "Signature:_______________________________      Date: <u>".date('Y/m/d h:i')."</u>";
	echo "</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "<td style=\"height:27mm;\">\n";
	echo "<table width=100%><tr><td></td><td align=center>".$hotel."</td><td></td></tr>\n";
	echo "<tr><td></td><td align=center>".$street." ".$city." ".$country." ".$postcode."</td><td></td></tr>\n";
	echo "<tr><td></td><td align=center>T: ".$phone." F: ".$fax." E: ".$email."</td><td align=right>".$pg."</td></tr></table>\n";
	echo "</td>\n";
	echo "</tr>\n";
	
	
	
	?>
	<tr  class="tdbgcl2"><td colspan=3 align=right>
		<!-- <input type="reset" value="Reset" name="reset" class="button" />&nbsp; -->
		<div class="noprint"><button onclick="window.print();">Print this page</button></div>
		</td>
	</tr>
	
	
<!-- End Email -->
