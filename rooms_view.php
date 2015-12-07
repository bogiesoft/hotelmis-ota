<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file rooms_view.php
 * @brief rooms list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup ROOM_MANAGEMENT
 * @{
 */
//error_reporting(E_ALL & ~E_NOTICE);

$result = array();
$rlist = array();
$blist = array();

include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/login_check.inc.php");

global $_L;
$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
date_default_timezone_set(TIMEZONE);
$fromdate = date('Y/m/d');

// Get the current settings
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);

if(isset($_GET['date'])) {
	$fromdate = $_GET['date'];
} 

if(isset($_POST['startDate']) ) {
	$fromdate = $_POST['startDate'];
}
if(isset($_GET['unlock'])) {
	$roomid = $_GET['unlock'];

	update_roomstatus($roomid, VACANT);
}

// Just load the rooms list
$roomlist = array();
$rms = get_roomslist($roomlist, 0,0,0);

if(isset($_SERVER['HTTPS'])) { $ssl = "s"; }
else { $ssl = ""; }
$path = "http".$ssl."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

// get all of the active bookings

$book = get_all_bookings($blist, BOOK_CHECKEDIN, "",0);

// Get all of the reservations for the period
$res = get_all_reservations("", "", "", $rlist, RES_ACTIVE);

$roomtype = array();
$allocations = array();
if(is_ebridgeCustomer()){
	include_once(dirname(__FILE__)."/OTA/advancedFeatures/ota_funcs.php");
	$i = strtotime($fromdate);
	$allocdate = date('Y-m-d H:i:s', $i);
	$allocations = OTA_AllocGet(0, $allocdate,$allocdate);
}
// Initialise the result table
foreach ($roomlist as $rmid => $val) {
	$result[$rmid]['roomid']= $rmid;
	$result[$rmid]['roomno']= $roomlist[$rmid]['roomno'];
	$result[$rmid]['roomname'] = $roomlist[$rmid]['roomname'];
	$result[$rmid]['roomtype']= $roomlist[$rmid]['roomtype'];
	$result[$rmid]['roomtypeid']= $roomlist[$rmid]['roomtypeid'];
	if($roomlist[$rmid]['status'] == LOCKED) {
		$rstatus = $roomlist[$rmid]['status'];
	} else {
		$rstatus = VACANT;
	}
	$result[$rmid][$fromdate]['status'] = $rstatus;
	$result[$rmid][$fromdate]['bookid'] = 0;
	$result[$rmid][$fromdate]['resid'] = 0;
	$result[$rmid][$fromdate]['bill_id'] = 0;
	$result[$rmid][$fromdate]['guestname'] = '';
	$result[$rmid][$fromdate]['voucher'] = '';
	$result[$rmid][$fromdate]['reservation_by'] = '';
	$result[$rmid][$fromdate]['ratesid'] = 0;
	$result[$rmid][$fromdate]['agentid'] = 0;
	$result[$rmid][$fromdate]['agentname'] = '';	
	$roomtype[$roomlist[$rmid]['roomtypeid']][$rmid] = 1;
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
	$bookid =  $blist[$bid]['book_id'];
	$bstart = $blist[$bid]['checkindate'];
	$bstop = $blist[$bid]['checkoutdate'];
	$guestname = $blist[$bid]['guestname'];
	$bill_id = $blist[$bid]['bill_id'];
	$rmid = $blist[$bid]['roomid'];
	$ratesid = $blist[$bid]['ratesid'];
	list($bstop, $rest) = preg_split('/ /', $bstop);
	list($day, $month, $year) = preg_split('/\//', $bstop);
	$bstop = $year."/".$month."/".$day;
	list($bstart, $rest) = preg_split('/ /', $bstart);
	list($day, $month, $year) = preg_split('/\//', $bstart);
	$bstart = $year."/".$month."/".$day;
	for ($i = strtotime($bstart); $i <= strtotime($bstop); $i += 86400) { 
		$dt = date('Y/m/d', $i);
		if(isset($result[$rmid][$dt])) {
			$result[$rmid][$dt]['status'] = BOOKED;
			$result[$rmid][$dt]['bookid'] = $bookid;
			$result[$rmid][$dt]['guestname'] = $guestname;
			$result[$rmid][$dt]['bill_id'] = $bill_id;
			$result[$rmid][$dt]['ratesid'] = $ratesid;
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
$errstr = "";
foreach($rlist as $idx => $val) {
	$rstart = $rlist[$idx]['checkindate'];
	$rstop = $rlist[$idx]['checkoutdate'];
	$resid = $rlist[$idx]['reservation_id'];
	$vch = $rlist[$idx]['voucher_no'];
	$guestname = $rlist[$idx]['guestname'];
	$reservation_by = $rlist[$idx]['reservation_by'];
	$ratesid = $rlist[$idx]['ratesid'];
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
				if($details[$didx]['status'] == RES_CHECKIN) {
					continue;
				}
				if($details[$didx]['roomid'] > 0) {
					$rmid = $details[$didx]['roomid'];
					// Make sure the date is relevent to the report
					if(isset($result[$rmid][$dt])) {
						if($result[$rmid][$dt]['status'] == VACANT) {
							$result[$rmid][$dt]['status'] = RESERVED;
						}
						$result[$rmid][$dt]['voucher'] = $vch;
						$result[$rmid][$dt]['resid'] = $resid;
						$result[$rmid][$dt]['guestname'] = $guestname;
						$result[$rmid][$dt]['reservation_by'] = $reservation_by;
						$result[$rmid][$dt]['ratesid'] = $ratesid;
					}
				}
			}
		}
	} else {
		$errstr .="Reservation for <a href='index.php?menu=reservation&resid=".$resid."'>".$guestname."</a> has no assigned room<br/>";
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
	$ratesid = $rlist[$idx]['ratesid'];
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
			// skip it if already checked in.
			if($details[$didx]['status'] == RES_CHECKIN) {
				continue;
			}
			if($details[$didx]['roomid'] == 0 && $details[$didx]['roomtypeid'] > 0) {
				$rtyp = $details[$didx]['roomtypeid'];
				$found = 0;
				foreach ($roomlist as $rmid => $val) {
					if($found) {
						break;
					}
					// skip rooms not of the type we need or type we want.
//					echo "Room ".$rmid." " .$roomlist[$rmid]['status']." ".$roomlist[$rmid]['roomtypeid']." ".$rtyp."<br/>";
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
						if(isset($result[$rmid][$dt])) {
							if($result[$rmid][$dt]['status'] != VACANT) {
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
							if(isset($result[$rmid][$dt])) {
								$result[$rmid][$dt]['status'] = 'T';
								$result[$rmid][$dt]['resid'] = $resid;
								$result[$rmid][$dt]['guestname'] = $guestname;
								$result[$rmid][$dt]['voucher'] = $vch;
								$result[$rmid][$dt]['reservation_by'] = $reservation_by;
								$result[$rmid][$dt]['ratesid'] = $ratesid;
							}
						}
					}
				}
				if(!$found) {
					$errstr .=  get_roomtype($rtyp)." Room Reservation for <a href='index.php?menu=reservation&resid=".$resid."'>".$guestname."</a> cannot auto allocate room<br/>";
					//print_r($details[$didx]);
				}
			}
		}
	}
}

// Parse the allocations
foreach($allocations as $alx => $val) {
	$ratesid = $allocations[$alx]['ratesid'];
	$agentid = 0;
	get_roomratetypes($ratesid, AGENTRATE, $agentid);
	$agentname = get_agentname_byid($agentid);
	$alcrmid = $allocations[$alx]['roomid'];
	if( $alcrmid > 0) {
//		echo "Found room id ".$alcrmid."<br/>";
		// Specifically allocation room for rate is locked
		if($result[$alcrmid][$fromdate]['status'] == LOCKED) {
			$errstr .=  get_roomtype($rtyp)."<a href='index.php?menu=rateSetup&id=".$ratesid."'>Room Rate Allocation for ".$agentname."</a> cannot auto allocate room ".$result[$alcrmid]['roomno'] ."<br/>";
			continue;
		}
		if($result[$alcrmid][$fromdate]['status'] != VACANT && $result[$alcrmid][$fromdate]['ratesid'] != $ratesid) {
			$errstr .=  get_roomtype($rtyp)." Room Rate Allocation for <a href='index.php?menu=rateSetup&id=".$ratesid."'>".$agentname."</a> cannot auto allocate room ".$result[$alcrmid]['roomno'] ."<br/>";
			continue;
		}
		if($result[$alcrmid][$fromdate]['status'] == VACANT) {
			$result[$alcrmid][$fromdate]['ratesid'] = $ratesid;
			$result[$alcrmid][$fromdate]['agentname'] = $agentname;
			$result[$alcrmid][$fromdate]['status'] = 'X';
			continue;
		}
		if($result[$alcrmid][$fromdate]['ratesid'] == $ratesid) {
			$result[$alcrmid][$fromdate]['agentname'] = $agentname;		
		}
	} else {
		$alcnum = $allocations[$alx]['roomcount'];
		$alcrmtyp = $allocations[$alx]['roomtypeid'];
		// Must do as 2 passes
		// Pass 1, check for bookings or reservations under the ID
		foreach ($result as $trmid => $val) {
			// finish when all allocated
			if($alcnum == 0) 
				break;
			// skip locked rooms
			if($result[$trmid][$fromdate]['status'] == LOCKED) 
				continue;
			// Skip rooms that are not of the allocated type
			if( $result[$trmid]['roomtypeid'] != $alcrmtyp)
				continue;
			// If not vacant and using this rate, then decriment the allocation
			if($result[$trmid][$fromdate]['status'] != VACANT && $result[$trmid][$fromdate]['ratesid'] == $ratesid) {
				$result[$trmid][$fromdate]['agentname'] = $agentname;
				$alcnum--;
				continue;
			}
		}
		// Pass 2, check for unused allocations requiring assignment
		if($alcnum > 0) {
			foreach ($result as $trmid => $val) {
				// finish when all allocated
				if($alcnum == 0) 
					break;
				if($result[$trmid][$fromdate]['status'] == LOCKED) 
					continue;
				// Skip rooms that are not of the allocated type
				if( $result[$trmid]['roomtypeid'] != $alcrmtyp)
					continue;
				if($result[$trmid][$fromdate]['status'] == VACANT) {
					$result[$trmid][$fromdate]['ratesid'] = $ratesid;
					$result[$trmid][$fromdate]['agentname'] = $agentname;
					$result[$trmid][$fromdate]['status'] = 'X';
					$alcnum--;
					continue;
				}
			}
		}
		if($alcnum > 0) {
			$errstr .=  "<a href='index.php?menu=rateSetup&id=".$ratesid."'>Room Rate Allocation for ".$agentname."</a> cannot auto allocate ".$alcnum ." room(s) of type ".get_roomtype($rtyp)."<br/>";
		}
	}

}
/**
 * @}
 * @}
 */

?>


	<script type='text/javascript'>
		function showrooms(roomtype) {
			var val = document.getElementById(roomtype);
			if(val.style.display == 'none') {
				val.style.display = '';
			} else {
				val.style.display = 'none';
			}
		}
	</script>
<table height="500px" class="listing-table">
	<tr>
		  <?php 
			if ($_GET['menu'] == "roomsview") {
				print_rightMenu_home();
			}?>	
	<td valign="top">
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
	  <table width="100%"  border="0" cellpadding="1" align="center" >
		<tr class="tdbgcl" valign="top">
		  <td>
		  <div>
			<div class="scroll">
			<table width="100%"  border="0" cellpadding="1">
			  <tr><td align="center"></td></tr>
			  <tr><td colspan="2"><h2><?php echo $_L['RM_list']; ?></h2></td></tr>
			  <tr>
				<td><h2><?php echo $_L['RM_view']; ?></h2></td> 
				<td>
				</td>
			  </tr>
			  
			  <tr>
				<td colspan="2">
				  <div id="Requests" style="overflow:auto; height:368px;">

					<table  border="1" cellspacing="0" cellpadding="3" width="100%" height="50px">
						<tr>
						<?php
							$i = strtotime($fromdate);
							echo "<th bgcolor='#CCCCCC'>".date('Y/m/d', $i)."</th>\n";
						?>
						</tr>
						<?php
						if($errstr) {
							print "<tr><td>".$errstr."</td></tr>";
						}
						foreach($roomtype as $rmtype=>$val) {
							echo "<tr><td><input class='button' type='button' onclick='showrooms(".$rmtype.");' value='Show' />".get_roomtype($rmtype)."</td></tr>";
							echo "<tr id='".$rmtype."' style='display: none;' ><td><table width='100%' cellspacing='0'>";
							echo "<tr><th width='10%' style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;' >".$_L['INV_room']."</th><th width='23%'  style='border-bottom:thin solid; border-right:thin solid; border-top:thin solid;'>".$_L['RSV_status']."</th><th width='10%' style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;' >".$_L['INV_room']."</th><th width='23%' style='border-bottom:thin solid; border-right:thin solid; border-top:thin solid;'>".$_L['RSV_status']."</th><th width='10%' style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;'>".$_L['INV_room']."</th><th width='24%' style='border-bottom:thin solid; border-right:thin solid; border-top:thin solid;'>".$_L['RSV_status']."</th></tr>";
							$rowcount = 0;
							echo "<tr height='60px'>";
							foreach($roomtype[$rmtype] as $rmid=>$val){
								$rowcount++;
								$i = strtotime($fromdate);
								$dt = date('Y/m/d', $i);
								if($result[$rmid][$dt]['status'] == LOCKED) {
									$bgcolor = "lightgrey";
									$res = "<a href='index.php?menu=roomsview&date=".$fromdate."&unlock=".$rmid."'> <img src='images/locked.jpg' title='Click to unlock' height='50px' width='50px' /></a><br/>".$_L['RM_locked'];
									if($result[$rmid][$dt]['bookid']) {
										$res .= "<br/><a href='".$path."/index.php?menu=booking&id=".$result[$rmid][$dt]['bookid']."'>".$_L['INV_booking']."</a>";
									}
									if($result[$rmid][$dt]['resid']) {
										$res .= "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid][$dt]['resid']."'>".$_L['ADM_reservation']." (".$result[$rmid][$dt]['voucher'] .")</a>";
									}
								}
								if($result[$rmid][$dt]['status'] == VACANT) {
									$bgcolor = "white";
									$res = "";
								}
								$billid = $result[$rmid][$dt]['bill_id'];
								if($result[$rmid][$dt]['resid']) 
									$billid = get_billID_byResID($result[$rmid][$dt]['resid']);
								if($billid && is_bill_inDebit($billid)) {
									$overdue = "<a href='".$path."/index.php?menu=invoice&id=".$billid."'><img src='images/dollar.png' title='Click to open invoice' height='50px' width='50px' />";
								} else { $overdue = ""; }
								if($result[$rmid][$dt]['status'] == RESERVED) {
									$bgcolor = "lightblue";
									$res = "<br/>".$overdue."<a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid][$dt]['resid']."'><img src='images/res_byroom.jpg' title='Click to open reservation' height='50px' width='50px' /><br/>".$_L['ADM_reservation'].": ".$result[$rmid][$dt]['guestname']." [".$result[$rmid][$dt]['reservation_by']."] (".$result[$rmid][$dt]['voucher'] .")</a>";
									if($result[$rmid][$dt]['agentname']) {
										$res .= "<br/>".$_L['ADM_agent'].":".$result[$rmid][$dt]['agentname'];
									}
								}
								if($result[$rmid][$dt]['status'] == BOOKED) {
									$bgcolor = "lightgreen";
									$res = "<br/>".$overdue."<a href='".$path."/index.php?menu=booking&id=".$result[$rmid][$dt]['bookid']."'><img src='images/bed3.jpg' title='Click to open booking' height='50px' width='50px' /><br/>".$_L['REG_checkedin'].": ".$result[$rmid][$dt]['guestname']."</a>";
									if($result[$rmid][$dt]['agentname']) {
										$res .= "<br/>".$_L['ADM_agent'].":".$result[$rmid][$dt]['agentname'];
									}
			
								}
								if($result[$rmid][$dt]['status'] == 'T') {
									$bgcolor = "yellow";
									$res = "<br/>".$overdue."<a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid][$dt]['resid']."'><img src='images/res_bytype.jpg' title='Click to open booking' height='50px' width='50px' /><br/>".$_L['RM_type']." ".$_L['ADM_reservation'].": ".$result[$rmid][$dt]['guestname']." [".$result[$rmid][$dt]['reservation_by']."] (".$result[$rmid][$dt]['voucher'] .")</a>";
									if($result[$rmid][$dt]['agentname']) {
										$res .= "<br/>".$_L['ADM_agent'].":".$result[$rmid][$dt]['agentname'];
									}
								}
								if($result[$rmid][$dt]['status'] == 'X') {
									$bgcolor = "white";
									$res = "<br/>".$overdue."<a href='".$path."/index.php?menu=rateSetup&id=".$result[$rmid][$dt]['ratesid']."'><img src='images/agentalloc.png' title='Click to open rate' height='50px' width='50px' /><br/>".$_L['ADM_agent'].":".$result[$rmid][$dt]['agentname']."<br/></a>";
								}
								if($result[$rmid][$dt]['bookid'] && $result[$rmid][$dt]['resid']) {
									$bgcolor = "red";
									$res = "Conflict";
									$res .= "<br/><a href='".$path."/index.php?menu=booking&id=".$result[$rmid][$dt]['bookid']."'>".$_L['INV_booking']."</a>";
									$res .= "<br/><a href='".$path."/index.php?menu=reservation&resid=".$result[$rmid][$dt]['resid']."'>".$_L['ADM_reservation']." (".$result[$rmid][$dt]['voucher'] .")</a>";
									if($result[$rmid][$dt]['agentname']) {
										$res .= "<br/>".$_L['ADM_agent'].":".$result[$rmid][$dt]['agentname'];
									}
								}
								echo "<td bgcolor='".$bgcolor."' style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;'><b>".$result[$rmid]['roomno']."<br/>".$result[$rmid]['roomname']."</b></td>";
								print "<td bgcolor='".$bgcolor."'  style='border-bottom:thin solid; border-right:thin solid; border-top:thin solid;'>";
								print $res;
								print "</td>";
								if(($rowcount % 3) == 0 ) {
									echo "</tr><tr height='60px'>";
								}
							}
							if($rowcount % 3 == 2) {
								echo "<td style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;'></td><td style='border-bottom:thin solid; border-top:thin solid; border-right:thin solid;'></td>";
							}
							if($rowcount %3 == 1) {
								echo "<td style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;'></td><td style='border-bottom:thin solid; border-top:thin solid; border-right:thin solid;'></td>";
								echo "<td style='border-bottom:thin solid; border-left:thin solid; border-top:thin solid;'></td><td style='border-bottom:thin solid; border-top:thin solid; border-right:thin solid;'></td>";
							}
							echo "</tr>";
							echo "</table></td></tr>";
						}
					?>
					</table> 

				  </div>
				</td>		
			  </tr>
			  <tr bgcolor="#66CCCC" ><td align="left" colspan="2"><div id="RequestDetails"></div></td></tr>
			</table>
			</div>
			<div>	
				<table align="right" height="60px">
					<tr>
						<td>
							<input type="button" name="Submit" value="<?php echo $_L['RM_listroom'];?>" onclick="self.location='index.php?menu=roomsList'" class="button"/>									
						</td>
					</tr>
				</table>			
			</div>
			</div>
		  </td>
		  
		  
		</tr>
	  </table>
    </form>
	</td>
	</tr>
</table>
