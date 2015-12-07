<?php
session_start();
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @brief export billings webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup INVOICE_MANAGEMENT Invoice setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */
error_reporting(E_ALL | E_STRICT);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/PHPExcel/Classes/PHPExcel.php");

$lang = get_language();
load_language($lang);
$logofile = Get_LogoFile();
date_default_timezone_set(TIMEZONE);
access("billing"); //check if user is allowed to access this page

$fromdate = date('Y-m-d H:i');
$todate = date('Y-m-d H:i');

if ($_POST['Submit']){
	
	$fromdate = $_POST['fromdate'];
	$todate = $_POST['todate'];
		
	$invoices = array();
	$inv_cnt = get_all_transactions_By_DateRange($fromdate, $todate, $invoices);
	//print_r($invoices);
	
	$receipts = array();
	$rcpt_cnt = get_all_receipts_By_DateRange($fromdate,$todate,$receipts);
	//print_r($receipts);
	while (ob_get_length()) {
	  ob_end_clean();
	}
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("e-novate Pte Ltd")
							 ->setLastModifiedBy("e-novate Pte Ltd");
							 
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Invoice No.");
	$objPHPExcel->getActiveSheet()->setCellValue('B1', "Guest Name.");
	$objPHPExcel->getActiveSheet()->setCellValue('C1', "Invoice Date");
	$objPHPExcel->getActiveSheet()->setCellValue('D1', "Booking No.");
	$objPHPExcel->getActiveSheet()->setCellValue('E1', "Reservation No.");	
	$objPHPExcel->getActiveSheet()->setCellValue('F1', "Transaction Date");
	$objPHPExcel->getActiveSheet()->setCellValue('G1', "Product Name");
	$objPHPExcel->getActiveSheet()->setCellValue('H1', "Amount");
	$objPHPExcel->getActiveSheet()->setCellValue('I1', "Service");
	$objPHPExcel->getActiveSheet()->setCellValue('J1', "Tax");
	$objPHPExcel->getActiveSheet()->setCellValue('K1', "Quantity");
	$objPHPExcel->getActiveSheet()->setCellValue('L1', "Total Service");
	$objPHPExcel->getActiveSheet()->setCellValue('M1', "Total Tax");
	$objPHPExcel->getActiveSheet()->setCellValue('N1', "Total Amount");
	$objPHPExcel->getActiveSheet()->setCellValue('O1', "Gross Amount");
	$objPHPExcel->getActiveSheet()->setCellValue('P1', "Invoice Status");	
	$objPHPExcel->getActiveSheet()->setCellValue('O1', "Currency");
	
	$i = 2;
	for ($j = 0; $j < $inv_cnt; $j++) {
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $invoices[$j]['invoiceNo']);
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, get_guestname($invoices[$j]['guestid']));
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $invoices[$j]['invoiceDate']);
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $i, get_bookingvoucher($invoices[$j]['bookID']));
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $i, get_reservationvoucher($invoices[$j]['resID']));
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $invoices[$j]['add_date']);
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $i, get_itemname($invoices[$j]['item_id']));
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $invoices[$j]['std_amount']);
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $invoices[$j]['std_svc']);
		$objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $invoices[$j]['std_tax']);
		$objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $invoices[$j]['quantity']);
		$objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $invoices[$j]['svc']);
		$objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $invoices[$j]['tax']);
		$objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $invoices[$j]['amount']);
		$objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $invoices[$j]['grossamount']);
		$status = get_general_status_text($invoices[$j]['status']);
		if(!$status)
			$status = $invoices[$j]['status'];
		$objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $status);
		$objPHPExcel->getActiveSheet()->setCellValue('O' . $i, $invoices[$j]['currency']);
		$i++;
	}	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Invoice');
	
	// Create new sheet
	$objPHPExcel->createSheet(1);
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "Receipt No.");
	$objPHPExcel->getActiveSheet()->setCellValue('B1', "Invoice No.");
	$objPHPExcel->getActiveSheet()->setCellValue('C1', "Guest Name.");
	$objPHPExcel->getActiveSheet()->setCellValue('D1', "Invoice Date");
	$objPHPExcel->getActiveSheet()->setCellValue('E1', "Receipt Date");
	$objPHPExcel->getActiveSheet()->setCellValue('F1', "Booking No.");
	$objPHPExcel->getActiveSheet()->setCellValue('G1', "Amount");
	$objPHPExcel->getActiveSheet()->setCellValue('H1', "Form of Payment");
	$objPHPExcel->getActiveSheet()->setCellValue('I1', "Card Type");
	$objPHPExcel->getActiveSheet()->setCellValue('J1', "Card Number");
	$objPHPExcel->getActiveSheet()->setCellValue('K1', "Auth");
	$objPHPExcel->getActiveSheet()->setCellValue('L1', "Transaction Date");
	$objPHPExcel->getActiveSheet()->setCellValue('M1', "Source Currency");
	$objPHPExcel->getActiveSheet()->setCellValue('N1', "Target Currency");
	$objPHPExcel->getActiveSheet()->setCellValue('O1', "Exchange Rate");
	$objPHPExcel->getActiveSheet()->setCellValue('P1', "Invoice Status");	
	
	$k = 2;
	for ($j = 0; $j < $rcpt_cnt; $j++) {
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $k, $receipts[$j]['receiptNo']);
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $k, $receipts[$j]['invoiceNo']);
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $k, get_guestname($receipts[$j]['guestid']));
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $k, $receipts[$j]['invoiceDate']);
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $k, $receipts[$j]['receiptDate']);
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $k, get_bookingvoucher($receipts[$j]['bookID']));
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $k, $receipts[$j]['amount']);
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $k, get_foptext($receipts[$j]['formOfPayment']));
		$objPHPExcel->getActiveSheet()->setCellValue('I' . $k, get_creditcardString($receipts[$j]['ccType']));
		$cardnum = '';
		if ($receipts[$j]['ccNumber']!="")
			$cardnum = mask_cardnumber($receipts[$j]['ccNumber']);
		$objPHPExcel->getActiveSheet()->setCellValue('J' . $k, $cardnum);
		$objPHPExcel->getActiveSheet()->setCellValue('K' . $k, $receipts[$j]['auth']);
		$objPHPExcel->getActiveSheet()->setCellValue('L' . $k, $receipts[$j]['createdDate']);
		$objPHPExcel->getActiveSheet()->setCellValue('M' . $k, $receipts[$j]['srcCurrency']);
		$objPHPExcel->getActiveSheet()->setCellValue('N' . $k, $receipts[$j]['tgtCurrency']);
		$objPHPExcel->getActiveSheet()->setCellValue('O' . $k, $receipts[$j]['exrate']);
		
		$status = $invoices[$j]['status'];
		if ($receipts[$j]['status'] == STATUS_OPEN)
			$status = $_L['STS_open'];
		elseif ($receipts[$j]['status'] == STATUS_CLOSED)
			$status = $_L['STS_closed'];
		elseif ($receipts[$j]['status'] == STATUS_CANCEL)
			$status = $_L['STS_cancel'];
		elseif ($receipts[$j]['status'] == STATUS_VOID)
			$status = $_L['STS_void'];
		$objPHPExcel->getActiveSheet()->setCellValue('P' . $k, $status);
		$k++;
	}	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Receipt');
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	//filename
	$filename = "invoice_" . date('Ymd') . ".xls"; 
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				while (ob_get_length()) {
				  ob_end_clean();
				}
	$objWriter->save('php://output');
	exit;
}

?>

       <table height="500" class="listing-table">	        
	       <tbody>
	       	<tr valign="top">      	
	 		<?php print_rightMenu_home();?> 
	          <td>  
				<table width="100%" height="92%" class="tdbgcl">
				<tr valign="top">
				<td>
				<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
				<table width="100%" border="0" cellpadding="1" align="center">
					<tr height="465" valign="top">
					<td>
					<div>
					<table width="100%"> 
						<tr valign="top" height="420px">
						  <td width="85%">
							<table width="100%" border="0" cellpadding="1">
							  <tr><td align="center"></td></tr>
							  <tr><td><h2><a href="https://www.youtube.com/watch?v=2WO3WYvRxh8" target="reshelp"  title="Youtube help video"><img src='images/help.png' width="25" height="25" title="Youtube help video" /><?php echo $_L['EXP_title']; ?></a></h2></td></tr>
							  <tr ><td align="center"> </td></tr>
							  <tr>
								<td>
								 
							  <table width="100%" border="0" cellspacing="0" cellpadding="3">
							  	<tr>
							  		<td colspan="2"><b><?php echo $_L['EXP_daterange'];?></b></td>
							  	</tr>
							  	<tr>
							  		<td><?php echo $_L['EXP_from'] ?></td>
							  		<td>
							       		<img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].fromdate,'yyyy-mm-dd hh:ii',this, true, 1000)"/>							  		
							  			<input type="text" readonly="readonly" name="fromdate" id="fromdate" value="<?php if (isset($_POST['fromdate'])) echo $_POST['fromdate']; else echo $fromdate; ?>" size="14" style="color:#999999;" onchange="document.forms[0].submit()" />
							  		</td>
							  	</tr>
							  	<tr><td>&nbsp;</td></tr>
							  	<tr>
							  		<td><?php echo $_L['EXP_to'] ?></td>
							  		<td>
							       		<img src= "images/ew_calendar.gif" width="16" height= "16" border="0" onclick="setCalendarLanguage('<?php echo $lang; ?>');displayCalendar(document.forms[0].todate,'yyyy-mm-dd hh:ii',this, true, 1000)"/>							  		
							  			<input type="text" readonly="readonly" name="todate" id="todate" value="<?php if (isset($_POST['todate'])) echo $_POST['todate']; else echo $todate; ?>" size="14" style="color:#999999;" onchange="document.forms[0].submit()" />
							  		</td>
							  	</tr>
							  	<tr><td>&nbsp;</td></tr>

							  </table> 
								</td>
							  </tr>
							</table>
						  </td>
						</tr>
						</table>
						</div>
						<div>
						<table align="right">						
							<tr>
								
								<td colspan="2" align="right"><input class="button" name="Submit" id="Submit" type="submit" value="<?php echo $_L['EXP_download'];?>" /></td>
							</tr>						
						</table>
						</div>
						</td>
						</tr>
					  </table>
				</form>
				</td>
				</tr>
				</table>
	          </td>	               
	        </tr>
	      </tbody>
      </table>
<?php
/**
 * @}
 * @}
 */
 ?>   