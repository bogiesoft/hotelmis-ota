<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file roomtypes.php
 * @brief roomtypes webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup ROOM_MANAGEMENT
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
access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();

if (isset($_GET["search"])){
	find($_GET["search"]);
}	
if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['BTN_save']:
			if(isset($_POST['roomtypecount']) && $_POST['roomtypecount'] > 0) {
				for($i = 1; $i <= $_POST['roomtypecount']; $i++) {
					if(isset($_POST['idx_'.$i])) {
						modify_roomtype($_POST['idx_'.$i],$_POST['roomtype_'.$i],$_POST['description_'.$i],$_POST['rateid_'.$i],$_POST['url_'.$i]);
						if(is_ebridgeCustomer()){
							include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
							CustomPagesFormRead( HTL_ROOM_TYPE, $_POST['idx_'.$i]);
						}
					}
				}
			}
			if($_POST['newroomtype'] || $_POST['newdescription']) {
				// instantiate form validator object
				$fv=new formValidator(); //from functions.php
				$fv->validateEmpty('newroomtype',$_L['RMT_normtyp']);
				$fv->validateEmpty('newdescription',$_L['RMT_noitmdes']);
				if($fv->checkErrors()){
					// display errors
					echo "<div align=\"center\">";
					echo "<h2>".$_L['PR_formerr']."</h2>";
					echo $fv->displayErrors();
					echo "</div>";
				} else {
					modify_roomtype(0,$_POST['newroomtype'],$_POST['newdescription'],$_POST['newrateid'],$_POST['newurl']);	
				}
			}
			break;
		case $_L['BTN_delete']:
			delete_roomtype($_POST['idx_'.$i]);
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
$roomtypes = array();
$noitems = get_roomtypelist($roomtypes);
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_admin();?> 	
	          <td valign="top">     

				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">

						<table height="465" width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						
						  <tr><td><h2><a href="https://www.youtube.com/watch?v=a_gYGm_OFfg" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['RMT_rtypetitle']; ?></a></h2></td></tr>
						  <tr>
							<td>
							<?php
								  if(is_ebridgeCustomer()){
									echo "<script type='text/javascript'>\n";
									echo "function showhidecustomrows(label) {\n";
										echo "	var disp = '';\n";
										foreach ($roomtypes as $idx=>$val) {
											echo "	if('RTI".$idx."' == label) {\n";
											echo "		disp='';\n";
											echo "	} else {\n";
											echo "		disp='none';\n";
											echo "	}\n";
											echo "	document.getElementById('RTI".$idx."').style.display = disp;\n";
										}
									echo "}\n";
									echo "</script>\n";
								  }
							?>
			<div id="TabbedPanels1" class="TabbedPanels">
                <ul id="tabgroup" class="TabbedPanelsTabGroup">                
                  <li class="TabbedPanelsTab" tabindex="0"><?php echo $_L['RM_type']; ?></li>
                  <?php if (is_ebridgeCustomer()) {?>
                  <li class="TabbedPanelsTab" tabindex="1"><?php echo $_L['CST_fields']; ?></li>
                  <?php }?>
                </ul>
                <div class="TabbedPanelsContentGroup">     
                  
                  <div class="TabbedPanelsContent" style="overflow:auto; width:780px; height:360px;">
							  <table height="" width="780px" class="tdbgcl" width="100%"  border="0" cellpadding="0">
								<?php
								if($noitems > 0) {
								  $i = 0;
								  echo "<tr bgcolor=\"#3593DE\"><th><input type=hidden name=roomtypecount value='".$noitems."' /></th><th>".$_L['RMT_rtype']."</th><th>".$_L['RMT_description']."</th><th>".$_L['RMT_rate']."</th><th>URL</th></tr>";
			
								  foreach ($roomtypes as $idx=>$val) {
									$i++;
									$j++;
									if($j%2==1){
									  echo "<tr bgcolor=\"#CCCCCC\">";
									}else{
									  echo "<tr bgcolor=\"#EEEEF8\">";
									}
									echo "<td> <input type=hidden name='idx_".$i."' value='".$idx."' /><input type=radio name=roomtypeid value='".$idx."'";
									if(is_ebridgeCustomer()){
										echo " onchange=\"showhidecustomrows('RTI".$idx."');\" ";
									}
									echo  "/> </td>\n";
									echo "<td> <input type=text name='roomtype_".$i."' value='".$roomtypes[$idx]['roomtype']."' size=15 maxlength=15 /></td>\n";
									echo "<td> <input type=text name='description_".$i."' value='".$roomtypes[$idx]['description']."' size=21 maxlength=100 /></td>\n";
									echo "<td> <select name='rateid_".$i."' > ";
									echo "<option value=0> </option>\n";
									  populate_select("rates","ratesid","ratecode",$roomtypes[$idx]['rateid'], "rate_type=".DEFAULTRATE);
									echo " </select></td>\n";
									echo "<td> <input type=text name='url_".$i."' value='".$roomtypes[$idx]['roomurl']."' size=25 /></td>\n";
									echo "</tr>\n";
			
								  }
								}
								?>
								<tr class="tdbgcl">
								  <td></td>
								  <td><input type=text name=newroomtype size=15 maxlength=15 /></td>
								  <td><input type=text name=newdescription size=21 maxlength=100 /></td>
							      <td>
									<select id="newrateid" name="newrateid">
									  <option value='0'></option>
									  <?php populate_select("rates","ratesid","ratecode",0, "rate_type=".DEFAULTRATE);?>
									</select>
								  </td> 
								  <td><input type=text name=newurl size=25 /></td>
								</tr>
							  </table>
				  </div>
                  <?php if (is_ebridgeCustomer()) {?>
                  <div class="TabbedPanelsContent" style="overflow:auto; width:780px; height:360px;">
						<table height="" width="780px" class="tdbgcl" border="0" cellpadding="0">
								<?php
									foreach ($roomtypes as $idx=>$val) {
										include_once(dirname(__FILE__)."/OTA/advancedFeatures/adv_functions.php");
										print "<tr class='tdbgcl' id='RTI".$idx."' style='display:none'><td colspan=5>\n";
										CustomPagesFormPrint(HTL_ROOM_TYPE, $idx, 650, 350);
										print "</td></tr>\n";
									}

								?>														
						</table>
                  </div>
                <?php }?>
				</div>
			</div>	
							</td>
						  </tr>
						  <tr><td>&nbsp;</td></tr>
						  <tr align="right">
							  <td colspan="5">
								  <input align="right" class="button" type=submit value="<?php echo $_L['BTN_save']; ?>" name="Submit" />
								  <input align="right" class="button" type=submit value='<?php echo $_L['BTN_refresh']; ?>' name='Submit' /> 
							  </td>
						  </tr>
						  <tr><td align="left"><div id="RequestDetails"></div></td></tr>
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