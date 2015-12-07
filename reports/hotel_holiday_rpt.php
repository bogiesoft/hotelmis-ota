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
access("reports"); //check if user is allowed to access this page		
$year = getYearHoliday();
$result=array();
$request="";
$submit="";
if (isset($_POST['Submit'])) {
	$submit=$_POST['Submit'];
}
	
if(isset($_POST['showReport'])) {
//	delHolidayByID($_POST['HolidayID']);
	if(isset($_POST['year'])){
		$result= getHoliday_by_year($_POST['year']);
	}
} 

if ($submit==$_L['EXP_download']) {
	$request='holidayRpt';
	include("reportfunction.php");
}

?>
       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr>      
	 		<?php print_rightMenu_reports();?> 	
	          <td valign="top">	           
				
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" name="reportol" id="reportol"  method="post" enctype="multipart/form-data">
				<div class="tdbgcl">
				<div >
				<h2><a href="https://www.youtube.com/watch?v=gQ8fJhxnYbk" target="reshelp" title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['RT_Holidayrpt']; ?></a></h2>
				<!--<h2><?php //echo $_L['RT_Holidayrpt']; ?></h2>-->
				<p><?php echo  $_L['RT_Holidaydescfull'];?></p>
				</div>
				<table height="370px" width=100%>
				
					<tr><td valign="top"  width=30%>
					 	<table border="0" cellspacing="0" cellpadding="3">
						  	<tr>
							<td colspan="2"><b><?php echo $_L['RT_hoidayYear'];?></b></td>
							</tr>
							<tr>
							 <td>
							 <select align="right"  name="year">
							 	<option value="">Please select year</option>
							 <?php 
							 	for($numyear=0; $numyear<count($year); $numyear++){
							 		echo "<option ";
								 	if ($_POST['year']== $year[$numyear])
								 		{ echo " selected";}
							 		echo " value=".$year[$numyear].">".$year[$numyear]."</option>";
							 	}
							 ?>
							 
							 </select>
							 </td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
							  <td colspan="2" align="right"><input name="showReport" id="showReport" type="submit"  value='<?php echo $_L['RT_Show'];?>' class='button' /></td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
							  <td colspan="2" align="right"><input name="Submit" id="Submit" type="submit"  value='<?php echo $_L['EXP_download'];?>' class='button' /></td>
							</tr>
						</table> 
					</td>
					
					<td valign="top"  width=70%>
					<div id="" style="overflow:auto; height:300px;">
					<table border="1" cellspacing="0" cellpadding="3" style='width: 300px' height="50px">					
							
							<tr bgcolor="#3593DE"><th color="#000000" ><?php echo $_L['RTS_holidays'];?></th><th><?php echo $_L['HL_Date'];?></th></tr>
							<?php 
								for($i=0; $i<count($result);$i++){
									echo "<tr><td width=65%>".$result[$i]['Description']."</td><td width=35%>".substr($result[$i]['days'],0,10)."</td></tr>";										
								}
							?>
							 <tr><td></td><td></td></tr>
							</tr>
							
							
						</table> 
						</div>
					</td></tr>
					<tr><td class="tdbgcl" >&nbsp;</td></tr>
				</table>
				</div>
				</form>
	          </td>	             
	        </tr>
	      </tbody>
      </table>