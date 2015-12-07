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

access("admin"); //check if user is allowed to access this page
$logofile = Get_LogoFile();
$lang = get_language();
load_language($lang);

if (isset($_POST['Submit'])){
	$action=$_POST['Submit'];
	switch ($action) {
		case $_L['PL_addpolicy']:
		case $_L['BTN_update']:

				/** 
				 * When the Room is to be added or updated the form data will be retrieved.
				 * This includes the phone, room number etc which is then
				 * submitted to the database. <br/>
				 */
				$policyindex=$_POST["policyindex"];
				$policyid=$_POST["policyid"];
				$rateid=$_POST["ratesid"];
				$title=$_POST["title"];
				$language=$_POST["language"];
				$description=$_POST["description"];
				$encode=$_POST['enc_desc'];
				
				if($encode != $description) {
					$description=$encode;
				}

				modify_policy($policyindex, $policyid ,$rateid, $title, $language, $description);

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
	if($_GET['search'] ) $idpolicy=$_GET["search"];
	$res = get_policy($idpolicy, $policy);
	$idpolicy = $policy['idpolicy'];
	$_GET['search'] = 1;
}


?>
	<script type="text/javascript">
	  <!--
	  var request;
	  var dest;
		/*
		convertToEntities()
		This is to convert characters to Unicode numbers
		*/
		function convertToEntities() {
		  var tstr = document.getElementById('description').value;
		  var bstr = '';
		  for(i=0; i<tstr.length; i++)
		  {
			if(tstr.charCodeAt(i)>127)
			{
			  bstr += '&#' + tstr.charCodeAt(i) + ';';
			}
			else
			{
			  bstr += tstr.charAt(i);
			}
		  }
		  document.getElementById('enc_desc').value = bstr;
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

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>
	 		<?php print_rightMenu_admin();?> 		
	          <td valign="top">
	          	
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
			      <table class="c16" width="100%" border="0" cellpadding="1" align="center">
			        <tr class="tdbgcl" height="465px" valign="top">
			   		  <td class="c4" width="65%">
			   		  <div style="overflow:auto; width:700px; height:410px;" >
						<table width="100%"  border="0" cellpadding="1">
						  <tr><td align="center"></td></tr>
						  
						  <tr><td><h2><a href="https://www.youtube.com/watch?v=JpMey_FUXWs" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['PL_setup']; ?></a></h2></td></tr>
						   <tr><td align="center">&nbsp;	</td></tr>
						   
						  <tr height="350">
							<td valign="top">
							  <table width="100%"  border="0" cellpadding="1">
								<tr style="display:none;">
								  <td width="84%"><input type="text" name="policyindex" value="<?php echo trim($policy['idpolicy']); ?>" /></td>
								</tr>
								<tr>
								  <td width="16%"><?php echo $_L['PL_id']; if($policyid) echo "<input type=hidden name=policyid id=policyid value='".$policyid."' />"; ?></td>
								  <td width="84%"><input type="text" name="policyid" value="<?php echo trim($policy['ID']); ?>" /></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td><?php echo $_L['PL_title']; ?></td>
								  <td><input type="text" name="title" value="<?php echo trim($policy['title']); ?>" /></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td><?php echo $_L['PL_rateid']; ?></td>
								  <td><select name="ratesid"><option value='0'></option><?php populate_select("rates","ratesid","ratecode",$policy['rateid'], "");?></select></td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td><?php echo $_L['PL_des']; ?></td>
								  <td><textarea name="description" id="description" cols="40" rows="2" onchange="convertToEntities();"><?php echo $policy['description']; ?></textarea>
								      <input type="hidden" id="enc_desc" name="enc_desc" value="<?php echo $policy['description']; ?>" />
								  </td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr>
								  <td><?php echo $_L['PL_lang']; ?></td>
								  <td><select name="language"><option value='0'></option><?php populate_select("languages","lang","lang",$policy['language'], "active = 1");?></select></td>
								</tr>
								</tr>
								<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
							  </table>
							</td>
						  </tr>
						  <tr><td align="left"><div id="RequestDetails"></div></td></tr>
						</table>
						</div>
					<div>
				  	<table align="right">
				  		<tr height="18px" ><td>&nbsp;</td></tr>
						<tr><td colspan="4"><input class="button" type="submit" name="Submit" value="<?php echo isset($_GET["search"]) ? $_L['BTN_update'] : $_L['PL_addpolicy']; ?>" />
							 <input class="button" type="button" name="Submit" value="<?php echo $_L['PL_listpolicy']; ?>" onclick="self.location='index.php?menu=policyList'"/>
							 </td></tr>				  	
				  	</table>
				    </div>						
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
