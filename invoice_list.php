<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file invoice_list.php
 * @brief billings webpage called by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup INVOICE_SEARCH Invoice listing and search page
 * @{
 * This documentation is for code maintenance, not a user guide.
 * 
 */
error_reporting(E_ALL & ~E_NOTICE);
include_once(dirname(__FILE__)."/login_check.inc.php");
include_once(dirname(__FILE__)."/queryfunctions.php");
include_once(dirname(__FILE__)."/functions.php");
include_once(dirname(__FILE__)."/dailyfunc.php");
include_once(dirname(__FILE__)."/lang/lang_en.php");

if(is_ebridgeCustomer()){
	include_once(dirname(__FILE__)."/OTA/advancedFeatures/invoice_list.php");
	return;
}
/**
 * @}
 * @}
 */
?>