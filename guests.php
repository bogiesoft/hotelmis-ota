<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file guests.php
 * @brief guests webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup GUEST_MANAGEMENT Guest setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("guest"); //check if user is allowed to access this page
// Short cut for modify guest by URL line.

$guestid=0;
$is_a_eBridgeCustomer=0;

if(isset($_GET['id'])) {
	$guestid=$_GET['id'];
}else if(isset($_POST['guestid'])) {
	$guestid=$_POST['guestid'];
}
//display the advanced user profile page with indispensable features 
//Only availble for ebridge customers
if(is_ebridgeCustomer()){
	include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
	$is_a_eBridgeCustomer=1;
	doDataMigration_fromGuests_toAdvProfile();
	$uri = "";
	if($guestid) {
		$uri="?id=".$guestid;
	}
	header("Location:advanced_profile.php".$uri);
	return 1;
}

if(isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['GST_addguest']:
		case $_L['GST_updguest']:
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('lastname',$_L['GST_ferr1']);
			$fv->validateEmpty('firstname',$_L['GST_ferr2']);
			$fv->validateEmpty('pp_id_no',$_L['GST_ferr3']);
			$fv->validateEmpty('countrycode',$_L['GST_ferr4']);
			//if (!empty($_POST["email"])) $fv->validateEmail('email','Please enter a valid email address');
			if($fv->checkErrors()){
				// display errors
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			}
			else {
				if(!$guestid && $_POST['guestid']) {
					$guestid = $_POST['guestid'];
				}
				$firstname=$_POST["firstname"];
				$middlename=$_POST["middlename"];
				$lastname=$_POST["lastname"];			
				$countrycode= $_POST["countrycode"];
				$nationality= $_POST["nationality"];
				$pp_no=($_POST["identification_no"]=="ppno") ?  $_POST["pp_id_no"] : '';
				$idno=($_POST["identification_no"]=="idno") ?   $_POST["pp_id_no"]  : 0;
				$address=$_POST["address"];
				$town=$_POST["town"];
				$postal_code=$_POST["postal_code"];
				$phone=$_POST["phone"];
				$email=$_POST["email"];
				$mobilephone=$_POST["mobilephone"];
				$ebridgeid=$_POST["eBridgeID"];
				$IM=$_POST["IM"];
				$salutation = $_POST['salutation'];
				$guestid= modify_guest($guestid,$lastname,$firstname,$middlename,$salutation,
						$pp_no,$idno,$countrycode,'',$address,$town,$postal_code,0,0,$phone,$email,
						$mobilephone, $ebridgeid, $IM, $nationality);
/*
				if(!$guestid){
					echo "<div align=\"center\"><h1>".$_L['GST_err']."</h1></div>";
				}
				else{
					echo "<div align=\"center\"><h1>".$_L['GST_success']."</h1></div>";
				}
*/
			}	
			break;
		case 'List':

			break;
		case 'Find':
			// check if user is searching using name, payrollno, national id number or other fields
			$guestid = $_POST["search"];
			break;
	}
}
if($guestid) {
	$thisguest = array();
	$ret = findguestbyid($guestid, $thisguest);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link href="css/new.css" rel="stylesheet" type="text/css">
  <title><?php echo $_L['MAIN_Title'];?></title>
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
	function BookGuest(){
		if(document.getElementById('guestid').value==""){
			alert("<?php echo $_L['GST_msg1']; ?>");
		}else{
			//check if guest with same id/pp no has been checked in.
			guestid=document.getElementById('guestid').value;
			self.location='index.php?menu=booking&guestid='+guestid
		}	
	}
	function ReserveGuest(){
		if(document.getElementById('guestid').value==""){
			alert("<?php echo $_L['GST_msg2']; ?>");
		}else{
			guestid=guestid=document.getElementById('guestid').value;
			self.location='index.php?menu=reservation&guestid='+guestid
		}	
	}
  </script>
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
	.narrowDropDown{
		width:16px;
		font-size:11px;
		-moz-max-content:16px;
		-moz-appearance: menuimage;
	}
  </style>
</head>
<body>
  <form action="index.php?menu=editprofile" method="post" enctype="multipart/form-data">
    <table  width="100%" border="0" cellpadding="1" align="center">
      <tr valign="top">
        <td class="c3" width="20%">
          <table width="100%" border="0" cellpadding="1" class="listing-table">
				<tr>
				  <?php 
					if ($_GET['menu'] == "editprofile") {
						print_rightMenu_home();
					}?> 		
				</tr>
		  </table>
        </td>
		<td  width="100%">
		  <table width="100%" ><tr><td>
		   <!--  INSERT TAB Here-->
		   <div  id="TabbedPanels1" class="TabbedPanels">
				<ul id="tabgroup" class="TabbedPanelsTabGroup">
					<li class="TabbedPanelsTab" tabindex="0" onclick="getTabeIndex(this);"><?php echo $_L['FRM_tabprofile']; ?></li>
					<li class="TabbedPanelsTab" tabindex="1" onclick="getTabeIndex(this);"><?php echo $_L['FRM_tabdocument']; ?></li>
					
					<li class="TabbedPanelsTab" tabindex="2" onclick="getTabeIndex(this);"><?php echo $_L['FRM_tabphones']; ?></li>
					
					<li class="TabbedPanelsTab" tabindex="3" onclick="getTabeIndex(this);"><?php echo $_L['FRM_tabemail']; ?></li>
					<li class="TabbedPanelsTab" tabindex="4" onclick="getTabeIndex(this);"><?php echo $_L['ADP_addresses']; ?></li>
					
				</ul>
				<div class="TabbedPanelsContentGroup">
					<!-- TAB CONTENT PROFILE DETAILS-->
					<div class="TabbedPanelsContent">
						<table class="tdbgcl" width="100%"  border="0" cellpadding="1">
							<tr><td width="13%" align="center"></td></tr>
							<tr><td colspan="2"><h2><a href="https://www.youtube.com/watch?v=yA8EKXgX-6g" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['GST_title']; ?></a></h2></td></tr>
							<tr>
								<td><?php echo $validationMsgs?></td>
							</tr>
							<tr>
							  <td style="padding:5" colspan="2">
								<div id="Requests">
								  <table width="100%"  border="0" cellpadding="1">
									<tr>
									  <td style="padding:5" width="25%"><?php echo $_L['GST_id']; ?></td>
									  <td style="padding:5" width="25%"><input type="text" name="guestid" id="guestid" value="<?php echo trim($thisguest['guestid']); ?>" readonly=""/></td>
									  <td width="25%">&nbsp;</td>
									  <td width="25%">&nbsp;</td>
									</tr>
									<tr>
									  <td style="padding:5" valign=bottom ><?php echo $_L['GST_Guest']; ?>
									  <?php 
											$selected="";
											if($guestid){
												$selected = $thisguest['salutid'];
											}else{
												$selected = "1";
											}
										?>
										<select name=salutation id=salutation>
											<?php populate_select("salutation","salute","Description",$selected,""); ?>
										 </select>
									  </td>
									  <td style="padding:5" ><?php echo $_L['GST_last']; ?><font color="#FF0000">*</font><br /><input type="text" name="lastname" id="lastname" value="<?php echo trim($thisguest['lastname']);?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_first']; ?><font color="#FF0000">*</font><br /><input type="text" name="firstname" id="firstname" value="<?php echo trim($thisguest['firstname']);?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_middle']; ?><br /><input type="text" name="middlename" id="middlename" value="<?php echo trim($thisguest['middlename']);?>" /></td>
									</tr>
									<tr>
									  <td style="padding:5" >
										<p>
										  <label><input type="radio" name="identification_no" value="ppno" <?php echo (($thisguest['pp_no']) ? "checked=\"checked\"" : ""); ?> /><?php echo $_L['GST_passport']; ?><font color="#FF0000">*</font></label>
										  <label><input type="radio" name="identification_no" value="idno" <?php echo (($thisguest['idno']) ? "checked=\"checked\"" : ""); ?> /><?php echo $_L['GST_id']; ?><font color="#FF0000">*</font></label>
										</p>
									  </td>
									  <td style="padding:5" ><input type="text" name="pp_id_no" value="<?php echo (($thisguest['pp_no']) ? $thisguest['pp_no'] : $thisguest['idno']); ?>" /></td>
									  <td style="padding:5"  colspan=2><font color="#FF0000">*</font>
										<select name="countrycode" class=plainDropDown>
										  <option value=""><?php echo $_L['GST_selcountry']; ?>
										  </option>
											<?php populate_select("countries","countrycode","country",$thisguest['countrycode'], "");?>
										</select></td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_nationality']; ?></td>
									  <td style="padding:5" colspan="3">
										<select name="nationality" class=plainDropDown >
										  <option value=""><?php echo $_L['GST_selcountry']; ?></option>
											<?php populate_select("countries","countrycode","country",$thisguest['nationality'], "");?>
										</select>
									  </td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_phone']; ?><br />(<?php echo $_L['GST_areacode']; ?>) </td>
									  <td style="padding:5" ><input type="text" name="phone" id="phone" value="<?php echo trim($thisguest['phone']); ?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_mobile']; ?></td>
									  <td style="padding:5" ><input type="text" name="mobilephone" id="mobilephone" value="<?php echo trim($thisguest['mobilephone']); ?>" /></td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_email']; ?></td>
									  <td style="padding:5" ><input type="text" name="email" value="<?php echo trim($thisguest['email']); ?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_fax']; ?></td>
									  <td style="padding:5" ><input type="text" name="fax" value="<?php echo trim($thisguest['fax']); ?>" /></td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_street']; ?></td>
									  <td style="padding:5" ><input type="text" name="address" value="<?php echo trim($thisguest['street_name']); ?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_IM']; ?></td>
									  <td style="padding:5" ><input type="text" name="IM" id="IM" value="<?php echo trim($thisguest['IM']); ?>" ?/></td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_city']; ?></td>
									  <td style="padding:5" ><input type="text" name="town" value="<?php echo trim($thisguest['town']); ?>" /></td>
									  <td style="padding:5" ><?php echo $_L['GST_ebridgeid']; ?></td>
									  <td style="padding:5" ><input type="text" name="eBridgeID" id="eBridgeID" value="<?php echo trim($thisguest['eBridgeID']); ?>" ?/></td>
									</tr>
									<tr>
									  <td style="padding:5" ><?php echo $_L['GST_pcode']; ?></td>
									  <td style="padding:5" ><input type="text" name="postal_code" value="<?php echo trim($thisguest['postal_code']); ?>" /></td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									</tr>
									<?php if(!$is_a_eBridgeCustomer){?>
									<tr>
									  <td style="padding:5"  colspan='4'>&nbsp;</td>
									  </tr>
									<tr>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									 </tr>
									<?php }?>
									<tr>
									  <td>&nbsp;</td>
									  <td>
										<?php if($thisguest['guestid']) { ?>
										<input type="button" name="button" class="button" value="<?php  echo $_L['GST_checkin']; ?>" onclick="BookGuest()"/>
										<?php } ?>
									  </td>
									  <td>
										<?php if($thisguest['guestid']) { ?>
										<input type="button" name="button"  class="button" value="<?php  echo $_L['GST_reservation']; ?>" onclick="ReserveGuest()"/>
										<?php } ?>
									  </td>
									  <td><input type="button" name="btnAdvprofile"  class="button" id="btnAdvprofile" value="<?php echo $_L['ADP_title']; ?>" onclick="window.location.href='advanced_profile.php'"/></td>
									</tr>
									<tr>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									  <td>&nbsp;</td>
									   <td>&nbsp;</td>
									</tr>
								  </table>
								   
										
									  
								</div>
							  </td>
							</tr>
							<tr bgcolor="#66CCCC" >
							  <td align="left" colspan="2">
								<div id="RequestDetails"></div>
							  </td>
							</tr>
						  </table>
					</div>
					<!-- TAB CONTENT DOCUMENT DETAILS-->
					<div class="TabbedPanelsContent">						
						<table width=100%>					
							<tr class="tdbgcl">
								<td align="left"></td>	
							</tr>
							<tr class="tdbgcl">
							<td>	
							  	<table>	
								<tr>							
									<td width="50%">
										<h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3><a href="https://www.e-bridgedirect.com" target="e-Bridge"><img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
									</td>
									<td align="center" width="50%" valign="top">
									<h2><?php echo $_L['FRM_tabdocument']; ?></h2><br/><br/><b><?php echo  $_L['RT_hotelguestprev'];?></b>
										<ul class="enlarge">								
											<li><img src="images/adv_documents.jpg" width="300px" height="300px" alt="Image" /><span><img  width="" height="" src="images/adv_documents.jpg" alt="Image" /><br /><b>Advance Documents</b></span></li>							</ul>
									</td>						
								</tr>
								</table>	
							</td>
						  	</tr>								
						</table>								
					</div>
					<!-- TAB CONTENT PHONES DETAILS-->
					<div class="TabbedPanelsContent">					
						<table width=100%>					
							<tr class="tdbgcl">
								<td align="left"></td>	
							</tr>
							<tr class="tdbgcl">
							<td>	
							  	<table>	
								<tr>							
									<td width="50%">
										<h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3><a href="https://www.e-bridgedirect.com" target="e-Bridge"><img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
									</td>
									<td align="center" width="50%" valign="top">
									<h2><?php echo $_L['FRM_tabphones']; ?></h2><br/><br/><b><?php echo  $_L['RT_hotelguestprev'];?></b>
										<ul class="enlarge">								
											<li><img src="images/adv_phones.jpg" width="300px" height="300px" alt="Image" /><span><img  width="" height="" src="images/adv_phones.jpg" alt="Image" /><br /><b>Advance Documents</b></span></li>							</ul>
									</td>						
								</tr>
								</table>	
							</td>
						  	</tr>								
						</table>					
					</div>
					<!-- TAB CONTENT EMAILS DETAILS-->
					<div class="TabbedPanelsContent">
						<table width=100%>					
							<tr class="tdbgcl">
								<td align="left"></td>	
							</tr>
							<tr class="tdbgcl">
							<td>	
							  	<table>	
								<tr>							
									<td width="50%">
										<h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3><a href="https://www.e-bridgedirect.com" target="e-Bridge"><img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
									</td>
									<td align="center" width="50%" valign="top">
									<h2><?php echo $_L['FRM_tabemail']; ?></h2><br/><br/><b><?php echo  $_L['RT_hotelguestprev'];?></b>
										<ul class="enlarge">								
											<li><img src="images/adv_emails.jpg" width="300px" height="300px" alt="Image" /><span><img  width="" height="" src="images/adv_emails.jpg" alt="Image" /><br /><b>Advance Documents</b></span></li>							</ul>
									</td>						
								</tr>
								</table>	
							</td>
						  	</tr>								
						</table>	
					</div>
					<!-- TAB CONTENT ADDRESS DETAILS-->
					<div class="TabbedPanelsContent">
						<table width=100%>					
							<tr class="tdbgcl">
								<td align="left"></td>	
							</tr>
							<tr class="tdbgcl">
							<td>	
							  	<table>	
								<tr>							
									<td width="50%">
										<h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3><a href="https://www.e-bridgedirect.com" target="e-Bridge"><img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
									</td>
									<td align="center" width="50%" valign="top">
									<h2><?php echo $_L['ADP_addresses']; ?></h2><br/><br/><b><?php echo  $_L['RT_hotelguestprev'];?></b>
										<ul class="enlarge">								
											<li><img src="images/adv_addresses.jpg" width="300px" height="300px" alt="Image" /><span><img  width="" height="" src="images/adv_addresses.jpg" alt="Image" /><br /><b>Advance Documents</b></span></li>							</ul>
									</td>						
								</tr>
								</table>	
							</td>
						  	</tr>								
						</table>	
					</div>
				</div>
		   
		   </div>
		   <table   border="0" cellpadding="1" align=right bgcolor="#FFFFFF" >
										 <tr><td>&nbsp;</td></tr>
										  <tr><td ><input type="submit" class="button" name="Submit" value="<?php if(! $guestid) echo $_L['GST_addguest']; else echo $_L['GST_updguest']; ?>"/></td><td><input type="button" class="button" name="Submit" value="<?php echo $_L['GST_listguest']; ?>" onclick="self.location='index.php?menu=profile'"/></td></tr>
										  
										</table>
		   
		   
		   
		   
		   
		   
		   
		   
		   <!--  INSERT TAB Here-->
		  </td></tr></table>
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