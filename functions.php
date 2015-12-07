<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file functions.php
 * @brief general purpose functions called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__).'/queryfunctions.php');
include_once(dirname(__FILE__).'/hotel_defs.php');
include_once(dirname(__FILE__).'/lang/lang_en.php');
/**
 * Check the operator setup exists
 * @ingroup ADMIN_MANAGEMENT * 
 * @return 0 if no operator setup  
 */
function Is_OperatorsetupExists(){
	global $conn;	
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$count = 0;
	$sql="Select count(*) as foundop from hotelsetup";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results){
		$row = $stmt->fetch();
		$count=$row['foundop'];	
	}	
	return $count;
}
/**
 * Logout of the webpage and delete the cookies
 * @ingroup USER_MANAGEMENT
 *
 */
function LogoutHotel() {
	setcookie("data_login","",time()-60);
	$_POST["login"] = "";
	$_SESSION["loginname"]="";
	$_SESSION["userid"]="";
	$_SESSION["logged"]=0;
	//ob_start();
	session_unset();
	session_destroy();
}
/**
 * Login to the hotel website
 * @ingroup USER_MANAGEMENT
 * 
 *  @param $username [in] User login name 
 *  @param $password [in] Password to verify from database
 *
 * @note if password is incorrect will send redirection code and bypass the 
 * the login screen. Updates $_SESSION variables and cookies directly.
 *
 * @retval 0 fail to login
 * @retval 1 Login successful.
 */
function LoginHotel($username, $password){
	global $conn;
	if($username && $password) {
		if(!$conn) $conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT) or die ("Whoops");    // Connect to the database, or if connection fails print error message.
		$password = md5($password);          // encode submited password with MD5 encryption and store it back in the same variable. If not on a windows box, I suggest you use crypt()
		$username = strip_specials($username);
		$sql = "select * from users where loginname='".$username."'";   // query statment that gets the username/password from 'login' where the username is the same as the one you submited
		$stmt = $conn->prepare( $sql);
		$results = $stmt->execute();
//		echo "User -".$username."-<br/>";
//		echo "Password -".$password."-<br/>";
//		echo $sql."<br/>";
//		return 0;
		// if no rows for that database come up, redirect.
		if($stmt->rowCount() == 0){
			$_SESSION["logged"]=0;
			$_SESSION["userid"]="";
			header("Location: index.php");  // This is the redirection, notice it uses $SCRIPT_NAME which is a predefined variable with the name of the script in it.
			echo "<center><font color=\"#FF0000\"><b>Invalid User Name or Password</b></font></center>";   
			return 0;
		}


		//$sql="select pass('$password') as pass, fname, sname from users";
		$sql="select pass, fname, sname, loginname, userid from users where loginname='".$username."' and pass='".$password."'";
		$stmt=$conn->prepare($sql);
		$results = $stmt->execute();
		$password = array();
		$password = $stmt->fetch();
		$_SESSION["employee"]=$password['fname'] ." ". $password['sname'];
		$_SESSION["loginname"]=$password['loginname'];
		$_SESSION["userid"]=$password['userid'];
		$pass=$password['pass'];		
		//******************************************************************
		//*Not the best option but produce the required results - unencrypted password saved to a cookie
		//******************************************************************
		setcookie("data_login","$username $userid",0);  // Set the cookie named 'candle_login' with the value of the username (in plain text) and the userid
		$_SESSION["logged"]=1;
		LoginPermissions($conn, $username);

		// set variable $msg with an HTML statement that basically says redirect to the next page. The reason we didn't use header() is that using setcookie() and header() at the sametime isn't 100% compatible with all browsers, this is more compatible.
		$msg = "<meta http-equiv=\"Refresh\" content=\"0;url=./index.php\">"; //put index.php
	}else{
		echo "<center><font color=\"#FF0000\"><b>Enter your UserName and Password to login on to the system</b></font></center>";
		$_SESSION["logged"]=0;
		$_SESSION['employee'] = "";
		$_SESSION['loginname'] = "";
		$_SESSION['userid'] = "";
	}
	if($msg) {
		echo $msg;  //if $msg is set echo it, resulting in a redirect to the next page.
		return 0;
	}
	return 1;
	//}
	}
function LoginHotelNew($username, $password){
	global $conn;
	if($username && $password) {
		if(!$conn) $conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT) or die ("Whoops");    // Connect to the database, or if connection fails print error message.
		$password = md5($password);          // encode submited password with MD5 encryption and store it back in the same variable. If not on a windows box, I suggest you use crypt()
		$username = strip_specials($username);
		$sql = "select * from users where loginname='".$username."'";   // query statment that gets the username/password from 'login' where the username is the same as the one you submited
		$stmt = $conn->prepare( $sql);
		$results = $stmt->execute();
//		echo "User -".$username."-<br/>";
//		echo "Password -".$password."-<br/>";
//		echo $sql."<br/>";
//		return 0;
		// if no rows for that database come up, redirect.
		if($stmt->rowCount() == 0){
			$_SESSION["logged"]=0;
			$_SESSION["userid"]="";
			header("Location: index.php");  // This is the redirection, notice it uses $SCRIPT_NAME which is a predefined variable with the name of the script in it.
			echo "<center><font color=\"#FF0000\"><b>Invalid User Name or Password</b></font></center>";   
			return 0;
		}


		//$sql="select pass('$password') as pass, fname, sname from users";
		$sql="select pass, fname, sname, loginname, userid from users where loginname='".$username."' and pass='".$password."'";
		$stmt=$conn->prepare($sql);
		$results = $stmt->execute();
		$password = array();
		$password = $stmt->fetch();
		$_SESSION["employee"]=$password['fname'] ." ". $password['sname'];
		$_SESSION["loginname"]=$password['loginname'];
		$_SESSION["userid"]=$password['userid'];
		$pass=$password['pass'];		
		//******************************************************************
		//*Not the best option but produce the required results - unencrypted password saved to a cookie
		//******************************************************************
		setcookie("data_login","$username $userid",0);  // Set the cookie named 'candle_login' with the value of the username (in plain text) and the userid
		$_SESSION["logged"]=1;
		LoginPermissions($conn, $username);

		// set variable $msg with an HTML statement that basically says redirect to the next page. The reason we didn't use header() is that using setcookie() and header() at the sametime isn't 100% compatible with all browsers, this is more compatible.
		$msg = "<meta http-equiv=\"Refresh\" content=\"0;url=./index.php\">"; //put index.php
	}else{
		echo "<center><font color=\"#FF0000\"><b>Enter your UserName and Password to login on to the system</b></font></center>";
		$_SESSION["logged"]=0;
		$_SESSION['employee'] = "";
		$_SESSION['loginname'] = "";
		$_SESSION['userid'] = "";
	}
	if($msg) {
		echo $msg;  //if $msg is set echo it, resulting in a redirect to the next page.
		return 0;
	}
	return 1;
	//}
	}
/**
 * check user, if user is match from database, can see that page.Use switch case for user.
 * @ingroup USER_MANAGEMENT
 * @param $page [in] The page name
 * @return >0 true 0 false
 */
function access($page){
	if(DEBUG > 3) {
		print "user ".$_SESSION["loginname"]."<br>\n";
		//	LoginPermissions(0, $_SESSION["loginname"]);
		print "admin ".$_SESSION['admin']."<br>\n";
		print "guest ".$_SESSION['guest']."<br>\n";
		print "reservation ".$_SESSION['reservation']."<br>\n";
		print "booking ".$_SESSION['booking']."<br>\n";
		print "agents ".$_SESSION['agents']."<br>\n";
		print "rooms ".$_SESSION['rooms']."<br>\n";
		print "billing ".$_SESSION['billing']."<br>\n";
		print "rates ".$_SESSION['rates']."<br>\n";
		print "lookup ".$_SESSION['lookup']."<br>\n";
		print "reports ".$_SESSION['reports']."<br>\n";
	}
	switch($page){
		case 'admin':
			$access=$_SESSION["admin"];
			break;
		case 'guest':
			$access=$_SESSION["guest"];
			break;
		case 'reservation':
			$access=$_SESSION["reservation"];
			break;
		case 'booking':
			$access=$_SESSION["booking"];
			break;
		case 'agents':
			$access=$_SESSION["agents"];
			break;
		case 'rooms':
			$access=$_SESSION["rooms"];
			break;
		case 'billing':
			$access=$_SESSION["billing"];
			break;
		case 'rates':
			$access=$_SESSION["rates"];
			break;
		case 'lookup':
			$access=$_SESSION["lookup"];
			break;
		case 'reports':
			$access=$_SESSION["reports"];
			break;		
	}
	//	if ($access==0) exit("If you were brought here it's because you do not have permission to view this page.");
	if($access ==0)
		header("Location: index.php");
	return $access;
}

/**
 * check user, if user is match from database, can see that page.Use switch case for user.
 * @ingroup USER_MANAGEMENT
 * @param $page [in] The page name
 * @return >0 true 0 false
 */
function accessNew($page){
	if(DEBUG > 3) {
		print "user ".$_SESSION["loginname"]."<br>\n";
		//	LoginPermissions(0, $_SESSION["loginname"]);
		print "admin ".$_SESSION['admin']."<br>\n";
		print "guest ".$_SESSION['guest']."<br>\n";
		print "reservation ".$_SESSION['reservation']."<br>\n";
		print "booking ".$_SESSION['booking']."<br>\n";
		print "agents ".$_SESSION['agents']."<br>\n";
		print "rooms ".$_SESSION['rooms']."<br>\n";
		print "billing ".$_SESSION['billing']."<br>\n";
		print "rates ".$_SESSION['rates']."<br>\n";
		print "lookup ".$_SESSION['lookup']."<br>\n";
		print "reports ".$_SESSION['reports']."<br>\n";
	}
	switch($page){
		case 'admin':
			$access=$_SESSION["admin"];
			break;
		case 'guest':
			$access=$_SESSION["guest"];
			break;
		case 'reservation':
			$access=$_SESSION["reservation"];
			break;
		case 'booking':
			$access=$_SESSION["booking"];
			break;
		case 'agents':
			$access=$_SESSION["agents"];
			break;
		case 'rooms':
			$access=$_SESSION["rooms"];
			break;
		case 'billing':
			$access=$_SESSION["billing"];
			break;
		case 'rates':
			$access=$_SESSION["rates"];
			break;
		case 'lookup':
			$access=$_SESSION["lookup"];
			break;
		case 'reports':
			$access=$_SESSION["reports"];
			break;		
	}
	//	if ($access==0) exit("If you were brought here it's because you do not have permission to view this page.");
//	if($access ==0)
//		header("Location: index.php");
	return $access;
}

/**
 * Get user details from userid
 *
 * @ingroup USER_MANAGEMENT
 * @param $userid [in] userid
 * @param $user [in/out] User array
 * @note user array has form. <br/>
 * $user['userid'] - System user id <br/>
 * $user['user'] - "Firstname Lastname"<br/>
 * $user['loginname'] - login name<br/>
 *
 * @return number of elements in user
 */
function get_userbyid($userid, &$user) {
	if(!$userid) return 0;
	if(!$conn ){
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT) ;
	}
	if(!$conn) return 0;
	$sql = "select userid, concat_ws(' ',fname,sname) as user,loginname from users where userid = ".strip_specials($userid);
	$stmt=$conn->prepare($sql);
	$results = $stmt->execute();
	$user = array();
//	print $sql."<br/>";
	if($results) {
		$row = $stmt->fetch();
		$user['userid'] = $userid;
		$user['user'] = $row['user'];
		$user['loginname'] = $row['loginname'];
	}
	
	return sizeof($user);

}
/**
 * Get the list of users for the OTA Hotel Management program
 * 
 * @ingroup USER_MANAGEMENT
 * @param $users [in/out] users array for result
 * @param $search [in] Search
 * @param $stype [in] search type
 * @param $usrlistby [in] user list by/ sort
 * @return number of elements in users
 */
function get_userslist(&$users, $search, $stype, $usrlistby) {
	global $conn;
	$sql="Select userid,concat_ws(' ',fname,sname) as user,loginname,phone,mobile,
		fax,email,dateregistered,admin,guest,reservation,
		booking,agents,rooms,billing,rates,lookup,reports
			From users	";
	if($search && $stype == 1) {
		$pos = strrpos($search, ' ');
		list($fname, $lname) = preg_split("/[\s,]+/", $search,2);
		$sql .= " where fname like '%".$fname."%' or sname like '%".$fname."%'";
	}
	if($search && $stype == 2) {
		$sql .= " where userid=".$search;
	}
	if($usrlistby) {
		if ($usrlistby=="agent")
		{
			if($search && ($stype == 1 || $stype == 2))
				$sql.=" AND ";
			else
				$sql.=" WHERE ";
			$sql.="agents=1";
		}
	}
	if(!$conn ){
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$stmt=$conn->prepare($sql);
	$results = $stmt->execute();
	//$users = array();
	while($row = $stmt->fetch()) {
		$users[$row['userid']]['userid'] = $row['userid'];
		$users[$row['userid']]['user'] = $row['user'];
		$users[$row['userid']]['loginname'] = $row['loginname'];
		$users[$row['userid']]['phone'] = $row['phone'];
		$users[$row['userid']]['mobile'] = $row['mobile'];
		$users[$row['userid']]['fax'] = $row['fax'];
		$users[$row['userid']]['email'] = $row['email'];
		$users[$row['userid']]['dateregistered'] = $row['dateregistered'];
		$users[$row['userid']]['admin'] = $row['admin'];
		$users[$row['userid']]['guest'] = $row['guest'];
		$users[$row['userid']]['reservation'] = $row['reservation'];
		$users[$row['userid']]['booking'] = $row['booking'];
		$users[$row['userid']]['agents'] = $row['agents'];
		$users[$row['userid']]['rooms'] = $row['rooms'];
		$users[$row['userid']]['billing'] = $row['billing'];
		$users[$row['userid']]['rates'] = $row['rates'];
		$users[$row['userid']]['lookup'] = $row['lookup'];
		$users[$row['userid']]['reports'] = $row['reports'];
	}
	//print_r($users);
	return sizeof($users);
}
/** 
 * Check the user permission and set against the session
 * @ingroup USER_MANAGEMENT
 *
 * @param $conn [in] current DB connection
 * @param $userid [in] User id of the person
 * @return 0 fail >0 success
 */
function LoginPermissions($conn, $userid) {
	global $conn;
	if(!$conn ){
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT) ;
	}
	if(!$conn) return 0;
	
	$sql = "select * from users where loginname='".$userid."'";  //sql statment that uses the username from the cookie.
	$stmt=$conn->prepare($sql);
	$results = $stmt->execute();

	if($stmt->rowCount() == 0) {    // if there are no rows, means no matches for that username
		header("Location: index.php");   // so go back to the login page
		return 0;
	}

	$chkusr = array();
	$chkusr = $stmt->fetch();
	/*if(unserialize(stripslashes($user[1])) != $chkusr[2]){ //if the password from cookie (notice we have to unserialize it) doesn't match the one from the database
		header("Location: userrequest.php");    // go back to the login page
		}*/
	//todo - put some code so that this is only done once in the lifetime of a session.
	$_SESSION["admin"]=$chkusr["admin"];
	$_SESSION["guest"]=$chkusr["guest"];
	$_SESSION["reservation"]=$chkusr["reservation"];
	$_SESSION["booking"]=$chkusr["booking"];
	$_SESSION["agents"]=$chkusr["agents"];
	$_SESSION["rooms"]=$chkusr["rooms"];
	$_SESSION["billing"]=$chkusr["billing"];
	$_SESSION["rates"]=$chkusr["rates"];
	$_SESSION["lookup"]=$chkusr["lookup"];
	$_SESSION["reports"]=$chkusr["reports"];
	if(DEBUG) {
		print "admin ".$_SESSION['admin']."<br>\n";
		print "guest ".$_SESSION['guest']."<br>\n";
		print "reservation ".$_SESSION['reservation']."<br>\n";
		print "booking ".$_SESSION['booking']."<br>\n";
		print "agents ".$_SESSION['agents']."<br>\n";
		print "rooms ".$_SESSION['rooms']."<br>\n";
		print "billing ".$_SESSION['billing']."<br>\n";
		print "rates ".$_SESSION['rates']."<br>\n";
		print "lookup ".$_SESSION['lookup']."<br>\n";
		print "reports ".$_SESSION['reports']."<br>\n";
		return 1;
	}
}
/**
 * This function prints a standard table data footer
 * @ingroup FORM_MANAGEMENT
 */
function print_footerinit() {
	print "	  
		<td colspan=2 ><a href=\"http://www.php.net\" target=\"_blank\"><img src=\"../images/php-power-white.gif\" width=\"88\" height=\"31\" border=\"0\" /></a>
		<a href=\"http://www.mysql.com\" target=\"_blank\">
		<img src=\"../images/powered-by-mysql-88x31.png\" width=\"88\" height=\"31\" border=\"0\" /></a>
		<a href=\"https://www.facebook.com/eNovate?ref=hl\" target=\"_blank\"> <img src=\"../images/social_facebook_box_blue.png\"  height=\"31\" /></a>
		<a href=\"https://www.e-bridgedirect.com\" target=\"_blank\" > <img src=\"../images/e-Bridge.jpg\"  height=\"31\"/> </a>
		<a href=\"http://www.e-novate.asia\" target=\"_blank\"> e-Novate Pte Ltd &copy; 2010-2013. &nbsp; </a>
		<a href=\"http://otahotel.sourceforge.net\" target=\"_blank\" ><img src=\"http://sflogo.sourceforge.net/sflogo.php?group_id=172638&amp;type=1\" width=\"88\" height=\"31\" border=\"0\" alt=\"SourceForge.net Logo\" /></a>
		<a href='#' onclick='window.open(\"doxygen/html/index.html\");' > Help </a></td>";
}
/**
 * This function prints a standard table data footer
 * @ingroup FORM_MANAGEMENT
 */
function print_footerNew() {
	print "	  
		<td colspan=\"2\" valign=\"right\"><a href=\"http://www.php.net\" target=\"_blank\"><img src=\"images/php-power-white.gif\" width=\"88\" height=\"31\" border=\"0\" /></a>
		<a href=\"http://www.mysql.com\" target=\"_blank\">
		<img src=\"images/powered-by-mysql-88x31.png\" width=\"88\" height=\"31\" border=\"0\" /></a>
		<a href=\"https://www.facebook.com/eNovate?ref=hl\" target=\"_blank\"> <img src=\"images/social_facebook_box_blue.png\"  height=\"31\" /></a>
		<a href=\"https://www.e-bridgedirect.com\" target=\"_blank\" > <img src=\"images/e-Bridge.jpg\"  height=\"31\"/></a>
		<a href=\"http://otahotel.sourceforge.net\" target=\"_blank\" ><img src=\"http://sflogo.sourceforge.net/sflogo.php?group_id=172638&amp;type=1\" width=\"88\" height=\"31\" border=\"0\" alt=\"SourceForge.net Logo\" /></a>
		</td>";
}
/**
 * @ingroup ROOM_MANAGEMENT
 * Delete all room amenities for a specific room
  * @param $roomid [in] room id
 */
function delete_roomamenities($roomid) {
	global $conn;
	if(! $roomid) {
		return;
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="DELETE FROM room_amenities WHERE room_id=".$roomid;
	$stmt = $conn->prepare( $sql);
	$res = $stmt->execute();
}


/**
 * Add a room amenity to the room
 * @ingroup ROOM_MANAGEMENT
 * 
 * @param $roomid [in] The room id
 * @param $amenity [in] the OTA amenity number
 *
 */
function add_roomamenity($roomid, $amenity) {
	global $conn;
	if(! $roomid) {
		return;
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="insert into room_amenities (room_id, OTA_number ) values (".$roomid.",".$amenity.")";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	//	print $sql."<br/>\n";
}
/**
 * Save the Hotel setup into the database.
 *
 * @ingroup ADMIN_MANAGEMENT
 * @param $hotel [in] Hotel name
 * @param $altname [in] Altername for the hotel
 * @param $company [in] company for the hotel
 * @param $register [in] Business registration number
 * @param $ebridgeid [in] e-Bridge ID
 * @param $tax1 [in] Tax ID
 * @param $tax2 [in] Tax ID
 * @param $phone [in] Telephone number
 * @param $fax [in] Fax number
 * @param $IM [in] Instant messenger ID
 * @param $street [in] Street address
 * @param $city [in] City name
 * @param $citycode [in] 3 Letter city code
 * @param $state [in] State or province
 * @param $postcode [in] Postcode
 * @param $countrycode [in] 2 letter countrycode
 * @param $country [in] Country name
 * @param $logo [in] html reference for the logo
 * @param $latitude [in] latitude ddd.mmm.ss.ss[NS]
 * @param $longitude [in] longitude ddd.mmm.ss.ss[EW]
 * @param $language [in] language code eg en en-us ko
 * @param $email [in] email address
 * @param $web [in] external website address
 * @param $ota [in] OTA out bound connector
 * @param $chaincode [in] list of chain codes.
 */
function Save_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode) {

	global $conn;
	if(! $countrycode) {
		return "";
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="delete from hotelsetup";
	$stmt = $conn->prepare( $sql);
	$res = $stmt->execute();


	$sql="insert  into `hotelsetup`(`HotelName`,`AltHotelName`,`CompanyName`,`Street`,`State`,`CityCode`,`City`,`Country`,`CountryCode`,`PostCode`,`Telephone`,`Fax`,`Email`,`Web`,`Registration`,`TaxID1`,`TaxID2`,`OTA_URL`,`lang`,`LogoFileURL`,`ChainCode`,`Latitude`,`Longitude`,`eBridgeID`,`IM`) values ";
	$sql .= "('".strip_specials($hotel)."',";
	$sql .= "'".strip_specials($altname)."',";
	$sql .= "'".strip_specials($company)."',";
	$sql .= "'".strip_specials($street)."',";
	$sql .= "'".strip_specials($state)."',";
	$sql .= "'".strip_specials($citycode)."',";
	$sql .= "'".strip_specials($city)."',";
	$sql .= "'".strip_specials($country)."',";
	$sql .= "'".strip_specials($countrycode)."',";
	$sql .= "'".strip_specials($postcode)."',";
	$sql .= "'".strip_specials($phone)."',";
	$sql .= "'".strip_specials($fax)."',";
	$sql .= "'".strip_specials($email)."',";
	$sql .= "'".strip_specials($web)."',";
	$sql .= "'".strip_specials($register)."',";
	$sql .= "'".strip_specials($tax1)."',";
	$sql .= "'".strip_specials($tax2)."',";
	$sql .= "'".strip_specials($ota)."',";
	$sql .= "'".strip_specials($language)."',";
	$sql .= "'".strip_specials($logo)."',";
	$sql .= "'".strip_specials($chaincode)."',";
	$sql .= "'".strip_specials($latitude)."',";
	$sql .= "'".strip_specials($longitude)."',";
	$sql .= "'".strip_specials($ebridgeid)."',";
	$sql .= "'".strip_specials($IM)."')";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return ;
}
/**
 * Get the logo file for the display in the upper left corner.
 * Default file "images/titanic1.gif" if not set.
 * file can be a URL.
 * @ingroup ADMIN_MANAGEMENT
 * @return logo URL
 */
function Get_LogoFile() {
	global $conn;

	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$default = "images/titanic1.gif";
	$sql="select LogoFileURL from hotelsetup";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$logo=$row['LogoFileURL'];
	$logo = str_replace(" ","",$logo);
	if($logo == "") {
		$logo = $default;
	}
	return $logo;

}


/**
 * Get the current list of rooms from the database and summary information<br/>
 * about the current usage.
 * @ingroup ROOM_MANAGEMENT
 * @param $rooms [in/out] The array of rooms to return
 * @param $stype [in] type of search
 * @param $search [in] criteria for search
 * @param $status [in] room status LOCKED, BOOKED, RESERVED, VACANT
 * @todo add in todays date into the booking criteria
 */
function get_roomslist(&$rooms, $search, $stype, $status) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_roomslist_advProfile($rooms, $search, $stype, $status);
	}
	else{
		$rooms = array();
		$sql="select rooms.roomid, rooms.roomno,guests.guestid,concat_ws(' ',guests.firstname,guests.middlename,guests.lastname) AS guest, rooms.roomname,
			booking.checkindate,booking.checkoutdate,DATEDIFF(booking.checkoutdate,booking.checkindate) AS nights,
			booking.no_adults,booking.no_child1_5,no_child6_12,no_babies,rooms.status,roomtype.roomtype,rates.ratecode,rooms.roomtypeid 
				From rooms
				LEFT JOIN booking ON rooms.roomid = booking.roomid
				LEFT JOIN guests ON booking.guestid = guests.guestid 
				LEFT JOIN roomtype ON rooms.roomtypeid = roomtype.roomtypeid 
				LEFT JOIN rates ON roomtype.rateid = rates.ratesid ";
		if($search || $status) $sql .= " where ";
		if($search) 
		{
			if ($stype==1)
			$sql.="rooms.roomno=".$search;
			elseif ($stype==2)
			$sql.="roomtype.roomtype='".$search."'";
		}
		if($search && $status) $sql .= " and ";
		if($status) $sql .= "rooms.status = '".strip_specials($status)."'";
		$sql .= " Order By rooms.roomno Asc, booking.checkoutdate ASC";
//		print $sql ."<br/>";
		$stmt = $conn->prepare($sql);
		$results = $stmt->execute();
		while($row = $stmt->fetch()) {
			//		print "found ".$row['roomid'] ." - ".$row['roomno']."<br/>\n";
			$status=$row['status'];
			$rooms[$row['roomid']]['roomno'] = $row['roomno'];
			$rooms[$row['roomid']]['roomname'] = $row['roomname'];
			$rooms[$row['roomid']]['guestid'] = $row['guestid'];		
			$rooms[$row['roomid']]['guest'] = $row['guest'];
			$rooms[$row['roomid']]['checkin_date'] = $row['checkindate'];
			$rooms[$row['roomid']]['checkout_date'] = $row['checkoutdate'];
			$rooms[$row['roomid']]['nights'] = $row['nights'];
			$rooms[$row['roomid']]['no_adults'] = $row['no_adults'];
			$rooms[$row['roomid']]['no_child'] = $row['no_child6_12'] + $row['no_child1_5'] + $row['no_babies'];
			$rooms[$row['roomid']]['status'] = $row['status'];
			$rooms[$row['roomid']]['roomtype'] = $row['roomtype'];
			$rooms[$row['roomid']]['roomtypeid'] = $row['roomtypeid'];
			$rooms[$row['roomid']]['ratecode'] = $row['ratecode'];
			
			if ($status==VACANT || $status==LOCKED)
			{
				//if room is vacant or locked guest details are not shown
				$rooms[$row['roomid']]['guestid'] = "";		
				$rooms[$row['roomid']]['guest'] = "";
				$rooms[$row['roomid']]['checkin_date'] = "";
				$rooms[$row['roomid']]['checkout_date'] = "";
				$rooms[$row['roomid']]['nights'] = "";
				$rooms[$row['roomid']]['no_adults'] = "";
				$rooms[$row['roomid']]['no_child'] = "";
				
			}
		}
	}
	return sizeof($rooms);
}
/**
 * Retrieve the hotel settings
 * @ingroup ADMIN_MANAGEMENT
 * @param $hotel [in/out] Hotel name
 * @param $altname [in/out] Altername for the hotel
 * @param $company [in/out] company for the hotel
 * @param $register [in/out] Business registration number
 * @param $ebridgeid [in/out] e-Bridge ID
 * @param $tax1 [in/out] Tax ID
 * @param $tax2 [in/out] Tax ID
 * @param $phone [in/out] Telephone number
 * @param $fax [in/out] Fax number
 * @param $IM [in/out] Instant messenger ID
 * @param $street [in/out] Street address
 * @param $city [in/out] City name
 * @param $citycode [in/out] 3 Letter city code
 * @param $state [in/out] State or province
 * @param $postcode [in/out] Postcode
 * @param $countrycode [in/out] 2 letter countrycode
 * @param $country [in/out] Country name
 * @param $logo [in/out] html reference for the logo
 * @param $latitude [in/out] latitude ddd.mmm.ss.ss[NS]
 * @param $longitude [in/out] longitude ddd.mmm.ss.ss[EW]
 * @param $language [in/out] language code eg en en-us ko
 * @param $email [in/out] email address
 * @param $web [in/out] external website address
 * @param $ota [in/out] OTA out bound connector
 * @param $chaincode [in/out] list of chain codes.
 */
function Get_HotelSettings(&$hotel, &$altname, &$company, &$register,
		&$ebridgeid, &$tax1, &$tax2, &$phone, &$fax, &$IM, &$street,
		&$city, &$citycode, &$state, &$postcode, &$countrycode, &$country,
		&$logo, &$latitude, &$longitude, &$language, &$email, &$web, &$ota, &$chaincode) {
	global $conn;

	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="Select `HotelName`,`AltHotelName`,`CompanyName`,`Street`,`State`,`CityCode`,`City`,`Country`,`CountryCode`,`PostCode`,`Telephone`,`Fax`,`Email`,`Web`,`Registration`,`TaxID1`,`TaxID2`,`OTA_URL`,`lang`,`LogoFileURL`,`ChainCode`,`Latitude`,`Longitude`,`eBridgeID`,`IM` from `hotelsetup`";
	$stmt=$conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$hotel=$row['HotelName'];		
	$altname=$row['AltHotelName'];		
	$company=$row['CompanyName'];		
	$register=$row['Registration'];		
	$ebridgeid=$row['eBridgeID'];		
	$tax1=$row['TaxID1'];		
	$tax2=$row['TaxID2'];		
	$phone=$row['Telephone'];		
	$fax=$row['Fax'];		
	$IM=$row['IM'];		
	$street=$row['Street'];		
	$city=$row['City'];		
	$citycode=$row['CityCode'];		
	$state=$row['State'];		
	$postcode=$row['PostCode'];		
	$countrycode=$row['CountryCode'];		
	$country=$row['Country'];		
	$logo=$row['LogoFileURL'];		
	$latitude=$row['Latitude'];		
	$longitude=$row['Longitude'];		
	$language=$row['lang'];		
	$email=$row['Email'];
	$web=$row['Web'];		
	$ota=$row['OTA_URL'];		
	$chaincode=$row['ChainCode'];		
	return;
}

/** 
 * Retrieve hotel setup details of a given Hotel
 * @param $profile [in/out] Output array
 * @note output array in form: <br/>
 * $profile['HotelName'] <br/>
 * $profile['AltHotelName']<br/>	
 * $profile['CompanyName']<br/>		
 * $profile['Registration']<br/>		
 * $profile['eBridgeID']<br/>		
 * $profile['TaxID1']<br/>		
 * $profile['TaxID2']<br/>		
 * $profile['Telephone']<br/>		
 * $profile['Fax']<br/>		
 * $profile['IM']<br/>	
 * $profile['Street']<br/>		
 * $profile['City']<br/>	
 * $profile['CityCode']<br/>		
 * $profile['State']<br/>		
 * $profile['PostCode']<br/>		
 * $profile['CountryCode']<br/>	
 * $profile['Country']<br/>		
 * $profile['LogoFileURL']<br/>		
 * $profile['Latitude']<br/>		
 * $profile['Longitude']<br/>		
 * $profile['lang']<br/>		
 * $profile['Email']<br/>
 * $profile['Web']<br/>		
 * $profile['OTA_URL']<br/>		
 * $profile['ChainCode']<br/>
 * @return size of hotel setup array
 */
function Get_HotelSetup(&$profile) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "select * from hotelsetup";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$profile= array();
	if($results) {		
		$row = $stmt->fetch();
		$profile['HotelName'] = $row['HotelName'];
		$profile['AltHotelName']=$row['AltHotelName'];		
		$profile['CompanyName']=$row['CompanyName'];		
		$profile['Registration']=$row['Registration'];		
		$profile['eBridgeID']=$row['eBridgeID'];		
		$profile['TaxID1']=$row['TaxID1'];		
		$profile['TaxID2']=$row['TaxID2'];		
		$profile['Telephone']=$row['Telephone'];		
		$profile['Fax']=$row['Fax'];		
		$profile['IM']=$row['IM'];		
		$profile['Street']=$row['Street'];		
		$profile['City']=$row['City'];		
		$profile['CityCode']=$row['CityCode'];		
		$profile['State']=$row['State'];		
		$profile['PostCode']=$row['PostCode'];		
		$profile['CountryCode']=$row['CountryCode'];		
		$profile['Country']=$row['Country'];		
		$profile['LogoFileURL']=$row['LogoFileURL'];		
		$profile['Latitude']=$row['Latitude'];		
		$profile['Longitude']=$row['Longitude'];		
		$profile['lang']=$row['lang'];		
		$profile['Email']=$row['Email'];
		$profile['Web']=$row['Web'];		
		$profile['OTA_URL']=$row['OTA_URL'];		
		$profile['ChainCode']=$row['ChainCode'];
	}
	$stmt =NULL;
	//print_r($profile);
	return sizeof($profile);
	
}

/**
 * Get the hotel galleries
 * @ingroup ADMIN_MANAGEMENT
 * @param $gallery [in/out] Output array
 * @param $pg [in] 0 for gallery page 1 for promo page, -1 for all
 * @param $img [in] 0 for image 1 for video, if pg = -1, this arg ignored
 * @note output array is indexed and each element has the following members
 * idx is from 0 to number or gallery images<br/>
 * $gallery[&lt;idx&gt;]['PicID'] - Pic ID<br/>
 * $gallery[&lt;$idx&gt;]['Title'] - Title of pic<br/>
 * $gallery[&lt;$idx&gt;]['Description'] - Description of pic<br/>
 * $gallery[&lt;$idx&gt;]['URL'] - URL of pic<br/>
 * @return number of pictures in gallery
 */
function get_hotelgallery(&$gallery, $pg = 0, $img = 0) {
	global $conn;
	if(! $conn) $conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return "";
	
	$sql="select PicID, Title, Description, page, imgtype, URL from hotelgallery ";
	if($pg >=  0) {
		$sql .= "where page =".$pg." and imgtype=".$img;
	}
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	
	$gallery = array();
	$idx=0;
	while($row = $stmt->fetch()) {
		$gallery[$idx]['PicID'] = $row['PicID'];
		$gallery[$idx]['Title'] = $row['Title'];
		$gallery[$idx]['Description'] = $row['Description'];
		$gallery[$idx]['URL'] = $row['URL'];
		$gallery[$idx]['page'] = $row['page'];
		$gallery[$idx]['imgtype'] = $row['imgtype'];

		$idx++;
	}
	//echo $sql;
	return sizeof($gallery);
}
/**
 * Get the default currency - from the country select
 * 
 * @ingroup ADMIN_MANAGEMENT
 *
 * @return currencycode
 */
function get_defaultcurrencycode() {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return "";
	$sql="select currency from countries, hotelsetup where countries.countrycode=hotelsetup.CountryCode";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$currency=$row['currency'];		
	return $currency;
}
/**
 * Retrieve the country name from the supplied country code
 * @ingroup ADMIN_MANAGEMENT
 * @param $countrycode [in] Country code
 * 
 * @return country name string 
 */
function Get_Country($countrycode){
	global $conn;
	if(! $countrycode) {
		return "";
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="Select country from countries where countrycode='".strip_specials($countrycode)."'";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$country=$row['country'];		
	return $country;
}
/**
 * Retrieve the Currency from the supplied country code
 * @ingroup ADMIN_MANAGEMENT
 * @param $countrycode [in] Country code
 * @return currency
 */
function Get_Currency_by_Countrycode($countrycode){
	global $conn;
	if(! $countrycode) {
		return "";
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="Select currency from countries where countrycode='".strip_specials($countrycode)."'";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$currency = $row['currency'];		
	return $currency;
}
/**
 * Save the currency for a country to the databse
 * @ingroup ADMIN_MANAGEMENT
 * @param $countrycode [in] Country code
 * @param $currency [in] Currency
 */
function Save_Currency($countrycode,$currency){
	global $conn;
	if(!$countrycode) {
		return 0;
	}
	if(!$currency) {
		return 0;
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="UPDATE countries SET currency='".strip_specials($currency)."' where countrycode='".strip_specials($countrycode)."'";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
}
/**
 * Retrieve the all currencies from the country table
 * @param $currencies [in/out] Currency Array
 * @return number of currencies
 */
function get_CurrencyList(&$currencies){
	global $conn;
	
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="SELECT currency FROM countries WHERE currency !='' ";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$idx=0;
	while ($row = $stmt->fetch()){
		$currencies[$idx] = $row['currency'];
		$idx++;	
	}	
	$stmt =NULL;
	return sizeof($currencies);
}

/**
 * This function generates option data to populate html form select from the database<br>
 * @ingroup FORM_MANAGEMENT
 * @param $checkindate [in] The check in date for the room
 * @param $checkoutdate [in] The check out date for the room
 * @param $selected [in] The currently selected item in the form.
 * @note Generate HTML output directly in web page.
 */
function populate_roomselect($checkindate, $checkoutdate, $selected){
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	list($bstart, $rest) = preg_split('/ /', $checkindate);
	list($day, $month, $year) = preg_split('/\//', $bstart);
	$checkindate = $year."-".$month."-".$day;
	list($bstart, $rest) = preg_split('/ /', $checkoutdate);
	list($day, $month, $year) = preg_split('/\//', $bstart);
	$checkoutdate = $year."-".$month."-".$day;

	$sql = "SELECT DISTINCT rooms.roomid AS roomid, rooms.roomno AS roomno, rooms.roomname as roomname FROM rooms ";
	$sql .= "LEFT JOIN booking ON booking.roomid = rooms.roomid ";
	$sql .= "WHERE (rooms.status='V' OR ";
	$sql .= "  (rooms.status='B' AND rooms.roomid = booking.roomid AND (DATE(booking.checkoutdate) < DATE('".$checkindate."') ";
	$sql .= "  OR DATE(booking.checkindate) >= DATE('". $checkoutdate."')) AND (booking.book_status = ".BOOK_CHECKEDIN." or "; 
	$sql .= "  booking.book_status = ".BOOK_REGISTERED."))) and ";
	$sql .= "  rooms.roomid NOT IN ( SELECT reservation_details.roomid FROM reservation, reservation_details ";
	$sql .= "         WHERE reservation_details.roomid > 0 AND reservation.reservation_id = reservation_details.reservation_id and ";
	$sql .= "        reservation.status = ".RES_ACTIVE." AND ( (DATE(reservation.checkindate) >= DATE('".$checkindate."') AND 
DATE(reservation.checkindate) < DATE('".$checkoutdate."')) OR (DATE(reservation.checkoutdate) >= DATE('".$checkindate."') AND 
DATE(reservation.checkoutdate) < DATE('".$checkoutdate."')) OR (DATE(reservation.checkindate) < DATE('".$checkindate."') AND DATE(reservation.checkoutdate) > DATE('".$checkoutdate."')) ) ) ";

	//echo $sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	while ($row = $stmt->fetch()){
		$ISSelected=($row['roomid']==$selected) ? " selected='selected'" : "";
		if($row['roomname']) {
			$value = $row['roomname'];
		} else { 
			$value = $row['roomno'];
		}
		echo "<option value='" . $row['roomid'] ."' title='".$value."' ". $ISSelected . ">";
		echo $value;
		echo "</option>";
	}
}
/**
 * This function generates option data to populate html form select from the database<br>
 * @ingroup FORM_MANAGEMENT
 * use constants <b>HOST</b>,<b>USER</b>,<b>PASS</b>,<b>DB</b>,<b>PORT</b> to connect to the database<br>
 * @param $table [in] The source table
 * @param $fields_id [in] Field to use as the ID
 * @param $fields_value [in] field name to use as the value, may be comma separated list
 * @param $selected [in] The currently selected item in the form.
 * @param $criteria [in] special criteria for selection
 * @note Generate HTML output directly in web page.
 */
function populate_select($table,$fields_id,$fields_value,$selected, $criteria){
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	// remove any extra spaces from the fields value.
	$fields_value = str_replace(' ', '', $fields_value);
	$sql="Select ".$fields_id;
	if($fields_id <> $fields_value) {
		$sql .= ",".$fields_value;
	}
	$sql .= " From $table ";
	if($criteria) {
		$sql .= "where ".$criteria;
	}
	$sql .= " Order By $fields_value";
	//echo $sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	while ($row = $stmt->fetch()){
		$ISSelected=($row[$fields_id]==$selected) ? " selected='selected'" : "";
		//$ISSelected=($row[$fields_id]==$selected) ? " selected" : "";		
		$fval = "";
		// if the comma , is found in the fields_value then split them apart and print 1 at a time
		if(strstr($fields_value,',')) {
			foreach(preg_split('/,/',$fields_value) as $val) {
				$fval .= $row[$val]. " ";
			}
		} else {
			$fval = $row[$fields_value];
		}

		echo "<option value='" . $row[$fields_id] ."' title='".$fval."' ". $ISSelected . ">";
		echo $fval;
		echo "</option>";
		//($row->$fields_id==$selected) ? 'selected' : '';
	}
}
/**
 * Get the language set in the Hotel settings.
 * @ingroup ADMIN_MANAGEMENT
 *
 * @return language - default is 'en-us'
 */
function get_language() {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="select lang from hotelsetup limit 1";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$row = $stmt->fetch();
	$lang = $row['lang'];
	if(!$lang) $lang = DEFAULT_LANG;
	return $lang;
}

/**
 * Get the list of country codes and country names from the database.
 * @param $countries [in/out] Array for result
 * @return count of entries in array
 * @note 
 * $countries[&lt;CN&gt;] = &lt;Name&gt;
 * @return number of countries
 */
function getCountryCodes(&$countries) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "select distinct CN, Country from Cities order by CN";
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$countries = array();

	while($trrow = $stmt->fetch()) {
		$countries[$trrow['CN']] = $trrow['Country'];

	}
	$stmt = null;
//	disconnect_XO_db($xodb);
	
	//echo $qry;
	
	return sizeof($countries);
}

/** 
 * Retrieve the list of holidays for a specific country code
 * @param $cn [in] 2 letter iso country code
 * @param $hols [in/out] list of holiday dates
 * @note 
 * $countries[&lt;CN&gt;] = &lt;Name&gt;
 * @return number of holidays
 */
function getHolidaysByCountryCode($cn, &$hols) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "select HolidayID, Description, CountryCode, DATE_FORMAT(Holiday,'%d/%m/%Y') as HDate from holidays";
	$qry .= " where CountryCode='".$cn."'";
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$hols = array();

	while($trrow = $stmt->fetch()) {
		$hols[$trrow['HolidayID']]['Holiday'] = $trrow['HDate'];
		$hols[$trrow['HolidayID']]['Description'] = $trrow['Description'];
		$hols[$trrow['HolidayID']]['CountryCode'] = $trrow['CountryCode'];
	}
	$stmt = null;
	return sizeof($hols);
}

/**
 * Add a holiday into the database
 * @param $countrycode [in] 2 letter ISO country code
 * @param $hol [in] Date of holiday
 * @param $desc [in] Description of holiday
 * @return 1 success 0 fail
 */
function addHoliday($countrycode,$hol,$desc) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "insert into holidays (Description, CountryCode, Holiday) values (";
		$qry.= "'".$desc."',";
		$qry.= "'".$countrycode."',";
		$qry.= "STR_TO_DATE('".$hol."', '%d/%m/%Y')";
		
		
		$qry.=")";
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
//	print $qry;
//	add_LogEntry($conn,$uid,'',$qry);
	$stmt = null;
	return 1;
}

/**
 * Delete a specific holiday
 * @param $hid [in] Holiday ID
 * @return 1 success 0 fail
 */
function delHolidayByID($hid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	
	$qry = "delete from Holidays where HolidayID =". $hid;
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
//	print $qry;
//	add_LogEntry($conn,$uid,'',$qry);
	$stmt = null;
	return 1;

}

/**
 * get the reports of the hotel reservation for the given date
 * @param $start [in] Start date of the report
 * @param $end [in] End date of the report
 * @param $status [in] Status of the report
 * @return reservation list from the query date
 */
function getReservationReport($start,$end,$status){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT booking.book_id,advprofile.profileid,booking.book_status,bills.billno, CONCAT_WS(' ',advprofile.firstname,advprofile.middlename,advprofile.lastname) AS guest, IF(booking.checkedin_date=booking.checkedout_date, '',booking.checkedout_date) AS checkout ,booking.checkedin_date
	FROM advprofile
	INNER JOIN booking ON booking.guestid =advprofile.profileid 
	INNER JOIN bills ON booking.book_id=bills.book_id
	WHERE booking.checkedin_date >= '".$start."' AND booking.checkedout_date <= '".$end."' ";
	if($status){
		$qry.="AND booking.book_status = ".$status;
	}
	
//	echo $qry;
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$res = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$res[$i]['guest'] = $trrow['guest'];
		$res[$i]['checkindate'] = $trrow['checkedin_date'];
		$res[$i]['checkoutdate'] = $trrow['checkout'];
		$res[$i]['book_status'] = $trrow['book_status'];
		$res[$i]['billno'] = $trrow['billno'];
		$i++;
	}
	$stmt = null;
	return $res;
}


/**
 * Get years that has holiday dates 
 * @return list of years with holiday
 */
function getYearHoliday(){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT DISTINCT YEAR(Holiday) AS years FROM holidays ORDER BY years ASC";
	
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$yrs = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$yrs[$i]= $trrow['years'];
		$i++;
	}
	$stmt = null;
	return $yrs;
}

/**
 * Get list of holidays for year selected 
 * @param $year [in] Year of holidays
 * @return list of holidays for year
 */
function getHoliday_by_year($year){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT HolidayID, Description, Holiday AS days FROM holidays WHERE YEAR(Holiday) = '".$year."'";
	
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$holidays = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$holidays[$i]['Description']= $trrow['Description'];
		$holidays[$i]['days']= $trrow['days'];
		$i++;
	}
	$stmt = null;
	return $holidays;
}

/**
 * get the reports of the hotel online reservation 
 * @param $start [in] Start date of the report
 * @param $end [in] End date of the report
 * @return reservation list 
 */
function getOnlineReservationReport($start,$end){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT CONCAT_WS('',advprofile.firstname,advprofile.middlename,advprofile.lastname) AS guest, IF(booking.checkedin_date=booking.checkedout_date, booking.checkedout_date,'') AS checkout,
	booking.checkedin_date,bills.billno,reservation.status, reservation.reserve_time,reservation.voucher_no
	FROM advprofile 
	INNER JOIN booking ON booking.guestid =advprofile.profileid 
	INNER JOIN bills ON booking.book_id=bills.book_id
	INNER JOIN reservation ON reservation.reservation_id=booking.reservation_id
	WHERE reservation.src = 'O' AND reservation.reserve_time >= '".$start."' AND reservation.reserve_time <= '".$end."'";
	
//	echo $qry;
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$res = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$res[$i]['guest'] = $trrow['guest'];
		$res[$i]['billno'] = $trrow['billno'];
		$res[$i]['voucher_no'] = $trrow['voucher_no'];
		$res[$i]['reserve_time'] = $trrow['reserve_time'];
		$res[$i]['checkindate'] = $trrow['checkedin_date'];
		$res[$i]['checkoutdate'] = $trrow['checkout'];
		$res[$i]['reservation_status'] = $trrow['status'];
		$i++;
	}
	$stmt = null;
	return $res;
}

/**
 * get the reports of the hotel room status  
 * @param $start [in] Start date of the report
 * @param $end [in] End date of the report
 * @return room status list 
 */
function get_roomstatus($start, $end){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$dateqry ="";
	$openbracket = "(";	
	$sqland = "";
	$qry = "SELECT rooms.roomid, rooms.roomno, rooms.status FROM rooms WHERE rooms.roomid NOT IN (SELECT reservation.roomid FROM reservation WHERE ";
	for ($i = strtotime($start); $i < strtotime($end); $i += 86400) {
		
		$dateqry .= $openbracket.$sqland." COALESCE('".date('Y/m/d', $i)."' BETWEEN DATE(reservation.checkindate) AND DATE(reservation.checkoutdate), TRUE) ";
		if($i==strtotime($start)){
			$openbracket = "";
			$sqland = " or ";
		}
	}
	if($dateqry){
		$dateqry .= ")";
	}	
	$qry .= $dateqry.") AND rooms.roomid NOT IN (SELECT booking.roomid FROM booking WHERE  ";
	$dateqry ="";
	$openbracket = "(";	
	$sqland = "";
	for ($i = strtotime($start); $i < strtotime($end); $i += 86400) {
		
		$dateqry .= $openbracket.$sqland." COALESCE('".date('Y/m/d', $i)."' BETWEEN DATE(booking.checkindate) AND DATE(booking.checkoutdate), TRUE) ";
		if($i==strtotime($start)){
			$openbracket = "";
			$sqland = " or ";
		}
	}	
	if($dateqry){
		$dateqry .= ")";
	}
	$qry .= $dateqry.")";
	
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$res = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$res[$i]['roomid'] = $trrow['roomid'];
		$res[$i]['roomno'] = $trrow['roomno'];
		$res[$i]['status'] = $trrow['status'];
		$i++;
	}
	$stmt = null;
	return $res;
	
}

/**
 * get the reports of the receipt created in the system 
 * @param $start [in] Start date of the report
 * @param $end [in] End date of the report
 * @param $sysdate [in] 0 use receipt date, 1 use add date
 * @param $fop [in] form of payment @see FOP_DEF_TAGS
 * @param $userid [in] transaction from specific userid
 * @return receipt list 
 */
function getReceiptReport($start,$end, $sysdate = 0, $fop = 0, $userid = 0){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT receipts.receipt_id,receipts.rcpt_no, bills.billno, CONCAT_WS('',users.fname,users.sname) AS created_by, receipts.amount,receipts.add_date,receipts.tgtCurrency, receipts.fop, receipts.status as rstatus, bills.status as bstatus, receipts.rcpt_date, receipts.book_id, receipts.reservation_id, bills.guestid,receipts.cctype, receipts.CCnum,receipts.srcCurrency,receipts.exrate, receipts.name
	FROM receipts
	INNER JOIN bills ON receipts.bill_id = bills.bill_id
	INNER JOIN users ON receipts.add_by = users.userid";
	if($sysdate) {
		$qry .= " WHERE receipts.add_date >= '".$start."' AND receipts.add_date <= '".$end."'";
	} else {
		$qry .= " WHERE receipts.rcpt_date >= '".$start."' AND receipts.rcpt_date <= '".$end."'";	
	}
	if($fop && $fop != FOP_VOUCHER) {
		$qry .= " AND receipts.fop=".$fop;
	}
	if($fop && $fop == FOP_VOUCHER) {
		$qry .= " AND (receipts.fop=".FOP_VOUCER." OR receipts.fop =".FOP_PP.")";
	}
	if($userid) {
		$qry .= " AND receipts.add_by=".$userid;
	}
		$qry .= " AND bills.flags=0";
//	echo $qry;
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$res = array();
	$curr = array();
	$i = 0;
	while($trrow = $stmt->fetch()) {
		$res[$i]['receipt_id'] = $trrow['receipt_id'];
		$res[$i]['rcpt_no'] = $trrow['rcpt_no'];
		$res[$i]['billno'] = $trrow['billno'];
		$res[$i]['created_by'] = $trrow['created_by'];
		$res[$i]['amount'] = $trrow['amount'];
		$res[$i]['rcpt_date'] = $trrow['rcpt_date'];
		$res[$i]['add_date'] = $trrow['add_date'];
		$res[$i]['tgtCurrency'] = $trrow['tgtCurrency'];
		$res[$i]['srcCurrency'] = $trrow['srcCurrency'];
		$res[$i]['exrate'] = $trrow['exrate'];
		$res[$i]['fop'] = $trrow['fop'];
		$res[$i]['rstatus'] = $trrow['rstatus'];
		$res[$i]['bstatus'] = $trrow['bstatus'];
		$res[$i]['book_id'] = $trrow['book_id'];
		$res[$i]['reservation_id'] = $trrow['reservation_id'];
		$res[$i]['guestid'] = $trrow['guestid'];
		$res[$i]['cctype'] = $trrow['cctype'];
		$res[$i]['CCnum'] = $trrow['CCnum'];
		$res[$i]['name'] = $trrow['name'];
		
		if(!in_array($trrow['tgtCurrency'], $curr)){
			$curr[$i] = $trrow['tgtCurrency'];
		}
		$i++;
	}
	
		
	$res['cur'] =$curr;
	
//	$stmt = null;
	return $res;
}


/**
 * get the usability reports of each room type created in the system 
 * @param $start [in] Start date of the report
 * @param $end [in] End date of the report
 * @return list of room types
 */
function getRoomUsabilityReport($start,$end){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$qry = "SELECT booking.book_id, booking.roomid, roomtype.roomtype,roomtype.roomtypeid,booking.checkedin_date,booking.checkedout_date FROM booking
		INNER JOIN roomtype ON roomtype.roomtypeid=booking.roomtypeid  
		WHERE booking.checkedin_date >= '".$start."' AND booking.checkedout_date <= '".$end."'";
	
//	echo $qry;
	$stmt = $conn->prepare( $qry);
	$stmt->execute();
	$res = array();
	$curr = array();
	$i = 0;
	$k = 1;
	while($trrow = $stmt->fetch()) {
		$res[$i]['book_id'] = $trrow['book_id'];
		$res[$i]['roomid'] = $trrow['roomid'];
		$res[$i]['roomtype'] = $trrow['roomtype'];
		$res[$i]['roomtypeid'] = $trrow['roomtypeid'];
		$i++;
	}
//	$stmt = null;
	return $res;
}

/** 
 * Get the agent name by agent id
 * @param $agentid [in] agent ID
 * @return "Agent" if not set or agent name
 */
function get_agentname_byid($agentid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$res = "Agent";
	if(!$conn) return 0;
	$sql = "select agentname from agents where agentid = ".$agentid;
//	echo $sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$res = $row['agentname'];
	}
	return $res;
}
/** 
 * Retrieve list of travel agents 
 * @ingroup AGENT_MANAGEMENT
 * @param $search [in] search values
 * @param $stype [in] 1 for iata 2 for name
 * @param $agent [in/out] Output array
 * 
 * @return number of elements in agent
 * @note
 * agent array has form:<br/>
 * $agent['agentid']<br/>
 * $agent['name']<br/>
 * $agent['iata']<br/>
 * $agent['contact']<br/>
 * $agent['phone']<br/>
 * $agent['fax']<br/>
 * $agent['email']<br/>
 * $agent['billing']<br/>
 * $agent['town']<br/>
 * $agent['postcode']<br/>
 * $agent['street']<br/>
 * $agent['building']<br/>
 * $agent['country']<br/>
 * $agent['eBridgeID']<br/>
 * $agent['IM']<br/>
 */
function get_agentlist($search, $stype ,&$agent) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$search = strip_specials($search);
	$sql = "select agentid, agentname,agents_ac_no,contact_person,telephone,fax,email,billing_address,town,postal_code,road_street,building,eBridgeID, country, IM from agents";
	if($search && $stype == 1) {
		$sql .= " where agents_ac_no like '%".$search."%'";
	}
	if($search && $stype == 2) {
		$sql .= " where agentname like '%".$search."%'";
	}
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$agent= array();
	if($results) {
		while($row = $stmt->fetch()) {
			$agent[$row['agentid']]['agentid'] = $row['agentid'];
			$agent[$row['agentid']]['name'] = $row['agentname'];
			$agent[$row['agentid']]['iata'] = $row['agents_ac_no'];
			$agent[$row['agentid']]['contact'] = $row['contact_person'];
			$agent[$row['agentid']]['phone'] = $row['telephone'];
			$agent[$row['agentid']]['fax'] = $row['fax'];
			$agent[$row['agentid']]['email'] = $row['email'];
			$agent[$row['agentid']]['billing'] = $row['billing_address'];
			$agent[$row['agentid']]['town'] = $row['town'];
			$agent[$row['agentid']]['postcode'] = $row['postal_code'];
			$agent[$row['agentid']]['street'] = $row['road_street'];
			$agent[$row['agentid']]['building'] = $row['building'];
			$agent[$row['agentid']]['eBridgeID'] = $row['eBridgeID'];
			$agent[$row['agentid']]['IM'] = $row['IM'];
			$agent[$row['agentid']]['country'] = $row['country'];
		}
	}
	$stmt =NULL;
	return sizeof($agent);
	
}
/** 
 * Retrieve details about a specific travel agency
 * @ingroup AGENT_MANAGEMENT
 * @param $agentid [in] The agency id.
 * @param $agent [in/out] Output array
 *
 * @return number of elements in agent
 * @note
 * agent array has form:<br/>
 * $agent['agentid']<br/>
 * $agent['name']<br/>
 * $agent['iata']<br/>
 * $agent['contact']<br/>
 * $agent['phone']<br/>
 * $agent['fax']<br/>
 * $agent['email']<br/>
 * $agent['billing']<br/>
 * $agent['town']<br/>
 * $agent['postcode']<br/>
 * $agent['street']<br/>
 * $agent['building']<br/>
 * $agent['country']<br/>
 * $agent['eBridgeID']<br/>
 * $agent['IM']<br/>
 */
function get_agent($agentid, &$agent) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "select agentname,agents_ac_no,contact_person,telephone,fax,email,billing_address,town,postal_code,road_street,building,eBridgeID, country,IM from agents where agentid=".strip_specials($agentid);
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$agent= array();
	if($results) {
		$row = $stmt->fetch();
		$agent['agentid'] = $agentid;
		$agent['name'] = $row['agentname'];
		$agent['iata'] = $row['agents_ac_no'];
		$agent['contact'] = $row['contact_person'];
		$agent['phone'] = $row['telephone'];
		$agent['fax'] = $row['fax'];
		$agent['email'] = $row['email'];
		$agent['billing'] = $row['billing_address'];
		$agent['town'] = $row['town'];
		$agent['postcode'] = $row['postal_code'];
		$agent['street'] = $row['road_street'];
		$agent['building'] = $row['building'];
		$agent['eBridgeID'] = $row['eBridgeID'];
		$agent['IM'] = $row['IM'];
		$agent['country'] = $row['country'];
	}
	$stmt =NULL;
	return sizeof($agent);
	
}

/** 
 * Get an agent set booking reference
 * @param $resid [in] Reservation id
 * @param $agentid [in] Agent ID
 * @param $bookref [in/out] Booking reference value
 * @param $bookref_id [in/out] Booking reference id
 * @return 1 success 0 fail
 * @note on fail, bookref_id and bookref will be blank.
 */
function get_agent_bookingref($resid = 0, $agentid = 0, &$bookref, &$bookref_id) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$bookref = "";
	$bookref_id = 0;
	$sql = "select agent_bookrefid,reservation_id,agentid,refno from agent_bookref where agentid=".strip_specials($agentid)." and reservation_id=".strip_specials($resid);
//	echo $sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$bookref = $row['refno'];
		$bookref_id = $row['agent_bookrefid'];
	}
	$stmt =NULL;
	
	return $results;
}

/**
 * Modify the agent booking engine
 * @param $bookref_id [in] Booking Reference ID 
 * @param $resid [in] Reservation ID
 * @param $agentid [in] Agent ID
 * @param $bookref [in] Agent Booking Reference
 */
function modify_agent_bookingref($bookref_id = 0, $resid = 0, $agentid = 0, $bookref = "") {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$bookref_id) {
		$sql="INSERT INTO agent_bookref (reservation_id, agentid, refno) values (";
		$sql.= $resid.",";
		$sql.= $agentid.",";
		$sql.= "'".$bookref."')";
	} else {
		$sql="update agent_bookref set";
		$sql.=" reservation_id=".$resid.", ";
		$sql.=" agentid=".$agentid.", ";
		$sql.=" refno='".$bookref."' ";
		$sql .= " where agent_bookrefid=".$bookref_id;
	}
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results && !$bookref_id) {
		$bookref_id =$conn->lastInsertId();
	}
	$stmt =NULL;
	return $bookref_id;
}

/**
 * Add/Updated the travel agent into the database.
 * @ingroup AGENT_MANAGEMENT
 * @param $agentid [in] Database id of the agent
 * @param $agentname [in] The agent name
 * @param $agents_ac_no [in] The agent account number
 * @param $contact_person [in] Contact name
 * @param $telephone [in] Telephone number
 * @param $fax [in] fax number
 * @param $email [in] Email address
 * @param $billing_address [in] The billing address
 * @param $town [in] City or town
 * @param $postal_code [in] Post code
 * @param $road_street [in] street address
 * @param $building [in] Building
 * @param $country [in] Country name
 * @param $ebridgeID [in] The e-Bridge ID
 * @param $IM [in] The instant messenger 
 * @return 0 fail or agentid
 */
function modify_agent($agentid, $agentname, $agents_ac_no,
		$contact_person, $telephone, $fax, $email, $billing_address,
		$town, $postal_code, $road_street, $building, $country, $ebridgeID, $IM ) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(empty($postal_code)){
		$postal_code=0;
	}
	if(!$agentid) {
		$sql="INSERT INTO agents (agentname,agents_ac_no,contact_person,telephone,fax,email,billing_address,town,postal_code,road_street,country,eBridgeID,IM,building)
		VALUES(";
		$sql .= "'".strip_specials($agentname)."',";
		$sql .= "'".strip_specials($agents_ac_no)."',";
		$sql .= "'".strip_specials($contact_person)."',";
		$sql .= "'".strip_specials($telephone)."',";
		$sql .= "'".strip_specials($fax)."',";
		$sql .= "'".strip_specials($email)."',";
		$sql .= "'".strip_specials($billing_address)."',";
		$sql .= "'".strip_specials($town)."',";
		$sql .= strip_specials($postal_code).",";
		$sql .= "'".strip_specials($road_street)."',";
		$sql .= "'".strip_specials($country)."',";
		$sql .= "'".strip_specials($ebridgeID)."',";
		$sql .= "'".strip_specials($IM)."',";
		$sql .= "'".strip_specials($building)."')";
	} else {
		$sql="update agents set ";
		$sql .= "agentname='".strip_specials($agentname)."',";
		$sql .= "agents_ac_no='".strip_specials($agents_ac_no)."',";
		$sql .= "contact_person='".strip_specials($contact_person)."',";
		$sql .= "telephone='".strip_specials($telephone)."',";
		$sql .= "fax='".strip_specials($fax)."',";
		$sql .= "email='".strip_specials($email)."',";
		$sql .= "billing_address='".strip_specials($billing_address)."',";
		$sql .= "town='".strip_specials($town)."',";
		$sql .= "postal_code=".strip_specials($postal_code).",";
		$sql .= "road_street='".strip_specials($road_street)."',";
		$sql .= "country='".strip_specials($country)."',";
		$sql .= "eBridgeID='".strip_specials($ebridgeID)."',";
		$sql .= "IM='".strip_specials($IM)."',";
		$sql .= "building='".strip_specials($building)."'";
		$sql .= " where agentid=".$agentid;
	}
		
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results && !$agentid) {
		$agentid =$conn->lastInsertId();
	}
	$stmt =NULL;

	return $agentid;
}
/**
 * Get the allocated amenities for a given room.
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] The roomid being queried
 * @param $allamens [in/out] The list of allocated amenities
 *
 * @return number of elements in allamens.
 */
function get_allocatedroomamenities($roomid, &$allamens) {
	$lang = get_language();
	$allamens = array();

	$ret = get_allocatedroomamenitiesbylang($roomid, $allamens, DEFAULT_LANG);
	get_allocatedroomamenitiesbylang($roomid, $allamens, $lang);
	return sizeof($ret);
}
/**
 * Get the allocated amenities for a given room.
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] The roomid being queried
 * @param $allamens [in/out] The list of allocated amenities
 * @param $lang [in] Language to select
 *
 * @return number of elements in allamens.
 */
function get_allocatedroomamenitiesbylang($roomid, &$allamens, $lang) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql = "select room_amenities.OTA_number, ota_roomamenity.Description FROM room_amenities, ota_roomamenity WHERE room_amenities.OTA_number = ota_roomamenity.OTA_Number AND room_id=".strip_specials($roomid)." AND lang='".strip_specials($lang)."'";
	//	print $sql."<br/>\n";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();

	while ($row=$stmt->fetch()){
		$allamens[$row['OTA_number']] = $row['Description'];
	}

	return sizeof($allamens);
}
/**
 * Get the room amenities list for the selected language.
 * @ingroup ROOM_MANAGEMENT
 *
 * @param $amens [in/out] The amenities list
 * @return number of elements in amens.
 *
 * @note 'en-us' is the default language, this will be loaded first.
 * if the language selected is not a complete list, then english
 * values will be returned.
 * 
 */
function get_roomamenities(&$amens) {
	$lang = get_language();
	$ret = get_OTAroomamenities('en-us', $amens, 1);
	if($lang && $lang <> 'en-us') {
		$ret = get_OTAroomamenities($lang, $amens, 0);
	}
	return $ret;
}
/**
 * Get the OTA room amenities list for the selected language.
 * @ingroup ROOM_MANAGEMENT
 *
 * @param $lang [in] The language code
 * @param $amens [in/out] The amenities list
 * @param $reset [in] Reset the amens list to empty before populating data else overwrite it.
 * 
 * @return number of elements in amens.
 * 
 */
function get_OTAroomamenities($lang, &$amens, $reset) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$lang) $lang = 'en-us';
	if($reset) $amens = array();
	$lang = strip_specials($lang);
	$sql = "select OTA_Number, Description from ota_roomamenity where lang='".strip_specials($lang)."'";
	$stmt =$conn->prepare($sql);
	$results = $stmt->execute();
	while ($row=$stmt->fetch()){
		$amens[$row['OTA_Number']] = $row['Description'];
	}

	return sizeof($amens);
}
/**
 * Get the OTA room amenities list for the selected roomid.
 * @ingroup ROOM_MANAGEMENT
 *
 * @param $roomid [in] The room id
 * @param $amens [in/out] The amenities list
 * @param $reset [in] Reset the amens list to empty before populating data else overwrite it.
 * 
 * @return number of elements in amens.
 * 
 */
function get_roomamenities_by_roomid($roomid, &$amens, $reset) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$roomid) return 0;
	if($reset) $amens = array();
	$sql = "select rooms.roomid, rooms.roomtypeid, amenity.OTA_number from rooms, room_amenities as amenity where rooms.roomid = '".$roomid."'and amenity.room_id = '".$roomid."'";
	$stmt =$conn->prepare($sql);
	$results = $stmt->execute();
	while ($row=$stmt->fetch()){
		$amens[] = $row['OTA_number'];
	}
	return sizeof($amens);
}
/**
 * Get the OTA room amenities list for the selected roomtype.
 * @ingroup ROOM_MANAGEMENT
 * @param $roomtype [in] The room type
 * @param $amens [in/out] The amenities list
 * @param $reset [in] Reset the amens list to empty before populating data else overwrite it.
 * @return number of elements in amens.
 */
function get_roomamenities_by_roomtype($roomtype, &$amens, $reset) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$roomtype) return 0;
	if($reset) $amens = array();
	$sql = "SELECT DISTINCT amenity.OTA_number FROM rooms, room_amenities AS amenity WHERE rooms.roomtypeid= '".$roomtype."'AND amenity.room_id = rooms.roomid";
	$stmt =$conn->prepare($sql);
	$results = $stmt->execute();
	$i=0;
	while ($row=$stmt->fetch()){
		$amens[$i] = $row['OTA_number'];
		$i++;
	}
	return $i;
}
/**
 * Delete user from database
 * @param $userid [in] user id
 * @ingroup USER_MANAGEMENT
 * @note uses global variables <i>users</i> to set the result and <i>conn</i> for DB connection.
 * @return true success or false fails
 */
function delete_user($userid){
	global $conn,$users;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="delete from users where userid='".strip_specials($userid)."'";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();

	return $results;
}
/**
 * get the user access permission from the database
 * @param $user [in] user login name
 * @param $access [in/out] Result array of access permissions
 * @ingroup USER_MANAGEMENT
 */
function get_useraccess($user, &$access){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}

	$sql = "select userid,admin,guest,reservation,booking,agents,rooms,billing,rates,lookup,reports,policy from users where loginname='".$user."'";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$row = $stmt->fetch();
	$access = $row;
	return $access;
}
/**
 * get the user name for the given userid
 * @ingroup USER_MANAGEMENT
 * @param $userid [in] user login id
 *
 * @return user name string
 */
function get_username($userid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return "";
	
	$sql = "select loginname from users where userid=".strip_specials($userid);
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['loginname'];
	}
	return "";
}
/**
 * The user data values in database based upon search criteria.
 * @param $search [in] search string userid or partial name
 * @param $stype [in] 0 for userid 1 for name.
 * @param $users [in/out] array for result
 * 
 * @ingroup USER_MANAGEMENT
 * @note global <i>conn</i> for DB connection.
 * @return number of elements in users
 */
function find_user($search, $stype, &$users){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$users = array();
	$search = strip_specials($search);
	$stype = strip_specials($stype);
	$sql="select userid,concat_ws(' ',fname,sname) as user,fname,sname,loginname,pass,phone,mobile,fax,email,countrycode,admin,
		guest,reservation,booking,agents,rooms,billing,rates,lookup,reports,dateregistered
			from users where ";
	if($stype) {
		$sql .= "fname like '%".$search."%' or sname like '%".$search."%' or loginname like '%".$search."%'";
	} else { 
		$sql .= "userid='".$search."'";
	}
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$users['userid'] = $row['userid'];
	$users['user'] = $row['user'];
	$users['fname'] = $row['fname'];
	$users['sname'] = $row['sname'];
	$users['loginname'] = $row['loginname'];
	$users['pass'] = $row['pass'];
	$users['phone'] = $row['phone'];
	$users['mobile'] = $row['mobile'];
	$users['fax'] = $row['fax'];
	$users['email'] = $row['email'];
	$users['countrycode'] = $row['countrycode'];
	$users['admin'] = $row['admin'];
	$users['guest'] = $row['guest'];
	$users['reservation'] = $row['reservation'];
	$users['booking'] = $row['booking'];
	$users['agents'] = $row['agents'];
	$users['rooms'] = $row['rooms'];
	$users['billing'] = $row['billing'];
	$users['rates'] = $row['rates'];
	$users['lookup'] = $row['lookup'];
	$users['reports'] = $row['reports'];
	$users['dateregistered'] = $row['dateregistered'];
	return sizeof($users);
}
/**
 * Sign on to the OTA Hotel Manager<br>
 *check login data by COOKIE<br>
 *@note <i>employee</i> by using SESSION
 * @ingroup USER_MANAGEMENT
 */
function signon(){
	$lang = get_language();
	global $_L;
	echo "<input name='login' type='submit' value='";
	echo !isset($_SESSION['userid']) ? $_L['PR_login'] : $_L['PR_logout'];
	echo "' /><br>";
	//echo "<font color=\"#339999\">Signed in as: " . $_COOKIE['data_login'] . "</font>";
	echo "<font color=\"#339999\">Signed in as: " . "<br/>" .$_SESSION["employee"] . "</font>";
}

/**
 * Display shift detail on main window
 * @note <i>employee</i> by using SESSION
 * @note only valid with shift function and report with e-Bridge membership.
 * @ingroup USER_MANAGEMENT
 */
function shift_times(){
	$lang = get_language();
	global $_L;
	if(isset($_SESSION['userid']) && is_ebridgeCustomer()) {
		//echo "<font color=\"#339999\">Signed in as: " . $_COOKIE['data_login'] . "</font>";
		echo "<font color=\"#339999\">" .shift_active($_SESSION['userid']) . "</font>";
		echo "<a href='reports.php?report=shiftRpt' class='btn'>Shift</a><br/>";
	}
}

/** 
 * check if there is a current shift within the last 24 hours
 * for user, assume no user should be on shift for more than 24 hours.
 * @param $userid [in] user id.
 * @return string of status.
 */
function shift_active($userid) {
	$starttime = shift_starttime($userid);
	if($starttime) {
		echo "<b>Shift started :<br/>".$starttime."</b><br/>";
	}
}
/** 
 * check if there is a current shift within the last 24 hours
 * for user, assume no user should be on shift for more than 24 hours.
 * @param $userid [in] user id.
 * @return start time.
 */
function shift_starttime($userid) {
	global $conn;

	if(!$conn) {
		/* makes connection */
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="select userid, startshift FROM shifts WHERE startshift = endshift AND HOUR(TIMEDIFF(NOW(), startshift)) <= 24 and userid=".strip_specials($userid)." ORDER BY startshift DESC";
//	print $sql."<br/>\n";
	$stmt1 = $conn->prepare($sql);
	$results = $stmt1->execute();
	if($results) {
		// only want the first shift.
		$row = $stmt1->fetch();
		$res = $row['startshift'];
	}
	return $res;
}

/**
 * Insert a new shift start time
 * @param $userid [in] User shift start
 * @param $starttime [in] start time YYYY-MM-DD HH:MI
 */
function shift_start($userid, $starttime) {
	global $conn;
	$res = '';
	if(!$conn) {
		/* makes connection */
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="insert into shifts (userid, startshift, endshift, notes) values (".strip_specials($userid).",'".$starttime."','".$starttime."','')";
//	print $sql."<br/>\n";
	$stmt1 = $conn->prepare($sql);
	$results = $stmt1->execute();
	return $res;
	
}

/**
 * Close off a shift
 * @param $userid [in] User shift start
 * @param $starttime [in] start time YYYY-MM-DD HH:MI
 * @param $endtime [in] end time YYYY-MM-DD HH:MI
 * @param $notes [in] text notes form shift.
 */
function shift_end($userid, $starttime, $endtime, $notes) {
	global $conn;
	$res = '';
	if(!$conn) {
		/* makes connection */
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="update shifts set endshift = '".$endtime."', notes = '".$notes."' where userid=".strip_specials($userid)." and startshift ='".$starttime."'";
//	print $sql."<br/>\n";
	$stmt1 = $conn->prepare($sql);
	$results = $stmt1->execute();
	return $res;

}
/**
 * Get the list of shifts that started in period for a specific user or all users
 * @param $userid [in] user id or 0 if all users
 * @param $startdate [in] date for first start of shift
 * @param $enddate [in] date for last start of shift.
 * @return array of shift details
 */
function shift_lists($userid, $startdate, $enddate) {
	global $conn;

	if(!$conn) {
		/* makes connection */
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="select userid, startshift, endshift, notes FROM shifts WHERE startshift >= '".$startdate."' and startshift <= '".$enddate."' ";
	if($userid > 0)  {
		$sql .= " and userid=".strip_specials($userid)." ORDER BY startshift DESC";
	} else {
		$sql .= " ORDER BY startshift DESC";
	} 
//	print $sql."<br/>\n";
	$stmt1 = $conn->prepare($sql);
	$shifts=array();
	$results = $stmt1->execute();
	if($results) {
		// only want the first shift.
		$lastuserid = "";
		$shiftid = 1;
		while($row = $stmt1->fetch()) {
			if($lastuserid != $row['userid'] ) {
				$shiftid = 1;
				$lastuserid = $row['userid'] ;
			}
			$shifts[$row['userid']][$shiftid]['startshift'] = $row['startshift'];
			$shifts[$row['userid']][$shiftid]['endshift'] = $row['endshift'];
			$shifts[$row['userid']][$shiftid]['notes'] = $row['notes'];
			$shifts[$row['userid']][$shiftid]['userid'] = $row['userid'];
			$shiftid++;
		}
	}
	return $shifts;
}
/**
 *Connect to the database to delete file by the http POST variable ID<br>
 *uses constants <b>HOST</b>,<b>USER</b>,<b>PASS</b>,<b>DB</b>,<b>PORT</b> to connect to the database
 */
function delete_copy() {
	global $conn;

	if(!$conn) {
		/* makes connection */
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	/* Creates SQL statement to retrieve the copies using the releaseID */
	$sql = "DELETE FROM ".$file." WHERE ".$recordid ."=" . strip_specials($_POST['ID']);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();

	$msg[0]="Sorry ERROR in deletion";
	$msg[1]="Record successful DELETED";			
	AddSuccess($results,$conn,$msg);
} 
/**
 * Form validator class
 * @ingroup FORM_MANAGEMENT
 */
class formValidator{
	private $errors=array();
	public function __construct(){}
	
	// validate empty field
	/**
	 * This function use to validate the empty data. 
	 * @param $field [in] validate by http POST variables 
	 * @param $errorMessage [in] validate by http POST variables 
	 * @param $min [in] validate by http POST variables 
	 * @param $max [in] validate by http POST variables 
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateEmpty($field,$errorMessage,$min=1	,$max=32){
		if(!isset($_POST[$field])||trim($_POST[$field])==''||strlen($_POST[$field])<$min||strlen($_POST[$field])>$max){
			$this->errors[]=$errorMessage;
		}
	}

	// validate integer field
	/**
	 * This function use to validate the integer number.
	 * @param $field [in] validate by http POST variables
	 * @param $errorMessage [in] validate by http POST variables
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateInt($field,$errorMessage){
		if(!isset($_POST[$field])||!is_numeric($_POST[$field])||intval($_POST[$field])!=$_POST[$field]){
			$this->errors[]=$errorMessage;
		}
	}

	// validate numeric field
	/**
	 * This function use to validate numbers.
	 * @param $field [in] validate by http POST variables
	 * @param $errorMessage [in] validate by http POST variables
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateNumber($field,$errorMessage){
		if(!isset($_POST[$field])||!is_numeric($_POST[$field])){
			$this->errors[]=$errorMessage;
		}
	}

	// validate if field is within a range
	/**
	 * This function use to validate the range between the input type minimum value and maximum value.
	 * @param $field [in] validate by http POST variables
	 * @param $errorMessage [in] validate by http POST variables
	 * @param $min [in] validate by http POST variables
	 * @param $max [in] validate by http POST variables 
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateRange($field,$errorMessage,$min=1,$max=99){
		if(!isset($_POST[$field])||$_POST[$field]<$min||$_POST[$field]>$max){
			$this->errors[]=$errorMessage;
		}
	}

	// validate alphabetic field
	/**
 	 * This function use to validate the alphabetic data.
	 * @param $field [in] input variables
	 * @param $errorMessage [in] input variables
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateAlphabetic($field,$errorMessage){
		if(!isset($_POST[$field])||!preg_match("/^[a-zA-Z]+$/",$_POST[$field])){
			$this->errors[]=$errorMessage;
		}
	}

	// validate alphanumeric field
	/**
	 * This function use to validate the alphanumeric data
	 * @param $field [in] input variables
	 * @param $errorMessage [in] input variables
	 * @note if out of validation ,show the input variable errorMessage 
	 */
	public function validateAlphanum($field,$errorMessage){
		if(!isset($_POST[$field])||!preg_match("/^[a-zA-Z0-9]+$/",$_POST[$field])){
			$this->errors[]=$errorMessage;
		}
	}

	// validate email - does not work on windows machine
	/**
	 * This function use to validate email by input variables 
	 * @param $field [in] input variables
	 * @param $errorMessage [in] input variables
	 * @note if out of validate email field,show the input variable errorMessage 
	 */
	public function validateEmail($field,$errorMessage){
		if(!isset($_POST[$field])||!preg_match("/.+@.+\..+./",$_POST[$field])||!checkdnsrr(array_pop(explode("@",$_POST[$field])),"MX")){
			$this->errors[]=$errorMessage;
		}
	}
	/**
	 * This function is used to validate if the field is left blank.
	 * @param $field [in] validate by http POST variables
	 * @param $errorMessage [in] validate by http POST variables
	 * if out of validation ,show the input variable errorMessage 
	 */
	public function EmptyCheck($field,$errorMessage){
		if(!isset($_POST[$field])||trim($_POST[$field])==''){
			$this->errors[]=$errorMessage;
		}
	}
	
	/**
	 * This function is used to print error message.
	 * @param $errorMessage [in] input errorMessage	
	 */
	public function addErrormsg($errorMessage){
		$this->errors[]=$errorMessage;	
	}
	// check for errors
	/**
	 * This function use to check errors
	 * @return boolean type true if there is error
	 */
	public function checkErrors(){
		if(count($this->errors)>0){
			return true;
		}
		return false;
	}

	// return errors
	/**
	 * This function use to display error
	 * @return the erroroutput variables
	 */
	public function displayErrors(){
		$errorOutput='<ul>';
		foreach($this->errors as $err){
			$errorOutput.='<li>'.$err.'</li>';
		}
		$errorOutput.='</ul>';
		return $errorOutput;
	}
}
/**
 * The guests data values in the database based upon search criteria.
 * @ingroup GUEST_MANAGEMENT
 * @param $id [in] array to hold the id data.
 * @param $guest [in] array to hold the guest data.
 */
function get_guest($id, &$guest) {
	return findguestbyid($id, $guest);
}
/**
 * The guests data values in the database based upon search criteria.<br>
 * @ingroup GUEST_MANAGEMENT
 * @param $id [in] search string with guest id.
 * @param $guest [in] array to hold the guest data.
 * @return number of elements in guest.
 * guest structure <br/>
 * $guest['guest'] = firstname middlename lastname
 * $guest['guestid']
 * $guest['lastname']
 * $guest['firstname']
 * $guest['middlename']
 * $guest['salutation'] = salutation string, Mr, Ms, Mrs etc
 * $guest['salutid'] = salutation id
 * $guest['pp_no'] = passport number
 * $guest['idno']
 * $guest['countrycode']
 * $guest['address'] - Address
 * $guest['town']
 * $guest['postal_code']
 * $guest['phone']
 * $guest['email']
 * $guest['mobilephone']
 * $guest['eBridgeID'] - e-Novate e-Bridge online connector id
 * $guest['IM'] - skype, MSN, yahoo id etc
 */
function findguestbyid($id, &$guest){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) {
		return 0;
	}
	$lang = get_language();
	$guest = array();
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		advfindguestbyid($id, $guest);

	} else {
		//check on wether search is being done on idno/ppno/guestid/guestname
		$sql = "SELECT guests.guestid, guests.lastname, guests.firstname, guests.middlename, guests.salutation, 
				s2.Description AS localsalute, salutation.salute, guests.pp_no, guests.idno, guests.countrycode,
				guests.street_no, guests.street_name, guests.town, guests.postal_code, guests.phone, guests.email, 
				guests.mobilephone, guests.eBridgeID, guests.IM, guests.areacode, guests.nationality FROM guests
	            LEFT JOIN salutation ON guests.salutation = salutation.salute AND salutation.lang = 'en-us' 
	            LEFT JOIN salutation AS s2 ON guests.salutation = s2.salute AND s2.lang = '".$lang."'
	            WHERE guests.guestid=".$id; 
		$stmt1 = $conn->prepare($sql);
		$results = $stmt1->execute();
	//	print $sql.'<br/>';
		if($results) {
			$row = $stmt1->fetch();
			$guest['guest'] = trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']);
			$guest['guestid']=$row['guestid'];
			$guest['lastname']=$row['lastname'];
			$guest['firstname']=$row['firstname'];
			$guest['middlename']=$row['middlename'];
			$guest['salute']=$row['salute'];
			$guest['salutid']=$row['salutation'];
			$guest['salutation']=$row['Description'];
			if($row['localsalute'] ) $guest['salutation']=$row['localsalute'];
			$guest['pp_no']=$row['pp_no'];
			$guest['idno']=$row['idno'];
			$guest['countrycode']=$row['countrycode'];
			$guest['nationality']=$row['nationality'];
			$guest['street_no']=$row['street_no'];
			$guest['street_name']=$row['street_name'];
			$guest['town']=$row['town'];
			$guest['postal_code']=$row['postal_code'];
			$guest['access']=$row['access'];
			$guest['area']=$row['areacode'];
			$guest['phone']=$row['phone'];
			$guest['email']=$row['email'];
			$guest['mobilephone']=$row['mobilephone'];
			$guest['eBridgeID']=$row['eBridgeID'];
			$guest['IM']=$row['IM'];
			$guest['address'] = $guest['street_name'].", ".get_Country($guest['countrycode'])." - ".$guest['postal_code'];
	//		print "e-Bridge".$row['IM']."<br/>\n";
		} else {
	//		print "Result = ".$results."<br/>\n";
		}
	}
	return sizeof($guest);
}

/**
 * Return the guestname for a specific guest
 * @ingroup GUEST_MANAGEMENT
 * @param $guestid [in] Guest name
 * @return guest name in full
 */
function get_guestname($guestid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return "";
	
	if(!$guestid) return "";
	$nm = "";
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		$profile = array();
		get_profile_by_id($guestid, $profile);
		$nm = get_salutation_bySalutationID($profile['salutation'])." ".$profile['firstname'];
		if(!empty($profile['middlename']))
			$nm.= " ".$profile['middlename'];
		$nm.= " ".$profile['lastname'];
	} else {
		//check on wether search is being done on idno/ppno/guestid/guestname
		$sql="select firstname, middlename, lastname from guests where guestid=".strip_specials($guestid);
	//	print $sql."<br/>\n";
		$stmt1 = $conn->prepare($sql);
		$results = $stmt1->execute();
		if($results) {
			$row = $stmt1->fetch();
			$nm=trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
	//		print "name ".trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']))."<br/>";
		}
	}
	return $nm;

}

/**
 * @ingroup FORM_MANAGEMENT
 *@param $results [in] Results
 *@param $conn [in] connection
 *@param $msg [in] message <br>
 *check result<br>
 *output message success or fail.<br>
 */
function AddSuccess($results,$conn,$msg){
	if ((int) $results==0){
		//should log mysql errors to a file instead of displaying them to the user
		echo 'Invalid query: ' . "<br>" ;
		echo "<div align=\"center\"><h1>$msg[0]</h1></div>";		
	}else{
		echo "<div align=\"center\"><h1>$msg[1]</h1></div>";
	}
}
/**
 * 
 * @ingroup FORM_MANAGEMENT
 *@param $nRecords [in] records
 *check record<br>
 *keep record in SESSION<br>
 */
function paginate($nRecords){
	$strOffSet=$_SESSION["strOffSet"];
	switch ($_POST["Navigate"]){
		case "<<":
			$strOffSet=0;
		break;
		case "<":
			if ($strOffSet>$nRecords){
				$strOffSet=$strOffSet-1;
			}else{
				$strOffSet=0;
			}
		//$strPage = $strPage==0 ? 1 : $strPage; //checks to see that page numbers don't go to neg
		break;
		case ">":
			if ($strOffSet<$nRecords){
				$strOffSet=$strOffSet+1;
			}else{
				$strOffSet=$nRecords-1;
			}	
		break;
		case ">>":
			$strOffSet=$nRecords;
		break;
		default:
		$strOffSet = $strOffSet==0 ? 0 : $strOffSet;
	}	
	$_SESSION["strOffSet"]=$strOffSet; //counts offset values
}
/**
 * Insert or Update an existing booking
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookid [in] reservation id for update, 0 if new reservation 
 * @param $guestid [in] guest ID for booking profile
 * @param $booking_type [in] type of booking
 * @param $meal_plan [in] 2 character code
 * @param $no_adults [in] number of adults
 * @param $no_child [in] count of child
 * @param $checkin_date [in] booking checkin date
 * @param $checkout_date [in] booking checkout date
 * @param $residence_id [in] residence id making booking
 * @param $payment_mode [in] name of mode in payment
 * @param $agents_ac_no [in] agent account number
 * @param $roomid [in] Room id
 * @param $checkedin_by [in] id of user that booked
 * @param $invoice_no [in] booking voucher number
 * @param $res_det_id [in] Rerservation room detail id
 */
function modify_bookingCalendar($bookid,$guestid,$booking_type,$meal_plan,
		$no_adults,$no_child,$checkin_date,$checkout_date,$residence_id,$payment_mode,
		$agents_ac_no,$roomid,$checkedin_by,$invoice_no, $res_det_id){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$roomtypeid=0;
	if($roomid) {
		$roomtypeid="(SELECT rooms.roomtypeid FROM rooms WHERE rooms.roomid=".$roomid.")";
	}
	if(!$bookid || $bookid=='0') {
		$sql="INSERT INTO booking (guestid,booking_type,meal_plan,no_adults,no_child,";
		$sql.="checkin_date,checkout_date,residence_id,payment_mode,agents_ac_no,roomid,";
		$sql.="checkedin_by,invoice_no,roomtypeid,res_det_id)";
		$sql.="VALUES(";
		$sql.=strip_specials($guestid).",";
		$sql.=strip_specials($booking_type).",";
		$sql.=strip_specials($meal_plan).",";
		$sql.=strip_specials($no_adults).",";
		$sql.=strip_specials($no_child).",";
		$sql.=strip_specials($checkin_date).",";
		$sql.=strip_specials($checkout_date).",";
		$sql.=strip_specials($residence_id).",";
		$sql.=strip_specials($payment_mode).",";
		$sql.=strip_specials($agents_ac_no).",";
		$sql.=strip_specials($roomid).",";
		$sql.=strip_specials($checkedin_by).",";
		$sql.=strip_specials($invoice_no).",";
		$sql.=$roomtypeid.",";
		$sql.=strip_specials($res_det_id).",";
		$sql.=")";
	}
	else{
		$sql="Update booking set";
		$comma = "";
		if($booking_type){
			$sql .="booking_type='".strip_specials($booking_type)."'";
			$comma =",";
		}
		if($meal_plan){
			$sql .=$comma."meal_plan='".strip_specials($meal_plan)."'";
			$comma =",";
		}
		if($no_adults){
			$sql .=$comma."no_adults='".strip_specials($no_adults)."'";
			$comma =",";
		}
		if($no_child){
			$sql .=$comma."no_child='".strip_specials($no_child)."'";
			$comma =",";
		}
		if($checkin_date){
			$sql .=$comma."checkin_date='".strip_specials($checkin_date)."'";
			$comma =",";
		}
		if($checkout_date){
			$sql .=$comma."checkout_date='".strip_specials($checkout_date)."'";
			$comma =",";
		}
		if($residence_id){
			$sql .=$comma."residence_id='".strip_specials($residence_id)."'";
			$comma =",";
		}
		if($payment_mode){
			$sql .=$comma."payment_mode='".strip_specials($payment_mode)."'";
			$comma =",";
		}
		if($agents_ac_no){
			$sql .=$comma."agents_ac_no='".strip_specials($agents_ac_no)."'";
			$comma =",";
		}
		if($roomid){
			$sql .=$comma."roomid='".strip_specials($roomid)."'";
			$comma =",";
		}
		if($checkedin_by){
			$sql .=$comma."checkedin_by='".strip_specials($checkedin_by)."'";
			$comma =",";
		}
		if($invoice_no){
			$sql .=$comma."invoice_no='".strip_specials($invoice_no)."'";
			$comma =",";
		}
		if($res_det_id){
			$sql .=$comma."res_det_id=".strip_specials($res_det_id);
			$comma =",";
		}
		if($roomtypeid){
			$sql .=$comma."roomtypeid=".$roomtypeid;
			$comma =",";
		}
		if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
		if(!$conn) return 0;
//		print $sql."<br/>";
		$stmt =$conn->prepare($sql);
		$results =$stmt->execute();
		$row=$stmt->fetch();
		$bookid=$row['ratesid'];
		$stmt =NULL;

		return $bookid;
	}
}
/**
 * Insert or Update an existing rate
 * @ingroup RATE_MANAGEMENT
 * @param $ratesid [in] rates id for update, 0 if new rate
 * @param $ratecode [in] rate code 
 * @param $desc [in] description of rate
 * @param $src [in] type of booking  
 * @param $occupancy [in] occupancy of room eg.single,double
 * @param $ratetype [in] type of rate to database
 * @param $minpax [in] Minimum persons
 * @param $maxpax [in] Maximum persons
 * @param $minstay [in] The minimum stay
 * @param $maxstay [in] The maximum stay
 * @param $minbook [in] The minimum advance booking
 * @param $currency [in] Currency code 
 * @param $date_started [in] started date for rate of rooms
 * @param $date_stopped [in] stopped date for rate of rooms
 * @return rateid added or modified
 */
function modify_rate($ratesid,$ratecode, $desc, $src,$occupancy,$ratetype,
	$minpax=1,$maxpax=1,$minstay=0, $maxstay=1, $minbook=0,$currency,$date_started,$date_stopped){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	
	if(!$ratesid){
		$sql="INSERT INTO rates (ratecode, description,bookingtype,occupancy,rate_type,";
		$sql.="min_people, max_people, min_stay, max_stay, min_advanced_booking, currency,date_started,date_stopped)";
		$sql.="VALUES(";
		$sql.="'".strip_specials($ratecode)."',";
		$sql.="'".strip_specials($desc)."',";
		$sql.=strip_specials($src).",";
		$sql.="'".strip_specials($occupancy)."',";
		$sql.=strip_specials($ratetype).",";
		$sql.=strip_specials($minpax).",";
		$sql.=strip_specials($maxpax).",";
		$sql.=strip_specials($minstay).",";
		$sql.=strip_specials($maxstay).",";
		$sql.=strip_specials($minbook).",";
		$sql.="'".strip_specials($currency)."',";
		$sql.="STR_TO_DATE('".$date_started."', '%d/%m/%Y'),";
		$sql.="STR_TO_DATE('".$date_stopped."', '%d/%m/%Y')";
		$sql.=")";
	}
	else{
		$sql ="Update rates set ";
		if($ratecode){
			$sql .="ratecode='".strip_specials($ratecode)."',";
		}
		if($desc){
			$sql .="description='".strip_specials($desc)."',";
		}
		if($src){
			$sql .="bookingtype=".strip_specials($src).",";
		}
		if($occupancy){
			$sql .="occupancy='".strip_specials($occupancy)."',";
		}
		if($ratetype){
			$sql .="rate_type=".strip_specials($ratetype).",";
		}
		if($minpax >= 1){
			$sql .="min_people=".strip_specials($minpax).",";
		}
		if($maxpax >= 1){
			$sql .="max_people=".strip_specials($maxpax).",";
		}
		// 0 for same day rental
		if($minstay ){
			$sql .="min_stay=".strip_specials($minstay).",";
		}
		if($maxstay >= 1){
			$sql .="max_stay=".strip_specials($maxstay).",";
		}
		if($minbook >= 0){
			$sql .="min_advanced_booking=".strip_specials($minbook).",";
		}
		if($currency){
			$sql .="currency='".strip_specials($currency)."',";
		}
		if($date_started){
			$sql .="date_started=STR_TO_DATE('".$date_started."', '%d/%m/%Y'),";
		}
		if($date_stopped){
			$sql .="date_stopped=STR_TO_DATE('".$date_stopped."', '%d/%m/%Y')";
		}
		$sql .= " where ratesid=".strip_specials($ratesid);
	}
//	print $sql."<br/>\n";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	if($results && !$ratesid) {
		$ratesid =$conn->lastInsertId();
	}
	$stmt =NULL;

	return $ratesid;	
}
/**
 * Insert or Update an existing booking
 * @ingroup RATE_MANAGEMENT
 * @param $rateitemid [in] Rate item ID
 * @param $ratesid [in] Rate ID
 * @param $itemid [in] Detail item ID
 * @param $dis [in] Discount type FOC, discount,
 * @param $period [in] bitmask of days/months
 * @param $service [in] inclusive of service fee flag
 * @param $tax [in] inclusive of tax flag
 * @param $discvalue [in] value of discount or fixed value
 * @param $max [in] max count items
 */
function modify_rateitem($rateitemid, $ratesid, $itemid, 
    $dis, $period, $service, $tax, $discvalue, $max) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratesid) return 0;
	if(!$rateitemid) {
		$sql = "insert into rateitems (ratesid, itemid, discounttype, validperiod, service, tax, discountvalue, maxcount) values (";
		$sql .= strip_specials($ratesid).",";
		$sql .= strip_specials($itemid).",";
		$sql .= strip_specials($dis).",";
		$sql .= strip_specials($period).",";
		$sql .= strip_specials($service).",";
		$sql .= strip_specials($tax).",";
		$sql .= strip_specials($discvalue).",";
		$sql .= strip_specials($max).")";
	} else {
		$sql = "update rateitems set ";
		$sql .= "ratesid =".strip_specials($ratesid).",";
		$sql .= "itemid =".strip_specials($itemid).",";
		$sql .= "discounttype =".strip_specials($dis).",";
		$sql .= "validperiod =".strip_specials($period).",";
		$sql .= "service =".strip_specials($service).",";
		$sql .= "tax =".strip_specials($tax).",";
		$sql .= "discountvalue =".strip_specials($discvalue).",";
		$sql .= "maxcount =".discvalue($max);
		$sql .= " where rateitemid =".$rateitemid;
	}
//	print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	if($results && !$rateitemid) {
		$rateitemid =$conn->lastInsertId();
	}
	$stmt =NULL;
	return $rateitemid;
}
/**
 * Delete the products assigned with the rate
 * @ingroup RATE_MANAGEMENT
 * @param $ratesid [in] rates id
 * @param $rateitemid [in] rate item id
 * 
 * @return 0 fail 1 success
 */
function delete_rateitem($ratesid, $rateitemid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratesid || !$rateitemid ) return 0;
	$sql = "delete from rateitems where ratesid=".$ratesid." and rateitemid=".$rateitemid;
//	print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	return $results;
}
/**
 * Get the products assigned with the rate
 * @ingroup RATE_MANAGEMENT
 * @param $ratesid [in] rates id
 * @param $rateitems [in/out] list of rate items
 * Structure has form <br/>
 * rateitems['rateitemid']['rateitemid'] <br/>
 * rateitems['rateitemid']['ratesid'] <br/>
 * rateitems['rateitemid']['itemid'] <br/>
 * rateitems['rateitemid']['discounttype'] <br/>
 * rateitems['rateitemid']['validperiod'] <br/>
 * rateitems['rateitemid']['service'] <br/>
 * rateitems['rateitemid']['tax'] <br/>
 * rateitems['rateitemid']['maxcount'] <br/>
 * rateitems['rateitemid']['discountvalue'] <br/>
 * @return number of elements in rateitems
 */
function get_rateitems($ratesid, &$rateitems) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$rateitems = array();
	if(!$ratesid) return 0;
	$sql = "select rateitemid, ratesid, itemid, discounttype, validperiod, service, tax, discountvalue, maxcount from rateitems where ratesid=".$ratesid;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	while($row = $stmt->fetch()) {
		$rateitems[$row['rateitemid']]['rateitemid'] = $row['rateitemid'];
		$rateitems[$row['rateitemid']]['ratesid'] = $ratesid;
		$rateitems[$row['rateitemid']]['itemid'] = $row['itemid'];
		$rateitems[$row['rateitemid']]['discounttype'] = $row['discounttype'];
		$rateitems[$row['rateitemid']]['validperiod'] = $row['validperiod'];
		$rateitems[$row['rateitemid']]['service'] = $row['service'];
		$rateitems[$row['rateitemid']]['tax'] = $row['tax'];
		$rateitems[$row['rateitemid']]['discountvalue'] = $row['discountvalue'];
		$rateitems[$row['rateitemid']]['maxcount'] = $row['maxcount'];	
	}
	return sizeof($rateitems);
}
/**
 * Get the rate code for a rate id
 * @ingroup RATE_MANAGEMENT
 * @param $rateid [in] the rate id
 * @return name of rate
 */
function get_ratecode($rateid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	global $_L;
	
	$sql = "select ratecode from rates where ratesid=".$rateid;
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$row=$stmt->fetch();
	$stmt = null;
	if($row['ratecode']) {
		$res = $row['ratecode'];
	} else {
		$res = $_L['INV_std'];
	}
	return $res;
}
/**
 * Get the ratecode by start and end date
 * @ingroup RATE_MANAGEMENT
 * @param $start [in] start date
 * @param $end [in] end date
 * @param $ratecodes [in/out] the ratecode array
 */
function get_ratecode_bydate($start, $end, $ratecodes){
	global $conn;
	if(!$conn){
		$conn = connect_Hotel_db(HOST, USER, PASS, DB, PORT);
	}
	if(!$conn) return 0;
	$sql="select ratecode from rates where date_started<='".$start."' AND date_stopped>='".$end."'";
	$ratecodes=array();
	$stmt=$conn->prepare($sql);
	$results=$stmt->execute();
	$i=0;
	while ($row=$stmt->fetch()){
		$ratecodes[$i]=$row['ratecode'];
		$i++;
	}
	return $ratecodes;
}
/**
 * Get the list of rates 
 * @ingroup RATE_MANAGEMENT
 * @param $search [in] search
 * @param $stype [in] search type
 * @param $rateslist [in/out] the rateslist result
 * @return number of items in rateslist
 */
function get_rateslist($search, $stype, &$rateslist) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$search = strip_specials($search);
	
	$sql = "select ratesid, ratecode, description, bookingtype, occupancy, rate_type, currency, DATE_FORMAT(date_started,'%d/%m/%Y') as datestart, DATE_FORMAT(date_stopped,'%d/%m/%Y') as datestop, max_stay, min_stay, max_people, min_people, min_advanced_booking from rates";
	if($search && $stype == 1) {
		$sql .= " where ratecode like '%".$search."%'";
	}
	if($search && $stype == 2) {
		$sql .= " where ratesid like '%".$search."%'";
	}	
	$rateslist = array();
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	while($row=$stmt->fetch()) {
		$rateslist[$row['ratesid']]['ratecode'] = $row['ratecode'];
		$rateslist[$row['ratesid']]['description'] = $row['description'];
		$rateslist[$row['ratesid']]['bookingtype'] = $row['bookingtype'];
		$rateslist[$row['ratesid']]['occupancy'] = $row['occupancy'];
		$rateslist[$row['ratesid']]['rate_type'] = $row['rate_type'];
		$rateslist[$row['ratesid']]['currency'] = $row['currency'];
		$rateslist[$row['ratesid']]['date_started'] = $row['datestart'];
		$rateslist[$row['ratesid']]['date_stopped'] = $row['datestop'];
		$rateslist[$row['ratesid']]['max_stay'] = $row['max_stay'];
		$rateslist[$row['ratesid']]['min_stay'] = $row['min_stay'];
		$rateslist[$row['ratesid']]['max_people'] = $row['max_people'];
		$rateslist[$row['ratesid']]['min_people'] = $row['min_people'];
		$rateslist[$row['ratesid']]['min_advanced_booking'] = $row['min_advanced_booking'];
	}
	return sizeof($rateslist);
}
/**
 * Get the number of avaulable rates 
 * @ingroup RATE_MANAGEMENT
 * @param $start [in] start date
 * @param $end [in] end date
 * @param $ratecode [in] rate code
 * @param $rateslist [in/out] the rates result
 * @return number of items in rateslist
 */
function get_rate_bydateandratecode($start, $end, $ratecode, &$rateslist) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$sql = "SELECT ratesid, ratecode, occupancy, rate_type, currency, DATE_FORMAT(date_started,'%d/%m/%Y') as datestart, DATE_FORMAT(date_stopped,'%d/%m/%Y') as datestop, 
			max_stay, min_stay, max_people, min_people, min_advanced_booking 
			FROM rates
			WHERE ratecode ='".$ratecode."'";
	if($start) {
		$sql .= " and date_started <='".$start."'";
	}
	if($end) {
		$sql .= " and date_stopped >= '".$end."'";
	}	
	//echo $sql ."\n";
	$rateslist = array();
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	while($row=$stmt->fetch()) {
		$rateslist[$row['ratesid']]['ratecode'] = $row['ratecode'];
		$rateslist[$row['ratesid']]['description'] = $row['description'];
		$rateslist[$row['ratesid']]['bookingtype'] = $row['bookingtype'];
		$rateslist[$row['ratesid']]['occupancy'] = $row['occupancy'];
		$rateslist[$row['ratesid']]['rate_type'] = $row['rate_type'];
		$rateslist[$row['ratesid']]['currency'] = $row['currency'];
		$rateslist[$row['ratesid']]['date_started'] = $row['datestart'];
		$rateslist[$row['ratesid']]['date_stopped'] = $row['datestop'];
		$rateslist[$row['ratesid']]['max_stay'] = $row['max_stay'];
		$rateslist[$row['ratesid']]['min_stay'] = $row['min_stay'];
		$rateslist[$row['ratesid']]['max_people'] = $row['max_people'];
		$rateslist[$row['ratesid']]['min_people'] = $row['min_people'];
		$rateslist[$row['ratesid']]['min_advanced_booking'] = $row['min_advanced_booking'];
	}
	if($rateslist != null)
		return 1;
}
/**
 * Retrieve the rateid by the ratecode, must be an exact match and is case
 * sensitive.
 * @ingroup RATE_MANAGEMENT
 * @param $ratecode [in] the ratecode to search for.
 * @return rateid
 */
function get_rateid_bycode($ratecode) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratecode) return 0;
	$sql = "SELECT ratesid FROM rates WHERE ratecode='".$ratecode."'";
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$row=$stmt->fetch();
	$ratesid = $row['ratesid'];
	$stmt = null;
	return $ratesid;
}
/**
 * Retrieve the currency code for the rate id 
 * @param $rateid [in] the rate id to search for.
 * @return currency code applicable for that rate
 */
function get_Currency_byRateID($rateid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$rateid) return 0;
	
	$currency="";
	$sql = "SELECT currency FROM rates WHERE ratesid=".$rateid;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$row=$stmt->fetch();
	$currency = $row['currency'];
	$stmt = null;
	return $currency;
}
/**
 * Retrieve a rate by a specific ID.
 * @ingroup RATE_MANAGEMENT
 * @param $rateid [in] rates id for update, 0 if new rate
 * @param $rate [in/out] Result rate array
 *
 * return number of elements in rate
 */
function get_rate($rateid, &$rate) {
	if(!$rateid) return 0;
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$sql = "select ratecode, description, bookingtype, occupancy, rate_type, currency,";
	$sql .= "DATE_FORMAT(date_started,'%d/%m/%Y') as datestart ,date_started as started, DATE_FORMAT(date_stopped,'%d/%m/%Y') as datestop, date_stopped as stopped,";
	$sql .= "max_stay, min_stay, max_people, min_people, min_advanced_booking from rates ";
	$sql .= " where ratesid=".$rateid;

	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$row=$stmt->fetch();
//	print $sql."<br/>\n";
	
	$rate = array();
	$rate['ratesid'] = $rateid;
	$rate['ratecode'] = $row['ratecode'];
	$rate['description']=$row['description'];
	$rate['bookingsrc']=$row['bookingtype'];
	$rate['occupancy']=$row['occupancy'];
	$rate['rate_type']=$row['rate_type'];
	$rate['currency']=$row['currency'];
	$rate['date_started']=$row['datestart'];
	$rate['date_stopped']=$row['datestop'];
	$rate['started']=$row['started'];
	$rate['stopped']=$row['stopped'];
	$rate['maxstay']=$row['max_stay'];
	$rate['minstay']=$row['min_stay'];
	$rate['maxpax']=$row['max_people'];
	$rate['minpax']=$row['min_people'];
	$rate['minbook']=$row['min_advanced_booking'];
	
	return sizeof($rate);
}
/**
 * Retrieve rate type by rate  ID 
 * @param $rateid [in] The rate ID
 * @return the rate type (DEFAULTRATE/PROMORATE/CUSTOMERRATE/AGENTRATE/DEFAULTFEE)
 */
function get_ratetype_by_rateID($rateid)
{
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$ratetype="";
	$sql="SELECT rate_type FROM rates WHERE ratesid = ".$rateid;
	//echo "<br/>SQL-->".$sql;
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$ratetype=$row['rate_type'];	
	}
	
	return $ratetype;
}
/**
 * Check the given rate is already been used
 * @param $rateid [in] The rate ID
 * @return 1 if the rate has been used, 0 if not been used
 */
function isRateUsed($rateid)
{
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="SELECT COUNT(1) AS usedCount FROM rooms WHERE rateid=".$rateid;
	//echo "<br/>SQL-->".$sql;
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	$usedCount=0;
	if($results) {
		$row = $stmt->fetch();
		$usedCount=$row['usedCount'];
	}
	if($usedCount>0){
		$usedCount =1;
	}
	return $usedCount;
}
/**
 * Get the values for the rates type specified
 * @param $ratesid [in] Rates id
 * @param $rtype [in] Rates item type
 * @param $val [in/out] Returned values can be an item or a list
 */
function get_roomratetypes($ratesid, $rtype, &$val) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratesid) return 0;
	if(!$rtype) return 0;
	
	$sql = "select typeitemid from rateroomtypes where ratesid =".$ratesid." and typeid=".$rtype;
//	print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	if($rtype == CUSTOMERRATE || $rtype == AGENTRATE) {
		$row=$stmt->fetch();
		$val=$row['typeitemid'];
		return 1;
	}
	if($rtype == ROOMRATE || $rtype == ROOMTYPERATE) {
		$val = array();
		while($row = $stmt->fetch()) {
			$val[] = $row['typeitemid'];
		}
	}
	return sizeof($val);
}
/**
 * Add ratetype id to the roomratetypes database
 * @ingroup RATE_MANAGEMENT
 *
 * @param $ratesid [in] Rates id
 * @param $rtype [in] Rates item type
 * @param $val [in] Value to add
 */
function add_roomratetypes($ratesid, $rtype, $val) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratesid) return 0;
	if(!$rtype) return 0;
	//if(!$val) return 0;
	
	$sql = "insert into rateroomtypes (ratesid, typeid, typeitemid) values (".$ratesid.",".$rtype.",".$val.")";
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt->fetch();
	$stmt =NULL;

	return 1;		

}
/**
 * Delete all information for a rateid rateroomtypes
 * @ingroup RATE_MANAGEMENT
 *
 * @param $ratesid [in] rates id
 *
 * @return 0 fail 1 success 
 */
function delete_rateroomtypes($ratesid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$ratesid) return 0;
	
	$sql = "delete from rateroomtypes where ratesid=".$ratesid;
//	print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;

	return 1;		

}

/**
 * @ingroup INVOICE_MANAGEMENT
 * Delete prorated invoice
  * @param $billid [in] bill id
 */
function delete_billing($billid) {
	global $conn;
	if(! $billid) {
		return;
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$resid = get_ReservationID_By_BillID($billid);
	$bid = get_book_id($resid);
	$rcptCount = get_receiptsCount_byBillID($billid);
	$transCount = get_transactionsCount_byBillID($billid);
	//echo "reservation id ".$resid."<br/>";
	//echo "booking id ".$bid."<br/>";
	//echo "receipt count ".$rcptCount."<br/>";
	//echo "trans count ".$transCount."<br/>";
	$sql = "";
	$sql .= "DELETE b,bk,rc,rv,tr FROM bills b ";
	$sql .= "LEFT JOIN booking bk ON b.bill_id = bk.bill_id ";
	$sql .= "LEFT JOIN receipts rc ON b.bill_id = rc.bill_id ";
	$sql .= "LEFT JOIN reservation rv ON b.bill_id = rv.bill_id ";
	$sql .= "LEFT JOIN transactions tr ON b.bill_id = tr.bill_id ";
	$sql .= "WHERE b.bill_id=".$billid;
	
	
	$stmt = $conn->prepare( $sql);
	$res = $stmt->execute();
	
	$sql2 = "DELETE FROM reservation_details WHERE reservation_id='".$resid."'";
	$stmt = $conn->prepare( $sql2);
	$res = $stmt->execute();
	
	

}
/**
 * Add room charges to the bill for the date, calculate from the rate id supplied
 * or the default room rate.
 *
 * @ingroup INVOICE_MANAGEMENT
 * @param $billid [in] Invoice /Bill id
 * @param $roomid [in] Room id for the charge to be used.
 * @param $rateid [in] Rate to use in calculation of discount
 * @param $start [in] Start date in dd/mm/yyyy format
 * @param $end  [in] End date in dd/mm/yyyy format
 * @param $userid [in] id of user adding the charges.
 * 
 */
function add_roomcharges($billid, $roomid, $rateid, $start, $end, $userid) {
	$quantity = 1;
	//print "Start ".$start." End ".$end."<br/>";
	$start = str_replace("/","-", $start);
	list($dd,$mm,$yy) = sscanf($start, "%d-%d-%d");
	$sdate = mktime(0,0,0,$mm,$dd,$yy);
	$end = str_replace("/","-", $end);
	list($dd,$mm,$yy) = sscanf($end, "%d-%d-%d");
	$edate = mktime(0,0,0,$mm,$dd,$yy);
	$dt = $sdate;
	$today = date("d/m/Y");
	if(!$rateid) {
		// Retrieve booking rate id
		$rateid = get_bookingrate(get_bill_bookid($billid));
//		print "Try using booking rate ".$rateid."<br/>";
	}
	while($dt < $edate) {
		$tottax = 0;
		$totamt = 0;
		$totsvc = 0;
		$amount = 0;
		$tax = 0;
		$svc = 0;
		$itemid = 0;
		$lrate = $rateid;
		$trans_date = date("d/m/Y",$dt);
//		print "Trans date ".$trans_date."<br/>";
		
		$ratedetails = array();
		get_rateitems($rateid,$ratedetails);
		$curr = get_Currency_byRateID($rateid);
		//print_r($ratedetails);
		$roomamt = 0;
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
		//$gross = get_ratecharges($itemid, $quantity, $roomid, $trans_date, $lrate, $std_amount, $std_tax, $std_svc, $amount, $tax, $svc);				
		modify_transaction(0, $billid,1,$today, $trans_date, $userid, $roomamt, $totsvc, $tottax, $roomamt, $totsvc, $tottax,$quantity,$rateid,"",$totamt,$curr);
		$dt = $dt + (24*60*60);
	}


}
/**
 * Check the rates for the selected item and return the billable amount for the product code.
 * If the <i>itemid</i> is set, then <i>std_amt</i>, <i>std_tax</i> and <i>std_svc</i> must be set will be compared against
 * the <i>rateid</i>. If the <i>rateid</i> does not contain the <i>itemid</i> then the <i>amt</i> will be set to 
 * <i>std_amt</i>, <i>tax</i> for <i>std_tax</i>, <i>svc</i><br/>
 * <i>roomid</i> and <i>itemid</i> should not be used at the same item, <i>roomid</i> will update <i>itemid</i>.<br/>
 * Only one day charge is calculated at a time for a room and 1 room at a time.
 * Multiple rooms require multiple registrations.
 * Gross amount charge will return actual charge amount, if 3 breakfasts are free, then charge for 1
 * will be returned. If 3 are discounted, then charge amount for 3 x discount and 1 by normal rate for
 * the day will be returned.
 *
 * @ingroup INVOICE_MANAGEMENT
 * @param $itemid [in/out] item id for - auto set for rooms. 
 * @param $qty [in] quantity of itemid (not room)
 * @param $roomid [in] room id
 * @param $chg_date [in] The charge date "dd/mm/yyyy" format
 * @param $rateid [in/out] requested rate id requested
 * @param $std_amt [in/out] Standard amount charge, if not populated will be collected from the rate id
 * @param $std_tax [in/out] Standard tax amount, if not populated will be collected from the rate id
 * @param $std_svc [in/out] Standard service charge, if not populated will collected from the rate id
 * @param $amt [in/out] Update from the rate and charge
 * @param $tax [in/out] Update from the rate and std tax
 * @param $svc [in/out] Update from the rate and std service charge
 *
 * @return gross amount charged
 */
function get_ratecharges(&$itemid, $qty, $roomid, $chg_date, &$rateid, &$std_amt, &$std_tax, &$std_svc, &$amt, &$tax, &$svc) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$stdrate = array();
	$sitems = array();
	$rate = array();
	$ritems = array();

//	print "item".$itemid." room".$roomid."<br/>";
	
	// Convert the charge date into a unix time number
	$chg_date = str_replace("/","-", $chg_date);
	list($dd,$mm,$yy) = sscanf($chg_date, "%d-%d-%d");
	$chrgdt = mktime(0,0,0,$mm,$dd,$yy);
	$dtmask = get_datemask($chg_date);
	
	// Don't look up the default rate if the item id is supplied.
	if($roomid > 0 && $itemid == 0) {
		$rm = array();
		get_room($roomid, $rm);
		// Will only happen if room is not setup correctly, get the standard room rate.
		if(! get_rate($rm['rateid'], $stdrate) || ! get_rateitems($rm['rateid'], $sitems)) {
//			print "standard rate not found<br/>";
			$amt = $std_amt;
			$tax = $std_tax;
			$svc = $std_svc;
			return $amt + $tax + $svc;
		}
		// room default rates have only room charges
		foreach ($sitems as $idx=>$val) {
//			print "standard rate found<br/>";
			// Found the valid charge for the day period.
			if($sitems[$idx]['validperiod'] & $dtmask && $sitems[$idx]['discounttype'] == STANDARD) {
				if(!$itemid) $itemid = $sitems[$idx]['itemid'];
//				print "Item ".$itemid." Rate item ".$sitems[$idx]['itemid']."<br/>";
				$std_amt = $sitems[$idx]['discountvalue'];
				if(!$sitems[$idx]['tax']) {
					$std_tax = $sitems[$idx]['discountvalue'] * TAXPCT / 100;
				} else {
					$std_tax = 0;
				}
				if(!$sitems[$idx]['service']) {
					$std_svc = $sitems[$idx]['discountvalue'] * SVCPCT / 100;
				} else {
					$std_svc = 0;
				}
			}
		}
	}
	// If the rate fails to return, just copy the
	// amount, taxes and service fee and return.
//	print "try to load rate ".$rateid."<br/>";
	if($rateid && ! get_rate($rateid, $rate) ) {
//		print "Cannot load selected rate ".$rateid."<br/>";
		$amt = $std_amt;
		$tax = $std_tax;
		$svc = $std_svc;
		return 0;
	}
	get_rateitems($rateid, $ritems);
	
	// first check if the request rate applies.
	$chg_date = str_replace("/","-", $rate['date_started']);
	list($dd,$mm,$yy) = sscanf($chg_date, "%d-%d-%d");
	$sdate = mktime(0,0,0,$mm,$dd,$yy);
	
	$chg_date = str_replace("/","-", $rate['date_stopped']);
	list($dd,$mm,$yy) = sscanf($chg_date, "%d-%d-%d");
	$edate = mktime(0,0,0,$mm,$dd,$yy);
	// Rate does not apply
	if($chrgdt < $sdate || $chrgdt > $edate) {
//		print "Rate outside of valid range for rateid ".$rateid."<br/>";
		$amt = $std_amt;
		$tax = $std_tax;
		$svc = $std_svc;
		return ($amt + $tax + $svc)*$qty;
	}
	$maxcount = 1;
	foreach ($ritems as $idx=>$val) {
		// Found the valid charge for the day period and correct item id.
		if($ritems[$idx]['validperiod'] & $dtmask && $ritems[$idx]['itemid'] == $itemid) {
			if($ritems[$idx]['discounttype'] == FOC) {
				$amt = 0;
			} else if ($ritems[$idx]['discounttype'] == FIXED) {
				$amt = $ritems[$idx]['discountvalue'];
			} else if ($ritems[$idx]['discounttype'] == PERCENT) {
			
			} else { // STANDARD
				if(!$std_amt) {
					$std_amt = $ritems[$idx]['discountvalue'];
					if($ritems[$idx]['tax']) {
						$std_tax = 0;
					} else {
						$std_tax = $ritems[$idx]['discountvalue'] * TAXPCT / 100;
					}
					
					if($ritems[$idx]['service']) {
						$std_svc = 0;
					} else {
						$std_svc = $ritems[$idx]['discountvalue'] * TAXSVC / 100;
					}
				}
				$amt = $std_amt;
			}
			$maxcount = $ritems[$idx]['maxcount'];
			if(!$ritems[$idx]['tax']) {
				$tax = $amt * TAXPCT / 100;
			} else {
				$tax = 0;
			}
			if(!$ritems[$idx]['service']) {
				$svc = $amt * SVCPCT / 100;
			} else {
				$svc = 0;
			}
		}
	}
	$sqty = 0;
	if($qty > $maxcount) {
		$sqty = $qty - $maxcount;
	}
	$grossamount = ($qty * ($amt + $tax + $svc)) + ($sqty * ($std_amt + $std_tax + $std_svc));

	return $grossamount;

}
/**
 * Get the Booking id for the bill
 * @ingroup INVOICE_MANAGEMENT
 * @param $bill_id [in] The id of the bill.
 * 
 * @return the booking id
 */
function get_bill_bookid($bill_id) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "select book_id from bills where bill_id=".strip_specials($bill_id);
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['book_id'];
	}

	return "";
}
/**
 * Get the Bill/Invoice Number for a specific bill.
 * @ingroup INVOICE_MANAGEMENT
 * @param $bill_id [in] The id of the bill.
 * 
 * @return the bill number string
 */
function get_billnumber($bill_id) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return "";
	$sql = "select billno from bills where bill_id=".strip_specials($bill_id);
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['billno'];
	}

	return "";
}
/**
 * Void an existing bill payment by change status of item to void.
 * @ingroup INVOICE_MANAGEMENT
 * @param $bid [in] Bill id
 * @param $rcptid [in] The receipt id
 * @return 0 fail > 0 success
 */
function receipt_void($bid, $rcptid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Update receipts set status =".STATUS_VOID;
	$sql .= " where receipt_id =".strip_specials($rcptid) . " and bill_id=".strip_specials($bid);
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	$sql="Update receipts set cvv = '', CCnum = ( CONCAT( REPEAT('X', CHAR_LENGTH(CCnum) - 4),SUBSTRING(CCnum, -4)) ) ";
	$sql .= " where fop=2 and receipt_id =".strip_specials($rcptid) . " and bill_id=".strip_specials($bid);
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	return $results;
}
/**
 * Insert or Update receipt for an existing bill
 * @ingroup INVOICE_MANAGEMENT
 * @param $rcpt_id [in] The id of the receipt.
 * @param $book_id [in] The id of the booking.
 * @param $bill_id [in] The id of the bill. 
 * @param $res_id [in] The reservation id
 * @param $rcptno [in] Receipt number
 * @param $rcpt_date [in] receipt date
 * @param $fop [in] Form of payment type CASH, CHEQUE, CARD, ATM
 * @param $cctype [in] Credit card type
 * @param $ccnum [in] Credit card number
 * @param $expiry [in] Card expiry
 * @param $cvv [in] Card Verification Value
 * @param $auth [in] authorize
 * @param $name [in] Name on card
 * @param $amt [in] Amount of payment.
 * @param $add_by [in] Added by user id
 * @param $add_date [in] date time the receipt was added
 * @param $exrate [in] The exchange rate of the paid currency from the base currency
 * @param $srcCur [in] source currency
 * @param $tgtCur [in] target currency
 * @return receipt id
 */
function modify_receipt($rcpt_id, $bill_id, $book_id, $res_id, $rcptno, $rcpt_date, $fop, $cctype, $ccnum, $expiry, $cvv, $auth, $name, $amt, $add_by, $add_date, $exrate=0,$srcCur='',$tgtCur='') {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	if(!$bill_id) return 0;
	// Always set the add_date to now.
	$add_date=date("d/m/Y H:i");
	if(!$cctype) $cctype = 0;
	if(!$add_by) $add_by = 0;
	if(!$res_id) $res_id = 0;
	if(!$book_id) $book_id = 0;
	
	if(!$rcpt_id) {
		$rcptno = get_nextdocumentno(1,3,1);
		$sql = "INSERT INTO receipts (bill_id,book_id,reservation_id,rcpt_no,rcpt_date,fop,cctype,CCnum,expiry,
				cvv,auth,`name`,amount, `status`, add_by, add_date, exrate,srcCurrency,tgtCurrency) VALUES (";
		$sql .= strip_specials($bill_id).",";
		$sql .= strip_specials($book_id).",";
		$sql .= strip_specials($res_id).",";
		$sql .= "'".strip_specials($rcptno)."',";
		$sql .= date_to_dbformat("DD/MM/YYYY HH:MI",1,$rcpt_date).",";
		$sql .= strip_specials($fop).",";
		$sql .= "'".strip_specials($cctype)."',";
		$sql .= "'".strip_specials($ccnum)."',";
		$sql .= "'".strip_specials($expiry)."',";
		$sql .= "'".strip_specials($cvv)."',";
		$sql .= "'".strip_specials($auth)."',";
		$sql .= "'".strip_specials($name)."',";
		$sql .= strip_specials($amt).",";
		$sql .= STATUS_OPEN.",";
		$sql .= strip_specials($add_by).",";
		$sql .= date_to_dbformat("DD/MM/YYYY HH:MI",1,$add_date).",";		
		$sql .= strip_specials($exrate).",";
		$sql .= "'".strip_specials($srcCur)."',";
		$sql .= "'".strip_specials($tgtCur)."'";
		$sql .= ")";
	} else {
		$sql = "UPDATE receipts SET ";
		$sql .= " bill_id =".strip_specials($bill_id).",";
		$sql .= " book_id =".strip_specials($book_id).",";
		$sql .= " reservation_id =".strip_specials($res_id).",";
		$sql .= " rcpt_no ='".strip_specials($rcptno)."',";
		$sql .= " rcpt_date =".date_to_dbformat("DD/MM/YYYY HH:MI",1,$rcpt_date).",";
		$sql .= " fop =".strip_specials($fop).",";
		$sql .= " cctype ='".strip_specials($cctype)."',";
		$sql .= " CCnum ='".strip_specials($ccnum)."',";
		$sql .= " expiry ='".strip_specials($expiry)."',";
		$sql .= " cvv ='".strip_specials($cvv)."',";
		$sql .= " auth ='".strip_specials($auth)."',";
		$sql .= " name ='".strip_specials($name)."',";
		$sql .= " amount =".strip_specials($amt).",";
		$sql .= " status =".strip_specials($status).",";
		$sql .= " exrate =".strip_specials($exrate).",";
		$sql .= " srcCurrency ='".strip_specials($srcCur)."',";
		$sql .= " tgtCurrency ='".strip_specials($tgtCur)."' ";		
		$sql .= " WHERE receipt_id=".strip_specials($rcpt_id);
	}
//	print $sql ."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	if($results && !$rcpt_id) {
		$rcpt_id =$conn->lastInsertId();
	}

	return $rcpt_id;		
}
/**
 * Insert or Update an existing bill
 * @ingroup INVOICE_MANAGEMENT
 * @param $bill_id [in] The id of the bill.
 * @param $billno [in] number of bill 
 * @param $book_id [in] Booking id
 * @param $res_id [in] Reservation id
 * @param $date_billed [in] date billed
 * @param $date_verified [in] date verified
 * @param $created_by [in] created by
 * @param $guestid [in] guest id
 * @param $status [in] Status of the booking OPEN, CLOSED, VOID
 * @param $notes [in] Notes for bill
 * @param $flags [in] Flags for invoice - 1 is proforma
 * @return bill_id
 */
function modify_bill($bill_id,$billno,$book_id,$res_id,$date_billed,$date_verified,$created_by,$guestid,$status, $notes='', $flags=0){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	// Only get the next bill number if the bill_id is not set.
	// Modify does not need to change the bill number.
	if(!$billno && !$bill_id) $billno = get_nextdocumentno(1, 2,1);
	if(!$created_by) return 0;
	if(!$book_id) $book_id = 0;
	if(!$guestid) $guestid = 0;
	if(!$res_id) $res_id = 0;
	if(!$date_created) $date_created = date("d/m/Y H:i");
	if(!$bill_id){
		$sql ="INSERT INTO bills (billno, book_id, reservation_id, date_billed, date_checked, created_by, guestid, status,notes, flags )";
		$sql .="VALUES(";
		$sql .="'".strip_specials($billno)."',";
		$sql .=strip_specials($book_id).",";
		$sql .=strip_specials($res_id).",";
		$sql .=date_to_dbformat("DD/MM/YYYY HH:MI",1,$date_billed).",";
		$sql .=date_to_dbformat("DD/MM/YYYY HH:MI",1,$date_verified).",";
		$sql .=strip_specials($created_by).",";
		$sql .=strip_specials($guestid).",";
		$sql .=strip_specials($status).",";
		$sql .="'".htmlspecialchars($notes, ENT_QUOTES)."'";
		$sql .="'".$notes."',";
		$sql .= $flags;
		$sql .=")";
	}
	else {
		// created_by and create_date are set on insert
		$sql="Update bills set ";
		$comma = "";
		if($billno) {
			$sql .=" billno = '".strip_specials($billno)."'";
			$comma = ",";
		}
		if($book_id) {
			$sql .=" book_id=".strip_specials($book_id);
			$comma = ",";
		}
		if($res_id) {
			$sql .= $comma." reservation_id=".strip_specials($res_id);
			$comma = ",";
		}
		if($date_verified) {
			$sql .= $comma." date_checked = ".date_to_dbformat("DD/MM/YYYY HH:MI",1,$date_verified);
			$comma = ",";
		}
		if($guestid) {
			$sql .= $comma." guestid=".strip_specials($guestid);
			$comma = ",";
		}
		if($status) {
			$sql .= $comma." status=".strip_specials($status);
			$comma = ",";
		}
		if($notes) {
			$sql .= $comma." notes='".htmlspecialchars($notes, ENT_QUOTES)."'";
//			$sql .= $comma." notes='".$notes."'";
			$comma = ",";
		}
		if(isset($flags)){
			$sql .= $comma." flags=".$flags;
			$comma = ",";
		}
		$sql .= " where bill_id =".strip_specials($bill_id);
	}
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	if($results && !$bill_id) {
		$bill_id =$conn->lastInsertId();
	}

	return $bill_id;		

}
/**
 * Void an existing bill item by change status of item to void.
 * @ingroup INVOICE_MANAGEMENT
 * @param $bid [in] Bill id
 * @param $transid [in] The transaction item id
 * @return 0 fail > 0 success
 */
function transaction_void($bid, $transid) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Update transactions set status =".STATUS_VOID;
	$sql .= " where transno =".strip_specials($transid) . " and bill_id=".strip_specials($bid);
//	print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	return $results;
}
/**
 * Insert or Update an existing bill item
 * @ingroup INVOICE_MANAGEMENT
 * @param $transid [in] The transaction item id
 * @param $bill_id [in] The id of the bill.
 * @param $item_id [in] The item id
 * @param $add_date [in] date added - default now()
 * @param $trans_date [in] transaction date - default now()
 * @param $add_by [in] Userid of staff adding charge
 * @param $std_amt [in] Standard charge amount 0.00
 * @param $std_svc [in] Standard service charge amount 0.00 
 * @param $std_tax [in] Standard tax charge amount 0.00
 * @param $amt [in] Amount charged 0.00
 * @param $svc [in] Service charged 0.00
 * @param $tax [in] Tax charged 0.00
 * @param $qty [in] created by
 * @param $rateid [in] Rate ID used
 * @param $details [in] text notes
 * @param $gross [in] Gross amount
 * @param $currency [in] currency
 * @param $XOID [in] XOID
 */
function modify_transaction($transid, $bill_id,$item_id,$add_date, $trans_date, $add_by, $std_amt, $std_svc, $std_tax, $amt, $tax, $svc,$qty,$rateid,$details,$gross,$currency='',$XOID=0){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	if(!$bill_id) return 0;
	if(!$add_date) $add_date = date("d/m/Y H:i");
	if(!is_numeric($std_amt) || !is_numeric($std_svc) || !is_numeric($std_tax) ||
	   !is_numeric($amt) || !is_numeric($svc) || !is_numeric($tax) || !is_numeric($qty)) {
	   return 0;
	}
	if(!$rateid) $rateid = 0;
	if(!$gross) $gross = (($amt + $svc + $tax) * $qty );
	
	if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $add_date))
		$add_date = "'".$add_date."'";
	else
		$add_date =date_to_dbformat("DD/MM/YYYY",1,$add_date);
	if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $trans_date))
		$trans_date = "'".$trans_date."'";
	else
		$trans_date =date_to_dbformat("DD/MM/YYYY",1,$trans_date);
		
			
	//echo "In modify transaction <br/>";
	if(!$transid){
		$sql ="INSERT INTO transactions (bill_id, item_id, add_date, add_by, std_amount, std_svc, std_tax, amount, svc, tax, quantity, grossamount, ratesid, details, trans_date,currency,XOID)";
		$sql .="VALUES(";
		$sql .=strip_specials($bill_id).",";
		$sql .=strip_specials($item_id).",";
		
		$sql .=$add_date.",";
		$sql .=strip_specials($add_by).",";
		$sql .=sprintf("%2.2f",$std_amt).",";
		$sql .=sprintf("%2.2f",$std_svc).",";
		$sql .=sprintf("%2.2f",$std_tax).",";
		$sql .=sprintf("%2.2f",$amt).",";
		$sql .=sprintf("%2.2f",$svc).",";
		$sql .=sprintf("%2.2f",$tax).",";
		$sql .=$qty.",";
		if ($XOID && $gross) {
			$sql .=  strip_specials($gross).",";
		} else {
			$sql .=  (($amt + $svc + $tax) * $qty ).",";
		}
		$sql .=strip_specials($rateid).",";
		$sql .="'".strip_specials($details)."',";
		$sql .=$trans_date.",";
		$sql .="'".strip_specials($currency)."',";
		$sql .=strip_specials($XOID);
		$sql .=")";
	}
	else {
		// created_by and create_date are set on insert
		$sql="Update transactions set ";
		$sql .="bill_id=".strip_specials($bill_id).",";
		$sql .="item_id=".strip_specials($item_id).",";
		$sql .="add_date=".$add_date.",";
		$sql .="add_by=".strip_specials($add_by).",";
		$sql .="std_amount=".sprintf("%2.2f",$std_amt).",";
		$sql .="std_svc=".sprintf("%2.2f",$std_svc).",";
		$sql .="std_tax=".sprintf("%2.2f",$std_tax).",";
		$sql .="amount=".sprintf("%2.2f",$amt).",";
		$sql .="svc=".sprintf("%2.2f",$svc).",";
		$sql .="tax=".sprintf("%2.2f",$tax).",";
		$sql .="quantity=".$qty.",";
		$sql .="grossamount=". $gross.",";
		$sql .="ratesid=".strip_specials($rateid).",";
		$sql .="details='".strip_specials($details)."',";
		$sql .="trans_date=".$trans_date.",";
		$sql .="currency='".strip_specials($currency)."',";
		$sql .="XOID=".strip_specials($XOID);
		$sql .= " where transno =".strip_specials($transid);
	}
	//print $sql ."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	if($results && !$transid) {
		$transid =$conn->lastInsertId();
	}
	return $transid;		
}
/**
 * Get the currency code for the Transaction ID 
 * @param $transID [in] The  Transaction ID 
 * @return the currency code used for this transaction
 */
function get_Currency_byTransactionID($transID)
{
	
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "SELECT currency FROM transactions WHERE transno = ".$transID;
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	
	$currency="";
	if($results) {
		$row = $stmt->fetch();
		$currency=$row['currency'];
	}	
	return $currency;
}
/**
 * Check if the bill has a debit balance
 * @param $billid [in] Id of the bill
 * @return 1 if true 0 if false (0 balance or credit)
 */
function is_bill_inDebit($billid) {

	$bill = array();
	if(get_bill($billid, $bill)) {
		if(isset($bill['btotal'])) {
			$total = 0;
			foreach ($bill['btotal'] as $cur => $value) {
				$total += $value;
				if(isset($bill['rtotal'][$cur])) {
					$total -= $bill['rtotal'][$cur];
				}
			}
		}
		if($total < 0) $total = 0;
		return $total;
	} 
	return 0;
}
/**
 * Retrieve an invoice and associated transactions
 * @ingroup INVOICE_MANAGEMENT
 * @param $bill_id [in] The id of the bill.
 * @param $bill [in/out] Bill array structure
 * @return number of elements in bill
 *
 * @note
 * $bill['bill_id'] <br/>
 * $bill['book_id'] <br/>
 * $bill['reservation_id'] <br/>
 * $bill['date_billed'] <br/>
 * $bill['billno'] <br/>
 * $bill['status']   // OPEN, CLOSED, VOID <br/>
 * $bill['date_checked']  // status set to closed, void or balance due is 0. <br/>
 * $bill['created_by'] <br/>
 * $bill['notes']<br/>
 * $bill['transcount'] // > 1 for number of invoice items <br/>
 * // from 0 to transcount <br/>
 * $bill['trans'][0]['trans_id'] <br/>
 * $bill['trans'][0]['item_id'] <br/>
 * $bill['trans'][0]['add_date'] <br/>
 * $bill['trans'][0]['add_by'] <br/>
 * $bill['trans'][0]['details'] <br/>
 * $bill['trans'][0]['std_amount'] <br/>
 * $bill['trans'][0]['std_svc'] <br/>
 * $bill['trans'][0]['std_tax'] <br/>
 * $bill['trans'][0]['amount'] <br/>
 * $bill['trans'][0]['svc'] <br/>
 * $bill['trans'][0]['tax'] <br/>
 * $bill['trans'][0]['quantity'] <br/>
 * $bill['trans'][0]['ratesid'] <br/>
 * $bill['trans'][0]['grossamount'] <br/>
 * $bill['trans'][0]['transdate'] <br/>
 * $bill['rcptcount']
 * $bill['rcpts][0]['receipt_id'] <br/>
 * $bill['rcpts][0]['rcpt_no'] <br/>
 * $bill['rcpts][0]['rcpt_date'] <br/>
 * $bill['rcpts][0]['fop'] <br/>
 * $bill['rcpts][0]['cctype'] <br/>
 * $bill['rcpts][0]['CCnum'] <br/>
 * $bill['rcpts][0]['CVV'] <br/>
 * $bill['rcpts][0]['name'] <br/>
 * $bill['rcpts][0]['amount'] <br/>
 * $bill['rcpts][0]['status'] <br/>
 */
function get_bill($bill_id, &$bill) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	if(!$bill_id) return 0;
	// Get the invoice
	$sql = "select book_id, DATE_FORMAT(date_billed,'%d/%m/%Y') as billdate, billno, status, DATE_FORMAT(date_checked,'%d/%m/%Y') as checkdate , reservation_id, created_by, guestid, notes, flags from bills where bill_id=".strip_specials($bill_id);
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	if(!$results) return 0;
	$row = $stmt->fetch();
	$bill = array();
	$bill['transcount'] = 0;
	$bill['rcptcount'] = 0;
	$bill['bill_id'] = $bill_id;
	$bill['guestid'] = $row['guestid'];
	$bill['book_id'] = $row['book_id'];
	$bill['reservation_id'] = $row['reservation_id'];
	$bill['date_billed'] = $row['billdate'];
	$bill['billno'] = $row['billno'];
	$bill['status'] = $row['status'];  // OPEN, CLOSED, VOID
	$bill['date_checked'] = $row['checkdate']; // status set to closed, void or balance due is 0.
	$bill['created_by'] = $row['created_by'];
	$bill['notes'] = htmlspecialchars_decode($row['notes'], ENT_NOQUOTES);
	$bill['flags'] = $row['flags'];
//	$bill['notes'] = $row['notes'];
	$stmt = null;
	// get the items
	$sql2 = "select transno, item_id, DATE_FORMAT(add_date,'%d/%m/%Y') as  adate, add_by, details, std_amount, std_svc, std_tax, DATE_FORMAT(trans_date,'%d/%m/%Y') as  tdate, amount, svc, tax, ratesid, quantity, grossamount, `status`, currency, XOID from transactions where bill_id=".strip_specials($bill_id);
	$stmt2 =$conn->prepare($sql2);
	$results =$stmt2->execute();	
	if($results) {
		$i=0;
		while($row = $stmt2->fetch()) {
			$bill['transcount']++;
			$bill['trans'][$i]['trans_id'] = $row['transno'];
			$bill['trans'][$i]['item_id'] = $row['item_id'];
			$bill['trans'][$i]['add_date'] = $row['adate'];
			$bill['trans'][$i]['add_by'] = $row['add_by'];
			$bill['trans'][$i]['details'] = $row['details'];
			$bill['trans'][$i]['std_amount'] = $row['std_amount'];
			$bill['trans'][$i]['std_svc'] = $row['std_svc'];
			$bill['trans'][$i]['std_tax'] = $row['std_tax'];
			$bill['trans'][$i]['amount'] = $row['amount'];
			$bill['trans'][$i]['svc'] = $row['svc'];
			$bill['trans'][$i]['tax'] = $row['tax'];
			$bill['trans'][$i]['quantity'] = $row['quantity'];
			$bill['trans'][$i]['ratesid'] = $row['ratesid'];
			$bill['trans'][$i]['rateid'] = $row['ratesid'];
			$bill['trans'][$i]['grossamount']  = $row['grossamount'];
			$bill['trans'][$i]['trans_date'] = $row['tdate']; 
			$bill['trans'][$i]['status'] = $row['status']; 
			$bill['trans'][$i]['currency'] = $row['currency'];
			$bill['trans'][$i]['XOID'] = $row['XOID'];
			if($row['status'] != STATUS_VOID ) {
				if(!isset($bill['btotal'][$row['currency']])) {
					$bill['btotal'][$row['currency']] = $row['amount'];
				} else {
					$bill['btotal'][$row['currency']] += $row['amount'];
				}
			}
			$i++;
		}
	}
	$stmt2 = null;
	$sql3 = "SELECT receipt_id, rcpt_no, DATE_FORMAT(rcpt_date,'%d/%m/%Y') as  rdate, fop, cctype, CCnum, expiry, cvv, name, 
			amount, status, auth, exrate,srcCurrency,tgtCurrency 
			FROM receipts WHERE bill_id=".strip_specials($bill_id);
	$stmt3 =$conn->prepare($sql3);
	$results =$stmt3->execute();
	if($results) {
		$i=0;
		while($row = $stmt3->fetch()) {
			$bill['rcptcount']++;
			$bill['rcpts'][$i]['receipt_id'] = $row['receipt_id'];
			$bill['rcpts'][$i]['rcpt_no'] = $row['rcpt_no'];
			$bill['rcpts'][$i]['rcpt_date'] = $row['rdate'];
			$bill['rcpts'][$i]['fop'] = $row['fop'];
			$bill['rcpts'][$i]['cctype'] = $row['cctype'];
			$bill['rcpts'][$i]['CCnum'] = $row['CCnum'];
			$bill['rcpts'][$i]['CVV'] = $row['cvv'];
			$bill['rcpts'][$i]['expiry']= $row['expiry'];
			$bill['rcpts'][$i]['name'] = $row['name'];
			$bill['rcpts'][$i]['amount'] = $row['amount'];
			$bill['rcpts'][$i]['auth'] = $row['auth'];
			$bill['rcpts'][$i]['status'] = $row['status'];
			$bill['rcpts'][$i]['exrate']=$row['exrate'];	
			$bill['rcpts'][$i]['srcCurrency']=$row['srcCurrency'];
			$bill['rcpts'][$i]['tgtCurrency']=$row['tgtCurrency'];	
			if($row['status'] != STATUS_VOID) {
				$src = $row['srcCurrency'];
				$tgt = $row['tgtCurrency'];
				$ex = $row['exrate'];
				if($src == $tgt) {
					$cur = $tgt;
					$val = $row['amount'];
				} else {
					$cur = $src;
					$val = $ex * $row['amount'];
				}
				if(!isset($bill['rtotal'][$cur])) {
					$bill['rtotal'][$cur] = $val;
				} else {
					$bill['rtotal'][$cur] += $val;
				}
			}
			$i++;
		}
	}	
	return sizeof($bill);
}

/**
 * Insert or Update an existing booking
 * @ingroup USER_MANAGEMENT
 * @param $userid [in] userid for update, 0 if new user 
 * @param $fname [in] firstname
 * @param $sname [in] surname
 * @param $loginname [in] login name
 * @param $pass [in] password
 * @param $phone [in] phone number
 * @param $mobile [in] mobile number
 * @param $fax [in] fax number
 * @param $email [in] email
 * @param $dateregistered [in] date registered
 * @param $admin [in] admin
 * @param $guest [in] guest
 * @param $reservation [in] reservation
 * @param $booking [in] booking
 * @param $agents [in] agents
 * @param $rooms [in] rooms
 * @param $billing [in] billing
 * @param $rates [in] rates
 * @param $lookup [in] lookup
 * @param $reports [in] reports
 * @return userid updated or added
 */
function modify_user($userid,$fname,$sname,$loginname,
		$pass,$phone='',$mobile='',$fax='',$email='',$dateregistered,$admin=0,
		$guest=0,$reservation=0,$booking=0,$agents=0,$rooms=0,$billing=0,
		$rates=0,$lookup=0,$reports=0){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) {
		return 0;
	}
	if(!$dateregistered){
		$dateregistered='now()';
	} else {
		$dateregistered= date_to_dbformat("DD/MM/YYYY HH:MI",1,$dateregistered);
	}
	if(empty($phone))
		$phone=0;
	if(empty($mobile))
		$mobile=0;
	if(empty($fax))
		$fax=0;
		
	if(!$userid){
		$sql="insert into users (fname,sname,loginname,";
		$sql.="pass,phone,mobile,fax,email,dateregistered,";
		$sql.="admin,guest,reservation,booking,agents,rooms,";
		$sql.="billing,rates,lookup,reports)";
		$sql.=" values (";
		$sql.="'".strip_specials($fname)."',";
		$sql.="'".strip_specials($sname)."',";
		$sql.="'".strip_specials($loginname)."',";
		$sql.="'".strip_specials($pass)."',";
		$sql.=strip_specials($phone).",";
		$sql.=strip_specials($mobile).",";
		$sql.=strip_specials($fax).",";
		$sql.="'".strip_specials($email)."',";
		$sql.=$dateregistered.",";
		$sql.=strip_specials($admin).",";
		$sql.=strip_specials($guest).",";
		$sql.=strip_specials($reservation).",";
		$sql.=strip_specials($booking).",";
		$sql.=strip_specials($agents).",";
		$sql.=strip_specials($rooms).",";
		$sql.=strip_specials($billing).",";
		$sql.=strip_specials($rates).",";
		$sql.=strip_specials($lookup).",";
		$sql.=strip_specials($reports).")";
	}
	else{
		$sql="update users set ";
			$sql .="fname='".strip_specials($fname)."',";
			$sql .="sname='".strip_specials($sname)."',";
			$sql .="loginname='".strip_specials($loginname)."',";
			$sql .="pass='".strip_specials($pass)."',";
			$sql .="phone=".strip_specials($phone).",";
			$sql .="mobile=".strip_specials($mobile).",";
			$sql .="fax=".strip_specials($fax).",";
			$sql .="email='".strip_specials($email)."',";
			$sql .="dateregistered=".$dateregistered.",";
			$sql .="admin=".strip_specials($admin).",";
			$sql .="guest=".strip_specials($guest).",";
			$sql .="reservation=".strip_specials($reservation).",";
			$sql .="booking=".strip_specials($booking).",";
			$sql .="agents=".strip_specials($agents).",";
			$sql .="rooms=".strip_specials($rooms).",";
			$sql .="billing=".strip_specials($billing).",";
			$sql .="rates=".strip_specials($rates).",";
			$sql .="lookup=".strip_specials($lookup).",";
			$sql .="reports=".strip_specials($reports);
			$sql .= " where userid=".strip_specials($userid);
	}
	//echo "<br/>sql-->".$sql;
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	if($results && !$userid) {
		$userid =$conn->lastInsertId();
	}
	$stmt =NULL;
	return $userid;
}
/**
 * List the guests.
 * @ingroup GUEST_MANAGEMENT
 * @param $search [in] guest passport number
 * @param $stype [in] 1-name 2-passport 3-id 4-passport/id 5-phone 6-email
 * @param $guests [in/out] guests array
 * @return number of elements in agent
 */
function list_guests($search, $stype, &$guests) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) {
		return 0;
	}
	$search = strip_specials($search);
	
	$sql="Select guests.guestid,guests.lastname,guests.firstname,guests.middlename,guests.pp_no,
		guests.idno,guests.countrycode,guests.town,guests.postal_code,guests.phone,
		guests.email,guests.mobilephone,c.country, guests.countrycode, guests.salutation,
		guests.nationality, n.country as nation 
		From guests
		LEFT JOIN countries as c ON guests.countrycode = c.countrycode 
		LEFT JOIN countries as n ON guests.nationality = n.countrycode";
	if($search && $stype == 1) {
		$sql .= " where lastname like '%".$search."%' or firstname like '%".$search."%'";
	}
	if($search && $stype == 2) {
		$sql .= " where pp_no='".$search."'";
	}
	if($search && $stype == 3) {
		$sql .= " where guestid=".$search;
	}
	if($search && $stype == 4) {
		$sql .= " where guestid like '%".$search."%' or pp_no like '%".$search."%'";
	}
	if($search && $stype == 5) {
		$sql .= " where phone like '%".$search."%' or mobilephone like '%".$search."%'";
	}
	if($search && $stype == 6) {
		$sql .= " where email like '%".$search."%'";
	}

	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	while($row = $stmt->fetch()) {
		$guests[$row['guestid']]['lastname'] = $row['lastname'];
		$guests[$row['guestid']]['firstname'] = $row['firstname'];
		$guests[$row['guestid']]['middlename'] = $row['middlename'];
		$guests[$row['guestid']]['salutation'] = $row['salutation'];
		$guests[$row['guestid']]['pp_no'] = $row['pp_no'];
		$guests[$row['guestid']]['idno'] = $row['idno'];
		$guests[$row['guestid']]['countrycode'] = $row['countrycode'];
		$guests[$row['guestid']]['address'] = $row['address'];
		$guests[$row['guestid']]['town'] = $row['town'];
		$guests[$row['guestid']]['postal_code'] = $row['postal_code'];
		$guests[$row['guestid']]['phone'] = $row['phone'];
		$guests[$row['guestid']]['email'] = $row['email'];
		$guests[$row['guestid']]['mobilephone'] = $row['mobilephone'];
		$guests[$row['guestid']]['country'] = $row['country'];
		$guests[$row['guestid']]['nationality'] = $row['nationality'];
		$guests[$row['guestid']]['nation'] = $row['nation'];
	}
//	echo $sql;
	return sizeof($guests);
}
/**
 * Delete guest from database - will not remove bookings or history
 * @ingroup GUEST_MANAGEMENT
 * @param $guestid [in] guest id to delete
 *
 * @return 0 fail 1 success
 */
function delete_guest($guestid) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="delete from guests where guestid=" .strip_specials($guestid);
	$stmt = $conn->prepare($sql);
	$results= $stmt->execute();
	$stmt =NULL;
	return $results;
}
/**
 * Delete roomtype from database
 * @ingroup GUEST_MANAGEMENT
 * @param $roomtypeid [in] room type to delete
 *
 * @return 0 fail 1 success
 */
function delete_roomtype($roomtypeid) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="delete from roomtype where roomtypeid=" .strip_specials($roomtypeid);
	$stmt = $conn->prepare($sql);
	$results= $stmt->execute();
	$stmt =NULL;
	return $results;
}
/**
 * Insert or Update an existing guest
 * @ingroup GUEST_MANAGEMENT
 * @param $guestid [in] guest id for update, 0 if new guest
 * @param $lastname [in] lastname of guest to database
 * @param $firstname [in] firstname of guest to database
 * @param $middlename [in] middlename of guest to database
 * @param $salutation [in] id of salutation mr/ms/mrs/miss/dr etc
 * @param $pp_no [in] passport number for guest 
 * @param $idno [in] ID for guest profile
 * @param $countrycode [in] 2 letter countrycode of <i>idno</i>
 * @param $street_no [in] street number
 * @param $street_name [in] street name
 * @param $town [in] town of guest live
 * @param $postal_code [in] postal code number of guest
 * @param $access [in] access
 * @param $area [in] area code
 * @param $phone [in] phone number of guest to contact
 * @param $email [in] email of guest to contact
 * @param $mobilephone [in] mobilephone number of guest
 * @param $ebridgeid [in] The ebridge id
 * @param $IM [in] Skype or MSN Id
 * @param $nationality [in] 2 letter country code for nationality 
 */
function modify_guest($guestid,$lastname,$firstname,$middlename,$salutation,
		$pp_no,$idno,$countrycode,$street_no,$street_name,$town,$postal_code,$access,$area,$phone,$email,
		$mobilephone,$ebridgeid,$IM,$nationality){
		
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!isset($idno) || $idno == "") {
		$idno = 0;
	}
	
	if(!$guestid){
		$sql="INSERT INTO guests (lastname,firstname,middlename,salutation,pp_no,idno,countrycode,nationality,street_no,street_name,town,";
		$sql.="postal_code,access,areacode,phone,email,mobilephone,eBridgeID,IM)";
		$sql.=" VALUES (";
		$sql.="'".strip_specials($lastname)."',";
		$sql.="'".strip_specials($firstname)."',";
		$sql.="'".strip_specials($middlename)."',";
		$sql.="".strip_specials($salutation).",";
		$sql.="'".strip_specials($pp_no)."',";
		$sql.=$idno.",";
		$sql.="'".strip_specials($countrycode)."',";
		$sql.="'".strip_specials($nationality)."',";
		$sql.="'".strip_specials($street_no)."',";
		$sql.="'".strip_specials($street_name)."',";
		$sql.="'".strip_specials($town)."',";
		$sql.="'".strip_specials($postal_code)."',";
		$sql.="'".strip_specials($access)."',";
		$sql.="'".strip_specials($area)."',";
		$sql.="'".strip_specials($phone)."',";
		$sql.="'".strip_specials($email)."',";
		$sql.="'".strip_specials($mobilephone)."',";
		$sql.="'".strip_specials($ebridgeid)."',";
		$sql.="'".strip_specials($IM)."'";
		$sql.=")";
	}
	else{
		$sql="update guests set ";
		$sql .= "lastname='".strip_specials($lastname)."', ";
		$sql .="firstname='".strip_specials($firstname)."', ";
		$sql .="middlename='".strip_specials($middlename)."', ";
		$sql .="salutation=".strip_specials($salutation).", ";
		$sql .="pp_no='".strip_specials($pp_no)."', ";
		$sql .="idno=".strip_specials($idno).", ";
		$sql .="countrycode='".strip_specials($countrycode)."', ";
		$sql .="nationality='".strip_specials($nationality)."', ";
		$sql .="street_no='".strip_specials($street_no)."', ";
		$sql .="street_name='".strip_specials($street_name)."', ";
		$sql .="town='".strip_specials($town)."', ";
		$sql .="postal_code='".strip_specials($postal_code)."', ";
		$sql .="access='".strip_specials($access)."', ";
		$sql .="areacode='".strip_specials($area)."', ";
		$sql .="phone='".strip_specials($phone)."', ";
		$sql .="email='".strip_specials($email)."', ";
		$sql .="mobilephone='".strip_specials($mobilephone)."', ";
		$sql .="eBridgeID='".strip_specials($ebridgeid)."', ";
		$sql .="IM='".strip_specials($IM)."' ";
		$sql .="where guestid=" .strip_specials($guestid);
	}
	//echo $sql;
	$stmt = $conn->prepare($sql);
	$results= $stmt->execute();
	$stmt =NULL;
	//print $sql."<br/>\n".$results."<br/>\n";

	if($results && !$guestid) {
		$guestid =$conn->lastInsertId();
	}
	return $guestid;
}
/**
 * Retrieve the list of registrations/booking
 *
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookings [in/out] array to contain result of registration 
 * @param $status [in] 0 active 1 for all
 * @param $name [in] name
 * @param $roomid [in] Room id
 * <br/>
 * $booking['book_id']['book_id'] (same as book_id) <br/>
 * $booking['book_id']['bill_id'] invoice id <br/>
 * $booking['book_id']['guestid']  <br/>
 * $booking['book_id']['reservation_id']  <br/>
 * $booking['book_id']['no_adults']  <br/>
 * $booking['book_id']['no_child']  total of babies, child 1 to 5 and child 6 to 12<br/>
 * $booking['book_id']['roomid']  <br/>
 * $booking['book_id']['roomtypeid']  <br/>
 * $booking['book_id']['ratesid']  <br/>
 * $booking['book_id']['voucher_no']  <br/>
 * $booking['book_id']['guestname']  <br/>
 * $booking['book_id']['checkindate'] dd/mm/yyyy hh:ii <br/>
 * $booking['book_id']['checkoutdate'] dd/mm/yyyy hh:ii   <br/>
 * $booking['book_id']['roomno']  <br/>
 * $booking['book_id']['book_status']  <br/>
 *
 * @return number of items in <i>bookings</i>
 */
function get_bookinglist(&$bookings, $status, $name, $roomid = 0) {
	global $conn;
	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_all_bookings_advProfile($bookings, $status, $name, $roomid);
	}
	else{
		$pos = strrpos($name, ' ');
		list($fname, $lname) = preg_split("/[\s,]+/", $name,2);
		$name = $fname;
		$bookings = array();
		$sql = "SELECT b.book_id, b.guestid, b.reservation_id, b.bill_id, b.no_adults, b.no_child1_5, b.no_child6_12, 
			b.no_babies, DATE_FORMAT(b.checkindate, '%d/%m/%Y %H:%i') AS checkin, DATE_FORMAT(b.checkoutdate, '%d/%m/%Y %H:%i') AS checkout,
			b.roomid, b.roomtypeid, b.rates_id, b.voucher_no, b.book_status, g.lastname,g.firstname,g.middlename, 
			g.pp_no, g.idno, g.street_name, g.postal_code, g.town, g.phone, g.email, g.mobilephone, g.eBridgeID, g.IM, g.nationality ,g.countrycode,
			n.country as nation, c.country, b.instructions, DATEDIFF(checkoutdate, checkindate) AS no_nights
			FROM booking AS b, guests AS g
			LEFT JOIN countries AS c ON g.countrycode = c.countrycode 
			LEFT JOIN countries AS n ON g.nationality = n.countrycode
			WHERE g.guestid = b.guestid ";
			
		if (!$status) {
			$sql .= "";
		} else if ($status == 1) {
			$sql .= " and b.book_status = 1 ";
		} else if ($status == 2) {
			$sql .= " and b.book_status = 2 ";
		} else if ($status == 3) {
			$sql .= " and b.book_status = 3 ";
		} else if ($status == 4) {
			$sql .= " and b.book_status = 4 ";
		} else if ($status == 5) {
			$sql .= " and b.book_status = 5 ";
		}
	
		//if ($roomtypeid) $sql .= "and  b.roomtypeid = ".strip_specials($roomtypeid);
		if ($name) $sql .= " and ( g.firstname like '%".$name."%' or g.middlename like '%".$name."%' or g.lastname like '%".$name."%' )";
		if ($roomid) $sql .= " and b.roomid = " .$roomid;
		
		$sql .= " order by book_id  asc"; //added by zc
		//echo $sql;
		//print $sql."<br/>";
		$stmt = $conn->prepare($sql);
		$results = $stmt->execute();
		$i=0;
		if($results) {
			while($row = $stmt->fetch()) {
				$bookings[$row['book_id']]['book_id'] = $row['book_id'];
				$bookings[$row['book_id']]['bill_id'] = $row['bill_id'];
				$bookings[$row['book_id']]['guestid'] = $row['guestid'];
				$bookings[$row['book_id']]['reservation_id'] = $row['reservation_id'];
				$bookings[$row['book_id']]['no_adults'] = $row['no_adults'];
				$bookings[$row['book_id']]['no_child'] = $row['no_child1_5'] + $row['no_child6_12'] + $row['no_babies'];
				$bookings[$row['book_id']]['roomid'] = $row['roomid'];
				$bookings[$row['book_id']]['roomtypeid'] = $row['roomtypeid'];
				$bookings[$row['book_id']]['ratesid'] = $row['rates_id'];
				$bookings[$row['book_id']]['voucher_no'] = $row['voucher_no'];
				$bookings[$row['book_id']]['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
				$bookings[$row['book_id']]['no_nights'] = $row['no_nights'];
				$bookings[$row['book_id']]['checkindate'] = $row['checkin'];
				$bookings[$row['book_id']]['checkoutdate'] = $row['checkout'];
				$bookings[$row['book_id']]['book_status'] = $row['book_status'];
				$bookings[$row['book_id']]['roomno'] = get_roomno($row['roomid']);
			
			}
		}
	}
	//print_r($bookings);
	$stmt = null;
	return sizeof ($bookings);
}
/**
 * Retrieve the list of registrations/booking
 *
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookings [in/out] array to contain result of registration 
 * @param $end [in] end date
 * @param $roomtype [in] Room type id
 * <br/>
 * $booking['book_id']['book_id'] (same as book_id) <br/>
 * $booking['book_id']['checkindate'] dd/mm/yyyy hh:ii <br/>
 * $booking['book_id']['checkoutdate'] dd/mm/yyyy hh:ii   <br/>
 * $booking['book_id']['roomtype']  <br/>
 * $booking['book_id']['book_status']  <br/>
 *
 * @return number of items in <i>bookings</i>
 */
function get_booking_bydateandtype(&$bookings, $end, $roomtype) {
	global $conn;
	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;

	$bookings = array();
	$start = date("Y-m-d H:i:s");
	//echo $start ."\n";
	$sql = "SELECT book_id, DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') AS checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') AS checkout, roomtypeid, book_status, instructions
			FROM booking
			WHERE book_status = 2 and checkindate <= '".$start."'";
	if($end) {
		$sql .=	" and checkoutdate >= '".$end." 10:00:00'";
	}
	if($roomtype) {
		$sql .=	" and roomtypeid = ".get_roomtypeid($roomtype);
	}
	//echo $sql ."\n";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {
			$bookings[$row['book_id']]['book_id'] = $row['book_id'];
			$bookings[$row['book_id']]['checkindate'] = $row['checkin'];
			$bookings[$row['book_id']]['checkoutdate'] = $row['checkout'];
			$bookings[$row['book_id']]['book_status'] = $row['book_status'];
			$bookings[$row['book_id']]['roomtype'] = get_roomtype($row['roomtypeid']);
		}
	}
	//print_r($bookings);
	$stmt = null;
	return sizeof ($bookings);
}
/**
 *Insert or Update an existing booking
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookid [in] booking id for update, 0 if new reservation 
 * @param $res_id [in] Reservation id
 * @param $bill_id [in] Billing id
 * @param $guestid [in] guest ID for booking profile
 * @param $no_adults [in] count of adults
 * @param $no_child6_12 [in] count of child 6 to 12
 * @param $no_child1_5 [in] count of child 1 to 5
 * @param $no_babies [in] count babies
 * @param $checkindate [in] booking checkin date
 * @param $checkoutdate [in] booking checkout date
 * @param $roomid [in] Room id
 * @param $roomtypeid [in] Room type id
 * @param $ratesid [in] Rates id
 * @param $instr [in] Instructions/Notes
 * @param $checkedin_by [in] Userid of staff doing registration
 * @param $checkedin_date [in] date time of check in
 * @param $checkedout_by [in] Userid of staff doing checkout
 * @param $checkedout_date [in] date time of check out
 * @param $cctype [in] Credit card type
 * @param $CCnum [in] Credit card number
 * @param $expiry [in] MMYY expiry date
 * @param $CVV [in] Card verification value
 * @param $voucher_no [in] voucher number
 * @param $book_status [in] booking status
 * @param $res_det_id [in] reservation details ID
 */
function modify_booking($bookid,$res_id,$bill_id,$guestid,
		$no_adults,$no_child6_12,$no_child1_5,$no_babies,$checkindate,
		$checkoutdate,$roomid,$roomtypeid,$ratesid, $instr,
		$checkedin_by, $checkedin_date, $checkedout_by, $checkedout_date,
		$cctype,$CCnum,$expiry,$CVV,$voucher_no,$book_status = BOOK_REGISTERED, $res_det_id = 0){
	global $conn;
	if(!$res_id) $res_id = 0;
	if(!$bill_id) $bill_id = 0;
	if(!$ratesid) $ratesid = 0;
	if(!$roomtypeid) $roomtypeid=0;
	if(!$roomid) $roomid = 0;
	if(!$no_adults) $no_adults = 0;
	if(!$no_babies) $no_babies = 0;
	if(!$no_child6_12) $no_child6_12 = 0;
	if(!$no_child1_5) $no_child1_5 = 0;
	if(!$checkedin_by) $checkedin_by = 0;
	if(!$checkedout_by) $checkedout_by = 0;

  
	if($roomid && !$roomtypeid) {
		$roomtypeid="(SELECT rooms.roomtypeid FROM rooms WHERE rooms.roomid=".$roomid.")";
	} else {
	    $roomtypeid = strip_specials($roomtypeid);
	}
	if(!$bookid){
		if(!$voucher_no) $voucher_no = get_nextdocumentno(1, 1, 1);
		$sql="INSERT INTO booking (guestid,reservation_id,bill_id,no_adults,";
		$sql.="no_child6_12,no_child1_5,no_babies,";
		$sql.="checkindate,checkoutdate,roomid,roomtypeid,rates_id,instructions,";
		$sql.= "voucher_no,checkedin_by,checkedin_date,";
		$sql.= "checkedout_by, checkedout_date, cctype, CCnum, expiry, CVV, book_status, res_det_id) ";
		$sql.= "VALUES(";
		$sql.= strip_specials($guestid).",";
		$sql.= strip_specials($res_id).",";
		$sql.= strip_specials($bill_id).",";
		$sql.= strip_specials($no_adults).",";
		$sql.= strip_specials($no_child6_12).",";
		$sql.= strip_specials($no_child1_5).",";
		$sql.= strip_specials($no_babies).",";
		$sql.= date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkindate).",";
		$sql.= date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkoutdate).",";
		$sql.= strip_specials($roomid).",";
		$sql.= $roomtypeid.",";
		$sql.= strip_specials($ratesid).",";
		$sql.= "'".strip_specials($instr)."',";
		$sql.= "'".strip_specials($voucher_no)."',";
		$sql.= strip_specials($checkedin_by).",";
		$sql.= date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkedin_date).",";
		$sql.= strip_specials($checkedout_by).",";
		$sql.= date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkedout_date).",";
		$sql.= "'".strip_specials($cctype)."',";
		$sql.= "'".strip_specials($CCnum)."',";
		$sql.= "'".strip_specials($expiry)."',";
		$sql.= "'".strip_specials($CVV)."',";
		$sql.= $book_status.",";
		$sql.= $res_det_id.")";
	}
	else{
		$sql="Update booking set ";
		$sql.="guestid=".strip_specials($guestid).",";
		$sql.="reservation_id=".strip_specials($res_id).",";
		$sql.="bill_id=".strip_specials($bill_id).",";
		$sql.="no_adults=".strip_specials($no_adults).",";
		$sql.="no_child6_12=".strip_specials($no_child6_12).",";
		$sql.="no_child1_5=".strip_specials($no_child1_5).",";
		$sql.="no_babies=".strip_specials($no_babies).",";
		$sql.="checkindate=".date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkindate).",";
		$sql.="checkoutdate=".date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkoutdate).",";
		$sql.="roomid=".strip_specials($roomid).",";
		$sql.="roomtypeid=".$roomtypeid.",";
		$sql.="rates_id=".strip_specials($ratesid).",";
		$sql.="instructions ='".strip_specials($instr)."',";
		$sql.="voucher_no ='".strip_specials($voucher_no)."',";
		$sql.="checkedin_by =".strip_specials($checkedin_by).",";
		$sql.="checkedin_date =".date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkedin_date).",";
		$sql.="checkedout_by =".strip_specials($checkedout_by).",";
		$sql.="checkedout_date =".date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkedout_date).",";
		$sql.="cctype ='".strip_specials($cctype)."',";
		$sql.="CCnum ='".strip_specials($CCnum)."',";
		$sql.="expiry ='".strip_specials($expiry)."',";
		$sql.="CVV ='".strip_specials($CVV)."',";
		$sql.="res_det_id =".$res_det_id.",";
		$sql.="book_status='".$book_status."'";
		$sql.= " where book_id=".strip_specials($bookid);
	}
	if(!$conn) $conn=connect_Hotel_db($HOST,$USER,$PASS,$DB,$PORT);
	if(!$conn) return 0;
	//print $sql ."<br/>";

	$stmt= $conn->prepare($sql);
	$results=$stmt->execute();
	$stmt=NULL;

	if($results && !$bookid){
		$bookid= $conn->lastInsertId();
		return $bookid;
	}
	return $bookid;
}
/**
 * Get the booking id by the reservation detail id
 * @ingroup BOOKING_MANAGEMENT
 * @param $resDetail_id [in] Reservation Details ID
 *
 * @return Booking id
 */
function get_bookid_by_resDetail_id($resDetail_id) {
	global $conn;
	if (!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;

	$res = 0;
	$sql = "SELECT book_id FROM booking WHERE res_det_id=".$resDetail_id;
//	print "Res id".$res_id."<br/>";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	if($row['book_id']) {
		$res = $row['book_id'];
	}
	return $res;
}
/**
 * Get the booking detail by the reservation id
 * This is the combination of user detail, country detail 
 * @ingroup BOOKING_MANAGEMENT
 * @param $res_id [in] Reservation ID
 * @param $resDetail_id [in] Reservation Details ID
 * @param $booking [in/out] result array
 *
 * @return number of elements in booking
 */
function get_booking_byresid($res_id,$resDetail_id, &$booking) {
	global $conn;
	if (!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
		
	$booking = array();
	$sql = "SELECT book_id FROM booking WHERE reservation_id=".strip_specials($res_id). "and res_det_id=".$resDetail_id;
//	print "Res id".$res_id."<br/>";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	if($row['book_id']) {
//		print "Found".$res_id."<br/>";
		return get_booking($row['book_id'], $booking);
	}
	$stmt = null;
	$res = array();
	$book_id = 0;
	$today = date("d/m/Y H:i");

	if(get_reservation($res_id, $res, $resDetail_id)) {
		
//	print "Not found load new".$res_id."<br/>";
		switch($res['status']) {
			case RES_QUOTE: {
				$book_status= BOOK_REGISTERED;
				break;
			}
			case RES_ACTIVE:
			case RES_CHECKIN: {
				$book_status= BOOK_CHECKEDIN;
				break;
			}
			case RES_CHECKOUT: {
				$book_status= BOOK_CHECKEDOUT;
				break;
			}
			case RES_CANCEL:
			case RES_EXPIRE:
			case RES_VOID:
			case RES_CLOSE: {
				$book_status= BOOK_CLOSE;
				break;
			}
			default:
				$book_status= BOOK_CHECKEDIN;
		}		
		$book_id = modify_booking(0,$res_id,$res['bill_id'],$res['guestid'],
			$res['no_adults'],$res['no_child6_12'],$res['no_child1_5'],$res['no_babies'],$res['checkindate'],
			$res['checkoutdate'],$res['roomid'],$res['roomtypeid'],$res['ratesid'], $res['instructions'],
			$_SESSION['userid'], $res['checkedin_date'], '', '',
			$res['cctype'],$res['CCnum'],$res['expiry'],$res['CVV'],$res['voucher_no'],$book_status, $resDetail_id);
			if($book_id && $res['roomid'])
				update_room_status($res['roomid'], BOOKED);
	}
	
	if(!$book_id) return 0;
	return get_booking($book_id, $booking);
}
/**
 * Get the booking detail by the guest id, first check any open reservation that is due today
 * if found, use this reservation, if not found then create a new booking filling in the guest
 * detail, but the booking will not be created like with a reservation.
 * @ingroup BOOKING_MANAGEMENT
 * @param $guestid [in] Guest ID
 * @param $booking [in/out] result array
 *
 * @return number of elements in booking
 * @todo complete this function.
 */
function get_booking_byguest($guestid, &$booking) {
	global $conn;
	if (!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$todaystart = date("d/m/Y")." 00:00";
	$todayend = date("d/m/Y")." 23:59";
	$rightnow = date("d/m/Y H:i");

	$booking = array();
	$sql = "select book_id from booking where guestid=".$guestid;
	$sql .= " and checkindate >= ".date_to_dbformat("DD/MM/YYYY HH:MI",1,$todaystart);
	$sql .= " and checkoutdate <= ".date_to_dbformat("DD/MM/YYYY HH:MI",1,$todayend);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results && ($row = $stmt->fetch())) {
		return get_booking($row['book_id'], $booking);
	}
	$guest = array();
	if(findguestbyid($guestid, $guest)) {
			$booking['book_id'] = 0;
			$booking['guestid'] = $guestid;
			$booking['reservation_id'] = 0;
			$booking['bill_id'] = 0;
			$booking['no_adults'] = 1;
			$booking['no_child1_5'] = 0;
			$booking['no_child6_12'] = 0;
			$booking['no_babies'] = 0;
			$booking['checkindate'] = $rightnow;
			$booking['checkoutdate'] = '';
			$booking['checkedin_by'] = 0;
			$booking['checkedout_by'] = 0;
			$booking['checkedout_date'] = 0;
			$booking['checkedin_date'] = '';
			$booking['cctype'] = '';
			$booking['CCnum'] = '';
			$booking['expiry'] = '';
			$booking['CVV'] = '';
			$booking['pp_no'] = $guest['pp_no'];
			$booking['idno'] = $guest['idno'];
			$booking['address'] = $guest['address'];
			$booking['town'] = $guest['town'];
			$booking['postal_code'] = $guest['postal_code'];
			$booking['phone'] = $guest['phone'];
			$booking['email'] = $guest['email'];
			$booking['mobilephone'] = $guest['mobilephone'];
			$booking['eBridgeID'] = $guest['eBridgeID'];
			$booking['IM'] = $guest['IM'];
			$booking['nationality'] = $guest['nationality'];
			$booking['countrycode'] = $guest['countrycode'];
			$booking['nation'] = Get_Country($guest['nationality']);
			$booking['country'] = Get_Country($guest['countrycode']);
			$booking['instructions'] = '';
			$booking['guestname'] = trim(trim($guest['firstname'])." ".trim($guest['middlename'])." ".trim($guest['lastname']));
	}
	return sizeof($booking);
}
/**
 * Get the booking detail
 * This is the combination of user detail, country detail 
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookid [in] Booking ID
 * @param $booking [in/out] result array
 *
 * @return number of elements in booking
 * @todo complete this function.
 */
function get_booking($bookid, &$booking) {
	global $conn;
	if (!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;

	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_booking_advProfile($bookid,$booking);
	}
	else{
		$booking = array();
		$sql = "SELECT b.book_id, b.guestid, b.reservation_id, b.bill_id, b.no_adults, b.no_child1_5, b.no_child6_12, b.res_det_id,
			b.no_babies, DATE_FORMAT(b.checkindate, '%d/%m/%Y %H:%i') AS checkin, DATE_FORMAT(b.checkoutdate, '%d/%m/%Y %H:%i') AS checkout,
			b.roomid, b.roomtypeid, b.rates_id, b.voucher_no, b.checkedin_by, DATE_FORMAT(b.checkedin_date, '%d/%m/%Y %H:%i') AS checkedin,
			b.checkedout_by, DATE_FORMAT(b.checkedout_date, '%d/%m/%Y %H:%i') AS checkedout, b.cctype, b.CCnum, b.expiry, b.CVV,b.book_status, g.lastname,g.firstname,g.middlename,
			g.pp_no, g.idno, g.street_name, g.postal_code, g.town, g.phone, g.email, g.mobilephone, g.eBridgeID, g.IM, g.nationality ,g.countrycode,
			n.country as nation, c.country, b.instructions, DATEDIFF(checkoutdate, checkindate) AS no_nights
			FROM booking AS b, guests AS g
			LEFT JOIN countries AS c ON g.countrycode = c.countrycode 
			LEFT JOIN countries AS n ON g.nationality = n.countrycode
			WHERE g.guestid = b.guestid and b.book_id=".$bookid;
		
		$stmt = $conn->prepare($sql);
		$results = $stmt->execute();
		if($results) {
			while($row = $stmt->fetch()) {
				$booking['voucher_no'] = $row['voucher_no'];
				$booking['book_id'] = $row['book_id'];
				$booking['guestid'] = $row['guestid'];
				$booking['reservation_id'] = $row['reservation_id'];
				$booking['bill_id'] = $row['bill_id'];
				$booking['no_adults'] = $row['no_adults'];
				$booking['no_child1_5'] = $row['no_child1_5'];
				$booking['no_child6_12'] = $row['no_child6_12'];
				$booking['no_babies'] = $row['no_babies'];
				$booking['checkindate'] = $row['checkin'];
				$booking['checkoutdate'] = $row['checkout'];
				$booking['checkedin_by'] = $row['checkedin_by'];
				$booking['checkedout_by'] = $row['checkedout_by'];
				$booking['checkedout_date'] = $row['checkedout'];
				$booking['checkedin_date'] = $row['checkedin'];
				$booking['no_nights'] = $row['no_nights'];
				$booking['roomid'] = $row['roomid'];
				$booking['roomno'] = get_roomno($row['roomid']);
				$booking['roomtypeid'] = $row['roomtypeid'];
				$booking['rates_id'] = $row['rates_id'];
				$booking['cctype'] = $row['cctype'];
				$booking['CCnum'] = $row['CCnum'];
				$booking['expiry'] = $row['expiry'];
				$booking['CVV'] = $row['CVV'];
				$booking['book_status'] = $row['book_status'];
				$booking['pp_no'] = $row['pp_no'];
				$booking['idno'] = $row['idno'];
				$booking['address'] = $row['street_name'];
				$booking['town'] = $row['town'];
				$booking['postal_code'] = $row['postal_code'];
				$booking['phone'] = $row['phone'];
				$booking['email'] = $row['email'];
				$booking['mobilephone'] = $row['mobilephone'];
				$booking['eBridgeID'] = $row['eBridgeID'];
				$booking['IM'] = $row['IM'];
				$booking['nationality'] = $row['nationality'];
				$booking['countrycode'] = $row['countrycode'];
				$booking['nation'] = $row['nation'];
				$booking['country'] = $row['country'];
				$booking['instructions'] = $row['instructions'];
				$booking['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
				$booking['nationality'] = $row['nationality'];
				$booking['res_det_id'] = $row['res_det_id'];
			}
		}
	}
	//print_r($booking);
	return sizeof($booking);
}
/**
 * Create the bill for the booking.
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookid [in] The booking ID
 * @param $resid [in] The reservation ID
 * @param $guestid [in] The guest ID
 * @param $userid [in] The user
 */
function create_booking_bill($bookid, $resid, $guestid, $userid){
	global $conn;

	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	// Get next invoice number
	$invno = get_nextdocumentno(1, 2,1);
	$sql="INSERT INTO bills (billno, book_id, reservation_id, date_billed, created_by, guestid, status) values(";
	$sql .= "'".$invno."',";
	$sql .= strip_specials($bookid).",";
	$sql .= strip_specials($resid).",";
	$sql .= "now(),";
	$sql .= strip_specials($userid).",";
	$sql .= strip_specials($guestid).",";
	$sql .= "1)";
	
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$bill_id = 0;
	if($results) {
		$bill_id = $conn->lastInsertId();
		$sql2 = "update booking set bill_id = ".$bill_id." where  book_id = ".strip_specials($bookid);
		$stmt2 = $conn->prepare( $sql2);
		$results = $stmt2->execute();
	}
	return $bill_id;
}
/**
 * Set the status of the reservation billing 
 *
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookid [in] Book ID
 * @param $billstatus [in] 1 billed, 0 not billed
 * 
 * @return 0 fail >0 success
 */
function update_booking_bill($bookid,$billstatus){
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql="Update booking set billed=".strip_specials($billstatus)." where book_id=".strip_specials($bookid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	return $results;
}
/**
 * Return the rate id used in a booking 
 *
 * @ingroup RES_MANAGEMENT
 * @param $bid [in] The book id
 * @return The rate id
 */
function get_bookingrate($bid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "select rates_id from booking where book_id=".strip_specials($bid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$stmt = null;
	return $row['rates_id'];
}
/**
 * Return the rate id used in a booking 
 *
 * @ingroup RES_MANAGEMENT
 * @param $bid [in] The book id
 * @return The rate id
 */
function get_bookingstatus($bid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "select book_status from booking where book_id=".strip_specials($bid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$stmt = null;
	return $row['book_status'];
}
/**
 * Return the voucher number used in a booking 
 *
 * @ingroup RES_MANAGEMENT
 * @param $bid [in] The book id
 * @return The rate id
 */
function get_bookingvoucher($bid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "select voucher_no from booking where book_id=".strip_specials($bid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$stmt = null;
	return $row['voucher_no'];
}

/**
 * Return the voucher number used in a reseration 
 *
 * @ingroup RES_MANAGEMENT
 * @param $rid [in] The reservation id
 * @return The rate id
 */
function get_reservationvoucher($rid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "select voucher_no from reservation where reservation_id=".strip_specials($rid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$stmt = null;
	return $row['voucher_no'];
}
/**
 * Return the voucher number used in a reseration 
 *
 * @ingroup RES_MANAGEMENT
 * @param $vchno [in] The voucher number
 * @return The Reservation id
 */
function get_ReservationID_By_VoucherNo($vchno) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "SELECT reservation_id FROM reservation WHERE voucher_no='".strip_specials($vchno)."'";
	$stmt = $conn->prepare($sql);
	
	//echo "\n sql -->".$sql;
	$results = $stmt->execute();
	$resid=0;
	if($results){
		$row = $stmt->fetch();
		$resid = $row['reservation_id'];
	}
	
	$stmt = null;
	return $resid;
}
/**
 * Return the reservation ID number for the Bill ID
 * @ingroup RES_MANAGEMENT
 * @param $billid [in] The bill ID
 * @return The Reservation id
 */
function get_ReservationID_By_BillID($billid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$resid=0;
	$sql = "SELECT reservation_id FROM bills WHERE bill_id=".strip_specials($billid);
	$stmt = $conn->prepare($sql);
	
	//echo "\n sql -->".$sql;
	$results = $stmt->execute();
	if($results){
		$row = $stmt->fetch();
		$resid = $row['reservation_id'];
	}
	
	$stmt = null;
	return $resid;
}
/**
 * Return the rate id used in a reservation 
 *
 * @ingroup RES_MANAGEMENT
 * @param $resid [in] The reservation id
 * @return The rate id
 */
function get_reservationrate($resid) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	$sql = "select ratesid from reservations where reservation_id=".strip_specials($resid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	$stmt = null;
	return $row['ratesid'];
}
/**
 * Get the text string for the reservation status code
 * @ingroup RES_MANAGEMENT
 * 
 * @param $sts [in] The res status code
 *
 * @return Reservation status string
 */
function get_res_status_text($sts) {
	global $_L;
	$res = "";

	if($sts == RES_QUOTE) $res = $_L['RSV_quote'];
	if($sts == RES_ACTIVE) $res = $_L['RSV_active'];
	if($sts == RES_CANCEL) $res = $_L['RSV_cancelled'];
	if($sts == RES_EXPIRE) $res = $_L['RSV_expired'];
	if($sts == RES_CHECKIN) $res = $_L['RSV_checkin'];
	if($sts == RES_VOID) $res = $_L['RSV_void'];
	if($sts == RES_CHECKOUT) $res = $_L['RSV_checkout'];
	
	return $res;
}
/**
 * Get the text string for the book status code
 * @ingroup RES_MANAGEMENT
 * 
 * @param $sts [in] The res status code
 *
 * @return Reservation status string
 */
function get_book_status_text($sts) {
	global $_L;
	$res = "";

	if($sts == BOOK_REGISTERED) $res = $_L['REG_registered'];
	if($sts == BOOK_CHECKEDIN) $res = $_L['REG_checkedin'];
	if($sts == BOOK_CHECKEDOUT) $res = $_L['REG_checkedout'];
	if($sts == BOOK_BILLED) $res = $_L['REG_billed'];
	if($sts == BOOK_CLOSE) $res = $_L['REG_close'];

	return $res;
}

/**
 * Get the text string for the general status code
 * @ingroup RES_MANAGEMENT
 * 
 * @param $sts [in] The res status code
 *
 * @return General status string
 */
function get_general_status_text($sts) {
	global $_L;
	$res = "";
	if ($sts == STATUS_OPEN) $res = $_L['STS_open'];
	if ($sts == STATUS_CLOSED) $res = $_L['STS_closed'];
	if ($sts == STATUS_CANCEL) $res = $_L['STS_cancel'];
	if ($sts == STATUS_VOID) $res = $_L['STS_void'];

	return $res;
}
/**
 * Get the list of reservations
 *
 * @ingroup RES_MANAGEMENT
 * @param $start [in] Start window date for reservations
 * @param $end [in] End window date for reservations
 * @param $name [in] guest name to search
 * @param $rlist [in/out] list of reservations
 * @param $active [in] open 
 * @param $vouchernum [in] voucher number
 *
 * $rlist['reservation_id']['guestname']
 * $rlist['reservation_id']['checkindate']
 * $rlist['reservation_id']['checkoutdate']
 * $rlist['reservation_id']['voucher_no']
 * $rlist['reservation_id']['no_nights']
 * $rlist['reservation_id']['no_pax']
 */
function get_reservationlist($start, $end, $name, &$rlist, $active = 0, $vouchernum = "") {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_reservationlist_advProfile($start, $end, $name, $rlist, $active, $vouchernum);
	}
	else{
		if($name) $name = strip_specials($name);
		$sql = "select src, reservation.guestid, reservation_id,
				DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') as checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') as checkout, no_adults,
		        no_child1_5, no_child6_12, no_babies, roomid, roomtypeid, ratesid, voucher_no, 
				reserved_by, DATE_FORMAT(reserved_date, '%d/%m/%Y') as resdate, confirmed_by, DATE_FORMAT(confirmed_date, '%d/%m/%Y') as conf_date, 
				DATE_FORMAT(reserve_time, '%d/%m/%Y %H:%i') as restime, 
				book_id, status, firstname, middlename, lastname, loginname, DATEDIFF(checkoutdate, checkindate) AS no_nights,
				booked_by_ebridgeid, cancelled_by_ebridgeid, cancelled_date
				from reservation, guests, users 
				where reservation.guestid = guests.guestid and users.userid = reservation.reserved_by ";
		if($name) {
			$sql .=	" and ( firstname like '%".$name."%' or lastname like '%".$name."%' )";
		}
		if($start) {
			$sql .=	" and checkindate >= ".date_to_dbformat("DD/MM/YYYY",1,$start);
		}
		if($end) {
			$sql .=	" and checkoutdate <= ".date_to_dbformat("DD/MM/YYYY",1,$end);
		}
		if($active) {
			$sql .=	" and `status` = ".$active;
		}
		if($vouchernum) {
			$sql .=	" and `voucher_no` like '%".$vouchernum."%' ";
		}
		$sql .= " order by checkindate asc";
		//print $sql."<br/>";
		//echo $sql;
		$stmt = $conn->prepare( $sql);
		$results = $stmt->execute();
		$rlist = array();
		if($results) {
			while($row = $stmt->fetch()) {
				$rlist[$row['reservation_id']]['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
				$rlist[$row['reservation_id']]['checkindate'] = $row['checkin'];
				$rlist[$row['reservation_id']]['checkoutdate'] = $row['checkout'];
				$rlist[$row['reservation_id']]['no_pax'] = $row['no_adults'] + $row['no_child1_5'] + $row['no_child6_12'] + $row['no_babies'];
				$rlist[$row['reservation_id']]['voucher_no'] = $row['voucher_no'];
				$rlist[$row['reservation_id']]['no_nights'] = $row['no_nights'];
				$rlist[$row['reservation_id']]['status'] = $row['status'];
				$rlist[$row['reservation_id']]['booked_by_ebridgeid'] = $row['booked_by_ebridgeid']; 
				$rlist[$row['reservation_id']]['cancelled_by_ebridgeid'] = $row['cancelled_by_ebridgeid']; 
				$rlist[$row['reservation_id']]['cancelled_date'] = $row['cancelled_date']; 
				$rlist[$row['reservation_id']]['reservation_id'] = $row['reservation_id']; 
			}
		}
	}
	//print_r($rlist);
	return sizeof($rlist);
}
/**
 * Get the list of reservations
 *
 * @ingroup RES_MANAGEMENT
 * @param $start [in] Start window date for reservations
 * @param $end [in] End window date for reservations
 * @param $roomtype [in] room type
 * @param $rlist [in/out] list of reservations
 * @param $active [in] open 
 *
 * $rlist['reservation_id']['guestname']
 * $rlist['reservation_id']['checkindate']
 * $rlist['reservation_id']['checkoutdate']
 * $rlist['reservation_id']['voucher_no']
 * $rlist['reservation_id']['no_nights']
 * $rlist['reservation_id']['no_pax']
 */
function get_reservation_bydateandtype($start, $end, $roomtype, &$rlist, $active) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;

	if($name) $name = strip_specials($name);
	$sql = "select reservation_id, DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') as checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') as checkout,
			roomtypeid, book_id, `status`, DATEDIFF(checkoutdate, checkindate) AS no_nights 
			from reservation
			where status = " .$active;
	if($start) {
		$sql .=	" and checkindate = '". $start ."'";
		//echo $start ."\n";
	}
	if($end) {
		$sql .=	" and checkoutdate = '". $end ."'";
		//echo $end ."\n";
	}
	if($roomtype) {
		$sql .=	" and roomtypeid = '". get_roomtypeid($roomtype) ."'";
		//echo $roomtype ."\n";
	}

			
	
	//$sql .= " order by checkindate  asc";
	//echo $sql ."\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$rlist = array();
	if($results) {
		while($row = $stmt->fetch()) {
			$rlist[$row['reservation_id']]['checkindate'] = $row['checkin'];
			$rlist[$row['reservation_id']]['checkoutdate'] = $row['checkout'];
			$rlist[$row['reservation_id']]['status'] = $row['status'];
			$rlist[$row['reservation_id']]['roomtype'] = get_roomtype($row['roomtypeid']); 
		}
	}
	//print_r($rlist);
	return sizeof($rlist);
}
/**
 * Retrieve the reservations for the specified reservation id.
 *
 * @ingroup RES_MANAGEMENT
 * @param $resid [in] Reservation ID (required)
 * @param $res [in/out] Reservation array data
 * @param $resDetail_id [in] reservation detail id
 * @return number of elements in res
 */
function get_reservation($resid, &$res, $resDetail_id=0) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(!$resid) return 0;
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_reservation_advProfile($resid,$res, $resDetail_id);
	}
	else{
		if(!$resDetail_id) {
			$sql = "select src, reservation.fop, reservation.amt, reservation.guestid, reservation.phone, reservation_by, reservation_by_phone, 
				DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') as checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') as checkout, no_adults,
		        no_child1_5, no_child6_12, no_babies, roomid, roomtypeid, ratesid, instructions, agentid, voucher_no, 
				reserved_by, DATE_FORMAT(reserved_date, '%d/%m/%Y') as resdate, confirmed_by, DATE_FORMAT(confirmed_date, '%d/%m/%Y') as conf_date, 
				DATE_FORMAT(reserve_time, '%d/%m/%Y %H:%i') as restime, cctype, CCnum, expiry, CVV, bill_id,
				book_id, status, firstname, middlename, lastname, loginname, DATEDIFF(checkoutdate, checkindate) AS no_nights,
				booked_by_ebridgeid, cancelled_by_ebridgeid, cancelled_date 
				from reservation, guests, users 
				where reservation.guestid = guests.guestid and users.userid = reservation.reserved_by and reservation_id = ".strip_specials($resid);
		} else {
			$sql = "select src, reservation.fop, reservation.amt, reservation.guestid, reservation.phone, reservation_by, reservation_by_phone, 
				DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') as checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') as checkout, no_adults,
		        no_child1_5, no_child6_12, no_babies, reservation_details.roomid, reservation_details.roomtypeid, reservation_details.ratesid, instructions, agentid, voucher_no, 
				reserved_by, DATE_FORMAT(reserved_date, '%d/%m/%Y') as resdate, confirmed_by, DATE_FORMAT(confirmed_date, '%d/%m/%Y') as conf_date, 
				DATE_FORMAT(reserve_time, '%d/%m/%Y %H:%i') as restime, cctype, CCnum, expiry, CVV, bill_id,
				book_id, reservation.status, firstname, middlename, lastname, loginname, DATEDIFF(checkoutdate, checkindate) AS no_nights,
				booked_by_ebridgeid, cancelled_by_ebridgeid, cancelled_date 
				from reservation, reservation_details, guests, users 
				where reservation.guestid = guests.guestid and users.userid = reservation.reserved_by and 
				reservation_details.reservation_id = reservation.reservation_id 
				and reservation.reservation_id = ".strip_specials($resid)." and reservation_details.id=".$resDetail_id;
			
		}
	//	print $sql."<br/>";	
		$stmt = $conn->prepare( $sql);
		$results = $stmt->execute();
		$res = array();
		if($results) {
			$row = $stmt->fetch();
			$res['src'] = $row['src'];
			$res['guestid'] = $row['guestid'];
			$res['phone'] = $row['phone'];
			$res['reservation_by'] = $row['reservation_by'];
			$res['reservation_by_phone'] = $row['reservation_by_phone'];
			$res['checkindate'] = $row['checkin'];
			$res['checkoutdate'] = $row['checkout'];
			$res['no_adults'] = $row['no_adults'];
			$res['no_child1_5'] = $row['no_child1_5'];
			$res['no_child6_12'] = $row['no_child6_12'];
			$res['no_babies'] = $row['no_babies'];
			$res['roomid'] = $row['roomid'];
			$res['roomtypeid'] = $row['roomtypeid'];
			$res['ratesid'] = $row['ratesid'];
			$res['instructions'] = $row['instructions'];
			$res['agentid'] = $row['agentid'];
			$res['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
			$res['voucher_no'] = $row['voucher_no'];
			$res['reserved_by'] = $row['reserved_by'];
			// This mapping reverses the name from the DB... because it was this way and I didn't 
			// notice before writing all of the other code. so just map it here.
			$res['reserved_date'] = $row['resdate'];
			$res['reserved_name'] = $row['loginname'];
			$res['confirmed_by'] = $row['confirmed_by'];
			$res['confirmed_date'] = $row['conf_date'];
			$res['reserve_time'] = $row['restime'];
			$res['cctype'] = $row['cctype'];
			$res['CCnum'] = $row['CCnum'];
			$res['expiry'] = $row['expiry'];
			$res['CVV'] = $row['CVV'];
			$res['book_id'] = $row['book_id'];
			$res['bill_id'] = $row['bill_id'];
			$res['status'] = $row['status'];
			// calculate the number of nights before return;
			$res['no_nights'] = $row['no_nights'];
			$res['reservation_id'] = $resid;
			$res['fop'] = $row['fop'];
			$res['amt'] = $row['amt'];
			$res['booked_by_ebridgeid'] = $row['booked_by_ebridgeid'];
			$res['cancelled_by_ebridgeid'] = $row['cancelled_by_ebridgeid'];
			$res['cancelled_date'] = $row['cancelled_date'];
		}
	}
	return sizeof($res);
}
/**
 * Insert or update an existing reservation
 *
 * @ingroup RES_MANAGEMENT
 * @param $resid [in] reservation id for update, 0 if new reservation
 * @param $src [in] reservation src character for tracking purposes
 * @param $guestid [in] guest ID for booking profile
 * @param $phone [in] Guest contact phone to use
 * @param $vchr [in] Voucher number - auto generate if not set.
 * @param $agentid [in] Travel Agent ID - 0 if not used
 * @param $res_by [in] Name of person calling for reservation
 * @param $res_by_phone [in] phone reservation data
 * @param $checkin [in] reservation checkin date - required
 * @param $checkout [in] reservation checkout date - required
 * @param $no_adults [in] cout of adults
 * @param $no_child1_5 [in] count of infants 0 5
 * @param $no_child6_12 [in] count of children 6 to 12
 * @param $no_babies [in] count of babies
 * @param $instructions [in] text instructions
 * @param $ccnum [in] Credit card number 
 * @param $cctype [in] Credit card type, AX, VI, DC, CA, etc
 * @param $expiry [in] Credit card expiry MMYY
 * @param $cvv [in] Credit card verification code 99999
 * @param $userid [in] id of person making the reservation
 * @param $reserved_date [in] date reservations was made
 * @param $confirmed_by [in] id of user that confirmed reservations
 * @param $confirmed_date [in] date that reservation was confirmed
 * @param $roomid [in] Room id 0 if not used 
 * @param $roomtypeid [in] Room type id, 0 if not used
 * @param $ratesid [in] Rates ID
 * @param $restime [in] The date time the reservation was stored in system.
 * @param $book_id [in] Booking id for linked bookings
 * @param $status [in] Reservation status;
 * @param $bill_id [in] invoice id for linked invoice.
 * @param $fop [in] The form of payment
 * @param $amt [in] The guarantee amount
 * @param $booked_by_ebridgeid [in] The ebridge id from which the booking information is received
 * @param $cancelled_by_ebridgeid [in] The ebridge id from which the cancellation request is received
 * @param $cancelled_date [in] The cancelled date and time
 * @note at least one of <i>roomid</i>, <i>roomtypeid</i>, <i>ratesid </i> must be selected <br/>
 * at least 1 adult or child 6 - 12 per room required <br/>
 * 
 */
function modify_reservation($resid, $src, $guestid, $phone, $vchr, $agentid,
		$res_by,$res_by_phone, $checkin, $checkout, 
		$no_adults, $no_child1_5, $no_child6_12, $no_babies,
		$instructions,$ccnum, $cctype, $expiry, $cvv,
		$userid,$reserved_date,$confirmed_by, $confirmed_date, 
		$roomid, $roomtypeid, $ratesid, $restime, $book_id, $status, $bill_id,$fop=0,$amt=0,$booked_by_ebridgeid="",$cancelled_by_ebridgeid="",$cancelled_date="") {

	// check the mandatories.
//	print "check in".$checkin;
//	if(strlen($checkin) == 0) return 0;
//	print "found checkin<br/>";
//	if(!$checkout) return 0;
//	print "found checkout<br/>";
	if(!$guestid) return 0;
//	print "found guestid<br/>";

	if(!$userid) return 0;
//	print "found userid<br/>";

	if(!$confirmed_by) $confirmed_by = 0;
	if(!$agentid) $agentid = 0;
	if(!$bill_id) $bill_id = 0;
	if(!$roomid) $roomid = 0;
	if(!$roomtypeid) $roomtypeid = 0;
	if(!$ratesid) $ratesid = 0;
	if(!$status) $status = 0;
	if(!$fop) $fop = 0;
	if(!$amt) $amt = 0;
	
	// must have a room, type or rate selected.
	//print "Rates ID ratesid ".$ratesid." roomtype ".$roomtypeid." room ".$roomid."<br/>\n";
	//if(!($ratesid + $roomtypeid + $roomid)) return 0;
	if(!$no_child1_5) $no_child1_5 = 0;
	if(!$no_child6_12) $no_child6_12 = 0;
	if(!$no_babies) $no_babies = 0;
	if(!$no_adults && !$no_child6_12 ) $no_adults = 1;
	// must have 1 person in the room, adult or 6-12 (no under 5 alone).
	if(!($no_adults + $no_child6_12)) return 0;
	
	// if no voucher number or new reservation, get a new voucher number
	// and update the document numbers.
	if(!$vchr || !$resid ) { $vchr = get_nextdocumentno(1, 1, 1); }
	
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	// now() is the mysql current date time function.
	if( !$reserved_date) {
		$reserved_date = 'now()';
	} else {
		$tempdate=str_replace('/','-',$reserved_date);
		$datein = $tempdate;
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $datein))
			$reserved_date = "'".$reserved_date."'";
		else
			$reserved_date =date_to_dbformat("DD/MM/YYYY",1,$reserved_date);
	}
	if(!$restime) {
		$restime = 'now()';
	} else {
		$datein=str_replace('/','-',$restime);
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-5][0-9]):([0-5][0-9])$/', $datein))
			$restime = "'".$restime."'";
		else
			$restime = date_to_dbformat("DD/MM/YYYY HH:MI",1,$restime);
	}
	//checkin date
	if($checkin){
		//	echo "Checkin: ".$checkin."\n";
		$tempdate=str_replace('/','-',$checkin);
		$datein = $tempdate;
		//$datein='2012-05-15';
		//echo "Datein: ".$datein."\n";
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-5][0-9]):([0-5][0-9]):([0-5][0-9])$/', $datein)){
			$checkin = "'".$checkin."'";
		}else if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $datein)){
			$checkin = "'".$checkin."'";
		}else{			
			$checkin = date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkin);
		}
	}
	//echo "Checkin22222222: ".$checkin."\n";
	//checkout date
	if ($checkout){
		$tempdate=str_replace('/','-',$checkout);
		$datein =$tempdate;
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) ([0-5][0-9]):([0-5][0-9]):([0-5][0-9])$/', $datein))
			$checkout = "'".$checkout."'";
		else if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $datein))
			$checkout = "'".$checkout."'";
		else
			$checkout = date_to_dbformat("DD/MM/YYYY HH:MI",1,$checkout);
	}
	//if($confirmed_date) $confirmed_date = date_to_dbformat("DD/MM/YYYY",1,$confirmed_date);
	if(!$confirmed_date) {
		$confirmed_date = "'0000-00-00 00:00:00'";
	} else {
		$confirmed_date = date_to_dbformat("DD/MM/YYYY HH:MI",1,$confirmed_date);
	}
	
	if(!empty($instructions)){
		$instructions = str_replace("'", "''", $instructions);		
	}
	// @todo verify the formats of the dates to ensure they are in the correct format
//	print "Ok<br/>";
	if(!$resid) {
		if(!$cancelled_date){
			$cancelled_date="0000-00-00";
		}
		$sql="INSERT INTO reservation (src,guestid,phone,reservation_by,reservation_by_phone,";
		$sql .= "checkindate,checkoutdate,no_adults,no_child1_5,no_child6_12,no_babies,roomid,";
		$sql .= "roomtypeid, ratesid,instructions,agentid,voucher_no,";
		$sql .= "reserved_by,reserved_date,confirmed_by,confirmed_date,";
		$sql .= "book_id,`status`,reserve_time,cctype,CCnum,expiry,CVV, bill_id, fop, amt,";
		$sql .= "booked_by_ebridgeid,cancelled_by_ebridgeid,cancelled_date";
		$sql .= ") ";
		$sql .= " VALUES(";
		$sql .= "'".strip_specials($src)."',";
		$sql .= strip_specials($guestid).",";
		$sql .= "'".strip_specials($phone)."',";
		$sql .= "'".strip_specials($res_by)."',";
		$sql .= "'".strip_specials($res_by_phone)."',";
		$sql .= $checkin.",";
		$sql .= $checkout.",";
		$sql .= strip_specials($no_adults).",";
		$sql .= strip_specials($no_child1_5).",";
		$sql .= strip_specials($no_child6_12).",";
		$sql .= strip_specials($no_babies).",";
		$sql .= strip_specials($roomid).",";
		$sql .= strip_specials($roomtypeid).",";
		$sql .= strip_specials($ratesid).",";
		$sql .= "'".strip_specials($instructions)."',";
		$sql .= strip_specials($agentid).",";
		$sql .= "'".strip_specials($vchr)."',";
		$sql .= strip_specials($userid).",";
		$sql .= $reserved_date.",";
		$sql .= strip_specials($confirmed_by).",";
		$sql .= $confirmed_date.",";
		$sql .= "0,"; // no booking (checkin) yet
		$sql .= $status.","; 
		$sql .= $restime.","; // reservation time is now.
		$sql .= "'".strip_specials($cctype)."',";
		$sql .= "'".strip_specials($ccnum)."',";
		$sql .= "'".strip_specials($expiry)."',";
		$sql .= "'".strip_specials($cvv)."',";
		$sql .= strip_specials($bill_id).",";
		$sql .= strip_specials($fop).",";
		$sql .= strip_specials($amt).",";
		$sql .= "'".strip_specials($booked_by_ebridgeid)."',";
		$sql .= "'".strip_specials($cancelled_by_ebridgeid)."',";
		$sql .= "'".strip_specials($cancelled_date)."')";
		
	} else {
		$comma = "";
		$sql = "UPDATE reservation SET ";
		if($src) {
			$sql .= "src ='".strip_specials($src)."'";
			$comma = ",";
		}
		if($guestid) {
			$sql .= $comma."guestid =".$guestid;
			$comma = ",";
		}
		if($phone) {
			$sql .= $comma."phone ='".strip_specials($phone)."'";
			$comma = ",";
		}
		if($res_by) {
			$sql .= $comma."reservation_by ='".strip_specials($res_by)."'";
			$comma = ",";
		}
		if($res_by_phone) {
			$sql .= $comma."reservation_by_phone ='".strip_specials($res_by_phone)."'";
			$comma = ",";
		}
		if($resdate) {
			$sql .= $comma."reserved_date =".$reserved_date;
			$comma = ",";
		}
		if($checkin) {
			$sql .= $comma."checkindate =".$checkin;
			$comma = ",";
		}
		if($checkout) {
			$sql .= $comma."checkoutdate =".$checkout;
			$comma = ",";
		}
		if(!empty($no_adults)) {
			$sql .= $comma."no_adults =".strip_specials($no_adults);
			$comma = ",";
		}
		if(!empty($no_child1_5)) {
			$sql .= $comma."no_child1_5 =".strip_specials($no_child1_5);
			$comma = ",";
		}
		if(!empty($no_child6_12)) {
			$sql .= $comma."no_child6_12 =".strip_specials($no_child6_12);
			$comma = ",";
		}
		if(!empty($no_babies)) {
			$sql .= $comma."no_babies =".strip_specials($no_babies);
			$comma = ",";
		}
		if($roomid) {
			$sql .= $comma."roomid =".strip_specials($roomid).",ratesid = '0', roomtypeid = '0'" ;
			$comma = ",";
		}
		if($roomtypeid) {
			$sql .= $comma."roomtypeid =".strip_specials($roomtypeid)." ,roomid = '0', ratesid = '0' ";
			$comma = ",";
		}
		if($status) {
			$sql .= $comma."`status` =".strip_specials($status)."";
			$comma = ",";
		}
		if($ratesid) {
			$sql .= $comma."ratesid =".strip_specials($ratesid).",roomid = '0', roomtypeid = '0'" ;
			$comma = ",";
		}
		if($instructions) {
			$sql .= $comma."instructions ='".strip_specials($instructions)."'";
			$comma = ",";
		}
		if($agentid) {
			$sql .= $comma."agentid =".strip_specials($agentid);
			$comma = ",";
		}
		if($vchr) {
			$sql .= $comma."voucher_no ='".strip_specials($vchr)."'";
			$comma = ",";
		}
		if($ccnum) {
			$sql .= $comma."CCnum ='".strip_specials($ccnum)."'";
			$comma = ",";
		}
		if($cctype) {
			$sql .= $comma."cctype ='".strip_specials($cctype)."'";
			$comma = ",";
		}
		if($expiry) {
			$sql .= $comma."expiry ='".strip_specials($expiry)."'";
			$comma = ",";
		}
		if($cvv) {
			$sql .= $comma."CVV ='".strip_specials($cvv)."'";
			$comma = ",";
		}
		if($userid) {
			$sql .= $comma."reserved_by =".strip_specials($userid);
			$comma = ",";
		}
		if($reserved_date) {
			$sql .= $comma."reserved_date =".$reserved_date;
			$comma = ",";
		}
		if($confirmed_by) {
			$sql .= $comma."confirmed_by =".strip_specials($confirmed_by);
			$comma = ",";
		}
		if($confirmed_date != null) {
			$sql .= $comma."confirmed_date =".$confirmed_date;
			$comma = ",";
		}
		if($bill_id) {
			$sql .= $comma."bill_id =".strip_specials($bill_id);
			$comma = ",";
		}
		if($book_id) {
			$sql .= $comma."book_id =".strip_specials($book_id);
			$comma = ",";
		}
		if($resid) {
			$sql .= $comma."fop =".strip_specials($fop);
			$comma = ",";
		}
		if($resid) {
			$sql .= $comma."amt =".strip_specials($amt);
			$comma = ",";
		}
		if($booked_by_ebridgeid) {
			$sql .= $comma."booked_by_ebridgeid ='".strip_specials($booked_by_ebridgeid)."'";
			$comma = ",";
		}
		if($cancelled_by_ebridgeid) {
			$sql .= $comma."cancelled_by_ebridgeid ='".strip_specials($cancelled_by_ebridgeid)."'";
			$comma = ",";
		}
		if($cancelled_date != null && !empty($cancelled_date)) {
			$sql .= $comma."cancelled_date ='".strip_specials($cancelled_date)."'";
			$comma = ",";
		}
		$sql .= " WHERE reservation_id = ".strip_specials($resid);
	}

	//print $sql."<br/>";
	//echo "\n SQL-->".$sql;
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$stmt = NULL;

	if($results && !$resid) {
		$resid = $conn->lastInsertId();;
		return $resid;
	}
	return $resid;
}	
/**
 * function to update reservation cancel details
 * @param $resid [in] The reservation  id
 * @param $cancelled_by_ebridgeid [in] The ebridge id from which the cancel request was sent
 * @param $canceldate [in] The cancellation date
 */
function update_cancelDetail($resid,$cancelled_by_ebridgeid,$canceldate=""){
	global $conn;

	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(empty($canceldate)){
		$canceldate = date("Y-m-d H:i");
	}
	$sql = "UPDATE reservation SET ";
	$sql .= "cancelled_by_ebridgeid ='".strip_specials($cancelled_by_ebridgeid)."',";
	$sql .= "cancelled_date ='".strip_specials($canceldate)."'";
	$sql .= " WHERE reservation_id = ".strip_specials($resid);
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$stmt = NULL;
}

/**
 * Create the bill for the reservation.
 * @ingroup RES_MANAGEMENT
 * @param $resid [in] The reservation ID.
 * @param $userid [in] The booking agent id.
 * @param $guestid [in] The guest id.
 */
function create_reservation_bill($resid, $userid, $guestid) {
	global $conn;

	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	// Get next invoice number
	$invno = get_nextdocumentno(1, 2,1);
	$sql="INSERT INTO bills (book_id, billno, reservation_id, date_billed, created_by, guestid, status) values(0,";
	$sql .= "'".$invno."',";
	$sql .= strip_specials($resid).",";
	$sql .= "now(),";
	$sql .= strip_specials($userid).",";
	$sql .= strip_specials($guestid).",";
	$sql .= "1)";
	
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$bill_id = 0;
	if($results) {
		$bill_id = get_bill_id($invno);
		//echo "bill id: ".$bill_id."<br/>";
		$sql2 = "update reservation set bill_id = ".$bill_id." where  reservation_id = ".strip_specials($resid);
		$stmt2 = $conn->prepare( $sql2);
		$results = $stmt2->execute();
		//echo  $sql2;
	}
	return $bill_id;
}
/**
 * Get the status of a reservation
 * @ingroup RES_MANAGEMENT
 * 
 * @param $resid [in] ID of the reservation 
 * @return 0 fail >0 success
 */
function get_reservation_status($resid){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Select status from reservation where reservation_id=".strip_specials($resid);
//	print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$status = $row['status'];
	}
	return $status;

}
/**
 * Get the first id of a checkin
 * @ingroup RES_MANAGEMENT
 * 
 * @param $resid [in] ID of the reservation 
 * @return 0 fail >0 success
 */
function get_book_id($resid){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Select book_id from booking, reservation_details where booking.reservation_id=".strip_specials($resid)." OR ( reservation_details.reservation_id=".strip_specials($resid). " AND booking.res_det_id = reservation_details.id )" ;
//	print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$book_id = $row['book_id'];
	}
	return $book_id;
}
/**
 * Get the billid using the bill number
 * @ingroup RES_MANAGEMENT
 * @param $billno [in] The bill number 
 * @return billid
 */
function get_bill_id($billno){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Select bill_id from bills where billno='".$billno."'";
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$bill_id = $row['bill_id'];
	}
	return $bill_id;
}
/**
 * Get the id of a reservation
 * @ingroup RES_MANAGEMENT
 * 
 * @param $book_id [in] book ID
 * @return 0 fail >0 success
 */
function get_reservation_id($book_id){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Select reservation_id from booking where book_id=".strip_specials($book_id);
//	print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$res_id = $row['reservation_id'];
	}
	return $res_id;
}
/**
 * Change the status of a reservation
 * @ingroup RES_MANAGEMENT
 * 
 * @param $resid [in] ID of the reservation 
 * @param $status [in] status of the reservation.
 * @return 0 fail >0 success
 * @see STATUS_DEFS
 */
function update_reservation_status($resid, $status){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Update reservation set status='".strip_specials($status)."' where reservation_id=".strip_specials($resid);
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return $results;

}
/**
 * Change the status of a booking
 * @ingroup RES_MANAGEMENT
 * 
 * @param $book_id [in] book ID
 * @param $status [in] status of the reservation.
 * @return 0 fail >0 success
 */
function update_booking_status($book_id, $status){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Update booking set book_status='".strip_specials($status)."' where book_id=".strip_specials($book_id);
//	print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return $results;

}
/**
 * Change the status of a room
 * @ingroup ROOM_MANAGEMENT
 * 
 * @param $roomid [in] ID of the room from the rooms table
 * @param $status [in] status of the room.
 * @return 0 fail >0 success
 */
function update_room_status($roomid, $status){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="Update rooms set status='".strip_specials($status)."' where roomid=".strip_specials($roomid);
//	print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return $results;
}
/**
 * Find the  details about a specific room.
 * @ingroup ROOM_MANAGEMENT
 * @param $roomno [in] Room number
 * @param $res [in,out] Room structure result
 *
 * @return count elements in room details structure.
 */
function find_room($roomno, &$res) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="select rooms.roomid,rooms.roomno,rooms.roomtypeid,roomtype.roomtype,rooms.roomname, rooms.occupancy,rooms.bedcount,
		rooms.noofrooms,rooms.bedtype1,rooms.bedtype2,rooms.bedtype3,rooms.bedtype4,rooms.status, rooms.photo, rooms.filetype, rooms.rateid, rooms.rateid as ratesid
			from rooms, roomtype where rooms.roomtypeid = roomtype.roomtypeid 
			and roomno='".strip_specials($roomno)."'";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$rooms = array();
	$rooms=$stmt->fetch();
	$res = array();
	$res = $rooms;
//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return sizeof($res);
}
/**
 * Get room by room id
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] Room id
 * @param $room [in,out] Room structure result
 *
 * @return count elements in room details structure.
 */
function get_room($roomid, &$room) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="select rooms.roomid,rooms.roomno,rooms.roomtypeid,roomtype.roomtype,rooms.roomname, rooms.occupancy,rooms.bedcount,
		rooms.noofrooms,rooms.bedtype1,rooms.bedtype2,rooms.bedtype3,rooms.bedtype4,rooms.status, rooms.photo, rooms.filetype, rooms.rateid, rooms.rateid as ratesid
			from rooms, roomtype where rooms.roomtypeid = roomtype.roomtypeid 
			and roomid=".strip_specials($roomid);

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$rooms=$stmt->fetch();
	$room = array();
	$room = $rooms;
//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return sizeof($room);
}


/**
 * Get list of room numbers
 * @ingroup ROOM_MANAGEMENT
 *
 * @return count elements in room details structure.
 */
function get_roomnolist() {
	global $conn;
	$rmnoList = array();
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="select roomno
			from rooms";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$i=0;
	while($row = $stmt->fetch()) {
		//		print "found ".$row['itemid'] ." - ".$row['description']."<br/>\n";
		
		$rmnoList[$i] =  $row['roomno'];
		$i++;
	}
	

//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return $rmnoList;
}
/**
 * Get room numbers by roomtypeid
 * @ingroup ROOM_MANAGEMENT
 * @param $roomtype [in] Room Type ID
 * @param $room [in,out] Room structure result
 *
 * @return count elements in room details structure.
 */
function get_roombyroomtype($roomtype, &$room) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="SELECT rooms.roomid,rooms.roomno,rooms.roomtypeid,roomtype.roomtype,rooms.roomname, rooms.occupancy,rooms.bedcount,
		rooms.noofrooms,rooms.bedtype1,rooms.bedtype2,rooms.bedtype3,rooms.bedtype4,rooms.status, rooms.photo, rooms.filetype, rooms.rateid, rooms.rateid as ratesid
			FROM rooms, roomtype WHERE rooms.roomtypeid = roomtype.roomtypeid ";
	if($roomtype)
		$sql .=	" and roomtype.roomtype = '".$roomtype."'";
	//echo $sql ."\n";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	//$rooms = $stmt->fetch();
	$room = array();
	if($results) {
		while($row = $stmt->fetch()) {
			$room[$row['roomid']]['roomtype'] = $row['roomtype'];
			$room[$row['roomid']]['roomid'] = $row['roomid'];
			$room[$row['roomid']]['roomno'] = $row['roomno'];
			$room[$row['roomid']]['status'] = $row['status'];
		}
	}
//	print_r($room);
//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return sizeof($room);
}


/**
 * Get room number by rooms by book id
 * @ingroup ROOM_MANAGEMENT
 * @param $bookid [in] Invoice id
 * @param $rooms [in,out] Rooms list
 * @note rooms in form of: <br/>
 * rooms['roomid']['roomno'] = room number value<br/>
 * rooms['roomid']['roomname'] = room name value<br/>
 *
 * @return count elements in room details structure.
 */
function get_roomnos_by_booking($bookid, &$rooms) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="SELECT roomid, roomno, roomname FROM rooms WHERE roomid IN 
	(SELECT roomid FROM booking WHERE book_id IN 
		(SELECT book_id FROM booking WHERE book_id = ".strip_specials($bookid)." or ( reservation_id > 0 and reservation_id IN 
			(SELECT reservation_id FROM booking WHERE book_id=".strip_specials($bookid)."))))";
	//echo $sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$rooms=array();
	if($results) {
		while($row=$stmt->fetch()) {
			$rooms[$row['roomid']]['roomno']=$row['roomno'];
			$rooms[$row['roomid']]['roomname'] = $row['roomname'];
		}
	}
	return sizeof($rooms);
}
/**
 * Get room number by room id
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] Room id
 *
 * @return count elements in room details structure.
 */
function get_roomno($roomid) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="select roomno
			from rooms
			where roomid='".strip_specials($roomid)."'";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$room=$stmt->fetch();

//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return $room['roomno'];
}
/**
 * Get room number by room id
 * @ingroup ROOM_MANAGEMENT
 * @param $roomno [in] Room id
 *
 * @return count elements in room details structure.
 */
function get_roomid($roomno) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="select roomid
			from rooms
			where roomno='".strip_specials($roomno)."'";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$room=$stmt->fetch();

//	print "Result ".sizeof($rooms).$sql."<br/>\n";
	return $room['roomid'];
}
/**
 * Get rate id by room id
 * @param $roomid [in] Room id
 * @return rate id
 */
function get_RateID_byRoomID($roomid) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="SELECT rateid
			FROM rooms
			WHERE roomid=".strip_specials($roomid);

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$rateid=0;
	if($results) {
		$row=$stmt->fetch();
		$rateid = $row['rateid'];
	}
	return $rateid;
}

/**
 * Modify the status of a specific room
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] Room id
 * @param $status [in] New room status
 */
function update_roomstatus($roomid, $status) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "update rooms set ";
	$sql .= "status ='".strip_specials($status)."'";
	$sql .= " where roomid=".strip_specials($roomid);
//	print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return $results;

}
/**
 * Modify the details of a room
 * @ingroup ROOM_MANAGEMENT
 * @param $roomid [in] ID of the room from the rooms table
 * @param $roomno [in] The room number
 * @param $roomtypeid [in] Type ID from from the room types ID.
 * @param $roomname [in] The name of the room
 * @param $noofrooms [in] The number of rooms
 * @param $occupancy [in] Occupancy
 * @param $bedcount [in] max number of beds in room default 1
 * @param $bedtype1 [in] OTA bed type 1 default type 1
 * @param $bedtype2 [in] OTA bed type 2 default type 1
 * @param $bedtype3 [in] OTA bed type 3 default type 1
 * @param $bedtype4 [in] OTA bed type 4 default type 1
 * @param $room_status [in] room status
 * @param $photo [in] URL of photo
 * @param $filetype [in] file type of the photo
 * @param $ratesid [in] The default rateid to use against the room.
 *
 * @return roomid. 
 */
function modify_room($roomid,$roomno,$roomtypeid,$roomname,$noofrooms,$bedcount,
		$bedtype1,$bedtype2,$bedtype3,$bedtype4,$occupancy,&$room_status,$photo,$filetype, $ratesid){
	global $conn;
	if(!$noofrooms) $noofrooms = 1;
	if(!$occupancy) $occupancy = 1;
	if(!$bedcount) $bedcount = 1;
	if(!$bedtype1) $bedtype1 = 1;
	if(!$roomtypeid) $roomtypeid = 1;
	if(!$ratesid) $ratesid = 0;
	if(!$bedtype2 && $bedcount >= 2) {
		$bedtype2 = 1;
	} 
	if(!$bedtype3 && $bedcount >= 3) {
		$bedtype3 = 1;
	} 
	if(!$bedtype4 && $bedcount == 4) {
		$bedtype4 = 1;
	} 
	$status=$room_status;
	if(!$roomid){
		$sql="insert into rooms (roomno,roomtypeid,roomname,noofrooms,occupancy,bedcount,bedtype1,bedtype2,bedtype3,bedtype4,status,photo,filetype, rateid)";
		$sql.=" values (";
		$sql.=strip_specials($roomno).",";
		$sql.=strip_specials($roomtypeid).",";
		$sql.="'".strip_specials($roomname)."',";
		$sql.=strip_specials($noofrooms).",";
		$sql.=strip_specials($occupancy).",";
		$sql.=strip_specials($bedcount).",";
		$sql.=strip_specials($bedtype1).",";
		$sql.=strip_specials($bedtype2).",";
		$sql.=strip_specials($bedtype3).",";
		$sql.=strip_specials($bedtype4).",";
		$sql.="'".strip_specials($status)."',";
		$sql.="'".strip_specials($photo)."',";
		$sql.="'".strip_specials($filetype)."',";
		$sql.=strip_specials($ratesid);
		$sql.=")";
	} else {
		$sql = "update rooms set ";
		$sql .= "roomno =".strip_specials($roomno).",";
		$sql .= "roomtypeid =".strip_specials($roomtypeid).",";
		$sql .= "roomname ='".strip_specials($roomname)."',";
		$sql .= "noofrooms =".strip_specials($noofrooms).",";
		$sql .= "occupancy =".strip_specials($occupancy).",";
		$sql .= "bedcount =".strip_specials($bedcount).",";
		$sql .= "bedtype1 =".strip_specials($bedtype1).",";
		$sql .= "bedtype2 =".strip_specials($bedtype2).",";
		$sql .= "bedtype3 =".strip_specials($bedtype3).",";
		$sql .= "bedtype4 =".strip_specials($bedtype4).",";
		$sql .= "status ='".strip_specials($status)."',";
		$sql .= "photo ='".strip_specials($photo)."',";
		$sql .= "filetype ='".strip_specials($filetype)."',";
		$sql .= "rateid =".strip_specials($ratesid);
		$sql .= " where roomid=".strip_specials($roomid);
	}
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	//print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results && !$roomid) {
		$roomid = $conn->lastInsertId();
	}

	return $roomid;
}
/**
 * Modify the Invoice detail item
 * @ingroup PRODUCT_MANAGEMENT
 * @param $itemid [in] ID of the invoice item
 * @param $item [in] The item to update
 * @param $description [in] Type ID from from the room types ID.
 * @param $sale [in] The name of the room
 * @param $expense [in] The number of rooms
 * @param $itype [in] Occupancy
 *
 * @return itemid. 
 */
function modify_item($itemid,$item,$description,$sale,$expense,$itype){
	global $conn;
	if(!isset($itemid)) $itemid = 0;
	if(empty($sale)) $sale = 0;
	if(!$expense) $expense = 0;
	if(!$itype) $itype = 1;
	
	if(!$itemid){
		$sql="insert into details (item,description,sale,expense,itype)";
		$sql.=" values (";
		$sql.="'".strip_specials($item)."',";
		$sql.="'".strip_specials($description)."',";
		$sql.=strip_specials($sale).",";
		$sql.=strip_specials($expense).",";
		$sql.=strip_specials($itype);
		$sql.=")";
	} else {
		$sql = "update details set ";
		$sql .= "item ='".strip_specials($item)."',";
		$sql .= "description ='".strip_specials($description)."',";
		$sql .= "sale =".strip_specials($sale).",";
		$sql .= "expense =".strip_specials($expense).",";
		$sql .= "itype =".strip_specials($itype);
		$sql .= " where itemid=".strip_specials($itemid);
	}
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	//print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results && !$itemid) {
		$itemid = $conn->lastInsertId();
	}

	return $itemid;
}
/**
 * Get the text string for the FOP code
 * @ingroup RATE_MANAGEMENT
 * 
 * @param $fop [in] The FOP type
 *
 * @return FOP string
 */
function get_foptext($fop) {
	global $_L;
	$lang = get_language();
	load_language($lang);
	$res = "";
	if($fop == FOP_CASH) $res = $_L['FOP_cash'];
	if($fop == FOP_CC) $res = $_L['FOP_cc'];
	if($fop == FOP_TT) $res = $_L['FOP_tt'];
	if($fop == FOP_DB) $res = $_L['FOP_db'];
	if($fop == FOP_CHEQUE) $res =  $_L['FOP_chq'];
	if($fop == FOP_COUPON) $res =  $_L['FOP_coupon'];
	if($fop == FOP_VOUCHER) $res =  $_L['FOP_voucher'];
	if($fop == FOP_REDEMPTION) $res =  $_L['FOP_redem'];
	if($fop == FOP_PP) $res =  $_L['FOP_voucher'];
	

	return $res;
}
/**
 * Get the item name
 * @ingroup PRODUCT_MANAGEMENT
 * @param $itemid [in] The item id.
 *
 * @return item name
 */
function get_itemname($itemid) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return "";
	$sql="select item from details where itemid =".strip_specials($itemid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['item'];
	}
	return "";
}
/**
 * Get the item type
 * @ingroup PRODUCT_MANAGEMENT
 * @param $itemid [in] The item id.
 *
 * @return item name
 */
function get_itemtype($itemid) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return "";
	$sql="select itype from details where itemid =".strip_specials($itemid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['itype'];
	}
	return "";
}

/**
 * Get the item description
 * @ingroup PRODUCT_MANAGEMENT
 * @param $itemid [in/out] The item id.
 *
 * @return item name
 */
function get_itemdescription($itemid) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return "";
	$sql="select description from details where itemid =".strip_specials($itemid);
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		return $row['description'];
	}
	return "";
}

/**
 * Get the current list of invoice items
 * @ingroup PRODUCT_MANAGEMENT
 * @param $items [in/out] The array of invoice items to return
 * @todo add in todays date into the booking criteria
 */
function get_itemslist(&$items) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	
	$items = array();
	$sql="select itemid, item, description, sale, expense, itype from details";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	while($row = $stmt->fetch()) {
		//		print "found ".$row['itemid'] ." - ".$row['description']."<br/>\n";
		$items[$row['itemid']]['item'] = $row['item'];
		$items[$row['itemid']]['description'] = $row['description'];
		$items[$row['itemid']]['sale'] = $row['sale'];
		$items[$row['itemid']]['expense'] = $row['expense'];
		$items[$row['itemid']]['itype'] = $row['itype'];
	}
	return sizeof($items);

}
/**
 * Get the current room types 
 * @ingroup ROOM_MANAGEMENT
 * @param $items [in/out] The room type list
 */
function get_roomtypelist(&$items) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	
	$items = array();
	$sql="select roomtypeid, roomtype, description, rateid, roomurl from roomtype";

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	while($row = $stmt->fetch()) {
		//		print "found ".$row['itemid'] ." - ".$row['description']."<br/>\n";
		$items[$row['roomtypeid']]['roomtype'] = $row['roomtype'];
		$items[$row['roomtypeid']]['description'] = $row['description'];
		$items[$row['roomtypeid']]['rateid'] = $row['rateid'];
		$items[$row['roomtypeid']]['roomurl'] = $row['roomurl'];
	}
	return sizeof($items);

}
/**
 * Get the roomtype by the roomtypeid
 * @ingroup ROOM_MANAGEMENT
 * @param $id [in] Room type identifier
 */
function get_roomtype($id) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	
	$roomtype = '';
	$sql="select roomtypeid, roomtype, description, rateid from roomtype where roomtypeid=".$id;

	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$roomtype = $row['roomtype'];
	}
	return $roomtype;

}
/**
 * Get rate id by room type id
 * @param $rtid [in] Room type id
 * @return rate id
 */
function get_RateID_byRoomTypeID($rtid) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="SELECT rateid
			FROM roomtype
			WHERE roomtypeid=".strip_specials($rtid);
	//echo $sql;
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$rateid=0;
	if($results) {
		$row=$stmt->fetch();
		$rateid = $row['rateid'];
	}
	return $rateid;
}
/**
 * Get the roomtype by the ratecode
 * @ingroup ROOM_MANAGEMENT
 * @param $ratecode [in] rate code identifier
 * @param $roomtypes [in/out] The room type list
 */
function get_roomtype_byratecode($ratecode,&$roomtypes) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$rateid = get_rateid_bycode($ratecode);
	$roomtypes = array();
	$sql="select roomtypeid,rateid from roomtype where rateid =".$rateid;
	//echo $sql ."\n";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	while($row = $stmt->fetch()) {
		//		print "found ".$row['itemid'] ." - ".$row['description']."<br/>\n";
		$rtid=$row['roomtypeid'];
		$rt=get_roomtype($rtid);
		$roomtypes[$row['roomtype']]['roomtype'] = $rt;
		$roomtypes[$row['roomtype']]['rateid'] = $row['rateid'];
	}
	return sizeof($roomtypes);

}
/**
 * Get the roomtypeid by the roomtype
 * @ingroup ROOM_MANAGEMENT
 * @param $type [in] Room type identifier
 * @param $items [in/out] The room type list
 */
function get_roomtypeid($type,&$items) {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	
	$items = array();
	$sql="select roomtypeid, roomtype, description, rateid from roomtype where roomtype='".$type ."'";
	//echo $sql ."\n";
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
		//		print "found ".$row['itemid'] ." - ".$row['description']."<br/>\n";
		$items['roomtypeid'] = $row['roomtypeid'];
		$items['description'] = $row['description'];
		$items['rateid'] = $row['rateid'];
	return $items['roomtypeid'];

}
/**
 * Modify the room type
 * @ingroup ROOM_MANAGEMENT
 * @param $roomtypeid [in] ID of the room type
 * @param $roomtype [in] The room type
 * @param $description [in] Type ID from from the room types ID.
 * @param $rateid [in] The id of the default rate
 * @param $imgurl [in] The room image URL
 * @return itemid. 
 */
function modify_roomtype($roomtypeid,$roomtype,$description,$rateid,$imgurl=''){
	global $conn;
	if(!isset($roomtypeid)) $roomtypeid = 0;
	if(!$rateid) $rateid = 0;
	
	if(!$roomtypeid){
		$sql="insert into roomtype (roomtype,description,rateid,roomurl)";
		$sql.=" values (";
		$sql.="'".strip_specials($roomtype)."',";
		$sql.="'".strip_specials($description)."',";
		$sql.=strip_specials($rateid).",";
		$sql.="'".$imgurl."'";
		$sql.=")";
	} else {
		$sql = "update roomtype set ";
		$sql .= "roomtype ='".strip_specials($roomtype)."',";
		$sql .= "description ='".strip_specials($description)."',";
		$sql .= "rateid =".strip_specials($rateid).",";
		$sql .= "roomurl ='".$imgurl."'";
		$sql .= " where roomtypeid=".strip_specials($roomtypeid);
	}
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	//print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results && !$roomtypeid) {
		$roomtypeid = $conn->lastInsertId();
	}

	return $roomtypeid;
}
/**
 * Get the list of rates, excluding the default rates.
 * Will check the reservation date to the datein against the minimum reservations
 * The booking source, agent and guest ids will be used 
 *
 * @ingroup RATE_MANAGEMENT
 * @param $datein [in] Start date YYYY-MM-DD
 * @param $dateout [in] End date YYYY-MM-DD
 * @param $agentid [in] Travel agent id
 * @param $guestid [in] Guest id reserved
 * @param $guestcnt [in] Number of guests (excl babies)
 * @param $src [in] Booking source 
 * @param $rates [in/out] result array
 *
 * @return number of elements in rates
 *
 * $rates['ratesid']['code'] <br/>
 * $rates['ratesid']['desc'] <br/>
 * $rates['ratesid']['start'] dd/mm/yyyy (required)<br/>
 * $rates['ratesid']['end'] dd/mm/yyyy (required)<br/>
 * $rates['ratesid']['minstay'] <br/>
 * $rates['ratesid']['maxstay'] <br/>
 * $rates['ratesid']['minpax'] <br/>
 * $rates['ratesid']['maxpax'] <br/>
 * $rates['ratesid']['minadv'] <br/>
 * $rates['ratesid']['status']
 */
function get_rates( $datein='', $dateout ='', $agentid = 0, $guestid=0, $guestcnt=0, $src='', &$rates) {
	global $conn;
	
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	
	$where = " where ";
	$sql = "select rates.ratesid as ratesid, ratecode, description, bookingtype, occupancy, rate_type, currency,";
	$sql .= " date_started, date_stopped, ";
	$sql .= "max_stay, min_stay, max_people, min_people, min_advanced_booking from rates ";
	if($agentid > 0 && $guestid == 0) {
		$sql .= ", rateroomtypes where rates.ratesid=rateroomtypes.ratesid and typeid=".AGENTRATE. " and typeitemid=".$agentid;
		$where = " and ";
	}
	if($guestid > 0 && $agentid == 0) {
		$sql .= ", rateroomtypes where rates.ratesid=rateroomtypes.ratesid and typeid=".CUSTOMERRATE. " and typeitemid=".$guestid;
		$where = " and ";
	}
	if($datein) {
		$sql .= $where." date_started <= '".$datein."'";
		$where = " and ";
	}
	if($dateout) {
		$sql .= $where." date_stopped >= '".$dateout."'";	
		$where = " and ";
	}
	if($guestcnt > 0) {
		$sql .= $where." min_people <= ".$guestcnt." and max_people >=" .$guestcnt;
		$where = " and ";
	}
	//print $sql."<br/>\n";
	$rates = array();
	if(!$conn) return 0;
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results) {
		while($row=$stmt->fetch()) {
		$rates[$row['ratesid']]['ratecode'] = $row['ratecode'];
		$rates[$row['ratesid']]['description'] = $row['description'];
		$rates[$row['ratesid']]['bookingtype'] = $row['bookingtype'];
		$rates[$row['ratesid']]['occupancy'] = $row['occupancy'];
		$rates[$row['ratesid']]['rate_type'] = $row['rate_type'];
		$rates[$row['ratesid']]['currency'] = $row['currency'];
		$rates[$row['ratesid']]['date_started'] = $row['date_started'];
		$rates[$row['ratesid']]['date_stopped'] = $row['datesdate_stoppedtop'];
		$rates[$row['ratesid']]['max_stay'] = $row['max_stay'];
		$rates[$row['ratesid']]['min_stay'] = $row['min_stay'];
		$rates[$row['ratesid']]['max_people'] = $row['max_people'];
		$rates[$row['ratesid']]['min_people'] = $row['min_people'];
		$rates[$row['ratesid']]['min_advanced_booking'] = $row['min_advanced_booking'];
		}
	}
	return sizeof($rates);
}
/**
 * Get all the rates available for a specific room
 * Used in rate calculation
 * @ingroup RATE_MANAGEMENT
 * @param $roomid [in] Room id
 * @param $rate [in/out] Rates array
 * @param $selected [in] Optional rate provided to select automatically
 * @return the number of rates for the room
 */
function get_ratebyroomid($roomid, &$rate, $selected = 0) {
	global $conn;	
	if(!$roomid) return 0;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$roomtypeid = 0;
	$ridx=0;
	$sql="SELECT distinct rooms.roomtypeid, rooms.rateid, rates.ratecode
			FROM rooms, rates
			WHERE ".$bracket." rates.ratesid = rooms.rateid
			AND roomid=".strip_specials($roomid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row=$stmt->fetch();
		if($row['rateid']){
			$roomtypeid = $row['roomtypeid'];
			$rate[$row['rateid']]['rateid']=$row['rateid'];
			$rate[$row['rateid']]['ratecode']=$row['ratecode'];
			$ridx++;
		}
	}
	
	$sql="SELECT distinct roomtype.rateid, rates.ratecode
			FROM rooms, roomtype, rates
			WHERE rooms.roomtypeid = roomtype.roomtypeid 
			AND rates.ratesid = roomtype.rateid
			AND roomid=".strip_specials($roomid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row=$stmt->fetch();
		if($row['rateid']){
			$rate[$row['rateid']]['rateid']=$row['rateid'];
			$rate[$row['rateid']]['ratecode']=$row['ratecode'];
			$ridx++;
		}		
	}
	if($selected > 0) {
		$sql="SELECT distinct rates.ratesid,rates.ratecode
			FROM rateroomtypes,rates
			WHERE rates.ratesid = ".$selected." OR ( rates.ratesid = rateroomtypes.ratesid
			AND ((typeid =".ROOMRATE." AND typeitemid=".$roomid.")
			OR (typeid=".ROOMTYPERATE." AND typeitemid=".$roomtypeid.")))";

	} else {
		$sql="SELECT distinct rates.ratesid,rates.ratecode
			FROM rateroomtypes,rates
			WHERE rates.ratesid = rateroomtypes.ratesid
			AND ((typeid =".ROOMRATE." AND typeitemid=".$roomid.")
			OR (typeid=".ROOMTYPERATE." AND typeitemid=".$roomtypeid."))";
	}
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {
			if($row['ratesid']){			
				$rate[$row['ratesid']]['rateid']=$row['ratesid'];
				$rate[$row['ratesid']]['ratecode']=$row['ratecode'];
				$ridx++;
			}			
		}
	}	
	return $ridx;
}
/**
 * Get all the rates available for a specific room type
 * Used in rate calculation
 * @ingroup RATE_MANAGEMENT
 * @param $roomtypeid [in] Room id
 * @param $rate [in/out] Rates array
 * @return the number of rates for the room type
 */
function get_ratebyroomtypeid($roomtypeid, &$rate) {
	global $conn;
	if(!$roomtypeid) return 0;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;

	$ridx=0;	
	$sql="SELECT roomtype.rateid, rates.ratecode
			FROM roomtype, rates
			WHERE rates.ratesid = roomtype.rateid
			AND roomtypeid=".strip_specials($roomtypeid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row=$stmt->fetch();
		if($row['rateid']){
			$rate[$row['rateid']]['rateid']=$row['rateid'];
			$rate[$row['rateid']]['ratecode']=$row['ratecode'];
			$ridx++;
		}		
	}
	$sql="SELECT rates.ratesid,rates.ratecode
			FROM rateroomtypes,rates
			WHERE rates.ratesid = rateroomtypes.ratesid
			AND typeid =".ROOMTYPERATE." AND typeitemid=".$roomtypeid;
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {	
			if($row['ratesid']){		
				$rate[$row['ratesid']]['rateid']=$row['ratesid'];
				$rate[$row['ratesid']]['ratecode']=$row['ratecode'];
				$ridx++;
			}			
		}
	}	
	return $ridx;
}

/**
 * Get all the roomtypes by the rate id
 * @ingroup ROOM_MANAGEMENT
 * @param $rateid [in] rate id
 * @param $roomtypes [in/out] The room type list
 * @return number of room type for the rate id
 */
function get_roomtype_byrateid($rateid,&$roomtypes) {
	global $conn;
	if(!$rateid) return 0;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$ridx=0;	
	$sql="SELECT roomtype.roomtypeid,roomtype.roomtype
			FROM roomtype, rates
			WHERE rates.ratesid = roomtype.rateid
			AND roomtype.rateid=".strip_specials($rateid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row=$stmt->fetch();
		if($row['roomtypeid']){
			$roomtypes[$row['roomtypeid']]['roomtypeid']=$row['roomtypeid'];
			$roomtypes[$row['roomtypeid']]['roomtype']=$row['roomtype']; 
			$ridx++;	
		}			
	}
	$sql="SELECT roomtype.roomtypeid,roomtype.roomtype
			FROM rateroomtypes,roomtype
			WHERE rateroomtypes.typeitemid = roomtype.roomtypeid
			AND rateroomtypes.typeid =".ROOMTYPERATE." AND rateroomtypes.ratesid=".$rateid;
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {	
			if($row['roomtypeid']){
				$roomtypes[$row['roomtypeid']]['roomtypeid']=$row['roomtypeid'];					
				$roomtypes[$row['roomtypeid']]['roomtype']=$row['roomtype'];
				$ridx++;	
			}		
		}
	}
	return $ridx;
}

/**
 * Get all the rooms by the rate id
 * @ingroup ROOM_MANAGEMENT
 * @param $rateid [in] rate id
 * @param $rooms [in/out] The room list
 * @return number of rooms for the rate id
 */
function get_rooms_byrateid($rateid,&$rooms) {
	global $conn;
	if(!$rateid) return 0;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$ridx=0;
	//room default rate	
	$sql="SELECT rooms.roomid,rooms.roomno,rooms.status
			FROM roomtype, rooms
			WHERE rooms.roomtypeid = roomtype.roomtypeid
			AND roomtype.rateid=".strip_specials($rateid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {
			if($row['roomid']){
				$rooms[$row['roomid']]['roomid']=$row['roomid'];
				$rooms[$row['roomid']]['roomno']=$row['roomno']; 
				$rooms[$row['roomid']]['status']=$row['status'];
				$ridx++;	
			}
		}			
	}
	//room other rate	
	$sql="SELECT rooms.roomid,rooms.roomno,rooms.status
			FROM rooms
			WHERE rooms.rateid =".strip_specials($rateid);
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {
			if($row['roomid']){
				$rooms[$row['roomid']]['roomid']=$row['roomid'];
				$rooms[$row['roomid']]['roomno']=$row['roomno']; 
				$rooms[$row['roomid']]['status']=$row['status'];
				$ridx++;	
			}
		}			
	}
	$sql="SELECT rooms.roomid,rooms.roomno,rooms.status
			FROM rateroomtypes,rooms
			WHERE rateroomtypes.typeitemid = rooms.roomid
			AND rateroomtypes.typeid =".ROOMRATE." AND rateroomtypes.ratesid=".$rateid;
	//echo "SQL-->".$sql."<br/>";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		while($row = $stmt->fetch()) {	
			if($row['roomid']){
				$rooms[$row['roomid']]['roomid']=$row['roomid'];
				$rooms[$row['roomid']]['roomno']=$row['roomno'];
				$rooms[$row['roomid']]['status']=$row['status'];
				$ridx++;	
			}		
		}
	}	
	return $ridx;		
}

/**
 * Get the rate summary detail for a specific rates id
 *
 * @ingroup RATE_MANAGEMENT
 * @param $ratesid [in] Rate id
 * @param $rate [in/out] Rate detail
 */
function get_ratebyratesid($ratesid, &$rate) {
	global $conn;
	global $_L;
	
	if(!$ratesid) return 0;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$fullrate = array();
//	print "get fullrate ".$ratesid."<br/>";
	if(! get_rate($ratesid, $fullrate)) return 0;
	
	$rate= array();
	$rate['rateid'] = $ratesid;
	$rate['name'] = $fullrate['description'];
	$rate['code'] = $fullrate['ratecode'];
	$rate['currency'] = $fullrate['currency'];	
	$items=array();
	get_rateitems($ratesid, $items);
	$rate['inclusions'] = "";
	foreach($items as $idx=>$val) {
		$rate['inclusions'] .= $_L['RTS_product']." : ". get_itemname($items[$idx]['itemid'])."<br/>";
		$rate['inclusions'] .= $_L['RTS_valid']." : ". get_ratesperiodstring($items[$idx]['validperiod'], 1,1,1)."<br/>";
		$rate['inclusions'] .= $_L['RTS_ratetype']." : ".get_discounttypestring($items[$idx]['discounttype'])."<br/>";
		$rate['inclusions'] .= $_L['RTS_price']. " : ".$rate['currency']." ".$items[$idx]['discountvalue']."<br/>";
			if($items[$idx]['discountvalue']) {
			if($items[$idx]['service']) $rate['inclusions'] .= $_L['RTS_including'];
				else $rate['inclusions'] .= $_L['RTS_excluding'];
			$rate['inclusions'] .= " ".$_L['RTS_service']."<br/>";
			if($items[$idx]['tax']) $rate['inclusions'] .= $_L['RTS_including'];
				else $rate['inclusions'] .= $_L['RTS_excluding'];
			$rate['inclusions'] .= " ".$_L['RTS_tax']."<br/>";
		}
		$rate['inclusions'] .= "<br/>";
		$rate['price'] = $items[$idx]['discountvalue'];	
	}	
	$rate['requirements'] = $_L['RTS_occupancy']." ". get_occupancystring($fullrate['occupancy'])."<br/>";
	$rate['requirements'] .= $_L['RTS_commencing']." ". $fullrate['date_started']."<br/>";
	$rate['requirements'] .= $_L['RTS_ending']." ". $fullrate['date_stopped']."<br/>";
	$rate['requirements'] .= $_L['RTS_minnights']." ". $fullrate['minstay']."<br/>";
	$rate['requirements'] .= $_L['RTS_maxnights']." ". $fullrate['maxstay']."<br/>";
	$rate['requirements'] .= $_L['RTS_minpax'] ." ". $fullrate['minpax']."<br/>";
	$rate['requirements'] .= $_L['RTS_maxpax'] ." ". $fullrate['maxpax']."<br/>";
	$rate['requirements'] .= $_L['RTS_minbook']." ". $fullrate['minbook']."<br/>";
	


}
/**
 * Get string for the discount type.
 *
 * @ingroup RATE_MANAGEMENT
 * @param $id [in] discount type id
 * @return discount string
 */
function get_discounttypestring($id) {
	global $_L;
	$str = "";
	if($id == STANDARD) $str =  $_L['RTS_standard'];
	if($id == FIXED) $str =  $_L['RTS_fixed'];
	if($id == PERCENT) $str =  $_L['RTS_percent'];
	if($id == FOC) $str =  $_L['RTS_foc'];
	return $str;
}
/**
 * Get string for the period
 *
 * @ingroup RATE_MANAGEMENT
 * @param $period [in] period mask identifier
 * @param $days [in] include days of week in string
 * @param $months [in] include months in string
 * @param $holidays [in] include holidays in the string
 *
 * @return period string
 */
function get_ratesperiodstring($period, $days, $months, $holidays=0) {
	global $_L;
	$wstr = "";
	if($days) {
		if(($period & HOTEL_WEEK) == HOTEL_WEEK) $wstr = $_L['RTS_allweek']; 
		else if(($period & HOTEL_WEND) == HOTEL_WEND) $wstr = $_L['RTS_weekend']; 
		else {
			if($period & HOTEL_SUN) $wstr = $_L['RTS_sun']." ";
			if($period & HOTEL_MON) $wstr .= $_L['RTS_mon']." ";
			if($period & HOTEL_TUE) $wstr .= $_L['RTS_tue']." ";
			if($period & HOTEL_WED) $wstr .= $_L['RTS_wed']." ";
			if($period & HOTEL_THU) $wstr .= $_L['RTS_thu']." ";
			if($period & HOTEL_FRI) $wstr .= $_L['RTS_fri']." ";
			if($period & HOTEL_SAT) $wstr .= $_L['RTS_sat']." ";
		}
	}
	$mstr = "";
	if($months) {
		if(($period & HOTEL_YEAR) == HOTEL_YEAR) $mstr = $_L['RTS_allyear']; 
		else {
			if($period & HOTEL_JAN) $mstr = $_L['RTS_jan']." ";
			if($period & HOTEL_FEB) $mstr .= $_L['RTS_feb']." ";
			if($period & HOTEL_MAR) $mstr .= $_L['RTS_mar']." ";
			if($period & HOTEL_APR) $mstr .= $_L['RTS_apr']." ";
			if($period & HOTEL_MAY) $mstr .= $_L['RTS_may']." ";
			if($period & HOTEL_JUN) $mstr .= $_L['RTS_jun']." ";
			if($period & HOTEL_JUL) $mstr .= $_L['RTS_jul']." ";
			if($period & HOTEL_AUG) $mstr .= $_L['RTS_aug']." ";
			if($period & HOTEL_SEP) $mstr .= $_L['RTS_sep']." ";
			if($period & HOTEL_OCT) $mstr .= $_L['RTS_oct']." ";
			if($period & HOTEL_NOV) $mstr .= $_L['RTS_nov']." ";
			if($period & HOTEL_DEC) $mstr .= $_L['RTS_dec']." ";
		}
	}
	$hstr = "";
	
	if($holidays){
		if(($period & HOTEL_HOLS) == HOTEL_HOLS) {
			$hstr = $_L['RTS_yesholiday'];
		} else{
			$hstr = $_L['RTS_noholiday'];
		}
	}
	return trim($wstr." ".$mstr." ".$hstr);

}
/**
 * Get the occupancy string value
 * 
 */
function get_occupancystring($occ) {
	global $_L;
	
	if($occ == OSINGLE) $str = $_L['RTS_osingle']; 
	if($occ == ODOUBLE) $str = $_L['RTS_odouble'];
	if($occ == OFAMILY) $str = $_L['RTS_ofamily']; 
	return $str;
}
/**
 * Get the current set document numbers from the database for a specific property
 * @ingroup ADMIN_MANAGEMENT
 * @param $propertyno [in] The id of the property invoicing for.
 * @param $invoiceno [in/out] The invoice number
 * @param $receiptno [in/out] The receipt number
 * @param $voucherno [in/out] The voucher number
 * @return 0 fail 1 success
 */
function get_documents($propertyno, &$invoiceno, &$receiptno, &$voucherno) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$sql = "select invoiceno, receiptno, voucherno from documents where propertyid=".$propertyno;
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$invoiceno = $row['invoiceno'];
		$receiptno = $row['receiptno'];
		$voucherno = $row['voucherno'];
	}
	return $results;
}
/**
 * Update/Insert the current set document numbers from the database for a specific property
 * @ingroup ADMIN_MANAGEMENT
 * @param $propertyno [in] The id of the property invoicing for.
 * @param $invoiceno [in/out] The invoice number
 * @param $receiptno [in/out] The receipt number
 * @param $voucherno [in/out] The voucher number
 * @return 0 fail 1 success
 */
function modify_documents($propertyno, $invoiceno,$receiptno,$voucherno) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$propertyno) $propertyno = 1;
	
	$sql = "select count(propertyid) as n from documents where propertyid = ".$propertyno;
	$stmt = $conn->prepare($sql);
	$results = $stmt->execute();
	$row = $stmt->fetch();
	
	if($row['n'] > 0) {
		$sql = "update documents set ";
		$sql .= " invoiceno = '".strip_specials($invoiceno)."', ";
		$sql .= " receiptno = '".strip_specials($receiptno)."', ";
		$sql .= " voucherno = '".strip_specials($voucherno)."' ";
		$sql .= " where propertyid = ".$propertyno;
	} else {
		$sql = "insert into documents (propertyid, invoiceno, receiptno, voucherno) values ";
		$sql .= "(".strip_specials($propertyno).",";
		$sql .= "'".strip_specials($invoiceno)."', ";
		$sql .= "'".strip_specials($receiptno)."', ";
		$sql .= "'".strip_specials($voucherno)."' )";
	}
//	print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	return $results;
}
/**
 * Get the next document number, 1 Voucher, 2 Invoice, 3 Receipt
 * @ingroup ADMIN_MANAGEMENT
 * @param $propertyno [in] property number
 * @param $doctype [in] 1 Voucher, 2 Invoice, 3 Receipt
 * @param $update [in] 1 update the document numbers.
 *
 * @return next document number
 */
function get_nextdocumentno($propertyno, $doctype, $update) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$propertyno) $propertyno = 1;

	get_documents($propertyno, $inv, $rcp, $vch);
	if($doctype == 1) {
		$doc = $vch;
		$vch++;
	}
	if($doctype == 2) {
		$doc = $inv;
		$inv++;
	}
	if($doctype == 3) {
		$doc = $rcp;
		$rcp++;
	}
	if($update) {
		modify_documents($propertyno, $inv,$rcp,$vch);
	}
	return $doc;
}



/**
 * Retrieve a policy by a specific ID.
 * @ingroup POLICY_MANAGEMENT
 * @param $policyid [in] policy id for update, 0 if new policy
 * @param $policy [in/out] Result policy array
 *
 * return number of elements in policy
 */
function get_policy($policyid, &$policy) {
	if(!$policyid) return 0;
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$sql = "select * from policy ";
	$sql .= " where idpolicy='".$policyid."'";

	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$row=$stmt->fetch();
	//print $sql."<br/>\n";
	
	//$policy = array();
	$policy['idpolicy'] = $row['idpolicy'];
	$policy['ID'] = $row['ID'];
	$policy['rateid'] = $row['rateid'];
	$policy['title']=$row['title'];
	$policy['language']=$row['language'];
	$policy['description']=$row['description'];
	
	return $policy['idpolicy'];
}

/**
 * Retrieve all policy.
 * @ingroup POLICY_MANAGEMENT
 * @param $policy [in/out] Result policy array
 *
 * return number of elements in policy
 */
function get_policylist(&$policy) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(! $conn) return 0;
	$sql = "select * from policy";

	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
//	print $sql."<br/>\n";
	$policy = array();
	while($row = $stmt->fetch()) {
		$policy[$row['idpolicy']]['idpolicy'] = $row['idpolicy'];
		$policy[$row['idpolicy']]['ID'] = $row['ID'];
		$policy[$row['idpolicy']]['rateid'] = $row['rateid'];
		$policy[$row['idpolicy']]['title']=$row['title'];
		$policy[$row['idpolicy']]['language']=$row['language'];
		$policy[$row['idpolicy']]['description']=$row['description'];
	}
	return sizeof($policy);
}

/**
 * Add a policy to policy database
 * @ingroup POLICY_MANAGEMENT
 *
 * @param $policyid [in] Policy id
 * @param $rateid [in] rateid for a rate
 * @param $title [in] Title of policy
 * @param $language [in] Language used for the policy
 * @param $description [in] Description for the policy
 */
function add_policy($policyid, $rateid, $title, $language, $description) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$policyid) return 0;
	if(!$title) return 0;
	if(!$rateid) $rateid = "";
	if(!$language) $language = "";
	if(!$description) $description = "";
	
	$sql = "insert into policy (ID, rateid, title, language, description) values ('".$policyid."','".$rateid."','".$title."','".$language."','".$description."')";
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt->fetch();
	$stmt =NULL;

	return 1;		
}

/**
 * @ingroup POLICY_MANAGEMENT
 * Delete all policy
 */
function delete_allpolicy() {
	global $conn;
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="delete from policy";
	$stmt = $conn->prepare( $sql);
	$res = $stmt->execute();
}

/**
 * @ingroup POLICY_MANAGEMENT
 * Delete policy by id
 * policyid [in] the id of the policy
 */
function delete_policybyid($policyid) {
	global $conn;
	if(! $policyid) {
		return;
	}
	if(! $conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	$sql="delete from policy where ID = '".$policyid."'";
	$stmt = $conn->prepare( $sql);
	$res = $stmt->execute();
}

/**
 * Modify a policy
 * @ingroup POLICY_MANAGEMENT
 *
 * @param $policyid [in] Policy id
 * @param $ID [in] ID
 * @param $rateid [in] rateid for a rate
 * @param $title [in] Title of policy
 * @param $language [in] Language used for the policy
 * @param $description [in] Description for the policy
 */
function modify_policy($policyid, $ID, $rateid, $title, $language, $description) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$policyid) {
		add_policy($ID, $rateid, $title, $language, $description);
	}else{
		$sql = "update policy set ";
		$sql .= "ID = '".$ID."',";
		$sql .= "rateid = '".$rateid."',";
		$sql .= "title = '".$title."',";
		$sql .= "language = '".$language."',";
		$sql .= "description = '".$description."'";
		$sql .= " where idpolicy = '".$policyid."'";
		//print $sql."<br/>";
		$stmt =$conn->prepare($sql);
		$results =$stmt->execute();
		$stmt->fetch();
		$stmt =NULL;
	}
	return 1;		
}

/**
 * Retrieve the list of registrations/booking
 * @ingroup BOOKING_MANAGEMENT
 * @param $bookings [in/out] array to contain result of registration 
 * @param $status [in] 0 active 1 for all
 * @param $name [in] name
 * @param $roomid [in] Room id
 * @return number of items in <i>bookings</i>
 */
function get_all_bookings(&$bookings, $status, $name, $roomid = 0) {
	global $conn;
	if(!$conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_all_bookings_advProfile($bookings, $status, $name, $roomid);
	}
	else{
		$pos = strrpos($name, ' ');
		list($fname, $lname) = preg_split("/[\s,]+/", $name,2);
		$name = $fname;
		$bookings = array();
		$sql = "SELECT b.book_id, b.guestid, b.reservation_id, b.bill_id, b.no_adults, b.no_child1_5, b.no_child6_12, 
			b.no_babies, DATE_FORMAT(b.checkindate, '%d/%m/%Y %H:%i') AS checkin, DATE_FORMAT(b.checkoutdate, '%d/%m/%Y %H:%i') AS checkout,
			b.roomid, b.roomtypeid, b.rates_id, b.voucher_no, b.book_status, g.lastname,g.firstname,g.middlename, 
			g.pp_no, g.idno, g.street_name, g.postal_code, g.town, g.phone, g.email, g.mobilephone, g.eBridgeID, g.IM, g.nationality ,g.countrycode,
			n.country as nation, c.country, b.instructions, DATEDIFF(checkoutdate, checkindate) AS no_nights
			FROM booking AS b, guests AS g
			LEFT JOIN countries AS c ON g.countrycode = c.countrycode 
			LEFT JOIN countries AS n ON g.nationality = n.countrycode
			WHERE g.guestid = b.guestid ";
			
		if (!$status) {
			$sql .= "";
		} else if ($status == 1) {
			$sql .= " and b.book_status = 1 ";
		} else if ($status == 2) {
			$sql .= " and b.book_status = 2 ";
		} else if ($status == 3) {
			$sql .= " and b.book_status = 3 ";
		} else if ($status == 4) {
			$sql .= " and b.book_status = 4 ";
		} else if ($status == 5) {
			$sql .= " and b.book_status = 5 ";
		}
	
		//if ($roomtypeid) $sql .= "and  b.roomtypeid = ".strip_specials($roomtypeid);
		if ($name) $sql .= " and ( g.firstname like '%".$name."%' or g.middlename like '%".$name."%' or g.lastname like '%".$name."%' )";
		if ($roomid) $sql .= " and b.roomid = " .$roomid;
		
		$sql .= " order by book_id  asc"; //added by zc
		//echo $sql;
		//print $sql."<br/>";
		$stmt = $conn->prepare($sql);
		$results = $stmt->execute();
		$i=0;
		if($results) {
			while($row = $stmt->fetch()) {
				$bookings[$i]['book_id'] = $row['book_id'];
				$bookings[$i]['bill_id'] = $row['bill_id'];
				$bookings[$i]['guestid'] = $row['guestid'];
				$bookings[$i]['reservation_id'] = $row['reservation_id'];
				$bookings[$i]['no_adults'] = $row['no_adults'];
				$bookings[$i]['no_child'] = $row['no_child1_5'] + $row['no_child6_12'] + $row['no_babies'];
				$bookings[$i]['roomid'] = $row['roomid'];
				$bookings[$i]['roomtypeid'] = $row['roomtypeid'];
				$bookings[$i]['ratesid'] = $row['rates_id'];
				$bookings[$i]['voucher_no'] = $row['voucher_no'];
				$bookings[$i]['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
				$bookings[$i]['no_nights'] = $row['no_nights'];
				$bookings[$i]['checkindate'] = $row['checkin'];
				$bookings[$i]['checkoutdate'] = $row['checkout'];
				$bookings[$i]['book_status'] = $row['book_status'];
				$bookings[$i]['roomno'] = get_roomno($row['roomid']);
				$i++;
			}
		}
	}
	//print_r($bookings);
	$stmt = null;
	return sizeof ($bookings);
}
/**
 * Get the list of reservations
 * @ingroup RES_MANAGEMENT
 * @param $start [in] Start window date for reservations
 * @param $end [in] End window date for reservations
 * @param $name [in] guest name to search
 * @param $rlist [in/out] list of reservations
 * @param $active [in] open 
 * @note results rlist has form:<br/>
 * $rlist[$i]['guestname']<br/>
 * $rlist[$i]['checkindate']<br/>
 * $rlist[$i]['checkoutdate']<br/>
 * $rlist[$i]['no_pax']<br/>
 * $rlist[$i]['voucher_no']<br/>
 * $rlist[$i]['no_nights']<br/>
 * $rlist[$i]['status']<br/>
 * $rlist[$i]['reservation_id']<br/> 
 * $rlist[$i]['booked_by_ebridgeid']<br/>
 * $rlist[$i]['cancelled_by_ebridgeid']<br/>
 * $rlist[$i]['cancelled_date']<br/>
 * $rlist[$i]['reservation_by']<br/>
 */
function get_all_reservations($start, $end, $name, &$rlist, $active) {
	global $conn;
	if(! $conn) $conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	if(!$conn) return 0;
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		get_reservationlist_advProfile($start, $end, $name, $rlist, $active);
	}
	else{
		if($name) $name = strip_specials($name);
		$sql = "select src, reservation.guestid, reservation_id,reservation_by,
				DATE_FORMAT(checkindate, '%d/%m/%Y %H:%i') as checkin, DATE_FORMAT(checkoutdate, '%d/%m/%Y %H:%i') as checkout, no_adults,
		        no_child1_5, no_child6_12, no_babies, roomid, roomtypeid, ratesid, voucher_no, 
				reserved_by, DATE_FORMAT(reserved_date, '%d/%m/%Y') as resdate, confirmed_by, DATE_FORMAT(confirmed_date, '%d/%m/%Y') as conf_date, 
				DATE_FORMAT(reserve_time, '%d/%m/%Y %H:%i') as restime, 
				book_id, status, firstname, middlename, lastname, loginname, DATEDIFF(checkoutdate, checkindate) AS no_nights,
				booked_by_ebridgeid, cancelled_by_ebridgeid, cancelled_date 
				from reservation, guests, users 
				where reservation.guestid = guests.guestid and users.userid = reservation.reserved_by ";
		if($name) {
			$sql .=	" and ( firstname like '%".$name."%' or lastname like '%".$name."%' or reservation_by like '%".$name."%' )";
		}
		if($start) {
			$sql .=	" and checkindate >= ".date_to_dbformat("DD/MM/YYYY",1,$start);
		}
		if($end) {
			$sql .=	" and checkoutdate <= ".date_to_dbformat("DD/MM/YYYY",1,$end);
		}
		if(($active) && ($active == 1))
			$sql .=	" and `status` = ".RES_QUOTE;
	
		if(($active) && ($active == 2))
			$sql .=	" and `status` = ".RES_ACTIVE;
			
		if(($active) && ($active == 3))
			$sql .=	" and `status` = ".RES_CANCEL;
			
		if(($active) && ($active == 4))
			$sql .=	" and `status` = ".RES_EXPIRE;
			
		if(($active) && ($active == 5))
			$sql .=	" and `status` = ".RES_CHECKIN;
			
		if(($active) && ($active == 6))
			$sql .=	" and `status` = ".RES_VOID;		
			
		if(($active) && ($active == 7))
			$sql .=	" and `status` = ".RES_CLOSE;
	
		$sql .= " order by checkindate  asc";
		//print $sql."<br/>";
		//echo $sql;
		$stmt = $conn->prepare( $sql);
		$results = $stmt->execute();
		$rlist = array();
		$i=0;
		if($results) {
			while($row = $stmt->fetch()) {
				$rlist[$i]['guestname'] = trim(trim($row['firstname'])." ".trim($row['middlename'])." ".trim($row['lastname']));
				$rlist[$i]['checkindate'] = $row['checkin'];
				$rlist[$i]['checkoutdate'] = $row['checkout'];
				$rlist[$i]['no_pax'] = $row['no_adults'] + $row['no_child1_5'] + $row['no_child6_12'] + $row['no_babies'];
				$rlist[$i]['voucher_no'] = $row['voucher_no'];
				$rlist[$i]['no_nights'] = $row['no_nights'];
				$rlist[$i]['status'] = $row['status'];
				$rlist[$i]['reservation_id'] = $row['reservation_id']; 
				$rlist[$i]['reservation_by'] = $row['reservation_by'];
				$rlist[$i]['booked_by_ebridgeid'] = $row['booked_by_ebridgeid'];
				$rlist[$i]['cancelled_by_ebridgeid'] = $row['cancelled_by_ebridgeid'];
				$rlist[$i]['cancelled_date'] = $row['cancelled_date'];
				$rlist[$i]['ratesid'] = $row['ratesid'];				
				$i++;
			}
		}
	}
	//print_r($rlist);
	return sizeof($rlist);
}

/**
 * Modify the details of a hotel gallery
 * @param $picid [in] ID of the picture from the hotelgallery table
 * @param $imgTitle [in] Title for the image
 * @param $imgURL [in] URL of the image
 * @param $imgDesc [in] Image description
 * @param $pg [in] 0 for gallery page, 1 for promo page
 * @param $img [in] 0 for image, 1 for video
 * @return picid 
 */

function modify_hotelgallery($picid,$imgTitle,$imgURL,$imgDesc,$pg = 0, $img = 0){
	global $conn;
	if(!isset($picid)) $picid = 0;
		
	if(!$picid){
		$sql="INSERT INTO hotelgallery (Title,URL,Description,page,imgtype)";
		$sql.=" values (";
		$sql.="'".strip_specials($imgTitle)."',";
		$sql.="'".strip_specials($imgURL)."',";
		$sql.="'".strip_specials($imgDesc)."',";
		$sql.="'".strip_specials($pg)."',";
		$sql.="'".strip_specials($img)."'";
		$sql.=")";
	} else {
		$sql = "UPDATE hotelgallery SET ";
		$sql .= "Title ='".strip_specials($imgTitle)."',";
		$sql .= "Description ='".strip_specials($imgDesc)."',";
		$sql .= "URL ='".strip_specials($imgURL)."',";
		$sql .= "page ='".strip_specials($pg)."',";
		$sql .= "imgtype ='".strip_specials($img)."'";
		$sql .= " where PicID=".strip_specials($picid);
	}
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	//print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results && !$picid) {
		$picid = $conn->lastInsertId();
	}

	return $picid;
}
/**
 * Delete the hotel gallery for a given picID
 * @param $id [in] picture id
 * @return 0 fail 1 success
 */
function delete_hotelgallery($id) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$id) return 0;
	$sql = "DELETE from hotelgallery WHERE PicID=".$id;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	return $results;
}

/**
 * Get all the receipts within a given date range
 * @param $start [in] The start date
 * @param $end [in] The end date
 * @param $receipt [in/out] The output array
 * @return size of the output array
 */
function get_all_receipts_By_DateRange($start,$end,&$receipt)
{
	global $conn;
	if(!$conn) {
		$conn=connect_Golf_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$sql="SELECT bill.bill_id, bill.billno, bill.date_billed, bill.book_id, bill.reservation_id, 
			rcp.receipt_id, rcp.rcpt_no, rcp.rcpt_date, rcp.fop, rcp.cctype, rcp.CCnum,
			rcp.expiry, rcp.cvv,rcp.auth, rcp.`name`, rcp.amount, bill.`status`, rcp.add_by, rcp.add_date, 
			bill.`status`, rcp.exrate,rcp.srcCurrency,rcp.tgtCurrency, bill.guestid  
			 FROM receipts AS rcp
			 LEFT JOIN `bills` AS bill ON bill.`bill_id`=rcp.`bill_id`";
	$sql.=" WHERE bill.`date_billed` BETWEEN '".$start."' AND '".$end."' AND rcp.status<>4 ORDER BY bill.`date_billed`";
	//echo "SQL--->".$sql;	
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	//one invoice can have multiple transactions
	$receipt = array();
	$idx = 0;	
	if($results) {
		while($row = $stmt->fetch()){			
			$receipt[$idx]['receiptID']=$row['receipt_id'];
			$receipt[$idx]['bookID']=$row['book_id'];
			$receipt[$idx]['receiptNo']=$row['rcpt_no'];
			$receipt[$idx]['invoiceNo']=$row['billno'];
			$receipt[$idx]['invoiceDate']=$row['date_billed'];
			$receipt[$idx]['receiptDate']=$row['rcpt_date'];
			$receipt[$idx]['formOfPayment']=$row['fop'];
			$receipt[$idx]['ccType']=$row['cctype'];
			$receipt[$idx]['ccNumber']=$row['CCnum'];
			$receipt[$idx]['expiry']=$row['expiry'];
			$receipt[$idx]['cvv']=$row['cvv'];
			$receipt[$idx]['auth']=$row['auth'];
			$receipt[$idx]['name']=$row['name'];
			$receipt[$idx]['amount']=$row['amount'];
			$receipt[$idx]['status']=$row['status'];
			$receipt[$idx]['createdBy']=$row['add_by'];
			$receipt[$idx]['createdDate']=$row['add_date'];		
			$receipt[$idx]['exrate']=$row['exrate'];
			$receipt[$idx]['srcCurrency']=$row['srcCurrency'];
			$receipt[$idx]['tgtCurrency']=$row['tgtCurrency'];
			$receipt[$idx]['guestid']=$row['guestid'];		
			$idx++;
		}
	}
	//print_r($receipt);
	return $idx;
}

/**
 * Get all the transactions within a given date range
 * @param $start [in] The start date
 * @param $end [in] The end date
 * @param $transactions [in/out] The output array
 * @return size of the output array
 */
function get_all_transactions_By_DateRange($start,$end,&$transactions)
{
	global $conn;
	if(!$conn) {
		$conn=connect_Golf_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql = "select transno, item_id, add_date as  adate, add_by, details, std_amount, ";
	$sql.= "std_svc, std_tax, trans_date as  tdate, amount, svc, tax, ratesid, quantity, grossamount, currency, XOID";
	$sql.= ",bill.bill_id, bill.billno, bill.date_billed, bill.book_id, bill.reservation_id, bill.guestid, bill.status";
	$sql.= " from transactions LEFT JOIN `bills` AS bill ON bill.`bill_id`=transactions.`bill_id`";
	$sql.=" WHERE bill.`date_billed` BETWEEN '".$start."' AND '".$end."' AND transactions.status<>4 ORDER BY bill.`date_billed`";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	//echo $sql;
	$i=0;
	if($results) {
		while($row = $stmt->fetch()) {
			$transactions[$i]['trans_id'] = $row['transno'];
			$transactions[$i]['item_id'] = $row['item_id'];
			$transactions[$i]['add_date'] = $row['adate'];
			$transactions[$i]['add_by'] = $row['add_by'];
			$transactions[$i]['details'] = $row['details'];
			$transactions[$i]['std_amount'] = $row['std_amount'];
			$transactions[$i]['std_svc'] = $row['std_svc'];
			$transactions[$i]['std_tax'] = $row['std_tax'];
			$transactions[$i]['amount'] = $row['amount'];
			$transactions[$i]['svc'] = $row['svc'];
			$transactions[$i]['tax'] = $row['tax'];
			$transactions[$i]['quantity'] = $row['quantity'];
			$transactions[$i]['ratesid'] = $row['ratesid'];
			$transactions[$i]['rateid'] = $row['ratesid'];
			$transactions[$i]['grossamount']  = $row['grossamount'];
			$transactions[$i]['trans_date'] = $row['tdate']; 
			$transactions[$i]['status'] = $row['status']; 
			$transactions[$i]['currency'] = $row['currency']; 
			$transactions[$i]['XOID'] = $row['XOID'];
			$transactions[$i]['guestid']=$row['guestid'];	
			$transactions[$i]['bookID']=$row['book_id'];
			$transactions[$i]['resID']=$row['reservation_id'];
			$transactions[$i]['invoiceNo']=$row['billno'];
			$transactions[$i]['invoiceDate']=$row['date_billed'];
			$transactions[$i]['guestid']=$row['guestid'];
			$i++;
		}
	}
	return $i;
}

/**
 * Function to mask the credit card number
 * @param $ccnum [in] The credit card number
 * @return the masked card number
 */
function mask_cardnumber($ccnum){
	$cclength = strlen($ccnum);
	$ccposshow = $cclength - 4;
	$ccdisp = substr($ccnum, $ccposshow);
	for ( $counter = 1; $counter <= $ccposshow; $counter += 1) {
	$ccdisp = "x" . $ccdisp;
	}
	return $ccdisp;  
}

/**
 * Function to get the credit card type string string
 * @param $cctype [in] The credit card type value
 * @return The credit card type string
 */
function get_creditcardString($cctype){
	global $_L;
	$lang = get_language();
	load_language($lang);
	switch ($cctype){
		case "AX":
			return $_L['CC_AX'];
			break;
		case "VI":
			return $_L['CC_VI'];
			break;
		case "CA":
			return $_L['CC_CA'];
			break;
		case "DC":
			return $_L['CC_DC'];
			break;
		case "EC":
			return $_L['CC_EC'];
			break;
		case "JCB":
			return $_L['CC_JCB'];
			break;
		default:
			return "";
	}
}

/**
 * Check the user is a ebridge customer
 * @return 1 if ebridge customer 0 if not
 */
function is_ebridgeCustomer(){
	if(file_exists(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php")){
		return 1;
	}else{
		return 0;
	}
}

/**
 * Retrieve ebridge ID from the operator setup 
 * @return tebridge ID
 */
function get_ebridgeID_fromOperatorSetup(){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$ebrid="";
	
	$sql="SELECT `eBridgeID` FROM `hotelsetup`";
		
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$ebrid=$row['eBridgeID'];	
	}
	return $ebrid;
}

/**
 * Function to get the saluation ID by salutation
 * @param $sal [in] The saluation
 * @return The saluation ID
 */
function get_salutationID_bySalutation($sal){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$salid = 0;
	
	$sql = "SELECT `saluteid` FROM `salutation` WHERE `Description`='".$sal."'";
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$salid=$row['saluteid'];	
	}
	return $salid;
}

/**
 * Function to get the saluation by salutation ID
 * @param $salid [in] The saluation ID
 * @return The saluation 
 */
function get_salutation_bySalutationID($salid){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sal = "";
	
	$sql = "SELECT `Description` FROM `salutation` WHERE `salute`='".$salid."'";
	//echo $sql;
	$stmt = $conn->prepare($sql);
	$results=$stmt->execute();
	if($results) {
		$row = $stmt->fetch();
		$sal=$row['Description'];	
	}
	return $sal;
}

/**
 * Modify the reservation_details
 * @ingroup RES_MANAGEMENT
 * @param $id [in] ID of the reservation details
 * @param $resid [in] reservation id 
 * @param $roomid [in] Room id 0 if not used 
 * @param $roomtypeid [in] Room type id, 0 if not used
 * @param $ratesid [in] Rates ID
 * @param $quantity [in] number of rooms
 * @param $status [in] status
 * @return id. 
 */
function modify_reservation_details($id,$resid,$roomid,$roomtypeid,$ratesid,$quantity,$status){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	if(!$resid) return 0;
	if(!$id) $id = 0;
	if(!$roomid) $roomid = 0;
	if(!$roomtypeid) $roomtypeid = 0;
	if(!$ratesid) $ratesid = 0;
	if(!$quantity) $quantity = 0;
	
	if($roomid && (! $roomtypeid || $roomtypeid == '0')) {
		$roomtypeid="(SELECT rooms.roomtypeid FROM rooms WHERE rooms.roomid=".$roomid.")";
	}else {
	    $roomtypeid = strip_specials($roomtypeid);
	}
	if(!$id){
		$sql="INSERT INTO reservation_details(reservation_id,roomid,roomtypeid,ratesid,quantity,`status`)";
		$sql.=" values (";
		$sql.=strip_specials($resid).",";
		$sql.=strip_specials($roomid).",";
		$sql.=$roomtypeid.",";
		$sql.=strip_specials($ratesid).",";
		$sql.=strip_specials($quantity).",";
		$sql.=strip_specials($status);
		$sql.=")";
	} else {
		$sql = "UPDATE reservation_details SET ";
		if($resid)
			$sql .= "reservation_id =".strip_specials($resid).",";
		if($roomid)
			$sql .= "roomid =".strip_specials($roomid).",";
		if($roomtypeid)
			$sql .= "roomtypeid =".$roomtypeid.",";
		if($ratesid)
			$sql .= "ratesid =".strip_specials($ratesid).",";
		if($quantity)
			$sql .= "quantity =".strip_specials($quantity).",";
		if($status)
			$sql .= "`status` =".strip_specials($status);
		$sql .= " WHERE id=".strip_specials($id);
	}
	
	//print $sql."<br/>\n";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	if($results && !$id) {
		$id = $conn->lastInsertId();
	}
	return $id;
}
/**
 * Retrieve all the reservation details by reservation id
 * @param $resid [in] The reservation ID
 * @param $details [in/out] reservation details array
 * @return 1 on success and 0 on fail
 */
function reservation_details_byResID($resid,&$details){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$rateitems = array();
	if(!$resid) return 0;
	$sql = "SELECT id,reservation_id,roomid,roomtypeid,ratesid,quantity,`status` 
			FROM reservation_details 
			WHERE reservation_id=".$resid;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$idx=0;
	while($row = $stmt->fetch()) {
		$details[$idx]['id'] = $row['id'];
		$details[$idx]['reservation_id'] = $row['reservation_id'];
		$details[$idx]['roomid'] = $row['roomid'];		
		$details[$idx]['ratesid'] = $row['ratesid'];
		$details[$idx]['quantity'] = $row['quantity'];	
		$details[$idx]['roomno']=get_roomno($row['roomid']);
		$details[$idx]['status']=$row['status'];
		if($row['roomid']){
			$room=array();
			get_room($row['roomid'],$room);
			$details[$idx]['roomtypeid'] = $room['roomtypeid'];
			$details[$idx]['roomtype']=get_roomtype($room['roomtypeid']);			
		}else{
			$details[$idx]['roomtypeid'] = $row['roomtypeid'];
			$details[$idx]['roomtype']=get_roomtype($row['roomtypeid']);			
		}
		$details[$idx]['ratecode']=get_ratecode($row['ratesid']);
		$idx++;	
	}
	return $idx;
}

/**
 * Retrieve all the reservation details by reservation id
 * @param $detailID [in] The reservation details ID
 * @param $details [in/out] reservation details array
 * @return 1 on success and 0 on fail
 */
function reservation_detail_byID($detailID,&$details){
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;	
	if(!$detailID) return 0;
	$details=array();
	
	$sql = "SELECT id,reservation_id,roomid,roomtypeid,ratesid,quantity,`status` 
			FROM reservation_details 
			WHERE id=".$detailID;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	while($row = $stmt->fetch()) {
		$details['id'] = $row['id'];
		$details['reservation_id'] = $row['reservation_id'];
		$details['roomid'] = $row['roomid'];		
		$details['ratesid'] = $row['ratesid'];
		$details['quantity'] = $row['quantity'];	
		$details['roomno']=get_roomno($row['roomid']);
		$details['status']=$row['status'];
		if($row['roomid']){
			$room=array();
			get_room($row['roomid'],$room);
			$details['roomtypeid'] = $room['roomtypeid'];
			$details['roomtype']=get_roomtype($room['roomtypeid']);			
		}else{
			$details['roomtypeid'] = $row['roomtypeid'];
			$details['roomtype']=get_roomtype($row['roomtypeid']);			
		}
		$details['ratecode']=get_ratecode($row['ratesid']);		
	}
	return sizeof($details);
}

/** 
 * Delete the reservation details by ID
 * @param $id [in] reservation details id
 * @return 0 fail 1 success
*/
function delete_resdetails($id) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	if(!$id) return 0;
	$sql = "DELETE FROM reservation_details WHERE id=".$id;
	//print $sql."<br/>";
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	return $results;
}
/**
 * Change the status of a reservation details
 * @ingroup RES_MANAGEMENT
 * 
 * @param $id [in] ID of the reservation details 
 * @param $status [in] status of the reservation.
 * @return 0 fail >0 success
 */
function update_resDetails_status($id, $status){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="UPDATE reservation_details SET `status`='".strip_specials($status)."' WHERE id=".strip_specials($id);
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();

	return $results;
}

/**
 * Get the Bill ID using the reservation ID
 * @ingroup RES_MANAGEMENT
 * @param $resid [in] The reservation id
 * @return bill id
 */
function get_billID_byResID($resid){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$bill_id=0;
	
	$sql="SELECT bill_id FROM bills WHERE reservation_id=".$resid;
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$bill_id = $row['bill_id'];
	}
	return $bill_id;
}

/**
 * Return a list of bills that have a status
 * @param $status [in] Bill status  OPEN, CLOSED, VOID
 * @param $billids [in/out] Result array of ID
 * @return count of billids
 */
function get_billids_by_status($status, &$billids) {
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	if(!$status) $status = STATUS_OPEN;
	
	$sql="SELECT bill_id FROM bills WHERE `status`=".$status;
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	$billids= array();
	if($results) {
		while($row = $stmt->fetch()) {
			$billids[$row['bill_id']] = $status;
		}
	}
	return sizeof($billids);


}
/**
 * Get receipts count using the bill ID
 * @ingroup RES_MANAGEMENT
 * @param $billid [in] The bill id
 * @return number of receipts for the bill
 */
function get_receiptsCount_byBillID($billid){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$receiptcount=0;
	
	$sql="SELECT COUNT(1) as rcptcount FROM receipts WHERE `status` != 4 and bill_id=".$billid ;
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$receiptcount = $row['rcptcount'];
	}
	return $receiptcount;
}
/**
 * Get transactions count using the bill ID
 * @ingroup RES_MANAGEMENT
 * @param $billid [in] The bill id
 * @return number of transactions for the bill
 */
function get_transactionsCount_byBillID($billid){
	global $conn;
	if(!$conn) {
		$conn = connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	
	$transcount=0;
	
	$sql="SELECT COUNT(1) AS transcount FROM transactions WHERE bill_id=".$billid;
	//print $sql."<br/>";
	$stmt = $conn->prepare( $sql);
	$results = $stmt->execute();
	
	if($results) {
		$row = $stmt->fetch();
		$transcount = $row['transcount'];
	}
	return $transcount;
}
/**
 * Update the database to mask and clear the credit card number and CVV for a receipt
 * @param $rcptid [in] The receipt id
 * @param $auth [in] The authorization code
 * @return 1 success 0 fail
 * @note only works when FOP is type for Creditcard.
 */
function update_receipt_auth($rcptid, $auth) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;

	if($auth != "") // clear the CVV as a minimum. just the CVV only at this time.
		mask_CC_clear_CVV($rcptid, 0);
		
	$sql="UPDATE receipts SET auth ='".strip_specials($auth)."'";
	$sql .= " WHERE receipt_id =".strip_specials($rcptid);
	//echo $sql;
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	return $results;	
}
/**
 * Update the database to mask and clear the credit card number and CVV for a receipt
 * @param $rcptid [in] The receipt id
 * @param $mask_CC [in] 1 to mask the credit card number in the database (optional)
 * @return 1 success 0 fail
 * @note only works when FOP is type for Creditcard.
 */
function mask_CC_clear_CVV($rcptid, $mask_CC=0) {
	global $conn;
	if(!$conn) {
		$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	}
	if(!$conn) return 0;
	$sql="UPDATE receipts SET cvv ='000'";
	// if mask cc is set then chop the last 4 chars and prepend 12x
	if($mask_CC) {
		$sql .= ",CCnum = concat('xxxxxxxxxxxx',substring(CCnum,-4)) ";
	}
	$sql .= " WHERE receipt_id =".strip_specials($rcptid) . " and fop=".FOP_CC;
	$stmt =$conn->prepare($sql);
	$results =$stmt->execute();
	$stmt =NULL;
	return $results;


}

?>
