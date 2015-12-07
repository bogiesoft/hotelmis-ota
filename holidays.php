<?php
/**
 * @author Neil Thyer
 * @version 1.0
 * @copyright e-Novate Pte Ltd 2012-2015
 */

session_start();
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__).'/functions.php');
include_once(dirname(__FILE__)."/lang/lang_en.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();
access("admin"); //check if user is allowed to access this page
date_default_timezone_set(TIMEZONE);

?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>  
	 		<?php print_rightMenu_admin();?> 		 	
	          <td valign="top">	
      
				<form action="<?php echo $_SERVER['REQUEST_URI'];?>" name="holidaySetup" id="holidaySetup" method="post" enctype="multipart/form-data">
				      <table class="c16" width="100%" border="0" cellpadding="1" align="center">
				        <tr class="tdbgcl" valign="top"> 
						  <td class="c4" width="85%">
							<table class="tdbgcl" width="100%"  border="0" cellpadding="1">
							  <tr>
								<td align="center"></td>
							  </tr>
							
							  <tr>
								<td><?php echo $validationMsgs?></td>
							  </tr>
							  <tr>
								<td><h2><a href="https://www.youtube.com/watch?v=Yb3i39H5v-o" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['HL_Title']; ?></a></h2></td>
							  </tr>
							  <tr>
								<td align="center">&nbsp;</td>
							  </tr>
							  <tr>
								<td>
								<table align=left width="60%"  border="0" cellpadding="1">
								 
								<!--  -->
								 
								 <tr><td>
									<div id="Requests">
									
									 <?php 
												  if(is_ebridgeCustomer()){
														include_once(dirname(__FILE__)."/OTA/advancedFeatures/holidayconfig.php");
												  }else{ ?>
													<table>
														<tr>
															<td><h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3></td>
														</tr>
														<tr>
														
															<td>
																<a href="https://www.e-bridgedirect.com" target="e-Bridge"> <img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
															</td>
														</tr>
													</table>				  		
												  <?php 
													}
												  ?>
									</div>
									</td>
									</tr>
								   <tr>
										  <td colspan="2">&nbsp;</td>
								   </tr>
								<!--  -->
								</table>
								</td>
								<td class="c4" width="5%"> </td>
							  </tr>
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


