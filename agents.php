<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file agents.php
 * @brief agents webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup AGENT_MANAGEMENT Travel Agent setup and management page
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
access("agents"); //check if user is allowed to access this page
$logofile = Get_LogoFile();

if (isset($_GET["search"])){
	$agentid = $_GET['search'];
}
if(isset($_GET['id'])) {
	$agentid = $_GET['id'];
}
if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['AGT_addagent']:
		case $_L['BTN_update']:
			// instantiate form validator object
			$fv=new formValidator(); //from functions.php
			$fv->validateEmpty('iata',$_L['AGT_iata_err']);
			$fv->validateEmpty('name',$_L['AGT_name_err']);
			$fv->validateEmpty('phone',$_L['AGT_phone_err']);
			$fv->validateEmpty('town',$_L['AGT_town_err']);
			$fv->validateEmpty('billing',$_L['AGT_billing_err']);

			if($fv->checkErrors()){
				// display errors
				$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
			
			}else {
				$agentid=(!empty($_POST["agentid"])) ? $_POST["agentid"] : 0;
				$country=$_POST['country'];
				$ebridgeID=$_POST['ebridgeID'];
				$IM=$_POST['IM'];
				$name=$_POST["name"];
				$iata=$_POST["iata"];
				$contact=$_POST["contact"];
				$phone=$_POST["phone"];
				$fax=(!empty($_POST["fax"])) ? $_POST["fax"] : '';
				$email=(!empty($_POST["email"])) ? $_POST["email"] : '';
				$billing=(!empty($_POST["billing"])) ? $_POST["billing"] : '';
				$town=$_POST["town"];
				$postcode=(!empty($_POST["postcode"])) ? $_POST["postcode"] : '';
				$street=(!empty($_POST["street"])) ? $_POST["street"] : '';
				$building=(!empty($_POST["building"])) ? $_POST["building"] : '';
				$agentid = modify_agent($agentid, $name, $iata,$contact, $phone, $fax, $email, $billing, $town, $postcode, $street, $building, $country, $ebridgeID, $IM );
			}
			break;
		case $_L['BTN_list']:
			//link ("self","agents_list.php");
			break;
		case $_L['BTN_search']:
			//check if user is searching using name, payrollno, national id number or other fields
			//			find($_POST["search"]);
			break;
	}
}
$agent = array();
if($agentid) {
	get_agent($agentid, $agent);
}
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      		
	 		<?php print_rightMenu_admin();?> 		
	          <td valign="top">
	          	
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
			      <table class="tdbgcl" class="c16" width="100%" cellpadding="1">
			        <tr valign="top">
					  <td width="65%" class="c4">
						<table height="465" width="100%" cellpadding="1">
						 
						  <tr><td><h2><a href="https://www.youtube.com/watch?v=RwX6olaj7qE" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['AGT_title']; ?></a></h2></td></tr>
						  <tr>
					  		<td><?php echo $validationMsgs?></td>
					  	</tr>
						  <tr>
							<td>
							  <div>
								<table height="370px" align="left" width="70%" border="0" cellpadding="1">
								  <tr>
									<td><?php echo $_L['AGT_id']; ?></td>
									<td><input size="40px" type="text" name="agentid" readonly value="<?php echo trim($agent['agentid']); ?>"/></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_ac']; ?><font color="#FF0000">*</font></td>
									<td><input size="40px" type="text" name="iata" value="<?php echo trim($agent['iata']); ?>"/></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_name']; ?><font color="#FF0000">*</font></td>
									<td><input size="40px" type="text" name="name" value="<?php echo trim($agent['name']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_contact']; ?></td>
									<td><input size="40px" type="text" name="contact" value="<?php echo trim($agent['contact']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_phone']; ?><font color="#FF0000">*</font></td>
									<td><input size="40px" type="text" name="phone" value="<?php echo trim($agent['phone']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_fax']; ?></td>
									<td><input size="40px" type="text" name="fax" value="<?php echo trim($agent['fax']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_ebridgeid']; ?></td>
									<td><input size="40px" type="text" name="ebridgeID" value="<?php echo trim($agent['eBridgeID']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_IM']; ?></td>
									<td><input size="40px" type="text" name="IM" value="<?php echo trim($agent['IM']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_email']; ?></td>
									<td><input size="40px" type="text" name="email" value="<?php echo trim($agent['email']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_billing']; ?><font color="#FF0000">*</font></td>
									<td><input size="40px" type="text" name="billing" value="<?php echo trim($agent['billing']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_city']; ?><font color="#FF0000">*</font></td>
									<td><input size="40px" type="text" name="town" value="<?php echo trim($agent['town']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_country']; ?></td>
									<td><select name=country ><?php populate_select("countries","countrycode","country",$agent['country'],""); ?></select>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_pcode']; ?></td>
									<td><input size="40px" type="text" name="postcode" value="<?php echo trim($agent['postcode']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_building']; ?></td>
									<td><input size="40px" type="text" name="building" value="<?php echo trim($agent['building']); ?>" /></td>
								  </tr>
								  <tr>
									<td><?php echo $_L['AGT_street']; ?></td>
									<td><input size="40px" type="text" name="street" value="<?php echo trim($agent['street']); ?>" /></td>
								  </tr>
								</table>
							  </div>
							</td>	
						  </tr>
						  <tr><td>&nbsp;</td></tr>
						  <tr align="right" ><td><input class="button" type="submit" name="Submit" value="<?php if(! $agentid) echo $_L['AGT_addagent']; else echo $_L['BTN_update'];?>" />
						  	<input class="button" type="button" name="Submit" value="<?php echo $_L['AGT_listagent']; ?>" onclick="self.location='index.php?menu=agentsList'"/>&nbsp;&nbsp;&nbsp;
						  </td></tr>
						  <tr><td>&nbsp;</td></tr>	 
						  <tr bgcolor="" ><td align="left"><div id="RequestDetails"></div></td></tr>
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
 */
?>