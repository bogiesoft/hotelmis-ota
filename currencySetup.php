<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file currencySetup.php
 * @brief currency setup web page called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup ADMIN_MANAGEMENT Hotel setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */

session_start();
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
access("admin"); //check if user is allowed to access this page

$logofile = Get_LogoFile();

// If the POST forms are set, override the retrieved settings
$currency = "";
$countrycode = "";

if($_POST['currencycode']) {
	$currency = $_POST['currencycode'];
}
if($_POST['countrycode']) {
	$countrycode = $_POST['countrycode'];
}

if($_POST['Save']) {
	$fv=new formValidator(); //from functions.php
	$fv->validateEmpty('countrycode',$_L['CUR_country_error']);
	$fv->validateEmpty('currencycode',$_L['CUR_currency_error1']);
	if (!empty($_POST['currencycode']))
		$fv->validateAlphabetic('currencycode', $_L['CUR_currency_error2']);
	if($fv->checkErrors()){
		//display errors
		$validationMsgs = "<div align=\"left\"><h2>".$_L['PR_formerr']."</h2>".$fv->displayErrors()."</div>";
	}
	else {
		Save_Currency($countrycode, $currency);
	}
}

// Retrieve the currency for the country code currently set.
$currency = Get_Currency_by_Countrycode($countrycode);

?>

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>   
	 		<?php print_rightMenu_admin();?> 		 	
	          <td valign="top">	          
					<form action="<?php echo $_SERVER['REQUEST_URI'];?>" name="currencySetup" id="currencySetup" method="post" enctype="multipart/form-data">
					      <table class="c16" width="100%" border="0" cellpadding="1" align="center">
					        <tr valign="top">  
					    <td class="tdbgcl" class="c4" width="85%">
					    <div>
						<table width="100%"  border="0" cellpadding="1">
					      <tr>
					        <td align="center"></td>
					      </tr>
					      
					      <tr>
					  		<td><?php echo $validationMsgs?></td>
					  	  </tr>
					      <tr>
					        <td><h2><a href="https://www.youtube.com/watch?v=beccHbOJOOY" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['CUR_title']; ?></a></h2></td>
					      </tr> <tr>
					        <td align="center">&nbsp;</td>
					      </tr>
						<tr>
					        <td><div id="Requests">
					<table width="40%"  border="0" cellpadding="1">
					  <tr>
					    <td><?php echo $_L['CUR_country']; ?></td>
					    <td><select name="countrycode" id="countrycode"  onchange="document.currencySetup.submit()">
					    	<option value=""><?php echo $_L['CUR_selcntry'];?></option>
							<?php populate_select("countries", "countrycode", "Country", $countrycode, ""); ?>
							</select>
						</td>
					  </tr>
					  <tr><td>&nbsp;</td></tr>
					  <tr>
					    <td><?php echo $_L['CUR_currency']; ?></td>
					    <td><input type="text" name="currencycode" maxlength=10 value="<?php echo $currency; ?>"/></td>
					  </tr>
					  <tr><td>&nbsp;</td></tr>
					    </table></div></td>
					  </tr>
					  <tr height="294"><td>&nbsp;</td></tr>
					</table>
					</div>
					<div>
						<table align="right">
						   <tr>
							<td align="right" ><input class="button" type="submit" name="Save" id="Save" value="<?php echo $_L['BTN_save']; ?>"/></td>
							</tr>
							<tr><td>&nbsp;</td></tr>		
						</table>
					</div>
					
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