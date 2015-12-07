<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file dailyfunc.php
 * @brief daily functions called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */

include_once (dirname(__FILE__)."/queryfunctions.php");

/**
 * Return all reservation headers for the supplied date
 * @param $rdate [in] the date
 * @param $rs [in/out] The result array of headers
 * 
 * @return Number of elements added to RS $rs
 * $rs[idx]['Id'] = Reservation id
 * $rs[idx]['Date'] = Reservation date
 * $rs[idx]['Time'] = Reservation time
 * $rs[idx]['Name'] = Guest Name
 */
function GetReservationDateForDate($rdate, &$rs) {

	$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	$rs = array();
	if(!$conn) {
		print "Cannot connect to database<br>\n";
	}

	$sql="SELECT g.lastname,g.firstname,g.middlename,r.reserve_checkindate,TIME(r.reserve_time) as Restime , r.reservation_id
		FROM guests g RIGHT OUTER JOIN reservation r ON g.guestid=r.guestid 
		WHERE r.reserve_checkindate='".$rdate."'
		ORDER BY r.reserve_time ASC";
	if(DEBUG) {
		print "Debug RESERVATION QRY ".$sql."<br>\n";
	}
	$result=mysql_query($sql,$conn);
	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		if (DEBUG) print "DEBUG Reservations ". $row['reserve_checkindate'] . $row['Restime']."<br>\n";
		$rs[$i]['Id'] =  $row['reservation_id'];
		$rs[$i]['Date'] =  $row['reserve_checkindate'];
		$rs[$i]['Time'] =  $row['Restime'];
		$rs[$i]['Name'] =  $row['lastname']. "," . $row['firstname']. " " . $row['middlename'];
		$i++;
	}
	db_close($conn);
	return sizeof($rs);
}
/**
 * Return all booking headers for the supplied date
 * @param $bdate [in] the date
 * @param $rs [in/out] The result array of headers
 * 
 * @return Number of elements added to RS $rs
 * $rs[idx]['Id'] = Booking Id
 * $rs[idx]['Date'] = Booking date
 * $rs[idx]['Time'] = Booking time
 * $rs[idx]['Name'] = Guest Name
 */
function GetBookingDataForDate ($bdate, &$rs) {
	$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) {
		print "Cannot connect to database<br>\n";
	}
	$rs = array();

	$sql="SELECT g.lastname,g.firstname,g.middlename,b.booking_type,b.checkout_date AS CODate, TIME(b.codatetime) AS COTime, b.book_id
		FROM guests g RIGHT OUTER JOIN booking b ON g.guestid=b.guestid
		WHERE b.checkout_date='".$bdate."'
		ORDER BY COTime ASC ";
	if(DEBUG) {
		print "Debug BOOKING QRY ".$sql."<br>\n";
	}
	$result=mysql_query($sql,$conn);

	$i = 0;
	while ($row = mysql_fetch_assoc($result)) {
		if (DEBUG) print "DEBUG Booking ". $row['CODate'] . $row['COTime']."<br>\n";
		$rs[$i]['Id'] =  $row['book_id'];
		$rs[$i]['Date'] =  $row['CODate'];
		$rs[$i]['Time'] =  $row['COTime'];
		$rs[$i]['Name'] =  $row['lastname']. "," . $row['firstname']. " " . $row['middlename'];
		$i++;
	}
	db_close($conn);
	return sizeof($rs);
}
/**
 * Return all booking headers for the supplied date
 * @param $rbdate [in] the date
 * @param $rs [in/out] The result array of headers
 * 
 * @return Number of elements added to RS $rs
 * $rs[idx]['Id'] =  ID (for reservations or booking table)
 * $rs[idx]['Date'] =  date
 * $rs[idx]['Time'] =  time
 * $rs[idx]['Name'] = Guest Name "Last, First Middle"
 * $rs[idx]['Type'] = [In|Out]
 */
function GetResAndBookingDataForDate($rbdate, &$rs) {
	$bk = array(); // result from getting bookings
	$rv = array(); // result from getting reservations
	$rs = array(); // merged result of bk and rv

	$rvcount = GetReservationDateForDate($rbdate, $rv);
	$bkcount = GetBookingDataForDate($rbdate, $bk);
	$i = 0;
	$j = 0;
	$k = 0;
	print "DEBUG BK ".$bkcount." RV ".$rvcount."<br>\n";
	while(($i < $rvcount || $j < $bkcount ) && $k < ($rvcount+$bkcount)) {
		if (DEBUG) print "DEBUG Merging<br>\n";
		if(($i < $rvcount && $j < $bkcount && $bk[$j]['Time'] < $rv[$i]['Time'] )|| ($i >= $rvcount && $j < $bkcount)) {
			if (DEBUG) print "DEBUG Add booking ".$j." to result ".$k."<br>\n";
			$rs[$k]['Id'] = $bk[$j]['Id'];
			$rs[$k]['Time'] = $bk[$j]['Time'];
			$rs[$k]['Date'] = $bk[$j]['Date'];
			$rs[$k]['Name'] = $bk[$j]['Name'];
			$rs[$k]['Type'] = 'Out';
			$k++;
			$j++;
			continue;
		} else if (($i < $rvcount && $j < $bkcount && $rv[$i]['Time'] < $bk[$j]['Time']) || ($j >= $bkcount && $i < $rvcount)) {
			if (DEBUG) print "DEBUG Add res ".$i." to result ".$k."<br>\n";
			$rs[$k]['Id'] = $rv[$i]['Id'];
			$rs[$k]['Time'] = $rv[$i]['Time'];
			$rs[$k]['Date'] = $rv[$i]['Date'];
			$rs[$k]['Name'] = $rv[$i]['Name'];
			$rs[$k]['Type'] = 'In';
			$k++;
			$i++;
			continue;
		} else if ($bk[$j]['Time'] == $rv[$i]['Time']) {
			if (DEBUG) print "DEBUG Add booking ".$j." and res ".$i." to result ".$k."<br>\n";
			$rs[$k]['Id'] = $bk[$j]['Id'];
			$rs[$k]['Time'] = $bk[$j]['Time'];
			$rs[$k]['Date'] = $bk[$j]['Date'];
			$rs[$k]['Name'] = $bk[$j]['Name'];
			$rs[$k]['Type'] = 'Out';
			$k++;
			$j++;
			$rs[$k]['Id'] = $rv[$i]['Id'];
			$rs[$k]['Time'] = $rv[$i]['Time'];
			$rs[$k]['Date'] = $rv[$i]['Date'];
			$rs[$k]['Name'] = $rv[$i]['Name'];
			$rs[$k]['Type'] = 'In';
			$k++;
			$i++;
			continue;
		}
	}
	return sizeof($rs);
}

?>
