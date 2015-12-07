<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */
error_reporting(E_ALL | E_STRICT);
include_once(dirname(__FILE__)."/../dailyfunc.php");
include_once(dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel.php");
include_once(dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");


$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
date_default_timezone_set(TIMEZONE);
// Get the current settings
Get_HotelSettings($hotel, $altname, $company, $register,
		$ebridgeid, $tax1, $tax2, $phone, $fax, $IM, $street,
		$city, $citycode, $state, $postcode, $countrycode, $country,
		$logo, $latitude, $longitude, $language, $email, $web, $ota, $chaincode);
		
?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      	
	 		<?php print_rightMenu_reports();?> 
	          <td valign="top">  
	          <?php if(is_ebridgeCustomer() && accessNew('reports')){ 
	          	include(dirname(__FILE__)."/../OTA/reports/hotel_roomusability_rpt.php");	          	
	          } else {?> 
				<form action="<?php echo $_SERVER['REQUEST_URI'];  ?>" name="reportres" id="reportres"  method="post" enctype="multipart/form-data">
				<table width=100%>
				
					<tr class="tdbgcl">
						<td align="left"></td>	
					</tr>
					<tr class="tdbgcl">
					<td>	
					  	<table>	
						<tr>							
							<td width="50%">
								<h3>This feature is available to e-Bridge customers only.<br/>Register with <a href="https://www.e-bridgedirect.com" target="e-Bridge">e-Bridge</a> </h3><a href="https://www.e-bridgedirect.com" target="e-Bridge"><img src="images/Splash_HotelGolf.jpg" width="100%" height="50%" /></a>
							</td>
							<td width="50%" valign="top">
							<h2><?php echo $_L['RT_roomusabilityrpt']; ?></h2><?php echo  $_L['RT_roomusabilitydescfull'];?><br/><br/><b><?php echo  $_L['RT_hotelguestprev'];?></b>
								<ul class="enlarge">								
									<li><img src="reports/images/hotel_roomusability_report_screen.jpg" width="100px" height="100px" alt="Image" /><span><img  width="" height="" src="reports/images/hotel_roomusability_report_screen.jpg" alt="Image" /><br /><b>Room Usability Report Screen Shot</b></span></li>
									<li><img src="reports/images/hotel_roomusability_report.jpg" width="100px" height="100px" alt="Image" /><span><img  width="" height="" src="reports/images/hotel_roomusability_report.jpg" alt="Image" /><br /><b>Room Usability Report Spreadsheet</b></span></li>						
								</ul>
							</td>						
						</tr>
						</table>	
					</td>
				  	</tr>				
				
				</table>
				
				</form>
			<?php }?>
	          </td>	               
	        </tr>
	        <tr>
	          <td colspan="2">&nbsp;</td>
	        </tr>
	      </tbody>
      </table>