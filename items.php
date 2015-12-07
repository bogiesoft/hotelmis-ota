<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file items.php
 * @brief items webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup PRODUCT_MANAGEMENT Product detail setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();
$lang = get_language();
load_language($lang);


if (isset($_GET["search"])){
	find($_GET["search"]);
}	

if (isset($_POST['Submit']) && $_POST['tab']=='items'){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['BTN_save']:
			if(isset($_POST['itemcount']) && $_POST['itemcount'] > 0) {
				 for($i = 1; $i <= $_POST['itemcount']; $i++) {
					$sale=0;					
					if(isset($_POST['sale_'.$i])){
						$sale=1;
					}
					if(isset($_POST['idx_'.$i])) {						
						modify_item($_POST['idx_'.$i],$_POST['item_'.$i],$_POST['description_'.$i],$sale,$_POST['expense_'.$i],$_POST['itype_'.$i]);
					}
				}
			}
			if($_POST['newitem'] || $_POST['newdescription']) {
				// instantiate form validator object
				$fv=new formValidator(); //from functions.php
				$fv->validateEmpty('newitem',$_L['ITM_noitm_err']);
				$fv->validateEmpty('newdescription',$_L['ITM_noitmdesc_err']);
				if($fv->checkErrors()){
					// display errors
					echo "<div align=\"center\">";
					echo "<h2>".$_L['PR_formerr']."</h2>";
					echo $fv->displayErrors();
					echo "</div>";
				} else {
					modify_item(0,$_POST['newitem'],$_POST['newdescription'],$_POST['newsale'],$_POST['newexpense'],$_POST['newitype']);
	
				}
			}
			break;
		case $_L['BTN_delete']:

		case $_L['BTN_list']:
			//link ("self","agents_list.php");
			break;
		case $_L['BTN_search']:
			//check if user is searching using name, payrollno, national id number or other fields
			//			find($_POST["search"]);
			break;
	}

}

$items = array();
$noitems = get_itemslist($items);


?>
				<form action="<?php $_SERVER['REQUEST_URI']?>" method="post" enctype="multipart/form-data">
				   
					<table width="100%"  border="0" cellpadding="1">
				      <tr>
				        <td align="center"></td>
				      </tr>
				      <tr>
				        <td>				   
							&nbsp;<input type="hidden" name="tab" id="tab" value="<?php echo "items"; ?>"/>
							<input type="hidden" name="activeTab" id="activeTab" value="<?php echo $tabvar;?>"/>
						</td>
				      </tr>
				      <tr>
				        <td><div id="Requests" style="overflow:auto; width:730px; height:320px;">
				          <table width="100%"  border="0" cellpadding="1">
				<?php
						if($noitems > 0) {
							$i = 0;
							print "<tr bgcolor=\"#3593DE\" align='center'><th><input type=hidden name=itemcount value='".$noitems."' /></th><th>".$_L['ITM_code']."</th><th>".$_L['ITM_description']."</th><th>".$_L['ITM_sale']."</th><th>".$_L['ITM_expense']."</th><th>".$_L['ITM_category']."</th></tr>";
							foreach ($items as $idx=>$val) {
								$i++;
								$j++;
								if($j%2==1){
									echo "<tr bgcolor=\"#CCCCCC\">";
									}else{
									echo "<tr bgcolor=\"#EEEEF8\">";
								}
								print "<td> <input type=hidden name='idx_".$i."' value='".$idx."' /><input type=radio name=itemid value='".$idx."' /> </td>\n";
								print "<td> <input type=text name='item_".$i."' value='".$items[$idx]['item']."' /></td>\n";
								print "<td> <input type=text name='description_".$i."' value='".$items[$idx]['description']."' /></td>\n";
								print "<td> <input type=checkbox name='sale_".$i."' value='1' ";
								if($items[$idx]['sale']){
									print "checked='checked'";
								}
								print " /></td>\n";
								print "<td> <input type=checkbox name='expense_".$i."' value='1' ";
								if($items[$idx]['expense']) print "checked ";
								print " /></td>\n";
								print "<td> <select name='itype_".$i."' >\n";
								print "<option value='".ROOM."'";
								if($items[$idx]['itype'] == ROOM ) print " selected ";
								print ">".$_L['ITM_room']."</option>\n";
								print "<option value='".FOOD."'";
								if($items[$idx]['itype'] == FOOD ) print " selected ";
								print ">".$_L['ITM_food']."</option>\n";
								print "<option value='".BEVERAGE."'";
								if($items[$idx]['itype'] == BEVERAGE ) print " selected ";
								print ">".$_L['ITM_beverage']."</option>\n";
								print "<option value='".TRANSPORT."'";
								if($items[$idx]['itype'] == TRANSPORT ) print " selected ";
								print ">".$_L['ITM_transport']."</option>\n";
								print "<option value='".FEE."'";
								if($items[$idx]['itype'] == FEE ) print " selected ";
								print ">".$_L['ITM_fee']."</option>\n";
								print "<option value='".HOTEL_PHONE."'";
								if($items[$idx]['itype'] == HOTEL_PHONE ) print " selected ";
								print ">".$_L['ITM_phone']."</option>\n";
								print "<option value='".SERVICE."'";
								if($items[$idx]['itype'] == SERVICE ) print " selected ";
								print ">".$_L['ITM_service']."</option>\n";
								print "<option value='".TAX."'";
								if($items[$idx]['itype'] == TAX ) print " selected ";
								print ">".$_L['ITM_tax']."</option>\n";
								print "<option value='".HOTEL."'";
								if($items[$idx]['itype'] == HOTEL ) print " selected ";
								print ">".$_L['ITM_hotel']."</option>\n";
								print "<option value='".TOUR."'";
								if($items[$idx]['itype'] == TOUR ) print " selected ";
								print ">".$_L['ITM_tour']."</option>\n";
								print "<option value='".GOLF."'";
								if($items[$idx]['itype'] == GOLF ) print " selected ";
								print ">".$_L['ITM_golf']."</option>\n";
								print "</select></td></tr>\n";
							}
						}
				// Start the next line of table for the new item input.
						$j++;
						if($j%2==1){
							echo "<tr bgcolor=\"#CCCCCC\">";
						}else{
							echo "<tr bgcolor=\"#EEEEF8\">";
						}
						?>
				
					<td></td><td> <input type=text name=newitem /> </td><td> <input type=text name=newdescription /></td>
					<td><input type=checkbox name=newsale value='1' /></td><td><input type=checkbox name=newexpense value='1' /></td>
					<td><select name=newitype>
						<option value='<?php echo ROOM; ?>' > <?php echo $_L['ITM_room']; ?> </option>
						<option value='<?php echo FOOD; ?>' > <?php echo $_L['ITM_food']; ?> </option>
						<option value='<?php echo BEVERAGE; ?>' > <?php echo $_L['ITM_beverage']; ?> </option>
						<option value='<?php echo TRANSPORT; ?>' > <?php echo $_L['ITM_transport']; ?> </option>
						<option value='<?php echo FEE; ?>' > <?php echo $_L['ITM_fee']; ?> </option>
						<option value='<?php echo HOTEL_PHONE; ?>' > <?php echo $_L['ITM_phone']; ?> </option>
						<option value='<?php echo SERVICE; ?>' > <?php echo $_L['ITM_service']; ?> </option>
						<option value='<?php echo TAX; ?>' > <?php echo $_L['ITM_tax']; ?> </option>
						<option value='<?php echo HOTEL; ?>' > <?php echo $_L['ITM_hotel']; ?> </option>
						<option value='<?php echo TOUR; ?>' > <?php echo $_L['ITM_tour']; ?> </option>
						<option value='<?php echo GOLF; ?>' > <?php echo $_L['ITM_golf']; ?> </option>
						</select>
					</td> </tr>
				</table>
				</div>
				<div>
				<table align="right" >
					<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
					<tr>					
					<td>
						<input align="right" class="button" type=submit value='<?php echo $_L['BTN_save']; ?>' name='Submit' /> 
						<input align="right" class="button" type=submit value='<?php echo $_L['BTN_refresh']; ?>' name='Submit' /> 
					</td></tr>
				<tr><td>&nbsp;</td></tr>
				</table>				
				</div>
				        </td>
						
				      </tr>
				    </table>
				</form>




<?php
/**
 * @}
 * @} 
 */
?>