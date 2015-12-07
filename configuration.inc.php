<?php
/**
 * @package OTA Hotel Management
 * @file admin.php
 * @brief admin web page called by OTA Hotel Management
 * see readme.txt for credits and references
 *
 * @addtogroup CODE_MANAGEMENT
 * @{
 * @defgroup DATABASE_MANAGEMENT Database setup and management page
 * @{
 * This documentation is for code maintenance, not a user guide.
 */
/** MySQL hostname or IP address */
define("HOST", "127.0.0.1");
/** MySQL Database port number */
define("PORT", 3306);
/** MySQL database user name for database - default hotelmis */
define("USER", "hotelmis");
/** MySQL database password for user name - default hotelmis */
define("PASS", "hotelmis");
/** MySQL database name - default hotelmis */
define("DB", "hotelmis");
/** Print out debug information */
define("DEBUG", 0);
/** Default Tax percentage */
define("TAXPCT", 10);
/** Default service charge percentage */
define("SVCPCT", 10);
/** TimeZone Information for local hotel
 * @see http://www.php.net/manual/en/timezones.php
 */
define ("TIMEZONE", "Asia/Manila");
/** Validate IATA against the ebridge id. 1-Validate 0-No Validation */
define("IATAEBRIDGE", 0);
/**
 * Auto processing cuttoff time for next day charges 
 */
define("NEXTDAY_CUTOFF", "20:00:00");
/** 
 * Group the same rooms in the voice as a single line by room type and rate code
 * Individual room charges 0 or group charge 1 
 */
define("GROUP_BY_ROOMTYPERATE", 1);
/** Default room item id for auto pricing */
define('DEFAULT_ROOMCODE', 1);
/** Default Check In Time */
define("CHECKIN", "14:00:00");
/** Default Check In Time */
define("CHECKOUT", "12:00:00");
/**
 * @}
 * @}
 */
 ?>