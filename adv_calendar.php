<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */
error_reporting(E_ALL | E_STRICT);
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/PHPExcel/Classes/PHPExcel.php");
include_once(dirname(__FILE__)."/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");
include_once(dirname(__FILE__)."/OTA/reports/reportfunction.php");

$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
date_default_timezone_set(TIMEZONE);

// Get the current settings
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
		



	 
	 
	 
$date  = mktime(0, 0, 0, date('m') + 1, 1,date('Y'));
$calmonth = date('m');
$calyear  = date('Y');
$monthstr =  date('F');

if(isset($_POST['next']) && $_POST['next']=="shownext"){
	$sdate  = mktime(0, 0, 0, $_POST['hiddenmonth'] + 1, 1,$_POST['hiddenyear']);
	$calmonth = date('m', $sdate);
	$calyear  = date('Y', $sdate);
	$monthstr = date('F', $sdate);
}elseif(isset($_POST['prev']) && $_POST['prev']=="showprev"){
	$edate  = mktime(0, 0, 0, $_POST['hiddenmonth'] - 1, 1,$_POST['hiddenyear']);
	$calmonth = date('m', $edate);
	$calyear  = date('Y', $edate);
	$monthstr = date('F', $edate);
}

$fromdate = $calyear."/".$calmonth."/01";
$endDate =  date('Y/m/t',strtotime('01-'.$calmonth.'-'.$calyear));

	 


$result = array();
$showreport = 1;
$download = 0;



$ssl="";
if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }

$path = "http".$ssl."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

// get all of the active bookings
$blist = array();
$book = get_all_bookings($blist, BOOK_CHECKEDIN, "",0);
// Get all of the reservations for the period
$rlist = array();
list($rstop, $rest) = preg_split('/ /', $endDate);
list($year, $month, $day) = preg_split('/\//', $rstop);
$rstop = $day."/".$month."/".$year;
list($rstart, $rest) = preg_split('/ /', $fromdate);
list($day, $month, $year) = preg_split('/\//', $rstart);
$rstart = $year."/".$month."/".$day;

$res = get_all_reservations("", "", "", $rlist, RES_ACTIVE);
$errstr = "";
$perr = "";
// Just load the rooms list
$roomlist = array();
$rms = get_roomslist($roomlist, 0,0,0);
// Initialise the result table
foreach ($roomlist as $rmid => $val) {
	$result[$rmid]['roomid']= $rmid;
	$result[$rmid]['roomno']= $roomlist[$rmid]['roomno'];
	$result[$rmid]['roomtype']= $roomlist[$rmid]['roomtype'];
	$result[$rmid]['roomtypeid']= $roomlist[$rmid]['roomtypeid'];
	if($roomlist[$rmid]['status'] == LOCKED) {
		$rstatus = $roomlist[$rmid]['status'];
	} else {
		$rstatus = VACANT;
	}
	// Initialise the array of days then set the status
	for ($i = strtotime($fromdate); $i <= strtotime($endDate); $i += 86400) { 
		$dt = date('Y/m/d', $i);
//		echo $rmid." ".$result[$rmid]['roomno']." Init ".$dt."<br/>";
		$result[$rmid]['days'][$dt]['status'] = $rstatus;
		$result[$rmid]['days'][$dt]['bookid'] = 0;
		$result[$rmid]['days'][$dt]['resid'] = 0;
		$result[$rmid]['days'][$dt]['guestname'] = '';
		$result[$rmid]['days'][$dt]['voucher'] = '';
		$result[$rmid]['days'][$dt]['reservation_by'] = '';
	}
}
// Assign the checked in rooms first
//	$blist[$bid]['book_id'] 
//	$blist[$bid]]['bill_id'] 
//	$blist[$bid]['guestid'] 
//	$blist[$bid]['reservation_id'] 
//	$blist[$bid]['no_adults'] 
//	$blist[$bid]['no_child'] 
//	$blist[$bid]['roomid'] 
//	$blist[$bid]['roomtypeid'] 
//	$blist[$bid]['ratesid'] 
//	$blist[$bid]['voucher_no'] 
//	$blist[$bid]['guestname'] 
//	$blist[$bid]['no_nights'] 
//	$blist[$bid]['checkindate'] 
//	$blist[$bid]['checkoutdate'] 
//	$blist[$bid]['book_status'] 
//	$blist[$bid]['roomno'] 
//print_r($blist);
foreach ($blist as $bid => $val) {
//	echo "Book id = ".$bid."<br/>";
	$bookid =  $blist[$bid]['book_id'];
	$bstart = $blist[$bid]['checkindate'];
	$bstop = $blist[$bid]['checkoutdate'];
	$guestname = $blist[$bid]['guestname'];
	$rmid = $blist[$bid]['roomid'];
	list($bstop, $rest) = preg_split('/ /', $bstop);
	list($day, $month, $year) = preg_split('/\//', $bstop);
	$bstop = $year."/".$month."/".$day;
	list($bstart, $rest) = preg_split('/ /', $bstart);
	list($day, $month, $year) = preg_split('/\//', $bstart);
	$bstart = $year."/".$month."/".$day;
	for ($i = strtotime($bstart); $i <= strtotime($bstop); $i += 86400) { 
		$dt = date('Y/m/d', $i);
		if(isset($result[$rmid]['days'][$dt])) {
			$result[$rmid]['days'][$dt]['status'] = BOOKED;
			$result[$rmid]['days'][$dt]['bookid'] = $bookid;
			$result[$rmid]['days'][$dt]['guestname'] = $guestname;
		}
	}
}
// Pass 1, check the reservations with room ids
//	$rlist[$idx]['guestname']
//	$rlist[$idx]['checkindate']
//	$rlist[$idx]['checkoutdate']
//	$rlist[$idx]['no_pax']
//	$rlist[$idx]['voucher_no']
//	$rlist[$idx]['no_nights']
//	$rlist[$idx]['status']
//	$rlist[$idx]['reservation_id']
//	$rlist[$idx]['reservation_by']
//	$rlist[$idx]['booked_by_ebridgeid']
//	$rlist[$idx]['cancelled_by_ebridgeid']
//	$rlist[$idx]['cancelled_date']
foreach($rlist as $idx => $val) {
	$rstart = $rlist[$idx]['checkindate'];
	$rstop = $rlist[$idx]['checkoutdate'];
	$resid = $rlist[$idx]['reservation_id'];
	$vch = $rlist[$idx]['voucher_no'];
	$guestname = $rlist[$idx]['guestname'];
	$reservation_by = $rlist[$idx]['reservation_by'];
	if(!$reservation_by) $reservation_by = $guestname;
	list($rstart, $rest) = preg_split('/ /', $rstart);
	list($day, $month, $year) = preg_split('/\//', $rstart);
	$rstart = $year."/".$month."/".$day;
	list($rstop, $rest) = preg_split('/ /', $rstop);
	list($day, $month, $year) = preg_split('/\//', $rstop);
	$rstop = $year."/".$month."/".$day;
	$details = array();
	// If the reservation has a room allocated
	if(reservation_details_byResID($resid, $details)) {
		// check for each day of the period if the room is allocated
		for ($i = strtotime($rstart); $i <= strtotime($rstop); $i += 86400) {
		$dt = date('Y/m/d', $i);
			// Check each element in the reservation_details
			foreach($details as $didx => $val) {
				if($details[$didx]['roomid'] > 0) {
					$rmid = $details[$didx]['roomid'];
					// Make sure the date is relevent to the report
					if(isset($result[$rmid]['days'][$dt])) {
						if($result[$rmid]['days'][$dt]['status'] == VACANT) {
							$result[$rmid]['days'][$dt]['status'] = RESERVED;
						}
						$result[$rmid]['days'][$dt]['voucher'] = $vch;
						$result[$rmid]['days'][$dt]['resid'] = $resid;
						$result[$rmid]['days'][$dt]['guestname'] = $guestname;
						$result[$rmid]['days'][$dt]['reservation_by'] = $reservation_by;
					}
				}
			}
		}
	} else {
		$errstr .="<b>Reservation for <a href='reservations.php?resid=".$resid."'>".$guestname."</a> has no assigned room</b><br/>";
		$perr .= "Reservation for ".$guestname." Voucher ".$vch." has no assigned room\r\n";
	}
}
// Now do a check for the reservations made by room type
// This is a 2 pass process. Look for the first room that will fit the reservation without overlap.
foreach($rlist as $idx => $val) {
	$rstart = $rlist[$idx]['checkindate'];
	$rstop = $rlist[$idx]['checkoutdate'];
	$resid = $rlist[$idx]['reservation_id'];
	$guestname = $rlist[$idx]['guestname'];
	$vch = $rlist[$idx]['voucher_no'];
	$reservation_by = $rlist[$idx]['reservation_by'];
	if(!$reservation_by) $reservation_by = $guestname;
	list($rstart, $rest) = preg_split('/ /', $rstart);
	list($day, $month, $year) = preg_split('/\//', $rstart);
	$rstart = $year."/".$month."/".$day;
	list($rstop, $rest) = preg_split('/ /', $rstop);
	list($day, $month, $year) = preg_split('/\//', $rstop);
	$rstop = $year."/".$month."/".$day;
	$details = array();
	// If the reservation has a room allocated
	if(reservation_details_byResID($resid, $details)) {
		// Check each element in the reservation_details
		foreach($details as $didx => $val) {
			if($details[$didx]['roomid'] == 0 && $details[$didx]['roomtypeid'] > 0) {
				$rtyp = $details[$didx]['roomtypeid'];
				$found = 0;
				foreach ($roomlist as $rmid => $val) {
					if($found) {
						break;
					}
					// skip rooms not of the type we need or type we want.
					if($roomlist[$rmid]['roomtypeid'] != $rtyp) {
						continue;
					}
					if($roomlist[$rmid]['status'] == LOCKED) {
						continue;
					}
					// room type matches
					// check for each day of the period if the room is allocated
					$available = 1;
					for ($i = strtotime($rstart); $i <= strtotime($rstop); $i += 86400) {
						$dt = date('Y/m/d', $i);
						if(!$available) {
							break;
						}
						// if it is not available reset
						if(isset($result[$rmid]['days'][$dt])) {
							if($result[$rmid]['days'][$dt]['status'] != VACANT) {
								$available = 0;
							}
						}
					}
					if($available) {
						$found=1;
						for ($i = strtotime($rstart); $i <= strtotime($rstop); $i += 86400) {
							$dt = date('Y/m/d', $i);
							if(!$available) {
								break;
							}
							// if it is not available reset
							if(isset($result[$rmid]['days'][$dt])) {
								$result[$rmid]['days'][$dt]['status'] = 'T';
								$result[$rmid]['days'][$dt]['resid'] = $resid;
								$result[$rmid]['days'][$dt]['guestname'] = $guestname;
								$result[$rmid]['days'][$dt]['voucher'] = $vch;
								$result[$rmid]['days'][$dt]['reservation_by'] = $reservation_by;
							}
						}
					}
				}
				if(!$found) {
					$errstr .=  "<b>Room type Reservation for <a href='reservations.php?resid=".$resid."'>".$guestname."</a> cannot auto allocate room</b><br/>";
					$perr .= "Room type Reservation for ".$guestname." Voucher ".$vch." cannot auto allocate room\r\n";
					//print_r($details[$didx]);
				}
			}
		}
	}
}

/**
 * Set the cell colour
 * @param objPHPExcel [in/out] The PHP Excel object
 * @param cells [in] The cell name
 * @param color [in] The RGB hex colour eg FF0000 red
 */
function cellColor(&$objPHPExcel, $cells,$color){
		ini_set('memory_limit', '256M');
        global $objPHPExcel;
        $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)
        ));
}


?>
<html>
<script type="text/javascript">
	function runDownLoad() {
		var url="OTA/reports/hotel_roomstatus_rpt.php?downLoadReport=1&startDate="+document.getElementById('startDate').value+"&endDate="+document.getElementById('endDate').value;
		window.open(url, "_blank");
		}
</script>
<link href='js/dhtmlgoodies_calendar.css' rel='stylesheet' />
<link href='css/styles2.css' rel='stylesheet' />
<SCRIPT type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
<body>
<table align="left" class='tdbgcl' width="100%" height="">
<tr>
<td>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="reportol" id="reportol"  method="post" enctype="multipart/form-data">


	<div>
	<table align="left">
	<tr class='tdbgcl' valign="top">
	
			 
			
			
			
		
	
	
	<td>
		<input type="hidden" id="hiddenmonth" name="hiddenmonth" value="<?php echo $calmonth; ?>">
		<input type="hidden" id="hiddenyear" name="hiddenyear" value="<?php echo $calyear; ?>">
		 <input type="hidden" readonly="readonly" name="startDate" id="startDate" value="<?php if (isset($fromdate)) echo $fromdate;?>" size="14" style="color:#999999;" />
		 <input type="hidden" readonly="readonly" name="endDate" id="endDate" value="<?php if (isset($endDate)) echo $endDate; ?>" size="14" style="color:#999999;" />	
		<table>
		<tr>
		<td><input name="prev" id="prev" type="Submit" value="showprev"></td>
		<td valign="center" align=center><h2><?php echo $monthstr;?></h2></td>
		<td align=right><input name="next" id="next" type="Submit" value="shownext"></td>
		</tr>
		<tr><td colspan=3>
		<?php 
			if($showreport) {
		?>
		
		<div class="scrollableContainer">
		<div class="scrollingArea" >
		
		<table class="cruises scrollable"  border="1" cellspacing="1" cellpadding="3" >
			<thead>
			<tr bgcolor="#3593DE">
			<td><div class="roomno"><?php echo $_L['RM_roomno'];?></div></td>
			<?php
				for ($i = strtotime($fromdate); $i <= strtotime($endDate); $i += 86400) {
					print "<td><div class='date'>".date('Y/m/d', $i)."</div></td>\n";
				}
			?>
			</tr></thead>
			<tbody>
			<?php 
				foreach($result as $rmid=>$val){
					echo "<tr>";
					
					echo "<td ><div class='roomno'>".$result[$rmid]['roomno']."</div></td>";
					
					for ($i = strtotime($fromdate); $i <= strtotime($endDate); $i += 86400) {
						$dt = date('Y/m/d', $i);
						if($result[$rmid]['days'][$dt]['status'] == LOCKED) {
							$bgcolor = "lightgrey";
							$res = $_L['RM_locked'];
							if($result[$rmid]['days'][$dt]['bookid']) {
								$res .= "<br/><a href='".$path."/index.php?menu=booking&id=".$result[$rmid]['days'][$dt]['bookid']."'>".$_L['INV_booking']."</a>";
							}
							if($result[$rmid]['days'][$dt]['resid']) {
								$res .= "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid]['days'][$dt]['resid']."'>".$_L['ADM_reservation']." (".$result[$rmid]['days'][$dt]['voucher'] .")</a>";
							}
						}
						if($result[$rmid]['days'][$dt]['status'] == VACANT) {
							$bgcolor = "white";
							$res = "";
						}
						if($result[$rmid]['days'][$dt]['status'] == RESERVED) {
							$bgcolor = "lightblue";
							$res = "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid]['days'][$dt]['resid']."'>".$_L['ADM_reservation'].": ".$result[$rmid]['days'][$dt]['guestname']." [".$result[$rmid]['days'][$dt]['reservation_by']."] (".$result[$rmid]['days'][$dt]['voucher'] .")</a>";
						}
						if($result[$rmid]['days'][$dt]['status'] == BOOKED) {
							$bgcolor = "lightgreen";
							$res = "<br/><a href='".$path."/index.php?menu=booking&id=".$result[$rmid]['days'][$dt]['bookid']."'>".$_L['REG_checkedin'].": ".$result[$rmid]['days'][$dt]['guestname']."</a>";

						}
						if($result[$rmid]['days'][$dt]['status'] == 'T') {
							$bgcolor = "yellow";
							$res = "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid]['days'][$dt]['resid']."'>".$_L['RM_type']." ".$_L['ADM_reservation'].": ".$result[$rmid]['days'][$dt]['guestname']." [".$result[$rmid]['days'][$dt]['reservation_by']."] (".$result[$rmid]['days'][$dt]['voucher'] .")</a>";
						}
						if($result[$rmid]['days'][$dt]['bookid'] && $result[$rmid]['days'][$dt]['resid']) {
							$bgcolor = "red";
							$res = "Conflict";
							$res .= "<br/><a href='".$path."/index.php?menu=booking&id=".$result[$rmid]['days'][$dt]['bookid']."'>".$_L['INV_booking']."</a>";
							$res .= "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid]['days'][$dt]['resid']."'>".$_L['ADM_reservation']." (".$result[$rmid]['days'][$dt]['voucher'] .")</a>";
						}
						print "<td bgcolor='".$bgcolor."' >";
						print $res;
						print "</td>";
					}
					echo "</tr>";	
					
				}
			?>
			</tbody>
			</table> 
		</div>
		</div>
			<?php
			}
			?>	</td>
			</tr>
			</table>
	</td>
	
	</tr>
	</table>
	</div>

	

</form>
</td>
</tr>

</table>
</body>
</html>