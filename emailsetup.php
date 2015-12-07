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
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("admin"); //check if user is allowed to access this page



?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>	
	 		<?php print_rightMenu_admin();?> 		
	          <td valign="top">
	          	
				  <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" enctype="multipart/form-data">
				    <table  class="tdbgcl" width="100%" border="0" cellpadding="1" align="center">
					  <tr><td><h2><a href="https://www.youtube.com/watch?v=4psa4mbTZ5Q" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['MNU_emailsetup']; ?></a></h2></td></tr>
				      <tr  height="" valign="top">
				        <td  width="">
						  <table  width="100%"  border="0" cellpadding="1">
						
							<?php 
							//display the advanced user profile page with indispensable features 
							//Only availble for ebridge customers
							if(is_ebridgeCustomer()){
								include_once(dirname(__FILE__)."/OTA/advancedFeatures/emailconfig.php");
							}else{
								?>
								<tr>
							  		<td><h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3></td>
							  	</tr>
							  	<tr>
									<td>
									<a href="https://www.e-bridgedirect.com" target="e-Bridge"> <img src="images/Splash_HotelGolf.jpg" width="50%" height="50%" /></a>
									</td>
							  	</tr>		  		
								<?php 
							}
							?>
							<tr><td>&nbsp;</td></tr>
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
