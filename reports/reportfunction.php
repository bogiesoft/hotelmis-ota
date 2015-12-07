<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 */
error_reporting(E_ALL | E_STRICT);
include_once(dirname(__FILE__)."/../dailyfunc.php");
include_once(dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel.php");
include_once(dirname(__FILE__)."/../PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");


if ($_POST['Submit']){
	
		switch ($request) {
			
			case 'holidayRpt':
					if($_POST['year']){
						$holidays = getHoliday_by_year($_POST['year']);
						ob_end_clean();
						// Create new PHPExcel object
						$objPHPExcel = new PHPExcel();
				
						// Set document properties
						$objPHPExcel->getProperties()->setCreator("e-Novate Pte Ltd")
											 ->setLastModifiedBy("e-Novate Pte Ltd");
						
						$objPHPExcel->setActiveSheetIndex(0);
						$objPHPExcel->getActiveSheet()->setCellValue('A1', "Holiday");
						$objPHPExcel->getActiveSheet()->setCellValue('B1', "Date");
						
						$i = 2;
						for ($j = 0; $j < count($holidays); $j++) {
							$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $holidays[$j]['Description']);
							$objPHPExcel->getActiveSheet()->setCellValue('B' . $i, substr($holidays[$j]['days'],0,10));
							$i++;
						}
						
						// Rename worksheet
						$objPHPExcel->getActiveSheet()->setTitle('Holidays');
						
						//filename
						$filename = "report_holiday_" . date('Ymd') . ".xls"; 
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
						ob_end_clean();
						$objWriter->save('php://output');
						exit;
					}
				break;
			
			}
				
		}
	
?>
