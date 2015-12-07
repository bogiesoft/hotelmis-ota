<?php
/**
 * @package OTA Hotel Management
 * @copyright e-Novate Pte Ltd 2012-2015
 * @file hotel_defs.php
 * @brief defintions of constants used by OTA Hotel Management
 * see readme.txt for credits and references
 * @addtogroup RES_MANAGEMENT

 * @{
 * 
 */

define("DEFAULT_LANG", "en-us");
/**
 * @defgroup RES_DEFS Reservation definitions
 * @{
 */
/**
 * Reservation status - Quote 
 */
define("RES_QUOTE", 1);
/**
 * Reservation status - Active Invoice should be created 
 * Booking can be created from reservation in this state.
 */
define("RES_ACTIVE", 2);
/**
 * Reservation status - Cancelled Invoice should be created 
 */
define("RES_CANCEL", 3);
/**
 * Reservation status - Expired reservation has passed the checkin date for a quote.
 */
define("RES_EXPIRE", 4);
/**
 * Reservation status - Reservation has passed to Booking/registration
 */
define("RES_CHECKIN", 5);
/**
 * Reservation status - Void NO invoice should be created
 */
define("RES_VOID", 6);
/**
 * Reservation status - guest checked out but not billed
 */
define("RES_CHECKOUT", 7);
/**
 * Reservation status - CLOSE guest checked out and billed
 */
define("RES_CLOSE", 8);
/**
 * Reservation status - Cancel Requested - A cancellation has been requested by the customer.
 */
define("RES_CANCELREQUESTED", 9);
/**
 * @}
 */
/**
 * @defgroup BOOK_DEFS Booking definitions
 * @{
 */
/**
 * Booking status - Registration made at counter
 */
define("BOOK_REGISTERED", 1);
/**
 * Booking status - Reservation has passed to Booking/registration or Checked in directly at counter
 */
define("BOOK_CHECKEDIN", 2);
/**
 * Booking status - Guest checked out but not billed
 */
define("BOOK_CHECKEDOUT", 3);
/**
 * Booking status - Guest checked out and billed, but not paid
 */
define("BOOK_BILLED", 4);
/**
 * Booking status - Guest billed and paid
 */
define("BOOK_CLOSE", 5);
/**
 * @}
 */
/**
 * @defgroup STATUS_DEFS Booking and reserveraton status definitions
 * @{
 */
/**
 *  General status Open
 */
define("STATUS_OPEN", 1);
/**
 *  General status Closed
 */
define("STATUS_CLOSED", 2);
/**
 *  General status Cancel
 */
define("STATUS_CANCEL", 3);
/**
 *  General status Void
 */
define("STATUS_VOID", 4);
/**
 * @}
 */
/** 
 * @addtogroup INVOICE_MANAGEMENT
 * @{
 * @defgroup FOP_DEF_TAGS Form of payment definitions
 * @{
 */
/**
 * Form of payment cash  
 */
define("FOP_CASH", 1);
/**
 * Form of payment credit card  
 */
define("FOP_CC", 2);
/**
 * Form of payment teletex transfer
 */
define("FOP_TT", 3);
/**
 * Form of payment direct debit
 */
define("FOP_DB", 4);
/**
 * Form of payment paypal or online gateway
 */
define("FOP_PP", 5);
/**
 * Form of payment Cheque  
 */
define("FOP_CHEQUE", 6);
/**
 * Form of payment Coupon for discount  
 */
define("FOP_COUPON", 7);
/**
 * Form of payment Gift voucher  
 */
define("FOP_VOUCHER", 8);
/**
 * Form of payment Membership redemption or write back 
 */
define("FOP_REDEMPTION", 9);
/**
 * Form of payment Cash Deposit - used for cash payments in advance
 */
define("FOP_CASH_DEP", 10);
/**
 * Form of payment CC Deposit - used for CC payments in advance
 */
define("FOP_CC_DEP", 11);
/**
 * @}
 * @}
 */
/** 
 * @addtogroup ROOM_MANAGEMENT
 * @{
 * @defgroup ROOM_DEF_TAGS Room status definitions 
 * @{
 */
/** Room reserved, room is available but not shown for published in OTA queries as available,
	check-ins can be made to this room for walk-in customers to override this state */
define("RESERVED","R");
/** Room is vacant and available, room rates are published in OTA queries */
define("VACANT", "V");
/** Room is currently occupied,  */
define("BOOKED", "B");
/** Room is unavailable for occupation for due to cleaning or maintenance so is not shown in
	availability or can be checked into */
define("LOCKED", "L");

/** Hotel Invoice Item type - Room */
define("ROOM", 1);
/** Hotel Invoice Item type - Food */
define("FOOD", 2);
/** Hotel Invoice Item type - Beverage */
define("BEVERAGE", 3);
/** Hotel Invoice Item type - Transfer */
define("TRANSPORT", 4);
/** Hotel Invoice Item type - Fee */
define("FEE", 5);
/** Hotel Invoice Item type - Phone */
define("HOTEL_PHONE", 6);
/** Hotel Invoice Item type - Service */
define("SERVICE", 7);
/** Hotel Invoice Item type - Tax */
define("TAX", 8);
/** Hotel Invoice Item type - Hotel */
define("HOTEL", 9);
/** Hotel Invoice Item type - Tour */
define("TOUR", 10);
/** Hotel Invoice Item type - Golf */
define("GOLF", 11);

/**
 * @}
 * @}
 * @addtogroup RATE_MANAGEMENT
 * @{
 * @defgroup RATE_DEF_TAGS Rate status/configuration definitions
 * @{
 */
/** Sunday relates to $_L['RTS_sunday'] */
define("HOTEL_SUN", 0x00000001);
/** Monday relates to $_L['RTS_monday'] */
define("HOTEL_MON", 0x00000002);
/** Tuesday relates to $_L['RTS_tuesday'] */
define("HOTEL_TUE", 0x00000004);
/** Wednesday relates to $_L['RTS_wednesday'] */
define("HOTEL_WED", 0x00000008);
/** Thursday relates to $_L['RTS_thursday'] */
define("HOTEL_THU", 0x00000010);
/** Friday relates to $_L['RTS_friday'] */
define("HOTEL_FRI", 0x00000020);
/** Saturday relates to $_L['RTS_saturday'] */
define("HOTEL_SAT", 0x00000040);
/** Weekend */
define("HOTEL_WEND",0x00000041);
/** All week */
define("HOTEL_WEEK",0x0000007F);
/** Holiday */
define("HOTEL_HOLS", 0x00000080);


/** January relates to $_L['RTS_january'] */
define("HOTEL_JAN", 0x00010000);
/** February relates to $_L['RTS_february']*/
define("HOTEL_FEB", 0x00020000);
/** March relates to $_L['RTS_march'] */
define("HOTEL_MAR", 0x00040000);
/** April relates to $_L['RTS_april'] */
define("HOTEL_APR", 0x00080000);
/** May relates to $_L['RTS_may'] */
define("HOTEL_MAY", 0x00100000);
/** June relates to $_L['RTS_june'] */
define("HOTEL_JUN", 0x00200000);
/** July relates to $_L['RTS_july'] */
define("HOTEL_JUL", 0x00400000);
/** August relates to $_L['RTS_august'] */
define("HOTEL_AUG", 0x00800000);
/** September relates to $_L['RTS_september'] */
define("HOTEL_SEP", 0x01000000);
/** October relates to $_L['RTS_october] */
define("HOTEL_OCT", 0x02000000);
/** November relates to $_L['RTS_november'] */
define("HOTEL_NOV", 0x04000000);
/** December relates to $_L['RTS_december'] */
define("HOTEL_DEC", 0x08000000);
/** All Year */
define ("HOTEL_YEAR", 0x0FFF0000);

/** Room rates type - default room rate relates to $_L['RTS_default']*/
define("DEFAULTRATE", 1);
/** Room rates type - promo rate relates to $_L['RTS_promo']*/
define("PROMORATE", 2);
/** Room rates type - customer rate relates to $_L['RTS_customer']*/
define("CUSTOMERRATE", 3);
/** Room rates type -  agent rate relates to $_L['RTS_agent']*/
define("AGENTRATE", 4);
/** Room rates type -  Promo rate room ids*/
define("ROOMRATE", 5);
/** Room rates type -  Promo room roomtype ids*/
define("ROOMTYPERATE", 6);
/** Room rates type, Default fee for policy */
define("DEFAULTFEE", 7);

/** All booking sources relates to $_L['RTS_allsrc'] */
define("ALLSRC", 0x000F);
/** Direct booking relates to $_L['RTS_direct'] */
define("DIRECT", 0x0001);
/** Agency booking relates to $_L['RTS_agent']*/
define("AGENT", 0x0002);
/** Web site booking relates to $_L['RTS_web']*/
define("WEB", 0x0004);
/** OTA booking relates to $_L['RTS_ota']*/
define("OTA", 0x0008);
/** Direct and Agent booking relates to $_L['RTS_directagent'] */
define("DIRECTAGENT", 0x0003);
/** Web and OTA booking relates to $_L['RTS_webota'] */
define("WEBOTA", 0x000C);

/** Occupancy type Single relates to $_L['RTS_osingle'] */
define("OSINGLE", "S");
/** Occupancy type Double relates to $_L['RTS_odouble'] */
define("ODOUBLE", "D");
/** Occupancy type Family relates to $_L['RTS_ofamily'] */
define("OFAMILY", "F");

/** Discount type - Standard Rate - used for DEFAULT rate types */
define("STANDARD", 1);
/** Discount type - Fixed price */
define("FIXED", 2);
/** Discount type - Percentage discount */
define("PERCENT", 3);
/** Discount type - Free of charge/included in package */
define("FOC", 4);
/**
 * @}
 * @}
 */
/** 
 * @defgroup RATE_CUSTOM_TAGS Custom tags
 * @{
 */
/** Pages all */
define("HTL_PAGE_ALL", 0);
/** Reservation page */
define("HTL_PAGE_RES", 1);
/** Booking/Registration page */
define("HTL_PAGE_BOOK", 2);
/** Rooms page */
define("HTL_PAGE_ROOM", 3);
/** Profile page */
define("HTL_PAGE_PROFILE", 4);
/** Invoice Page */
define("HTL_PAGE_BILL", 5);
/** Rate Page */
define("HTL_PAGE_RATE", 6);
/** Room type Page */
define("HTL_ROOM_TYPE", 7);
/** Custom type NUMBER */
define("CUST_TYPE_NUMBER", 0);
/** Custom type DATE */
define("CUST_TYPE_DATE", 1);
/** Custom type TIME */
define("CUST_TYPE_TIME", 2);
/** Custom type DATETIME */
define("CUST_TYPE_DATETIME", 3);
/** Custom type TEXT 100 chars max */
define("CUST_TYPE_TEXT100", 4);
/** Custom type TEXT 500 500 chars max */
define("CUST_TYPE_TEXT500", 5);
/** Custom type BOOLEAN True/False*/
define("CUST_TYPE_BOOLEAN", 6);

/**
 * @}
 */
/**
 * @}
 */

?>
