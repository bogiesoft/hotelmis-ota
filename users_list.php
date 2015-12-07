<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file users_list.php
 * @brief user list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup USER_MANAGEMENT
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
access("admin"); //check if user is allowed to access this page

$usrlistby = $_POST['usrlistby'];

$search = "";
$stype = 0;
$users = array();

if (isset($_GET['action'])){
	$action = $_GET['action'];
	$id = $_GET['id'];
	if ($action == 'remove') {
		if ($id == 2) {
			echo $_L['USR_deladmin']; //id=2 is admin id, which could not delete.
		} else {
			$results=delete_user($id);
			$msg[0]=$_L['USR_error'];
			$msg[1]=$_L['USR_success'];
			AddSuccess($results,$conn,$msg);
		}
	}
}
if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	$id=$_POST['id'];
	switch ($action) {
		case 'List':
		
			break;
		case $_L['BTN_search']:
			$search=$_POST["search"];
			if($_POST['optFind'] == $_L['PR_username']) $stype = 1;
			if($_POST['optFind'] == $_L['PR_userid']) $stype = 2;
			break;
	}
}

?>

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>	
	 		<?php print_rightMenu_admin();?> 		
	          <td valign="top">	          	
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				  <table width="100%"  border="0" cellpadding="1" align="center" class="tdbgcl">
					<tr valign="top">
					  <td width="50%" class="tdbgcl">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						  <tr><td colspan="2"><h2><?php echo $_L['USR_title']; ?> </h2></td></tr>					  
						<tr align="center">
							<td colspan="5">
								<label> <?php echo $_L['PR_search'].":"; ?>
								  <input type="radio" name="optFind" value="<?php echo $_L['PR_username']; ?>" /><?php echo $_L['PR_username']; ?>
								</label>
								<label>
								  <input type="radio" name="optFind" value="<?php echo $_L['PR_userid']; ?>" /><?php echo $_L['PR_userid']; ?>
								</label>
							</td>
						</tr>	
						<tr align="center">
							<td colspan="5">
								<input type="text" name="search" width="50" />
								<input type="submit" name="Submit" class="button" value="<?php echo $_L['BTN_search']; ?>"/>
						  						
							</td>
						</tr>				
					<tr><td>&nbsp;</td></tr>
						  <tr bgcolor="#2E71A7">
							<td><h1><font color="#000000"><?php echo $_L['USR_options']; ?></font></h1></td> 
							<td colspan="2">
							 <?php 
								$sel1 = "checked";
								$sel2 = "";
								$sel3 = "";
								$sel4 = "";	
								if($_POST['usrlistby'] == "employee") {
								  $sel1 = "";
								  $sel2 = "checked";
								  $sel3 = "";
								  $sel4 = "";			
								}
								if($_POST['usrlistby'] == "agent") {
								  $sel1 = "";
								  $sel2 = "";
								  $sel3 = "checked";
								  $sel4 = "";			
								}
								if($_POST['usrlistby'] == "guest") {
								  $sel1 = "";
								  $sel2 = "";
								  $sel3 = "";
								  $sel4 = "checked";			
								}
							 ?>
							  <label><input type="radio" name="usrlistby" <?php echo $sel1; ?> value="all" onchange="document.forms[0].submit();"  /><font color="#000000"><?php echo $_L['USR_all']; ?></font></label>
							  <!-- <label><input type="radio" name="usrlistby" <?php //echo $sel2; ?> value="employee" onchange="document.forms[0].submit();"  /><?php //echo $_L['USR_employee']; ?></label> --> 
							  <label><input type="radio" name="usrlistby" <?php echo $sel3; ?> value="agent" onchange="document.forms[0].submit();"  /><font color="#000000"><?php echo $_L['USR_agents']; ?></font></label>
							  <!-- <label><input type="radio" name="usrlistby" <?php //echo $sel4; ?> value="guest" onchange="document.forms[0].submit();"  /><?php //echo $_L['USR_guests']; ?></label> --> 
							</td>
						  </tr>
						  <tr>
							<td valign="top" height="288" colspan="2">
							  <div id="Requests"  style="overflow:auto; width:772; height:220;">
							  <?php 
							  $usr = get_userslist($users, $search, $stype, $usrlistby);
							  ?>
								<table align="center" border="1" cellspacing="0" cellpadding="3">
								<tr bgcolor="#3593DE">
								  <th colspan="2"><?php echo $_L['USR_action']; ?></th>
								  <th><?php echo $_L['PR_userid']; ?></th>
								  <th><?php echo $_L['PR_username']; ?></th>
								  <th><?php echo $_L['USR_admin']; ?></th>
								  <th><?php echo $_L['USR_guest']; ?></th>
								  <th><?php echo $_L['USR_reservation']; ?></th>
								  <th><?php echo $_L['USR_booking']; ?></th>
								  <th><?php echo $_L['USR_agents']; ?></th>
								  <th><?php echo $_L['USR_rooms']; ?></th>
								  <th><?php echo $_L['USR_bills']; ?></th>
								  <th><?php echo $_L['USR_rates']; ?></th>
								  <th><?php echo $_L['USR_lookup']; ?></th>
								  <th><?php echo $_L['USR_reports']; ?></th>				
								</tr>
								<?php
								$j = 0;
								foreach ($users as $idx=>$val) {
								  $j++;
								  if($j%2==1){
									echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#CCCCCC\">";
								  }else{
									echo "<tr id=\"row$j\" onmouseover=\"javascript:setColor('$j')\" onmouseout=\"javascript:origColor('$j')\" bgcolor=\"#EEEEF8\">";
								  }
								  echo "<td><a href=\"index.php?menu=userSetup&id=".$users[$idx]['userid']."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view/edit user details\"/></a></td>";
								  echo "<td><a href=\"index.php?menu=usersList&id=".$users[$idx]['userid']."&action=remove\"><img src=\"images/button_remove.png\" width=\"16\" height=\"16\" border=\"0\" title=\"remove user\"/></a></td>";
								  echo "<td>" . $users[$idx]['userid'] . "</td>";
								  echo "<td>" . $users[$idx]['user'] . "</td>";
								  echo "<td>" . $users[$idx]['admin'] . "</td>";
								  echo "<td>" . $users[$idx]['guest'] . "</td>";
								  echo "<td>" . $users[$idx]['reservation'] . "</td>";
								  echo "<td>" . $users[$idx]['booking'] . "</td>";
								  echo "<td>" . $users[$idx]['agents'] . "</td>";
								  echo "<td>" . $users[$idx]['rooms'] . "</td>";
								  echo "<td>" . $users[$idx]['billing'] . "</td>";
								  echo "<td>" . $users[$idx]['rates'] . "</td>";
								  echo "<td>" . $users[$idx]['lookup'] . "</td>";					
								  echo "<td>" . $users[$idx]['reports'] . "</td>";										
								  echo "</tr>"; //end of - data rows
								} //end of while row
								echo "</table>";
								?>
							 
							</td>		
						  </tr>
						  <tr><td align="left" colspan="2"><div id="RequestDetails"></div></td></tr>
						</table>
						</div>
					  </td>
					</tr>
					<tr class="tdbgcl" ><td>&nbsp;</td></tr>
					<tr><td>&nbsp;</td></tr>
											  <tr>
							<td colspan="2" align="right"><input class="button" type="button" name="Submit" value="<?php echo $_L['USR_adduser'] ?>" onclick="self.location='index.php?menu=userSetup'"/>
								<input class="button" type="button" name="Submit" value="<?php echo $_L['USR_listuser'] ?>" onclick="self.location='index.php?menu=usersList'"/>&nbsp;&nbsp;
							</td>							  				
						</tr>	
						<tr><td>&nbsp;</td></tr>
				  </table>
				</form>	           
	          </td>          
	        </tr>
	        <tr>
	          <td colspan="2">&nbsp;</td>
	        </tr>
	      </tbody>
      </table>
<?php 
/**
 * @}
 * @}
 */?>

