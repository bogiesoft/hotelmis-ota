<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file bookings_list.php
 * @brief bookings list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup BOOKING_MANAGEMENT
 * @{
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();

access("booking"); //check if user is allowed to access this page
$lang = get_language();

$status = 2;
//$roomtype = '';
$roomid = '';
$name = '';

if (!empty($_POST['statusInd'])) {
	$_POST['Submit']=$_POST['statusInd'];
}
if (!empty($_POST['Submit']) || !empty($_GET['action'])){
	if(isset($_POST['Submit'])) 
		$action=$_POST['Submit'];
	else if(isset($_GET['action'])) 
		$action = $_GET['action'];
		
	switch ($action) {
		case $_L['BTN_search']:
			if($_POST['optFind'] == $_L['RGL_room']) {
				$roomno = $_POST['search'];
				$roomid = get_roomid($roomno);
				//$status = '';
			}
			if($_POST['optFind'] == $_L['RGL_guest']) {
				$name = $_POST['search'];
				//$status = '';
			}
			if (isset($_POST['active'])) {
				$status =$_POST['active'];
			}
			break;
	}

}
$bookings = array();
get_bookinglist($bookings, $status, $name, $roomid);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="css/new.css" rel="stylesheet" type="text/css" />
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	<script type="text/javascript" src="js/datefuncs.js"></script>
	<title><?php echo $_L['MAIN_Title'];?></title>
	<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
	<style>
	  .plainDropDown{
		width:130px;
		font-size:11px;
	  }
	  .plainDropDown2{
		width:75px;
		font-size:11px;
	  }
	  .plainSelectList{
		width:250px;
		font-size:11px;
	  }
	  .plainButton {
		font-size:11px;	
	  }
	</style>
  </head>
  <body>
	<form action="index.php?menu=listbooking" method="post" enctype="multipart/form-data">
	  <table  class="listing-table" width="100%"  border="0" cellpadding="1" align="center">
		<tr valign="top" style="padding:5;">
            <?php 
				if ($_GET['menu'] == "listbooking") {
					print_rightMenu_home();
				}?> 
		  
		  <td style="padding:10;" height="430">
			<table width="100%"  border="0" class="tdbgcl">
				<tr><td align="center">&nbsp;</td></tr>
				<tr><td><h2><?php echo $_L['RGL_title']; ?></h2></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>
				<table width="100%"  border="0" cellpadding="1">
				
					<tr align="center"><td><?php echo $_L['PR_actions']; ?>
					<input type='hidden' name='statusInd' id='statusInd' value=""/>&nbsp;
					<select name="active" id="active" onchange="document.getElementById('statusInd').value='<?php echo $_L['BTN_search'];?>';document.forms[0].submit();">
						  <option value="0"><?php echo $_L['REG_lists'];?></option>
						  <option value="0"><?php echo $_L['BTN_listall'];?></option>
						  <option value="1" <?php if ($_POST['active']==1) {echo "selected";} ?>><?php echo $_L['REG_registered'];?></option>
						  <option value="2" <?php if ($_POST['active']==2) {echo "selected";} ?>><?php echo $_L['REG_checkedin'];?></option>
						  <option value="3" <?php if ($_POST['active']==3) {echo "selected";} ?>><?php echo $_L['REG_checkedout'];?></option>
						  <option value="4" <?php if ($_POST['active']==4) {echo "selected";} ?>><?php echo $_L['REG_billed'];?></option>
						  <option value="5" <?php if ($_POST['active']==5) {echo "selected";} ?>><?php echo $_L['REG_close'];?></option>
					</select>&nbsp;<input type="submit" name="Submit" id="Submit"  class="button" value="<?php echo $_L['BTN_search']; ?>"/><br/>
					<label> <?php echo $_L['PR_criteria']; ?>:
						  <input type="radio" name="optFind" value="<?php echo $_L['RGL_guest']; ?>" <?php if ($_POST['optFind']==$_L['RGL_guest']) {echo "checked";} ?> /><?php echo $_L['RGL_guest']; ?></label>
						<label><input type="radio" name="optFind" value="<?php echo  $_L['RGL_room']; ?>"  <?php if ($_POST['optFind']==$_L['RGL_room']) {echo "checked";}?> /> <?php echo  $_L['RGL_room']; ?> </label>
						<input type="text" name="search" id="search" width="100" <?php if (isset($_POST['search'])) { echo "value='".$_POST['search']."'";} ?>/><br>
					</td></tr>
					
					<tr><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
					
				</table>
				</td></tr>
				<tr>
				  <td>
					<div class="scroll" id="Requests">
					  <table align="center">
						<tr bgcolor="#3593de">
						  <th style='padding:3'colspan="2"><?php echo $_L['PR_actions']; ?></th>
						  <th><?php echo $_L['STS_title']; ?></th>
						  <th><?php echo $_L['RGL_room']; ?></th>
						  <th><?php echo $_L['RGL_guest']; ?></th>
						  <th><?php echo $_L['REG_checkin']; ?></th>
						  <th><?php echo $_L['REG_checkout']; ?></th>
						  <th><?php echo $_L['RGL_nights']; ?></th>
						  <th><?php echo $_L['RGL_adults']; ?></th>
						  <th><?php echo $_L['RGL_child']; ?></th>
						</tr>
						<?php
						foreach ($bookings as $idx=>$val) {
						  //alternate row colour
						  $j++;
						  if($j%2==1){
							echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
						  }else{
							echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#E7E7E7\">";
						  }
						  echo "<td style='padding:3'><a href=\"index.php?menu=booking&id=".$bookings[$idx]['book_id']."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"". $_L['RGL_book']."\"/></a></td>";					
						  echo "<td><a href=\"index.php?menu=invoice&id=".$bookings[$idx]['bill_id']."\" ><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"". $_L['RGL_bill']."\"/></a></td>";
						  echo "<td>" . get_book_status_text($bookings[$idx]['book_status']) . "</td>";	
						  echo "<td>" . $bookings[$idx]['roomno'] . "</td>";						
						  echo "<td>" . trim($bookings[$idx]['guestname']) . "</td>";
						  echo "<td>" . $bookings[$idx]['checkindate'] . "</td>";
						  echo "<td>" . $bookings[$idx]['checkoutdate'] . "</td>";
						  echo "<td>" . $bookings[$idx]['no_nights'] . "</td>";			
						  echo "<td>" . $bookings[$idx]['no_adults'] . "</td>";
						  echo "<td>" . $bookings[$idx]['no_child'] . "</td>";
					      echo "</tr>"; //end of - data rows
						} //end of while row
						?>
					  </table>
					</div>
				  </td>
				</tr>
				<tr bgcolor="#66CCCC" ><td align="left"><div id="RequestDetails"></div></td></tr>
			</table>
		  </td>
		  
		</tr>
		
	  </table>
	</form>
  </body>
</html>
<?php
/**
 * @}
 * @}
 */
?>