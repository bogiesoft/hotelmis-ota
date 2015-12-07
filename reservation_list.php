<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file reservation_list.php
 * @brief reservation list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup RES_MANAGEMENT
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$logofile=Get_LogoFile();
access("reservation");
$lang = get_language();
load_language($lang);

$start = "";
$end = "";
$name = "";
$vouchernum = "";

$today = strtotime("today");
$start = date("d-m-Y", $today);

//if(!empty($_POST['startdate'])) $start = $_POST['startdate'];
//if(!empty($_POST['enddate'])) $end = $_POST['enddate'];
//if(!empty($_POST['name'])) $name = $_POST['name']; // Change to '$name' By ZC

$active = 0;
if (!empty($_POST['Submit']) || !empty($_GET['action'])){
	if(isset($_POST['Submit'])) 
		$action=$_POST['Submit'];
	else if(isset($_GET['action'])) 
		$action = $_GET['action'];
		
	switch ($action) {

		case 'remove':
			// remove only exists as a GET variable with id.
			$resid = $_GET['resid'];
			$res_sts = intval(get_reservation_status($resid));
			$res_txt = get_res_status_text($res_sts);
			if ($res_txt == Quote)
				update_reservation_status($resid, RES_EXPIRE);
			if ($res_txt == Active)
				update_reservation_status($resid, RES_VOID);

			break;
		case $_L['BTN_search']:
			if(isset($_POST['active'])) {
				$active = $_POST['active'];
			}
			if(isset($_POST['startdate'])) {
				$start = $_POST['startdate'];
				//$_POST['startdate'] = '';
			}
			if(isset($_POST['enddate'])) {
				$end = $_POST['enddate'];
				//$_POST['enddate'] = '';
			}
			if($_POST['optFind'] == $_L['RSV_guest']) { 
				if(isset($_POST['name'])) 
					$name = $_POST['name'];
				//$active = 0;
			}
			
			if($_POST['optFind'] == $_L['RSV_voucherno']) {
				if(isset($_POST['voucher']))
					$vouchernum = $_POST['voucher'];
				//$active = 0;
			}			
			
			break;
	}
}
if(!isset($_POST['active'])) {
	$active = RES_ACTIVE;
}

$reslist = array();
$res = get_reservationlist($start, $end, $name, $reslist,$active,$vouchernum);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen"></link>
	<script type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	<script type="text/javascript" src="js/datefuncs.js"></script>
	<link href="css/new.css" rel="stylesheet" type="text/css" />
	<title> <?php echo $_L['MAIN_Title'];?></title>
	<script type="text/javascript">
	  <!--
	  var request;
	  var dest;

	  function loadHTML(URL, destination){
		dest = destination;
		if (window.XMLHttpRequest){
          request = new XMLHttpRequest();
          request.onreadystatechange = processStateChange;
          request.open("GET", URL, true);
          request.send(null);
		} else if (window.ActiveXObject) {
          request = new ActiveXObject("Microsoft.XMLHTTP");
          if (request) {
            request.onreadystatechange = processStateChange;
            request.open("GET", URL, true);
            request.send();
          }
		}
	  }

	  function processStateChange(){
		if (request.readyState == 4){
          contentDiv = document.getElementById(dest);
		  if (request.status == 200){
            response = request.responseText;
            contentDiv.innerHTML = response;
          } else {
            contentDiv.innerHTML = "Error: Status "+request.status;
          }
		}
	  }

	  function loadHTMLPost(URL, destination){
		dest = destination;
		if (window.XMLHttpRequest){
          request = new XMLHttpRequest();
          request.onreadystatechange = processStateChange;
          request.open("POST", URL, true);
          request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
       	  request.setRequestHeader("Content-length", parameters.length);
      	  request.setRequestHeader("Connection", "close");
		  request.send("good");
		} else if (window.ActiveXObject) {
          request = new ActiveXObject("Microsoft.XMLHTTP");
          if (request) {
            request.onreadystatechange = processStateChange;
            request.open("POST", URL, true);
            request.send();
          }
		}
	  }
	  //-->	 
	</script>
	<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
  </head>
  <body>
	<form action="index.php?menu=reservationlist" method="post" enctype="multipart/form-data">
      <table class="listing-table" width="100%" border="0" cellpadding="1" align="center">
        <tr valign="top">
           <?php 
			if ($_GET['menu'] == "reservationlist") {
				print_rightMenu_home();
			}?> 
		  <td class=c4>
			<table width="100%"  border="0" cellpadding="1">
			  <tr><td align="center"></td></tr>
			  <tr><td><h2><?php echo $_L['RSV_list']; ?> </h2></td></tr>
			  <tr>
			  <td >
				<table width="60%" cellpadding="1">	 
					<tr><td><?php echo $_L['PR_actions']; ?>
						<input type='hidden' name='Submit' id='Submit' value=""/>
						<select name="active" onChange="document.getElementById('Submit').value='<?php echo $_L['BTN_search'];?>';document.forms[0].submit();">
						  <option value="0"><?php echo $_L['RSV_list']; ?></option>
						  <option value="0" ><?php echo $_L['BTN_listall'];?></option>
						  <option value="<?php echo RES_QUOTE;?>" <?php if ($_POST['active']==RES_QUOTE) {echo 'selected';}?>><?php echo $_L['RSV_quote'];?></option>
						  <option value="<?php echo RES_ACTIVE;?>" <?php if ($_POST['active']==RES_ACTIVE || $_POST['active']=="") {echo 'selected';}?> ><?php echo $_L['RSV_active'];?></option>
						  <option value="<?php echo RES_CANCEL;?>" <?php if ($_POST['active']==RES_CANCEL) {echo 'selected';}?>><?php echo $_L['RSV_cancelled'];?></option>
						  <option value="<?php echo RES_EXPIRE;?>" <?php if ($_POST['active']==RES_EXPIRE) {echo 'selected';}?>><?php echo $_L['RSV_expired'];?></option>
						  <option value="<?php echo RES_CHECKIN;?>" <?php if ($_POST['active']==RES_CHECKIN) {echo 'selected';}?>><?php echo $_L['RSV_checkin'];?></option>
						  <option value="<?php echo RES_VOID;?>" <?php if ($_POST['active']==RES_VOID) {echo 'selected';}?>><?php echo $_L['RSV_void'];?></option>
						  <option value="<?php echo RES_CLOSE;?>" <?php if ($_POST['active']==RES_CLOSE) {echo 'selected';}?>><?php echo $_L['RSV_close'];?></option>
						</select>
						</td>
					  <td>
					  <input class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_search']; ?>"/></br>
					  </td>
					</tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
					  <td>
						<label><?php echo $_L['PR_criteria'];?>:<br />
						  <img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].startdate,'dd/mm/yyyy',this, false, 0)"/>
						  <input type="text" name="startdate" id="startdate" size=16 maxlength=16 readonly value="<?php if (!$_POST['startdate']) {echo $start;} else {echo trim($_POST['startdate']);} ?>" />
						  <br/>
						  <img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].enddate,'dd/mm/yyyy',this, false, 0)"/>
						  <input type="text" name="enddate" id="enddate" size=16 maxlength=16 readonly value="<?php if (!empty($_POST['enddate'])) echo trim($_POST['enddate']);?>"/>
						  <br/>
						</label>
						</td>
						<td><label><input type="radio" name="optFind" id="optFind" value="<?php echo $_L['RSV_guest']; ?>" <?php if ($_POST['optFind']==$_L['RSV_guest']) {echo "checked";}?> /><?php echo $_L['RSV_guest']; ?></label>
						
						<input type="text" name="name" id="name" width="100" <?php if(isset($_POST['name'])) echo "value='".$_POST['name']."'"; ?> /><br>
						
						<label><input type="radio" name="optFind" id="optFind" value="<?php echo $_L['RSV_voucherno']; ?>" <?php if ($_POST['optFind']==$_L['RSV_voucherno']) {echo "checked";}?> /><?php echo $_L['RSV_voucherno']; ?></label>
						
						<input type="text" name="voucher" id="voucher" width="100" <?php if(isset($_POST['voucher'])) echo "value='".$_POST['voucher']."'"; ?> /><br/>
					
						
		
					  </td>
						</tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
				</table>
			  </td>				
			  </tr>

			
			  <tr>
				<td>
				  <div class="scroll2" id="Requests">				  
					<table align=\"center\">
					<?php
					//get field names to create the column header
					echo "<tr bgcolor=\"#3593de\">
						<th colspan=\"4\">Action</th>
						<th>".$_L['RSV_voucherno']."</th>
						<th>".$_L['STS_title']."</th>
						<th>".$_L['RSV_guest']."</th>
						<th>".$_L['RSV_arrival']."</th>
						<th>".$_L['RSV_depart']."</th>
						<th>".$_L['RSV_nightsno']."</th>
						<th>".$_L['RSV_guestsno']."</th>
						</tr>";
					//end of field header
					//get data from selected table on the selected fields
					foreach($reslist as $idx => $val) {
					  //alternate row colour
					  if(($reslist[$idx]['status'] == RES_VOID) || ($reslist[$idx]['status'] == RES_CANCEL) || ($reslist[$idx]['status'] == RES_EXPIRE)) {
						$del = "<del>";
						$edel = "</del>";
					  } else {
						$del = "";
						$edel = "";
					  }
					  $j++;
					  if($j%2==1){
						echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
					  }else{
						echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
					  }
					  echo "<td><a href=\"index.php?menu=reservation&resid=".$reslist[$idx]['reservation_id']."\" ><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view/edit reservation\"/></a></td>";
					  echo "<td><a href=\"index.php?menu=booking&resid=".$reslist[$idx]['reservation_id']."\" ><img src=\"images/bed.jpg\" width=\"16\" height=\"16\" border=\"0\" title=\"checkin guest\"/></a></td>";
					  echo "<td><a href=\"index.php?menu=invoice&id=".$reslist[$idx]['reservation_id']."\" ><img src=\"images/button_signout.png\" width=\"16\" height=\"16\" border=\"0\" title=\"invoice guest\"/></a></td>";
					  echo "<td><a href=\"index.php?menu=reservationlist&resid=".$reslist[$idx]['reservation_id']."&action=remove\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"delete reservation\"/></a></td>";
					  echo "<td>" .$del. $reslist[$idx]['voucher_no'] .$edel. "</td>";					
					  echo "<td>" . get_res_status_text($reslist[$idx]['status']) . "</td>";
					  echo "<td>" .$del. $reslist[$idx]['guestname'] .$edel. "</td>";
					  echo "<td>" .$del. $reslist[$idx]['checkindate'] .$edel. "</td>";
					  echo "<td>" .$del. $reslist[$idx]['checkoutdate'] .$edel. "</td>";
					  echo "<td>" .$del. $reslist[$idx]['no_nights'] .$edel. "</td>";			
					  echo "<td>" .$del. $reslist[$idx]['no_pax'].$edel. "</td>";
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