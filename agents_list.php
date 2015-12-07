<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file agents_list.php
 * @brief agents list webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 * @addtogroup CODE_MANAGEMENT
 * @defgroup AGENT_MANAGEMENT Travel Agent setup and management page
 *
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

/**< language $lang */
$lang = get_language(); 
load_language($lang);
/**< logo file $logofile */
$logofile = Get_LogoFile();

if(!$stype) $stype = 0;
if(!$search) $search = "";

if (isset($_POST['Submit'])){
	$conn=connect_Hotel_db(HOST,USER,PASS,DB,PORT);
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['BTN_update']:

			break;
		case $_L['BTN_list']:

			return;
			break;
		case $_L['BTN_search']:
			$search=$_POST["search"];
			if($_POST['optFind'] == $_L['AGT_name']) $stype = 2;
			if($_POST['optFind'] == $_L['AGT_ac']) $stype = 1;
			break;
	}
}

/**< agent list $agent */
$agent = array();
/**< size $act */
$act = get_agentlist($search, $stype, $agent);
?>

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_admin();?> 
	          <td valign="top">

				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				  <table class="tdbgcl" width="100%"  border="0" cellpadding="1" align="center">
					<tr valign="top">
					  <td width="65%">
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						  <tr><td><h2><?php echo $_L['AGT_title_list']; ?></h2></td></tr>						  									
							<tr align="center">
							  <td colspan="9">
								<label> <?php echo $_L['PR_search'].":"; ?>
								  <input type="radio" name="optFind" value="<?php echo $_L['AGT_name'] ?>" />
								  <?php echo $_L['AGT_name'] ?>
								</label>
								<label>
								  <input type="radio" name="optFind" value="<?php echo $_L['AGT_ac'] ?>" />
								  <?php echo $_L['AGT_ac'] ?>
								</label>
							  </td>
							</tr>
						  <tr align="center">
							  <td>
								<input type="text" name="search" width="100" />
								<input class="button" type="submit" name="Submit" value="<?php echo $_L['BTN_search'] ?>"/>									
							  </td>
						  </tr>								
						  <tr height="343">
							<td>
							  <div id="Requests" style="overflow:auto; width:772;height:300;">
								<table align="center">	
																
								<tr>
								<td>
								<table align="center" border="1" cellspacing="0" cellpadding="3">
								  <tr bgcolor="#3593DE">
									<th></th>
									<th><?php echo $_L['AGT_ac']; ?></th>
									<th><?php echo $_L['AGT_name']; ?></th>
									<th><?php echo $_L['AGT_contact']; ?></th>
									<th><?php echo $_L['AGT_fax']; ?></th>
									<th><?php echo $_L['AGT_phone']; ?></th>
									<th><?php echo $_L['AGT_email']; ?></th>
									<th><?php echo $_L['AGT_street']; ?></th>
									<th><?php echo $_L['AGT_pcode']; ?></th>
								  </tr>
								  <?php  
								  /**< counter $j */
								  $j = 0;
								  foreach ($agent as $idx => $val) {
									$j++;
									if($j%2==1){
									  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#CCCCCC\">";
									}else{
									  echo "<tr id=\"row".$j."\" onmouseover=\"javascript:setColor('".$j."')\" onmouseout=\"javascript:origColor('".$j."')\" bgcolor=\"#EEEEF8\">";
									}
									echo "<td><a href=\"index.php?menu=agentSetup&id=".$idx."\"><img src=\"images/button_view.png\" width=\"16\" height=\"16\" border=\"0\" title=\"View\"/></a></td>";
									echo "<td>" . $agent[$idx]['iata'] . "</td>";
									echo "<td>" . $agent[$idx]['name'] . "</td>";
									echo "<td>" . $agent[$idx]['contact'] . "</td>";
									echo "<td>" . $agent[$idx]['fax'] . "</td>";
									echo "<td>" . $agent[$idx]['phone'] . "</td>";
									echo "<td>" . $agent[$idx]['email'] . "</td>";
									echo "<td>" . $agent[$idx]['billing'] . "</td>";					
									echo "<td>" . $agent[$idx]['town'] . '-' . $agent[$idx]['postcode'] . "</td>";
									echo "</tr>"; //end of - data rows
								  } //end of while row
								  ?>
								  </table>
								  </td>
								  </tr>
								</table>
							  </div>
							</td>
						  </tr>						
						  <tr><td align="left">&nbsp;</td></tr>
						  <tr align="right"><td colspan="9"><input class="button" type="button" name="Submit" value="<?php echo $_L['AGT_addagent']; ?>" onclick="self.location='index.php?menu=agentSetup'"/>
							<input class="button" type="button" name="Submit" value="<?php echo $_L['AGT_listagent']; ?>" onclick="self.location='index.php?menu=agentsList'"/>
							</td></tr>	
						<tr><td align="left">&nbsp;</td></tr>
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
  */
 ?>