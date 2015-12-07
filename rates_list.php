<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file rates_list.php
 * @brief rates list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @addtogroup RATE_MANAGEMENT
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
access("rates"); //check if user is allowed to access this page
//$bedtype = $_POST['search'];
$search = "";
$stype = 0;
$rateslist = array();

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
			if($_POST['optFind'] == $_L['RTS_code']) $stype = 1;
			if($_POST['optFind'] == $_L['RTS_rateid']) $stype = 2;
			break;
	}
}

get_rateslist($search, $stype, $rateslist);
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_admin();?> 	
	          <td valign="top">

				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				  <table width="100%"  border="0" cellpadding="1" align="center">
					<tr class="tdbgcl" valign="top">
					  <td width="65%" class="c4">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						  <tr><td colspan="2"><h2><?php echo $_L['RTS_listrates']; ?></h2></td></tr>						 
					      <tr align="center">
							  <td>
								<label> <?php echo $_L['PR_criteria']; ?>:
								  <input type="radio" name="optFind" value="<?php echo $_L['RTS_code']; ?>" />
								  <?php echo $_L['RTS_code']; ?>
								</label>
								<label>
								  <input type="radio" name="optFind" value="<?php echo $_L['RTS_rateid']; ?>" />
								  <?php echo $_L['RTS_rateid']; ?>
								</label>
							  </td>
						  </tr>	
						  <tr align="center">
						  	<td>
								<input type="text" name="search" width="100" />
								<input class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_search']; ?>"/>						  	
						  	</td>
						  </tr>
						  <tr><td>&nbsp;</td></tr>
						  						  
						  <tr>
							<td colspan="2">
						      <div id="Requests" style="overflow:auto; width:772; height:328;">
								<?php
			
								echo "<table align=\"center\"  border=\"1\" cellspacing=\"0\" cellpadding=\"3\">";
								//get field names to create the column header
								echo "<tr bgcolor=\"#3593DE\">
									<th></th>
									<th>".$_L['RTS_code']."</th>
									<th>".$_L['RTS_desc']."</th>
									<th>".$_L['RTS_datefrom']."</th>
									<th>".$_L['RTS_dateto']."</th>
									<th>".$_L['RTS_ratetype']."</th>
									<th>".$_L['RTS_currency']."</th>
									<th>".$_L['RTL_pax']."</th>
									<th>".$_L['RTL_stay']."</th>
									<th>".$_L['RTS_minbook']."</th>
									</tr>";
								//end of field header
								//get data from selected table on the selected fields
								foreach ($rateslist as $idx => $val) {
									//alternate row colour
									$j++;
									if($j%2==1){
										echo "<tr bgcolor=\"#CCCCCC\">";
									}else{
										echo "<tr bgcolor=\"#EEEEF8\">";
									}
									echo "<td><a href=\"index.php?menu=rateSetup&id=".$idx."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"view rate details\"/></a></td>";
									echo "<td>" . $rateslist[$idx]['ratecode'] . "</td>";
									echo "<td>" . $rateslist[$idx]['description'] . "</td>";
									echo "<td>" . $rateslist[$idx]['date_started'] . "</td>";
									echo "<td>" . $rateslist[$idx]['date_stopped'] . "</td>";
									$ratetype = $rateslist[$idx]['rate_type'];
									$ratetypestr = "";
									if ($ratetype==1)
										$ratetypestr= "Default";
									elseif ($ratetype==2)
										$ratetypestr= "PromoRate";
									elseif ($ratetype==3)
										$ratetypestr= "CustomerRate";
									elseif ($ratetype==4)
										$ratetypestr= "AgentRate";
									elseif ($ratetype==7)
										$ratetypestr= "Fee";
									echo "<td>" . $ratetypestr . "</td>";
									echo "<td>" . $rateslist[$idx]['currency'] . "</td>";
									echo "<td>" . $rateslist[$idx]['min_people'] . " - " . $rateslist[$idx]['max_people'] . "</td>";					
									echo "<td>" . $rateslist[$idx]['min_stay']. " - ". $rateslist[$idx]['max_stay'] . "</td>";
									echo "<td>" . $rateslist[$idx]['min_advanced_booking'] . "</td>";
									echo "</tr>"; //end of - data rows
								} //end of while row
								echo "</table>";
								?>
							  </div>
							</td>		
						  </tr>
						  <tr class="tcbgcl" ><td >&nbsp;</td></tr>
						     						  <tr align="right"><td><input class="button" type="button" name="Submit" value="<?php echo $_L['RTS_addrate']; ?>" onClick="self.location='index.php?menu=rateSetup'" />
						  	<input class="button" type="button" name="Submit" value="<?php echo  $_L['RTS_listrates']; ?>" onclick="self.location='index.php?menu=ratesList'"/>
						  </td></tr>														  
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
 */
?>
