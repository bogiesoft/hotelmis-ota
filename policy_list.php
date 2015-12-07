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
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr> 
	 		<?php print_rightMenu_admin();?> 
	          <td valign="top">
	          	
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				  <table class="tdbgcl" width="100%"  border="0" cellpadding="1" align="center" bgcolor="">
					<tr valign="top">
					  <td width="65%" bgcolor="">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align=""></td></tr>
						  <tr><td colspan="2"><h2><?php echo $_L['PL_view']; ?></h2></td></tr>
						  <tr bgcolor="#FF9900">
						  <!--	<td><h1><?php //echo $_L['PL_view']; ?> </h1></td> --></tr>
						  <tr valign="top" class="tdbgcl" height="385">
							<td colspan="2">
							  <div id="Requests" style="overflow:auto; width:772; height:330;">
								<?php
								//$rooms = array();
								//get_roomslist($rooms,$bedtype, $status);
								$policy = array();
								get_policylist($policy);
								echo "<table align=\"center\"  border=\"1\" cellspacing=\"0\" cellpadding=\"3\">";
								//get field names to create the column header
								echo "<tr bgcolor=\"#2E71A7\">
									<th></th>
									<th>".$_L['PL_id']."</th>
									<th>".$_L['PL_title']."</th>
									<th>".$_L['PL_rateid']."</th>
									<th>".$_L['PL_des']."</th>
									<th>".$_L['PL_lang']."</th>
									</tr>";
									//end of field header
									//get data from selected table on the selected fields
								  foreach ($policy as $idx => $val) {
									//alternate row colour
									$j++;
									if($j%2==1){
									  echo "<tr bgcolor=\"#CCCCCC\">";
									}else{
								      echo "<tr bgcolor=\"#EEEEF8\">";
									}
									  echo "<td><a href=\"index.php?menu=policySetup&search=".$policy[$idx]['idpolicy']."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view room details\"/></a></td>";
									  echo "<td>" . $policy[$idx]['ID'] . "</td>";
									  echo "<td>" . $policy[$idx]['title'] . "</td>";
									  echo "<td>" . $policy[$idx]['rateid'] . "</td>";
									  echo "<td>" . $policy[$idx]['description'] . "</td>";
									  echo "<td>" . $policy[$idx]['language'] . "</td>";
									  echo "</tr>"; //end of - data rows
								  } //end of while row
								  echo "</table>";
								  ?>
							  </div>
							</td>		
						  </tr>
						  <tr><td>&nbsp;</td></tr>
						  <tr align="right">
							  <td colspan="6">							
								<input class="button" type="button" name="Submit" value="<?php echo $_L['PL_addpolicy'];?>" onclick="self.location='index.php?menu=policySetup'"/>
								<input class="button" type="button" name="Submit" value="<?php echo $_L['PL_listpolicy'];?>" onclick="self.location='index.php?menu=policyList'"/>
							  </td>						  
						  </tr>
						  <tr><td>&nbsp;</td></tr>
						  <tr bgcolor="#66CCCC" ><td align="left" colspan="2"><div id="RequestDetails"></div></td></tr>
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
 */?>
