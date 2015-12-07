<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file sessions.php
 * @brief sessions functions called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */

/**
 *Connect to the database<br> 
 *uses constants <b>HOST</b>,<b>USER</b>,<b>PASS</b> to connect to the database.<br>
 */
mysql_connect(HOST,USER,PASS);
mysql_select_db(DB);
/** 
 *This function use to session open <br>
 *@param $sess_path [in] session path
 *@param $sess_name [in] session name
 *@note return boolean type if session open.
 */
function sess_open($sess_path, $sess_name) {
	return true;
}
/**
 *This function use to sessiion close
 *@note return true boolean type if session close.
 */
function sess_close() {
	return true;
}

/**
 *This function use to read session data <br>
 *@param $sess_id [in] query to the database
 *@note return sess_Data variable <i>sess_Data</i> to generate for use in web page. 
 */
function sess_read($sess_id) {
	$result = mysql_query("SELECT Data FROM sessions WHERE SessionID = '$sess_id';");
	if (!mysql_num_rows($result)) {
		$CurrentTime = time();
		mysql_query("INSERT INTO sessions (SessionID, DateTouched) VALUES ('$sess_id', $CurrentTime);");
		return '';
	} else {
		extract(mysql_fetch_array($result), EXTR_PREFIX_ALL, 'sess');
		mysql_query("UPDATE sessions SET DateTouched = $CurrentTime WHERE SessionID = '$sess_id';");
		return $sess_Data;
	}
}
/**
 *This function use to write session data<br>
 *@param $sess_id [in] session ID
 *@param $data [in] query to the database<br>
 *the data go to the database by query with session_id<br>
 *@note return true boolean type if session data write.
 */
function sess_write($sess_id, $data) {
	$CurrentTime = time();
	mysql_query("UPDATE sessions SET Data = '$data', DateTouched = $CurrentTime WHERE SessionID = '$sess_id';");
	return true;
}
/**
 *This function use to destroy session<br>
 *@param $sess_id [in] query to the database<br>
 *the session delete query to the database with the session_id<br>
 *@note return true boolean type if session data delete.
 */
function sess_destroy($sess_id) {
	mysql_query("DELETE FROM sessions WHERE SessionID = '$sess_id';");
	return true;
}
/**
 *This function use to delete session with condition data<br>
 *@param $sess_maxlifetime [in] query to the database with condition<br>
 *the sessiion delete query to the database with condition based on session maximun lifetime<br>
 *@note return true boolean type if session conditioned data delete.
 */
function sess_gc($sess_maxlifetime) {
	$CurrentTime = time();
	mysql_query("DELETE FROM sessions WHERE DateTouched + $sess_maxlifetime < $CurrentTime;");
	return true;
}

session_set_save_handler("sess_open", "sess_close", "sess_read", "sess_write", "sess_destroy", "sess_gc");
session_start();

$_SESSION['foo'] = "bar";
$_SESSION['baz'] = "wombat";
?> 
