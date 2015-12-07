<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file admin.php
 * @brief admin web page called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup USER_MANAGEMENT User setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once (dirname(__FILE__)."/queryfunctions.php");
include_once (dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
//access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();
$myuser = array();

if (($_GET['menu'] == "mysettings" || $_GET['menu'] == 'myProfile') && isset($_SESSION['userid'])) {
	find_user($_SESSION['userid'], $tt, $myuser);
	$id=$_SESSION['userid'];
}

if (isset($_GET["id"])){
	$tt = 0;
	if($_GET['optFind'] == "Name") $tt = 1;
	find_user($_GET["id"], $tt, $myuser);
	$id = $_GET['id'];
}
if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {		
		case $_L['USR_adduser']:
		case $_L['BTN_update']:
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('fname',$_L['ADM_nameerr']);
			$fv->validateEmpty('sname',$_L['ADM_famerr']);
			$fv->validateEmpty('loginname',$_L['ADM_loginerr']);
			$fv->validateEmpty('pass',$_L['ADM_passerr']);
			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			} 
			else {
				$userid=$_POST["userid"]; 
				$fname=$_POST["fname"];
				$sname=$_POST["sname"];
				$loginname=$_POST["loginname"];
				$pass=md5($_POST["pass"]);
				$phone=(!empty($_POST["phone"])) ? $_POST["phone"] : '';
				$mobile=(!empty($_POST["mobile"])) ? $_POST["mobile"] : '';
				$fax=(!empty($_POST["fax"])) ? $_POST["fax"] : '';				
				$email=(!empty($_POST["email"])) ? $_POST["email"] : '';
				$admin=(empty($_POST["admin"])) ? 0 : $_POST["admin"];
				$guest=(empty($_POST["guest"])) ? 0 : $_POST["guest"];
				$reservation=(empty($_POST["reservation"])) ? 0 : $_POST["reservation"];
				$booking=(empty($_POST["booking"])) ? 0 : $_POST["booking"];
				$agents=(empty($_POST["agents"])) ? 0 : $_POST["agents"];
				$rooms=(empty($_POST["rooms"])) ? 0 : $_POST["rooms"];
				$billing=(empty($_POST["billing"])) ? 0 : $_POST["billing"];
				$billing=(empty($_POST["advbilling"])) ? $billing : $_POST["advbilling"];
				$rates=(empty($_POST["rates"])) ? 0 : $_POST["rates"];
				$lookup=(empty($_POST["lookup"])) ? 0 : $_POST["lookup"];
				$reports=(empty($_POST["reports"])) ? 0 : $_POST["reports"];

				$userid=modify_user($userid,$fname,$sname,$loginname,
						$pass,$phone,$mobile,$fax,$email,0,$admin,
						$guest,$reservation,$booking,$agents,$rooms,$billing,
						$rates,$lookup,$reports);
				if(!$userid){
					echo "<div align=\"center\"><h1>".$_L['ADM_error']."</h1></div>";
				}
				else{
					if($action == $_L['BTN_update']){
						echo "<div align=\"center\"><h1>".$_L['ADM_updatesuccess']."</h1></div>";
					} else {
						echo "<div align=\"center\"><h1>".$_L['ADM_addsuccess']."</h1></div>";
					}
				}
			}
			break;
		case $_L['BTN_list']:
		
			break;
	}
}
if(isset($_POST['userid'])) {
		find_user($_POST["userid"], $tt,$myuser);
}
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>
	 		<?php 
	 		if (isset($_SESSION['userid']) && ($_GET['menu'] == "mysettings" || $_GET['menu'] == 'myProfile')) {
	 			print_rightMenu_mySettings();
	 		} elseif ($_GET['menu'] == "userSetup" && accessNew('admin')) {
	 			print_rightMenu_admin();
	 		}?> 		
	 	

	          <td valign="top">
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
			      <table class="tdbgcl" width="100%" border="0" cellpadding="1" align="center">
				   <tr><td><h2><a href="https://www.youtube.com/watch?v=2CFUJo3Bctc" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['ADM_title']; ?></a></h2></td></tr>
			        <tr  valign="top">
					  <td class="c4" width="65%">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						 
						 
						  <tr>
					  		<td><?php echo $validationMsgs?></td>
					  	  </tr>
						 
						  <tr>
							<td>
							  <div id="Requests">
								<table height="350" width="82%"  border="0" cellpadding="1">
								 <tr><td align="center">&nbsp;</td></tr>
								  <tr>
									<td width="19%"><?php echo $_L['ADM_userid']; ?></td>
									<td width="28%"><input type="text" name="userid" readonly="readonly" value="<?php echo trim($myuser['userid']); ?>"/></td>
									<td width="21%"><?php echo $_L['ADM_date']; ?></td>
									<td width="32%"><input type="text" name="dateregistered" readonly="readonly" value="<?php echo trim($myuser['dateregistered']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['ADM_username']; ?><font color="#FF0000">*</font></td>
									<td><input type="text" name="loginname" value="<?php echo trim($myuser['loginname']); ?>"/></td>
									<td><?php echo $_L['ADM_password']; ?><font color="#FF0000">*</font></td>
									<td><input type="password" name="pass" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['ADM_first']; ?><font color="#FF0000">*</font></td>
									<td><input type="text" name="fname" value="<?php echo trim($myuser['fname']); ?>"/></td>
							
									<td><?php echo $_L['ADM_family']; ?><font color="#FF0000">*</font></td>
									<td><input type="text" name="sname" value="<?php echo trim($myuser['sname']); ?>"/></td>
								
								  </tr>
								  <tr>
									<td><?php echo $_L['ADM_phone']; ?></td>
									<td><input type="text" name="phone" value="<?php echo trim($myuser['phone']); ?>"/></td>
									<td><?php echo $_L['ADM_mobile']; ?></td>
									<td><input type="text" name="mobile" value="<?php echo trim($myuser['mobile']); ?>"/></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['ADM_fax']; ?></td>
									<td><input type="text" name="fax" value="<?php echo trim($myuser['fax']); ?>"/></td>
									<td><?php echo $_L['ADM_email']; ?></td>
									<td><input type="text" name="email" value="<?php echo trim($myuser['email']); ?>"/></td>
								  </tr>
								  <?php if (isset($_SESSION['userid']) && $_GET['menu'] == "mysettings") { ?>
								  <tr>
										  <td>
										  <input type="hidden" name="admin" value="<?php if($myuser['admin']==1) echo 1;?>"/>
										  <input type="hidden" name="guest" value="<?php if($myuser['guest']==1) echo 1;?>"/>
										  <input type="hidden" name="reservation" value="<?php if($myuser['reservation']==1) echo 1;?>"/>
										  <input type="hidden" name="booking" value="<?php if($myuser['booking']==1) echo 1;?>"/>
										  <input type="hidden" name="agents" value="<?php if($myuser['agents']==1) echo 1;?>"/>
										  <input type="hidden" name="rooms" value="<?php if($myuser['rooms']==1) echo 1;?>"/>
										  <input type="hidden" name="billing" value="<?php if($myuser['billing']==1) echo 1;?>"/>
										  <input type="hidden" name="rates" value="<?php if($myuser['rates']==1) echo 1;?>"/>
										  <input type="hidden" name="lookup" value="<?php if($myuser['lookup']==1) echo 1;?>"/>
										  <input type="hidden" name="reports" value="<?php if($myuser['reports']==1) echo 1;?>"/>
										  </td>
														  
								  </tr>
								  <?php } ?>
								  <?php if (accessNew("admin") && $_GET['menu']=="userSetup") {?>
								  <tr>
									<td><h3><?php echo $_L['ADM_rights']; ?></h3> </td>
									<td></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								  </tr>								  
								  <tr>
									<td colspan="4">
									  <table width="100%" cellpadding="1">
										<tr>
										  <td width="13%"><?php echo $_L['ADM_admin']; ?></td>
										  <td width="87%" colspan=3><input type="checkbox" name="admin" value="1" <?php if($myuser['admin']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td width="13%"><?php echo $_L['ADM_guest']; ?></td>
										  <td width="87%" colspan=3><input type="checkbox" name="guest" value="1" <?php if($myuser['guest']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_reservation']; ?></td>
										  <td colspan=3><input type="checkbox" name="reservation" value="1" <?php if($myuser['reservation']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_booking']; ?></td>
										  <td colspan=3><input type="checkbox" name="booking" value="1" <?php if($myuser['booking']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_agents']; ?></td>
										  <td colspan=3><input type="checkbox" name="agents" value="1" <?php if($myuser['agents']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_rooms']; ?></td>
										  <td colspan=3><input type="checkbox" name="rooms" value="1" <?php if($myuser['rooms']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td width="13%"><?php echo $_L['ADM_billing']; ?></td>
										  <td width="37%"><input type="checkbox" name="billing" value="1" <?php if($myuser['billing']>=1) echo "checked";?>/></td>
										  <td width="13%"><?php echo $_L['BTN_advanced']; ?></td>
										  <td width="37%"><input type="checkbox" name="advbilling" value="2" <?php if($myuser['billing']==2) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_rates']; ?></td>
										  <td colspan=3><input type="checkbox" name="rates" value="1" <?php if($myuser['rates']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_lookup']; ?></td>
										  <td colspan=3><input type="checkbox" name="lookup" value="1" <?php if($myuser['lookup']==1) echo "checked";?>/></td>
										</tr>
										<tr>
										  <td><?php echo $_L['ADM_reports']; ?></td>
										  <td colspan=3><input type="checkbox" name="reports" value="1" <?php if($myuser['reports']==1) echo "checked";?>/></td>
										</tr>
									  </table>
									</td>
								  </tr>
								  <?php } ?>
								  
								<?php if (isset($_SESSION['userid']) && ($_GET['menu'] == "mysettings" || $_GET['menu'] == 'myProfile')) { ?>
									<tr class="tdbgcl" ><td>&nbsp;</td></tr>
									<tr class="tdbgcl" align="right"><td colspan="4"><input class="button" type="submit" name="Submit" value="<?php if (isset($myuser['userid'])) { echo $_L['BTN_update'];}?>"/></td></tr>				
						
								<?php }?>								  									  
								</table>
							  </div>
							</td>	
						  </tr>
						  <tr bgcolor="#66CCCC" ><td align="left" colspan="2"><div id="RequestDetails"></div></td></tr>
						</table>
					  </td>
					</tr>
					  <?php if (accessNew("admin") && $_GET['menu']=="userSetup") {?>
					  			<tr class="tdbgcl" ><td>&nbsp;</td></tr>
								<tr class="tdbgcl" align="right"><td colspan="4"><input class="button" type="submit" name="Submit" value="<?php echo isset($myuser['userid']) ? $_L['BTN_update'] : $_L['USR_adduser'] ?>"/>
								<input class="button" type="button" name="Submit" value="<?php echo $_L['USR_listuser']; ?>" onclick="self.location='index.php?menu=usersList'"/>
								&nbsp;&nbsp;</td></tr>
					  <?php } ?>	
					<tr class="tdbgcl"><td>&nbsp;</td></tr>
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