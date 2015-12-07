<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file reportqueries.php
 * @brief report query functions called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");

$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);

//bookedguests();
$gueststatus = $_POST["button"];
switch ($gueststatus){
	case "all":
		//call same function only thing to change is sql statement and actions
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.address,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode";
	guestslist($sql);
	//allguests();		
	break;
	case "booked":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.address,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country,booking.codatetime
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join booking ON guests.guestid = booking.guestid
			Where isnull(booking.codatetime)";
	guestslist($sql);
	break;		
	case "reserved":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.address,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country,reservation.reserve_checkindate
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join reservation ON guests.guestid = reservation.guestid
			Where reservation.reserve_checkindate >= current_date()"; //date variable user to select a date for arrivals to do
			break;
	case "arrivals":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.address,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country,reservation.reserve_checkindate
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join reservation ON guests.guestid = reservation.guestid
			Where reservation.reserve_checkindate >= current_date()";
	break;
	case "departures":
		$sql="Select guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) as guest,guests.pp_no,
		guests.idno,guests.address,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,countries.country
			From guests
			Inner Join countries ON guests.countrycode = countries.countrycode
			Inner Join booking ON guests.guestid = booking.guestid
			Where booking.checkout_date=current_date()";
	guestslist($sql);
	break;
	case "dep_summ":
		$sql="Select rooms.roomno as RoomNo,transactions.doc_no as DocNo,transactions.doc_type,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) AS Name,
		transactions.dr as Debit,transactions.cr as Credit,details.item as Remarks,transactions.doc_date as DocDate
			From transactions
			left Join details ON transactions.details = details.itemid
			left Join bills ON transactions.billno = bills.billno
			Inner Join booking ON bills.book_id = booking.book_id
			Inner Join guests ON booking.guestid = guests.guestid
			Inner Join rooms ON booking.roomid = rooms.roomid";
	//Where transactions.details = '$details' and transactions.doc_date = '$date'
	echo "<table>
		<tr><td><h2>Departmental Summary Control Sheet</h2></td></tr>
		<tr>
		<td>Department: <select name=\"itemid\" id=\"itemid\"\">
		<option value=\"All\" >All</option>";
	populate_select("details","itemid","item",$details);
	echo "</select></td>
		<td>Date:<input name=\"date\" id=\"date\" type=\"text\" size=\"10\" readonly=\"true\">
		<small><a href=\"javascript:showCal('Calendar8')\"> <img src=\"images/ew_calendar.gif\" width=\"16\" height=\"15\" border=\"0\"/></a></small>
		</td>
		<td><input name=\"submit\" type=\"submit\" value=\"Submit\"></td>
		</tr>
		</table>";
	getdata();
	break;
	default:
	echo "<h2>Under construction</h2>";
}				
/**
 *getting all guestlist from database based upon sql query<br>
 *@param $sql [in] sql query<br>
 *Connecto to the database and the result display in web page<br>
 *uses constants <b>HOST</b>,<b>USER</b>,<b>PASS</b>,<b>DB</b>,<b>PORT</b> to connecto to the database <br>
 *Generate HTML output by table in web page.<br>
 */
function guestslist($sql){
	//global $gueststatus;
	$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	$results=mkr_query($sql,$conn);
	echo "<table align=\"center\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th colspan=\"4\">Action</th>
		<th>Guest</th>
		<th>PP. No./ID. No.</th>
		<th>Mobile</th>
		<th>Phone</th>
		<th>Email</th>
		<th>P. O. Box</th>
		<th>Town-Postal code</th>
		</tr>";
	//end of field header
	//get data from selected table on the selected fields
	while ($guest = fetch_object($results)) {
		//alternate row colour
		$j++;
		if($j%2==1){
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
		}else{
			echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
		}
		echo "<td><a href=\"guests.php?search=$guest->guestid\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view guests details\"/></a></td>";
		echo "<td><a href=\"bookings.php?search=$guest->guestid\"><img src=\"images/bed.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"book guest\"/></a></td>";
		echo "<td><a href=\"reservations.php?search=$guest->guestid\"><img src=\"images/bed2.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"guest reservtion\"/></a></td>";
		echo "<td><a href=\"billings.php?search=$guest->guestid\"><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"bill guest\"/></a></td>";
		echo "<td>" . trim($guest->guest) . "</td>";
		echo "<td>" . $guest->pp_no . "/" .$guest->idno . "</td>";
		echo "<td>" . $guest->mobilephone . "</td>";
		echo "<td>" . $guest->phone . "</td>";
		echo "<td>" . $guest->email . "</td>";
		echo "<td>" . $guest->address . "</td>";					
		echo "<td>" . $guest->town . '-' . $guest->postal_code . "</td>";
		echo "</tr>"; //end of - data rows
	} //end of while row
	echo "</table>";
}
/**
 *This function is getting data from database<br>
 *@note global variables <i>sql</i>,<i>con</i> to set the result.<br>
 *display result to web page.<br>
 *Generate HTML output directly for use in web page.<br>
 */
function getdata(){
	global $sql,$conn;
	$results=mkr_query($sql,$conn);
	/*$totRows = mysql_query("SELECT FOUND_ROWS()"); //get total number of records in the select query irrespective of the LIMIT clause
		$totRows = mysql_result($totRows , 0);
		$_SESSION["nRecords"]=$totRows;	
		$_SESSION["totPages"]=ceil($totRows/$strRows);
		$_SESSION["RowsDisplayed"]=$strRows;*/
	echo "<table align=\"center\">";
	//get field names to create the column header
	echo "<tr bgcolor=\"#009999\">
		<th>Action</th>";
	while ($i < mysql_num_fields($results)) {
		$meta = mysql_fetch_field($results, $i);
		$field=$meta->name;
		echo "<th>" . $field . "</th>";
		$i++;
	}		
	"</tr>";
	//end of field header
	if  ((int)$results!==0){
		//get data from selected table on the selected fields
		while ($row = fetch_object($results)) {
			//alternate row colour
			$j++;
			if($j%2==1){
				echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
			}else{
				echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
			}
			echo "<td><a href=\"reportqueries.php?search=$row->ID\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view\"/></a></td>";
			$i = 0;
			while ($i < mysql_num_fields($results)) {
				$meta = mysql_fetch_field($results, $i);
				$field=$meta->name;
				echo "<td>" . $row->$field . "</td>";
				$i++;
			}
			//			
			echo "</tr>"; //end of - data rows
		} //end of while row
		echo "</table>";
	}
	free_result($results);
}
"Select
rooms.roomno,
	guests.lastname,
	guests.firstname,
	guests.middlename,
	booking.checkin_date,
	booking.checkout_date,
	booking.bk_date
	From
	rooms
	Inner Join booking ON rooms.roomid = booking.roomid
	Inner Join guests ON booking.guestid = guests.guestid
	Where
	year(booking.checkin_date ) = '2006' AND
	month(booking.checkin_date ) = '1'
	Order By
	booking.checkin_date Asc";
	?>
