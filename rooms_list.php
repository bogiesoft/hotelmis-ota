<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file rooms_list.php
 * @brief rooms list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup ROOM_MANAGEMENT
 * @{
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();

$bedtype = $_POST['search'];
$status = $_POST['roomstatus'];

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	$id=$_POST['id'];
	switch ($action) {
		case 'List':
		
			break;
		case $_L['BTN_search']:
			$search=$_POST["search"];
			if($_POST['optFind'] == $_L['RM_roomno']) $stype = 1;
			if($_POST['optFind'] == $_L['RM_type']) $stype = 2;
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
				  <table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
					<tr valign="top">
					  <td class="tdbgcl" width="65%" bgcolor="#FFFFFF">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						  <tr><td colspan="2"><h2><?php echo $_L['RM_listroom']; ?></h2></td></tr>
						  <tr align="center">
							 <td colspan="3">
								<label> <?php echo $_L['PR_search']; ?>:</label>
								<label>
									<input type="radio" name="optFind" value="<?php echo $_L['RM_roomno']; ?>" /><?php echo $_L['RM_roomno']; ?> 
									<input type="radio" name="optFind" value="<?php echo $_L['RM_type']; ?>" /><?php echo $_L['RM_type']; ?> 
								</label>
							 </td>
						  </tr>		
						  <tr align="center">
						  	<td colspan="3">
							 	<input type="text" name="search" size="10" />
								<input class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_search']; ?>"/>						  	
						  	</td>
						  </tr>					  
						  <tr><td>&nbsp;</td></tr>						  
						  <tr bgcolor="#2E71A7">
							<td><h1><font color="#000000"><?php echo $_L['RM_view']; ?></font></h1></td> 
							<td>
							  <label>
								<?php 
								$sel0 = "checked";
								$sel1 = "";
								$sel2 = "";
								$sel3 = "";
								$sel4 = "";			
			
								if($_POST['roomstatus'] == VACANT) {
								  $sel0 = "";
								  $sel1 = "checked";
								  $sel2 = "";
								  $sel3 = "";
								  $sel4 = "";			
								}
								if($_POST['roomstatus'] == RESERVED) {
								  $sel0 = "";
								  $sel1 = "";
								  $sel2 = "checked";
								  $sel3 = "";
								  $sel4 = "";			
								}
								if($_POST['roomstatus'] == BOOKED) {
								  $sel0 = "";
								  $sel1 = "";
								  $sel2 = "";
								  $sel3 = "checked";
								  $sel4 = "";			
								}
								if($_POST['roomstatus'] == LOCKED) {
								  $sel0 = "";
								  $sel1 = "";
								  $sel2 = "";
								  $sel3 = "";
								  $sel4 = "checked";			
								}
								?>
								<input type="radio" name="roomstatus" value="" <?php echo $sel0; ?> onchange="document.forms[0].submit();" /><font color="#000000"><?php echo $_L['RM_all']; ?></font>
							  </label>
							  <label>
								<input type="radio" name="roomstatus" <?php echo $sel1; ?> value="<?php echo VACANT; ?>" onchange="document.forms[0].submit();" /><font color="#000000"><?php echo $_L['RM_vacant']; ?></font>
							  </label>
							  <label>
								<input type="radio" name="roomstatus" <?php echo $sel2; ?> value="<?php echo RESERVED; ?>" onchange="document.forms[0].submit();" /><font color="#000000"><?php echo $_L['RM_reserved']; ?></font>
							  </label>
							  <label>
								<input type="radio" name="roomstatus" <?php echo $sel3; ?> value="<?php echo BOOKED; ?>" onchange="document.forms[0].submit();" /><font color="#000000"><?php echo $_L['RM_booked']; ?></font>
							  </label>
							  <label>
								<input type="radio" name="roomstatus" <?php echo $sel4; ?> value="<?php echo LOCKED; ?>" onchange="document.forms[0].submit();" /><font color="#000000"><?php echo $_L['RM_locked']; ?></font>
							  </label>
							</td>
						  </tr>
						  <tr>
							<td colspan="2">
							  <div id="Requests" style="overflow:auto; width:772; height:303;">
								<?php
								$rooms = array();
								get_roomslist($rooms, $search, $stype, $status);
								echo "<table align=\"center\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">";
								//get field names to create the column header
								echo "<tr bgcolor=\"#3593DE\">
									<th></th>
									<th>".$_L['RM_roomno']."</th>
									<th>".$_L['RM_type']."</th>
									<th>".$_L['RM_guest']."</th>
									<th>".$_L['RM_in']."</th>
									<th>".$_L['RM_out']."</th>
									<th>".$_L['RM_nights']."</th>
									<th>".$_L['RM_rateplan']."</th>
									<th>".$_L['RM_adults']."</th>
									<th>".$_L['RM_children']."</th>
									<th>".$_L['RM_status']."</th>
									</tr>";
									//end of field header
									//get data from selected table on the selected fields
								  foreach ($rooms as $idx => $val) {
									//alternate row colour
									$j++;
									if($j%2==1){
									  echo "<tr bgcolor=\"#CCCCCC\">";
									}else{
								      echo "<tr bgcolor=\"#EEEEF8\">";
									}
									  
									  echo "<td><a href=\"index.php?menu=roomSetup&search=".$rooms[$idx]['roomno']."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view room details\"/></a></td>";
									  echo "<td>" . $rooms[$idx]['roomno'] . "</td>";
									  echo "<td>" . $rooms[$idx]['roomtype'] . "</td>";
									  echo "<td>" . $rooms[$idx]['guest'] . "</td>";
									  echo "<td>" . $rooms[$idx]['checkin_date'] . "</td>";
									  echo "<td>" . $rooms[$idx]['checkout_date'] . "</td>";
									  echo "<td>" . $rooms[$idx]['nights'] . "</td>";
									  echo "<td>" . $rooms[$idx]['ratecode'] . "</td>";
									  echo "<td>" . $rooms[$idx]['no_adults'] . "</td>";					
									  echo "<td>" . $rooms[$idx]['no_child'] . "</td>";
									  echo "<td>" . $rooms[$idx]['status'] . "</td>";
									  echo "</tr>"; //end of - data rows
								  } //end of while row
								  echo "</table>";
								  ?>
							  </div>
							</td>		
						  </tr>		
						  <tr class="tdbgcl" ><td>&nbsp;</td></tr>	
						  <tr align="right"><td colspan="2">
						  	<?php if (accessNew('rooms')) { ?>
						  		<input class="button" type="button" name="Submit" value="<?php echo $_L['RM_addroom'];?>" onclick="self.location='index.php?menu=roomSetup'"/>
						  	<?php } ?>
						  	<input class="button" type="button" name="Submit" value="<?php echo $_L['RM_listroom'];?>" onclick="self.location='index.php?menu=roomsList'"/>
						  </td></tr>
						  <tr><td>&nbsp;</td></tr>						  				
						  <tr class="tdbgcl" ><td align="left" colspan="2"><div id="RequestDetails"></div></td></tr>
						</table>
					</tr>
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
 */
?>
