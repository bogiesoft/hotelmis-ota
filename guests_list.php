<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file guests_list.php
 * @brief guest list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup GUEST_MANAGEMENT
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
error_reporting(E_ALL & E_STRICT);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("guest"); //check if user is allowed to access this page

$search = "";
$stype = 0;
$agent= array();
if(isset($_GET['del']) && $_GET['del']>0){

	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		delete_advProfile($_GET['del']);
	}
}
if(isset($_GET['id']) && isset($_GET['action']) && $_GET['id'] > 0 && $_GET['action'] == "remove") {
	delete_guest($_GET['id']);
}
if(isset($_POST['Submit'])){
	$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	$action=$_POST['Submit'];
	switch ($action) {
		case 'List':

			return;
			break;
		case $_L['BTN_search']:
			//check if user is searching using name, payrollno, national id number or other fields
			$search=$_POST["search"];
			$stype = 0;
			if(isset($_POST["optFind"])) 
				$stype = $_POST["optFind"];
			break;
	}
}

$gct = list_guests($search, $stype, $agent);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="css/new.css" rel="stylesheet" type="text/css" />
	<link href="css/styles.css" rel="stylesheet" type="text/css" />
	<title><?php echo $_L['MAIN_Title'];?></title>

	<script type="text/javascript">
	  <!--
	  var request;
	  var dest;

	  function deleteguest(idx, guest) {
		var msg = "<?php echo $_L['GSL_delete']; ?>" + idx + " " + guest;
		var answer=confirm(msg);
		if(answer) {
		  window.location ="index.php?menu=profile&id="+idx+"&action=remove"
		}
		return answer;
	  }
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
	  function loadHTMLPost(URL, destination, button){
		dest = destination;
		var str = 'button=' + button;

		if (window.XMLHttpRequest){
          request = new XMLHttpRequest();
          request.onreadystatechange = processStateChange;
          request.open("POST", URL, true);
          request.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
		  request.send(str);
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
	<form action="index.php?menu=profile" method="post" enctype="multipart/form-data">
	  <table height="500" border="0" class="listing-table" cellpadding="1" align="center" bgcolor="#FFFFFF">
		<tr valign="top" style="padding:5;">
		
				  <?php 
					if ($_GET['menu'] == "profile") {
						print_rightMenu_home();
					}?> 		
				
		  <td  style="padding:10;" height="430">
			<table width="100%" class="tdbgcl"><tr><td>
			<table width="100%" >
			  <tr><td  valign="top" align="center">&nbsp;</td></tr>
			  <tr><td colspan="2" align="left"><h2><?php echo $_L['GSL_title']; ?></h2></td></tr>
			  <tr >
			  <td valign="top" >
				<table width="100%"  border="0" cellpadding="1">	  
				<tr><td>&nbsp;</td></tr>
				  <tr>
				  
					<td>
					  <table width="100%" border="0" cellpadding="1" class="tdbgcl" >
						
						
						<tr  align="center" >
						  <td  style="padding:5;">
							<label><?php echo $_L['PR_search']; ?>:
							<input type="text" name="search" width="100" />
							<input  class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_search']; ?>"/><br/>
							  <input type="radio" name="optFind" value="1" />
							  <?php echo $_L['GSL_name']; ?>
							</label>
							<label>
							  <input type="radio" name="optFind" value="4" />
							  <?php echo $_L['GSL_srchid']; ?>
							</label>
							<label>
							  <input type="radio" name="optFind" value="6" />
							  <?php echo $_L['GSL_email']; ?>
							</label>
							<label>
							  <input type="radio" name="optFind" value="5" />
							  <?php echo $_L['GSL_phone']; ?>
							</label>
						  </td>
						</tr>
						
					  </table>
					</td>
				  </tr>
				</table>
			  </td>
			  </tr>
			 
			  <tr>
				<td colspan="2" style='padding:3'>
				  <div class="scroll" id="Requests" >
				  <?php 
				  	//display the list from advanced profile for e-Bridge customers
					if(is_ebridgeCustomer()){
						include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_pagefuncs.php");
						$sname = "";
						$sphone = "";
						$sid = "";
						$semail = "";
						if($search && $stype) {
							if($stype == 1) {
								$sname = $search;
							}
							if($stype == 4) {
								$sid = $search;
							}
							if($stype == 5) {
								$sphone = $search;
							}
							if($stype == 6) {
								$semail = $search;
							}
						}
						print_profileList($sname, $sid, $sphone,$semail);
					} else {
				  ?>
					<table  border="1" width="100%" align="center" >
					  <tr bgcolor="#3593de">
						<th colspan=2><?php echo $_L['GSL_action']; ?></th>
						<th><?php echo $_L['GSL_name']; ?></th>
						<th><?php echo $_L['GSL_passport']; ?></th>
						<th><?php echo $_L['GSL_email']; ?></th>
						<th><?php echo $_L['GSL_phone']; ?></th>
						<th><?php echo $_L['GSL_country']; ?></th>
					  </tr>
					  <?php
					  $j = 0;
					  foreach ($agent as $idx => $val) {
						$j++;
						if($j%2==1){
						  echo "<tr   id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
						}else{
						  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#E7E7E7\">";
						}
						echo "<td style='padding:3'><a href=\"index.php?menu=editprofile&id=".$idx."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"".$_L['GSL_viewedit']."\"/></a></td>";
						echo "<td><a onClick=\"deleteguest(".$idx.",'".$agent[$idx]['lastname']."');\" ><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"".$_L['GSL_remove']."\"/></a></td>";
						echo "<td>".$agent[$idx]['lastname'].",".$agent[$idx]['firstname']." ".$agent[$idx]['middlename']."</td>";
						echo "<td>";
						if($agent[$idx]['pp_no']) {
						  echo $agent[$idx]['pp_no'];
						} else {
						  echo $agent[$idx]['idno'];
						}
						echo "</td>";
						echo "<td>".$agent[$idx]['email']."</td>";
						echo "<td>";
						if($agent[$idx]['phone']) {
						  echo $agent[$idx]['phone'];
						} else {
						  echo $agent[$idx]['mobilephone'];
						}
						echo "</td>";
						echo "<td>".$agent[$idx]['country']."</td>";
						echo "</tr>";
					  }
					  ?>
					</table>
					<?php } ?>
				  </div>
				</td>		
			  </tr>
			 
			</table></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
			<td align=right><input class="button" type="button" name="Submit" value="<?php echo $_L['GST_addguest'];?>" onclick="self.location='index.php?menu=editprofile'" />
			<input  class="button" type="submit" name="Submit" value="<?php echo $_L['GST_listguest']; ?>" /></td>
			</tr></table>
		  </td>
		   
		  
		  
		</tr>
		<tr>
		  <td colspan=3>
			&nbsp;
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