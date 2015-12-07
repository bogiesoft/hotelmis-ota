<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file rooms.php
 * @brief rooms webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ROOM_MANAGEMENT Room setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

access("rooms"); //check if user is allowed to access this page
$logofile = Get_LogoFile();
$lang = get_language();
load_language($lang);


//get the list of all the room number in the system
$rmNoList = get_roomnolist();

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['RM_addroom']:
		case $_L['BTN_update']:
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('roomno',$_L['RMT_noroom']);
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			}
			else {
				/** 
				 * When the Room is to be added or updated the form data will be retrieved.
				 * This includes the phone, room number etc which is then
				 * submitted to the database. <br/>
				 */
				//gets photo.
				if ((isset($_REQUEST['form_submit'])) && ('form_uploader' == $_REQUEST['form_submit'])){

					if  (is_uploaded_file($_FILES['photo']['tmp_name'])) {
						$filename = $_FILES['photo']['name'];
						$filetype=$_FILES['photo']['type'];
						$file_temp=$_FILES['photo']['tmp_name'];	
						$filesize=filesize($file_temp);
						$photo=base64_encode(fread(fopen($file_temp, "rb"),$filesize));
					} 
				}
				$roomid=$_POST["roomid"];
				$roomno=$_POST["roomno"];
				$roomtypeid=$_POST["roomtypeid"];
				$roomname=$_POST["roomname"];
				$noofrooms=$_POST["noofrooms"];
				$occupancy=$_POST["occupancy"];
				$status=$_POST["status"];
				$photo=$_POST["photo"];
				$filetype=$_POST["filetype"];
				$bedcount=$_POST["bedcount"];
				$bedtype1=$_POST["bedtype1"];
				$bedtype2=$_POST["bedtype2"];
				$bedtype3=$_POST["bedtype3"];
				$bedtype4=$_POST["bedtype4"];
				$ratesid =$_POST["ratesid"];
				if($roomid > 0 || ($roomid == 0 && !in_array($roomno,$rmNoList))){
					$roomid=modify_room($roomid,$roomno,$roomtypeid,$roomname,$noofrooms,$bedcount,
						$bedtype1,$bedtype2,$bedtype3,$bedtype4,$occupancy,$status,$photo,$filetype,$ratesid);
					if(is_ebridgeCustomer()){
						include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
						CustomPagesFormRead( HTL_PAGE_ROOM, $roomid);
					}
				}
				if(!$roomid){
					echo "<div align=\"center\"><h1>".$_L['RM_error'] ."</h1></div>";
				}
				else{
					if ($action == $_L['BTN_update']) {
						echo "<div align=\"center\"><h1>".$_L['RM_updatesuccess']."</h1></div>";
					} else {
						echo "<div align=\"center\"><h1>".$_L['RM_addsuccess']."</h1></div>";
					}
				}
				/** After successfully saving the room details, the room amenities
				 * will be deleted then re-added 1 at a time from the new list
				 */
				delete_roomamenities($roomid);
				if(isset($_POST['RoomAmenity'])) {
					foreach ($_POST['RoomAmenity'] as $idx=>$val) {
						add_roomamenity($roomid, $val);
					}
				}	 	
			}
			break;
		case $_L['BTN_list']:

			break;
		case $_L['BTN_search']:
			//check if user is searching using name, payrollno, national id number or other fields
			$roomno=$_POST["search"];
			$res = find_room($roomno, $rooms);
			$roomid = $rooms['roomid'];
			break;
	}
}
if($_GET['search'] || $_POST['roomno']) {
	$roomno = $_POST['roomno'];
	if($_GET['search'] ) $roomno=$_GET["search"];
	$res = find_room($roomno, $rooms);
	$roomid = $rooms['roomid'];
	$_GET['search'] = 1;
}

$roomamenities = array();
get_roomamenities($roomamenities);

$allocated = array();
if($roomid) {
	get_allocatedroomamenities($roomid, $allocated);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<link href="css/new.css" rel="stylesheet" type="text/css">
	<title><?php echo $_L['MAIN_Title']; ?></title>
	<script type="text/javascript">
	  <!--
	  var request;
	  var dest;
	  /**
	   * Delete the selected amenity from the RoomAmenity select list
	   * and add the value back into the RoomAmenityList pulldown.
	   */
	  function removeRoomAmenitySelected() {
		var elSel = document.getElementById('RoomAmenity');
		var i;
		for (i = elSel.length - 1; i>=0; i--) {
		  if (elSel.options[i].selected) {
			appendAmenityItemList(elSel.options[i].value, elSel.options[i].text);
			elSel.remove(i);
		  }
		}
	  }
	  /** 
	   * When submitting the form, only the selected items are passed as POST variables
	   * back to the from. In order to retrieve what was added to the RoomAmenity select
	   * list, all items must be selected at the "onSubmit" call.
	   */
	  function selectallAmenities() {
		var elSel = document.getElementById('RoomAmenity');
		var i;
//		alert("Selecting all amenities");
		for (i = elSel.length - 1; i>=0; i--) {
		  elSel.options[i].selected = true;
		}
	  }
	  /** 
	   * Remove the item from the amenity list RoomAmenityList pulldown.
	   * finds the selected item and deletes it from the list
	   */
	  function removeAmenityItem() {
		var elSel = document.getElementById('RoomAmenityList');
		var i;
		for (i = elSel.length - 1; i>=0; i--) {
		  if (elSel.options[i].selected) {
			elSel.remove(i);
		  }
		}
//		elSel.options.length--;
	  }
	  /**
	   * Add the item back into the amenity list RoomAmenityList pulldown.
	   * @param val [in] The value for the option
	   * @param txt [in] The description text from the option
	   */
	  function appendAmenityItemList(val, txt) {
		var elSel = document.getElementById('RoomAmenityList');
		var num = elSel.length + 1;
		var elOptNew = document.createElement('option');
		elOptNew.text = txt;
		elOptNew.value = val;
		if(val) {
		  try {
			elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		  }
		  catch(ex) {
			elSel.add(elOptNew); // IE only
		  }
		}
	  }
	  /** 
	   * Javascript to add the amenity into the want list
	   * Gets the RoomAmenityList pull down, finds the selected item.
	   * add the item to the RoomAmenity select list and deletes
	   * it from the RoomAmenityList.
	   */
	  function appendAmenity() {
		var AmSel = document.getElementById('RoomAmenityList');
		var i;
		var val;
		var txt;
		for (i = AmSel.length - 1; i>=0; i--) {
		  if (AmSel.options[i].selected) {
			val = AmSel.options[i].value;
			txt = AmSel.options[i].text;
		  }
		}
		var elSel = document.getElementById('RoomAmenity');
		var num = elSel.length + 1;
		var elOptNew = document.createElement('option');
		if(val) {
		  elOptNew.text = txt;
		  elOptNew.value = val;
		  try {
			elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
		  }
		  catch(ex) {
			elSel.add(elOptNew); // IE only
		  }
		  removeAmenityItem();
		}
	  }
//-->	 
	</script>
	<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
	<style>
	  .plainDropDown{
		width:150px;
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
<?php
	$onsubmit = '';
	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
		CustomPagesOnSubmitFunctionCode(HTL_PAGE_ROOM);
		$onsubmit=CustomPagesOnSubmitFunctionCall(HTL_PAGE_ROOM);
		if($onsubmit) $onsubmit = 'onsubmit="'.$onsubmit.'"';
	}
?>

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_admin();?> 	
	          <td valign="top">          

				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data" <?php echo $onsubmit; ?>>
			      <table class="c16" width="100%" border="0" cellpadding="1" align="center">
			        <tr valign="top">
			   		  <td class="c4" width="65%">
						<table height="450" width="100%"  border="0" cellpadding="1">						  
						  <tr><td><h2><a href="https://www.youtube.com/watch?v=0PTN5L_uKs0" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['RM_title']; ?></h2></a></td></tr>
						  <tr>
					  		<td><?php echo $validationMsgs;?></td>
					  	</tr>
						  <tr>
							<td valign="top">
							
							             <div id="TabbedPanels1" class="TabbedPanels">
                <ul id="tabgroup" class="TabbedPanelsTabGroup">                
                  <li class="TabbedPanelsTab" tabindex="0"><?php echo $_L['USR_rooms']; ?></li>
                  <?php if (is_ebridgeCustomer()) {?>
                  	<li class="TabbedPanelsTab" tabindex="1"><?php echo $_L['CST_fields']; ?></li>
                  <?php }?>
                </ul>
                <div class="TabbedPanelsContentGroup">     
                  
                  <div class="TabbedPanelsContent">
							  <table height="380px" class="tdbgcl" width="100%"  border="0" cellpadding="1">
								<tr>
								  <td width="16%"><?php echo $_L['RM_roomno']; if($roomid) echo "<input type=hidden name=roomid id=roomid value='".$roomid."' />"; ?><font color="#FF0000">*</font></td>
								  <td width="84%"><input type="text" name="roomno" value="<?php echo trim($rooms['roomno']); ?>" /></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_type']; ?></td>
								  <td><select name="roomtypeid"><?php populate_select("roomtype","roomtypeid","roomtype",$rooms['roomtypeid'], "");?></select></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_otherRate']; ?></td>
								  <td><select name="ratesid"><option value='0'></option><?php populate_select("rates","ratesid","ratecode",$rooms['rateid'], "rate_type=".DEFAULTRATE);?></select></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_name']; ?></td>
								  <td><input type="text" name="roomname" value="<?php echo trim($rooms['roomname']); ?>" /></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_numrooms']; ?></td>
								  <td><input type="text" name="noofrooms" value="<?php echo trim($rooms['noofrooms']); ?>" /></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_bedc']; ?>
									<select id="bedcount" name="bedcount" onchange="
										if(this.value==1){
										 document.getElementById('bendcntOne').style.display = 'block';
										 document.getElementById('bendcntTwo').style.display = 'none';
										 document.getElementById('bendcntThree').style.display = 'none';
										 document.getElementById('bendcntFour').style.display = 'none';
										}else if(this.value==2){
										 document.getElementById('bendcntOne').style.display = 'block';
										 document.getElementById('bendcntTwo').style.display = 'block';
										 document.getElementById('bendcntThree').style.display = 'none';
										 document.getElementById('bendcntFour').style.display = 'none';
										}else if(this.value==3){
										 document.getElementById('bendcntOne').style.display = 'block';
										 document.getElementById('bendcntTwo').style.display = 'block';
										 document.getElementById('bendcntThree').style.display = 'block';
										 document.getElementById('bendcntFour').style.display = 'none';
										}else{
										 document.getElementById('bendcntOne').style.display = 'block';
										 document.getElementById('bendcntTwo').style.display = 'block';
										 document.getElementById('bendcntThree').style.display = 'block';
										 document.getElementById('bendcntFour').style.display = 'block';
										}							 
									">
									  <option value=1 <?php if($rooms['bedcount'] == 1) echo "selected"; ?> > 1 </option>
									  <option value=2 <?php if($rooms['bedcount'] == 2) echo "selected"; ?> > 2 </option>
									  <option value=3 <?php if($rooms['bedcount'] == 3) echo "selected"; ?> > 3 </option>
									  <option value=4 <?php if($rooms['bedcount'] == 4) echo "selected"; ?> > 4 </option>
									</select>
								  </td>
								  <td>	
								  <table align="left"><tr><td>
								  <div id="bendcntOne" style="display:block">
					  				<table width="100%">
					  				<tr>
					  				<td>
					  				<select name="bedtype1"><option value=0> </option><?php populate_select("ota_bedtype","OTA_Number","Description",$rooms['bedtype1'], "lang='".$lang."'");?></select>
									</td>
					  				</tr>
					  				</table>
								  </div>
								  </td>
								  <td>
								  <div id="bendcntTwo" style="<?php if($rooms['bedcount']&&$rooms['bedcount']!='1') echo "display:block"; else echo "display:none";?>">
								   <table width="100%">
					  				<tr>
					  				<td>
					  				<select name="bedtype2"><option value=0> </option><?php populate_select("ota_bedtype","OTA_Number","Description",$rooms['bedtype2'], "lang='".$lang."'");?></select>
									</td>
					  				</tr>
					  				</table>
								  </div>
								  </td>
								  <td>
								  <div id="bendcntThree" style="<?php if($rooms['bedcount']&&($rooms['bedcount']=='3'||$rooms['bedcount']=='4')) echo "display:block"; else echo "display:none";?>">
								   <table width="100%">
					  				<tr>
					  				<td>
					  				<select name="bedtype3"><option value=0> </option><?php populate_select("ota_bedtype","OTA_Number","Description",$rooms['bedtype3'], "lang='".$lang."'");?></select>
									</td>
					  				</tr>
					  				</table>
								  </div>
								  </td>
								  <td>
								  <div id="bendcntFour" style="<?php if($rooms['bedcount']&&$rooms['bedcount']=='4') echo "display:block"; else echo "display:none";?>">
					  				<table width="100%">
					  				<tr>
					  				<td>
					  				<select name="bedtype4"><option value=0> </option><?php populate_select("ota_bedtype","OTA_Number","Description",$rooms['bedtype4'], "lang='".$lang."'");?></select>					 
					  				</td>
					  				</tr>
					  				</table>
								  </div>
								  </td></tr></table>	
								 </td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_occupancy'] ?></td>
								  <td><input type="text" name="occupancy" value="<?php echo trim($rooms['occupancy']); ?>" /></td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_amenity']; ?></td>
								  <td>
									<table align="left">
									  <tr>
										<td>
										  <select size=4 name="RoomAmenity[]" id=RoomAmenity multiple class="plainDropDown" >
											<?php
											// Re-add any items already submitted in the previous query.
											foreach ($allocated as $idx=>$val) { print "<option value='".$idx."'> ".$val."</option>\n"; }
											?>					
										  </select>
										</td>
										<td>
										  <select name=RoomAmenityList id=RoomAmenityList size=1 class="plainDropDown" >
											<?php
											foreach($roomamenities as $idx=>$val) {
											  if(! $allocated[$idx] ) print "<option value='".$idx."'> ".$val."</option>\n";
											}
											?>
										  </select><br/>
										  <input type=button name=AddAmenity id=AddAmenity class="plainButton" value='<?php echo $_L['BTN_add']; ?>' onclick="appendAmenity();" /><br/>
										  <input type=button name=RemoveAmenity id=RemoveAmenity class="plainButton" value='<?php echo $_L['BTN_delete']; ?>' onclick="removeRoomAmenitySelected();" />
										</td>
									  </tr>	
									</table>
								  </td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_status']; ?></td>
								  <td>
									<table width="90%" border="0" cellpadding="1">
									  <tr>
										<td width="24%" ><label><input type="radio" name="status" checked="checked" value="<?php echo VACANT; ?>" <?php echo ($rooms['status']==VACANT ? "checked=\"checked\"" : ""); ?> />
										<?php echo $_L['RM_vacant']; ?></label></td>
										<td width="28%"><label><input type="radio" name="status" value="<?php echo RESERVED; ?>" <?php echo ($rooms['status']==RESERVED ? "checked=\"checked\"" : ""); ?> />
										<?php echo $_L['RM_reserved']; ?></label></td>
										<td width="25%"><label><input type="radio" name="status" value="<?php echo BOOKED; ?>" <?php echo ($rooms['status']==BOOKED ? "checked=\"checked\"" : ""); ?> />
										<?php echo $_L['RM_booked']; ?></label></td>
										<td width="23%"><label><input type="radio" name="status" value="<?php echo LOCKED; ?>" <?php echo ($rooms['status']==LOCKED ? "checked=\"checked\"" : ""); ?> />
										<?php echo $_L['RM_locked']; ?></label></td>
									  </tr>
									</table>
								  </td>
								</tr>
								<tr>
								  <td><?php echo $_L['RM_imgurl']; ?></td>
								  <td>
								  <input type="text" name="photo" id="photo" size="48" value="<?php echo $rooms['photo'];?>"/>
								  <!-- 
									<input type="file" name="photo" />
									<input name="form_submit" type="hidden" id="form_submit" value="form_uploader" />
									-->
								  </td>
								</tr>
							  </table>
							  <table align="right">
								  <tr><td align="right"><input class="button" type="submit" name="Submit" value="<?php echo isset($_GET["search"]) ? $_L['BTN_update'] : $_L['RM_addroom']; ?>" onclick="selectallAmenities();" />
								  	  <input class="button" type="button" name="Submit" value="<?php echo $_L['RM_listroom']; ?>" onclick="self.location='index.php?menu=roomsList'"/>
								  </td></tr>							  
							  </table>
                  </div>
                  <?php if (is_ebridgeCustomer()) {?>
                  <div class="TabbedPanelsContent">
						<table height="380px" class="tdbgcl" cellpadding="1">
								<?php
									if(is_ebridgeCustomer()){
										include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
										print "<tr valign='top'><td colspan=2>\n";
										CustomPagesFormPrint(HTL_PAGE_ROOM, $roomid, 700, 350);
										print "</td></tr>\n";		
									}
								?>														
						</table>
						<table align="right">
						  <tr><td><input class="button" type="submit" name="Submit" value="<?php echo isset($_GET["search"]) ? $_L['BTN_update'] : $_L['RM_addroom']; ?>" onclick="selectallAmenities();" />
						  <input class="button" type="button" name="Submit" value="<?php echo $_L['RM_listroom']; ?>" onclick="self.location='index.php?menu=roomsList'"/>
						  </td></tr>							  
						</table>						
                  </div>
                <?php }?>

				</div>
			</div>				
			

							</td>
						  </tr>					

						  <tr><td align="left"><div id="RequestDetails"></div></td></tr>
						</table>
					  </td>
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
 * End of Room management documentation
 */
?>