<?php

/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file queryfunctions.php
 * @brief database query functions called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup DATABASE_MANAGEMENT 
 * @{
 * 
 */
include_once(dirname(__FILE__)."/configuration.inc.php");


/**
 * Remove special characters from sql input
 * prevents execution of arbitary sql
 * remove ;"<",
 * @param $txt [in] text to be cleansed
 * @return cleansed text
 */
function strip_specials($txt) {
	$spc = array(";","\"","(", ")", ",");
	$ret = str_replace($spc, "", $txt);
	return $ret; 	
}
/**
 * Convert utft \&\#NNNN; to \%uNNNN
 * @param $in [in] Input string
 * @return converted string
 * if no &# is found, then return unmodified string
 */
function utf8_javascript($in) {
	if(preg_match('/&#/',$in)) {
		$in = str_replace('&#','%u',$in);
		$in = str_replace(';','',$in);
	}
	return $in;
}
/**
 * Load Language files for the selected language
 * Language file will attempt to be loaded from \<install\>/lang/lang_\<$lang\>.php
 * 
 * @param $lang [in] language code  en, kr, th, zh-hk etc.
 */
function load_language($lang) {
//	print "include ".$lang."<br/>\n";
	
	global $_L;
	$base = dirname(__FILE__)."/lang/";
	$file = "lang_".trim($lang).".php";
//	print "check file ".$file."<br/>";
//	echo file_exists($file);
	if(file_exists($file) ) {
//		print "read file ".$file."<br/>";
		include_once($file);
	}
//	print "check file ".$base.$file."<br/>";
	if(file_exists($base.$file)) {
//		print "read file ".$base.$file."<br/>";
		include_once($base.$file);
	}
}
/**
 * Connect to the HOTEL Database as defined from system configuration file.
 * 
 * @return connection value
 */ 
function connect_Hotel_db($HOST,$USER,$PASS,$DB,$PORT) {

	if($GLOBALS['db'] == null) {
		try {
			$GLOBALS['db'] = new PDO('mysql:host='.$HOST.';port='.$PORT.';dbname='.$DB,$USER,$PASS);
		} catch (Exception $e) {
			echo "Failed: " . $e->getMessage();
			$GLOBALS['db']->rollBack();
			return null;
		}
	}
	return $GLOBALS['db'];
}


/**
 * Discconnect from the Exchange Database as defined from system configuration file.
 * @param $con [in] - existing connection
 * @return 1
 * @see connect_Hotel_db
 */ 
function disconnect_Hotel_db($con){
	return 1;
}

/**
 * This function is return sqlerror <br>
 * @return error number
 */
function get_db_error()
{
	return mysql_error();
}

/**
 * This function is return sqlerror string <br>
 * @param $id [in] error id
 * @return string
 */
function get_db_error_str($id)
{
	return mysql_error();
}

/**
 * Return the day/month mask for a supplied date. Used by rates checking.
 * @param $datein [in] Date format "dd/mm/yyyy"
 *
 * @return integer mask of day and month used in rates.
 */
function get_datemask($datein) {
	$datein = str_replace("/","-", $datein);
	list($dd,$mm,$yy) = sscanf($datein, "%d-%d-%d");
//	$dt = mktime(0,0,0,$mm+0,$dd+0,$yy+0);
	// reformat into computer time default yyyy-mm-dd
	$datein = $yy."-".$mm."-".$dd;
	$dt = strtotime($datein);
	// Mon = 1 Sunday = 7
	$day = date("N", $dt);
	// Jan = 1 Dec = 12
	$mon = date("m", $dt);
	$mask = 0;
	switch($day) {
		case 1:
			$mask = HOTEL_MON;
			break;
		case 2:
			$mask = HOTEL_TUE;
			break;
		case 3:
			$mask = HOTEL_WED;
			break;
		case 4:
			$mask = HOTEL_THU;
			break;
		case 5:
			$mask = HOTEL_FRI;
			break;
		case 6:
			$mask = HOTEL_SAT;
			break;
		case 7:
		default:
			$mask = HOTEL_SUN;
			break;
	}
	switch(intval($mon)) {
		case 1:
			$mask += HOTEL_JAN;
			break;
		case 2:
			$mask += HOTEL_FEB;
			break;
		case 3:
			$mask += HOTEL_MAR;
			break;
		case 4:
			$mask += HOTEL_APR;
			break;
		case 5:
			$mask += HOTEL_MAY;
			break;
		case 6:
			$mask += HOTEL_JUN;
			break;
		case 7:
			$mask += HOTEL_JUL;
			break;
		case 8:
			$mask += HOTEL_AUG;
			break;
		case 9:
			$mask += HOTEL_SEP;
			break;
		case 10:
			$mask += HOTEL_OCT;
			break;
		case 11:
			$mask += HOTEL_NOV;
			break;
		case 12:
			$mask += HOTEL_DEC;
			break;
		default:
			break;
	}
	return $mask;
}


 
/**
 * function to convert a date/time format into the format
 * required for insert into database
 * @param $fmt [in] format string of time input in datein 
 * @param $tm [in] include time in the output format, 1 include 0 not included
 * @param $datein [in] the date/time input in format fmt
 * 
 * @return 0 fail, formatted string date.
 * @todo get function call to determine DB type.
 */
function date_to_dbformat($fmt, $tm, $datein) {
date_default_timezone_set(TIMEZONE);
	$dd = 0;
	$mm = 0;
	$yy = 0;
	$hh = 0;
	$mi = 0;
	
	// Function call to get the installation db type.
	// Let it fall through to default.
	$dbtype = 0;
	$today = date("d/m/Y");
	$todayfull = date("d/m/Y G:i");

//	print $fmt."<br/>";
//	print $datein."<br/>";
	
	if(!$dbtype) $dbtype=1;
	$ofmt = "%Y-%m-%d";
	if($tm) {
		$ofmt = "%Y-%m-%d %H:%M:%S";
		if(!$datein) $datein =  $todayfull;
	}	
	if(!$datein && !$tm ) $datein =  $today;

	$mi = 0;
	$hh = 0;
	
	$fmt = strtoupper($fmt);
	$datein = str_replace("/","-", $datein);
	$fmt = str_replace("/","-", $fmt);
//	print "date in ".$datein."<br/>";
//	print "out fmt ".$ofmt."<br/>";
	// check the date input is valid against the format fmt
	if($fmt == "DD-MM-YY" || $fmt == "DD-MM-YYYY") {
		list($dd, $mm, $yy) = sscanf($datein, "%d-%d-%d");
	}
	if($fmt == "DD-MM-YY HH:MI" || $fmt == "DD-MM-YYYY HH:MI") {
		list($dd, $mm, $yy, $hh, $mi) = sscanf($datein, "%d-%d-%d %d:%d");
	}
	if($fmt == "MM-DD-YY" || $fmt == "MM-DD-YYYY") {
		list($mm, $dd, $yy) = sscanf($datein, "%d-%d-%d");
	}
	if($fmt == "DD-MM-YY HH:MI" || $fmt == "MM-DD-YYYY HH:MI") {
		list($mm, $dd, $yy, $hh, $mi) = sscanf($datein, "%d-%d-%d %d:%d");
	}
	// Assume all years > 2000.
	if ($yy < 100) {
		$yy += 2000;
	}
//	print "dd mm yy ". $dd." ".$mm." ".$yy."<br/>";
	$dateout = "'".strftime($ofmt,mktime($hh,$mi,0,$mm,$dd, $yy))."'";
//	print "dateout ".$dateout."<br/>";
	if($dbtype = 1) {
		return $dateout;
	}
	if($dbtype = 2 && $tm) {
		return "convert(datetime, ".$dateout.", 120)";
	}
	if($dbtype = 2 && !$tm) {
		return "convert(date, ".$dateout.", 120)";
	}
	return '';

}



/**
 * @}
 * @}
 */

?>
