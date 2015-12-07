<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file reports.php
 * @brief reports webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * 
 */
//error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/PHPExcel/Classes/PHPExcel.php");

$lang = get_language();
load_language($lang);
$logofile=Get_LogoFile();



access("reports"); //check if user is allowed to access this page


$request = $_GET["report"];

if ($_POST['Submit']){
 	if(is_ebridgeCustomer()){
		include_once(dirname(__FILE__)."/OTA/reports/reportfunction.php");
	}else{
		include_once(dirname(__FILE__)."/reports/reportfunction.php");
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css/new.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="js/dhtmlgoodies_calendar.css" media="screen">
<title><?php echo $_L['MAIN_Title'];?></title>

<script type="text/javascript">
<!--

//-->	 
</script>
<script language="JavaScript" src="js/highlight.js" type="text/javascript"></script>
	<SCRIPT type="text/javascript" src="js/dhtmlgoodies_calendar.js"></script>
	

</head>

<body>
<form action="<?php echo $_SERVER['REQUEST_URI'];  ?>" name="report" id="report" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="1" align="center" bgcolor="#66CCCC">
  <tr valign="top">
          <td class="c3" width="15%">
            <table width="100%" border="0" cellpadding="1">
              <tr>
                <td class="c2" width="15%">
                  <table class="c1" cellspacing="0" cellpadding="0"
                  width="100%" align="left">
                    <tr>
                      <td width="110" align="center">
                        <a href="index.php"><img src="<?php echo $logofile; ?>" width="70" height="74" border="0" /><br /> <?php echo $_L['MAIN_Home'];?></a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        &nbsp;
                      </td>
                    </tr>
                    <tr>
                      <td align="center">
                        <?php signon();?>
                      </td>
                    </tr>
					  <tr>
						<td>&nbsp;</td>
					  </tr>
					  <tr>
						<td align="center">
							<?php shift_times(); ?>
						</td>
					  </tr>
                  </table>
                </td>
              </tr><?php require_once("menu_header.php");?>
            </table>
          </td>
    
    <td bgcolor="#66CCCC"><table width="100%"  bgcolor="#66CCCC" border="0" cellpadding="1">
      <tr>
		<td>
		
		<table width="100%"  border="0" cellpadding="1">
			  <tr>
				<td align="center"></td>
			  </tr>
			  <tr>
				<td><?php echo $validationMsgs;?></td>
			  </tr>
			  <tr>
				<td><h2><?php echo $_L['RT_Title']; ?></h2></td>
			  </tr>
			  <tr>
				<td>
				<table width="60%"  border="0" cellpadding="1">
				 
				<!--  -->
				 <?php 
				 if($request){
				 	$displayList = "none";
				 }else{
				 	$displayList = "block";
				 }
				 
				 ?>
				 <tr><td align=left>
					<div id="ReportList"  style="display:<?php echo $displayList;?>;">
						
						<table width="100%"  border="1" cellpadding="1">
						<tr><th><?php echo $_L['RT_reportName']; ?></th><th> <?php echo $_L['RT_reportDesc'];?></th><th></th></tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_Holidayrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_Holidaydesc'];?></td>
						<td><input type="button" name="showholidayBtn" id="showholidayBtn" onclick="showreport(this, 'holidayRpt');" value="Get Report"></input></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_hotelguestrpt'];?></u></b></a>
                      	</td>
                      	
						<td align="left"><?php echo  $_L['RT_hotelguestdesc'];?></td>
						<td><input type="button" name="showResBtn" id="showResBtn" onclick="showreport(this, 'resRpt');" value="Get Report"></input></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_OnlineBookingrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_OnlineBookingdesc'];?></td>
						<td><input type="button" name="showOlBtn" id="showOlBtn" onclick="showreport(this, 'onlineBookRpt');" value="Get Report"></input></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_roomstatusrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_roomstatusdesc'];?></td>
						<td><input type="button" name="showrmstatBtn" id="showrmstatBtn" onclick="showreport(this, 'rmStatusRpt');" value="Get Report"></input></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_ReceiptDailyrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_ReceiptDailydesc'];?></td>
						<td><input type="button" name="showreceiptBtn" id="showreceiptBtn" onclick="showreport(this, 'receiptDailyRpt');" value="Get Report"></input></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_Receiptrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_Receiptdesc'];?></td>
						<td><input type="button" name="showreceiptBtn" id="showreceiptBtn" onclick="showreport(this, 'receiptRpt');" value="Get Report"></input></td>
						</tr>
						
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_roomusabilityrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_roomusabilitydesc'];?></td>
						<td><input type="button" name="showroomaccBtn" id="showroomaccBtn" onclick="showreport(this, 'rmusabilityRpt');" value="Get Report"></input></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_shiftrpt'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_shiftdesc'];?></td>
						<td><input type="button" name="showshiftBtn" id="showshiftBtn" onclick="showreport(this, 'shiftRpt');" value="Get Report"></input></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_taxreport'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_taxdesc'];?></td>
						<td><input type="button" name="showtaxBtn" id="showtaxBtn" onclick="showreport(this, 'taxRpt');" value="Get Report"></input></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_agodareport'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_AgodaBookingdesc'];?></td>
						<td><input type="button" name="showtaxBtn" id="showtaxBtn" onclick="showreport(this, 'agodaBkRpt');" value="Get Report"></input></td>
						</tr>
						<tr>
						<td align="left" >
						<a id="res" name="res" ><b><u> <?php echo  $_L['RT_tourismreport'];?></u></b></a>
                      	</td>
						<td align="left"><?php echo  $_L['RT_tourismdesc'];?></td>
						<td><input type="button" name="showtaxBtn" id="showtaxBtn" onclick="showreport(this, 'tourRpt');" value="Get Report"></input></td>
						</tr>						
						</table>
					
					</div>
					<?php 
					
					 if($request){
					 	$displayData = "block";
					 }else{
					 	$displayData = "none";
					 }
					 
					 ?>
					<div id="ReportData"  style="display:<?php echo $displayData;?>;">
						
					<!-- Place holder for the report data-->
					 <?php
					 
					  if(is_ebridgeCustomer()){
						  switch ($request) {
						  		case 'resRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_res_rpt.php");
									break;
								case 'onlineBookRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_olbook_rpt.php");
									break;
								case 'holidayRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_holiday_rpt.php");
									break;
								case 'rmStatusRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_roomstatus_rpt.php");
									break;
								case 'receiptDailyRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_receiptdaily_rpt.php");
									break;
								case 'receiptRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_receipt_rpt.php");
									break;
								case 'rmusabilityRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_roomusability_rpt.php");
									break;
								case 'shiftRpt':
									include_once(dirname(__FILE__)."/OTA/reports/shift_rpt.php");
									break;
								case 'taxRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_tax_rpt.php");
									break;
								case 'agodaBkRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_agodaBooking_rpt.php");
									break;
								case 'tourRpt':
									include_once(dirname(__FILE__)."/OTA/reports/hotel_tourism_rpt.php");
									break;
						  }
					  }else{
						  switch ($request) {
						  		case 'resRpt':
									include_once(dirname(__FILE__)."/reports/hotel_res_rpt.php");
								
									break;
								case 'onlineBookRpt':
									include_once(dirname(__FILE__)."/reports/hotel_olbook_rpt.php");
									break;
								case 'holidayRpt':
									include_once(dirname(__FILE__)."/reports/hotel_holiday_rpt.php");
									break;
								case 'rmStatusRpt':
									include_once(dirname(__FILE__)."/reports/hotel_roomstatus_rpt.php");
									break;
								case 'receiptDailyRpt':
									include_once(dirname(__FILE__)."/reports/hotel_receiptdaily_rpt.php");
									break;
								case 'receiptRpt':
									include_once(dirname(__FILE__)."/reports/hotel_receipt_rpt.php");
									break;
								case 'rmusabilityRpt':
									include_once(dirname(__FILE__)."/reports/hotel_roomusability_rpt.php");
									break;
								case 'shiftRpt':
									include_once(dirname(__FILE__)."/reports/shift_rpt.php");
									break;
								case 'taxRpt':
									include_once(dirname(__FILE__)."/reports/hotel_tax_rpt.php");
									break;
								case 'agodaBkRpt':
									include_once(dirname(__FILE__)."/reports/hotel_agodaBooking_rpt.php");
									break;
								case 'tourRpt':
									include_once(dirname(__FILE__)."/reports/hotel_tourism_rpt.php");
									break;
						  	}
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
      <tr>
        <td valign="top" colspan="6">&nbsp;</td>
      </tr>
	  <tr>
        <td align="left" colspan="6"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
	<td colspan=2>
	<table> <tr>
 
   </tr></table>
   </td>
  </tr>
</table>
</form>
</body>
<script>

function showreport(obj, value){

	var resRpt = obj.name;
	document.getElementById("ReportData").style.display="block";
	window.location.href = document.URL + "?report=" + value;
	
}
</script>
</html>


