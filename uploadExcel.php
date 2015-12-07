<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file uploadExcel.php
 * @brief called by OTA Hotel Management
 *
 */
error_reporting(E_ALL | E_STRICT);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/PHPExcel/Classes/PHPExcel/IOFactory.php");
include_once(dirname(__FILE__)."/../SDK/PHP/OTA/Common/common_defs.php");

$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
date_default_timezone_set(TIMEZONE);
access("admin");

$rowsNotAddedGuests=array();
$rowsNotAddedRes=array();
$noDocAndAddress=array();
$msgGuests="";
$msgRes="";
$maxGuests=0;
$maxRes=0;
$max=0;
$fileName="";
$submit="";
$tab="";
if (isset($_POST['Submit'])) {
	$submit=$_POST['Submit'];
}
if (isset($_POST['tab'])) {
	$tab=$_POST['tab'];
}
if ($submit== $_L['UPL_uploadGuestResBtn'] && $tab=="uploadExcel") {	
//	$target_dir = $_SERVER['DOCUMENT_ROOT'];
//	$target_file = $target_dir ."/". basename($_FILES["excelFile"]["name"]);
//	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
//	$tmp_path = $_FILES["excelFile"]["tmp_name"];
//	copy($tmp_path,$target_file);
//	echo " Submit=".$_POST['Submit'].' $tmp_path='.$tmp_path.' $target_file='.$target_file."<br/>";
//	$filename=substr($target_dir,strrpos($target_dir, ':') +2)."/". basename($_FILES["excelFile"]["name"]);//extension of the file
//	echo " filename=".$filename."<br/>";
	if (is_ebridgeCustomer()) {
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");	
		$fileName="OTA_Hotel_reservation_load_template.xls";
		$msgGuests = import_guests_from_spreadsheet($fileName,$rowsNotAddedGuests,$noDocAndAddress);
		$msgRes = import_reservations_from_spreadsheet($fileName,$rowsNotAddedRes);
	}	 
}							

?>
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 DW6 
<head>
 Copyright 2005 Macromedia, Inc. All rights reserved. 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php //echo $_L['MAIN_Title'];?></title>
<link href="css/new.css" rel="stylesheet" type="text/css" />
<link href="css/styles2.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
</head>
<body class="tdbgcl">
-->
<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
	<table class="tdbgcl" width="100%"  border="0" cellpadding="1" align=""> 
		<tr valign="top">
		  <td width="85%">
			<table width="100%" border="0" cellpadding="1">
			  <tr><td align=""></td></tr>
			  
			  <tr><br/><br/><td><b><?php echo $_L['UPL_uploadGuestRes'];?></b>
				  <input type="hidden" name="tab" id="tab" value="<?php echo "uploadExcel"; ?>"/>
				  <input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/>
			  </td></tr>
			  <tr ><td align=""> <br/></td></tr>
			  <tr>
				<td>
				 
			  <table border="0" cellspacing="0" cellpadding="3"><!--
			  	<tr>
			  		<td colspan="2"><b><?php //echo "Select file:";?></b></td>
			  	</tr>
			  	<tr>
			  		<td>
			  			<input type="text" name="excelFile" id="excelFile"> <span>File name with type</span>
			  		</td>
			  	</tr>
			  	--><tr><br/>
			  		<td colspan="" align="right"><input class="button" name="Submit" id="Submit" type="submit" value="<?php echo $_L['UPL_uploadGuestResBtn'];?>" /></td>
			  	</tr>		  	
			  	
			  </table> 
			  <table>
				<tr>
			  		<td colspan="2" align="left"><br/>
			  			<?php 
			  				if ($msgGuests) {
			  					echo $msgGuests;
			  				}
			  				if (count($rowsNotAddedGuests)>0) {
			  					$maxGuests = max(array_keys($rowsNotAddedGuests));
			  					echo $_L['ERR_guestRowsNotAdded'];
			  					for ($i=0;$i<count($rowsNotAddedGuests);$i++) {
			  						echo $rowsNotAddedGuests[$i];
			  						if ($i==$maxGuests) {
			  							echo ".";
			  						} else {
			  							echo ",";
			  						}
			  					}
			  					echo "<br/>";
			  				}			  				
			  				if (count($noDocAndAddress)>0) {
			  					$max = max(array_keys($noDocAndAddress));
			  					echo $_L['ERR_noDocAndAddress'];
			  					for ($i=0;$i<count($noDocAndAddress);$i++) {
			  						echo $noDocAndAddress[$i];
			  						if ($i==$max) {
			  							echo ".";
			  						} else {
			  							echo ",";
			  						}
			  					}
			  				}
			  				echo "<br/><br/>";
			  				if ($msgRes) {
			  					echo $msgRes;
			  				}
			  				if (count($rowsNotAddedRes)>0) {
			  					$maxRes = max(array_keys($rowsNotAddedRes));
			  					echo $_L['ERR_resRowsNotAdded'];
			  					for ($i=0;$i<count($rowsNotAddedRes);$i++) {
			  						echo $rowsNotAddedRes[$i];
			  						if ($i==$maxRes) {
			  							echo ".";
			  						} else {
			  							echo ",";
			  						}
			  					}
			  					echo "<br/>";
			  				}
			  				
			  			?>
			  		</td>
			  	</tr>				  
			  </table>
				</td>
			  </tr>
			</table>
		  </td>

		</tr>
	</table>
</form>
<!--</body>-->
<!--</html>-->


