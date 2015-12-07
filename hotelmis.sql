/*
SQLyog Community v9.51 
MySQL - 5.5.24-0ubuntu0.12.04.1 : Database - hotelmis
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`hotelmis` /*!40100 DEFAULT CHARACTER SET latin1 */;
CREATE USER 'hotelmis'@'localhost' IDENTIFIED BY 'hotelmis';
GRANT ALL PRIVILEGES ON hotelmis.* TO 'hotelmis'@'localhost';

USE `hotelmis`;

/*Table structure for table `activity` */

DROP TABLE IF EXISTS `activity`;

CREATE TABLE `activity` (
  `activityid` INT(50) NOT NULL AUTO_INCREMENT,
  `activity` VARCHAR(100) NOT NULL,
  `activitydesc` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`activityid`)
)  ;

/*Table structure for table `agent` */

DROP TABLE IF EXISTS `agent`;

CREATE TABLE `agent` (
  `agentID` INT(10) NOT NULL AUTO_INCREMENT,
  `agentName` VARCHAR(100) NOT NULL,
  `agentAcntNo` VARCHAR(10) NOT NULL,
  `contactPerson` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(50) DEFAULT NULL,
  `fax` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `billingAddress` VARCHAR(250) DEFAULT NULL,
  `town` VARCHAR(100) DEFAULT NULL,
  `postalCode` INT(10) DEFAULT NULL,
  `roadStreet` VARCHAR(250) DEFAULT NULL,
  `building` VARCHAR(100) DEFAULT NULL,
  `eBridge` VARCHAR(150) DEFAULT NULL,
  `IM` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(150) DEFAULT NULL,
  PRIMARY KEY (`agentID`),
  UNIQUE KEY `agentID_UNIQUE` (`agentID`)
) ;

/*Table structure for table `agents` */

DROP TABLE IF EXISTS `agents`;

CREATE TABLE `agents` (
  `agentid` INT(11) NOT NULL AUTO_INCREMENT,
  `agentname` VARCHAR(100) NOT NULL,
  `agents_ac_no` VARCHAR(10) NOT NULL,
  `contact_person` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(100) DEFAULT NULL,
  `fax` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(200) DEFAULT NULL,
  `billing_address` VARCHAR(250) DEFAULT NULL,
  `town` VARCHAR(100) DEFAULT NULL,
  `postal_code` INT(10) DEFAULT NULL,
  `road_street` VARCHAR(250) DEFAULT NULL,
  `building` VARCHAR(100) DEFAULT NULL,
  `eBridgeID` VARCHAR(250) DEFAULT NULL,
  `IM` VARCHAR(250) DEFAULT NULL,
  `country` VARCHAR(250) DEFAULT NULL,
  PRIMARY KEY (`agentid`),
  UNIQUE KEY `agentcode` (`agents_ac_no`)
)   COMMENT='InnoDB free: 120832 kB; InnoDB free: 120832 kB; InnoDB free:';

/*Table structure for table `amenities` */

DROP TABLE IF EXISTS `amenities`;

CREATE TABLE `amenities` (
  `amenityID` INT(10) NOT NULL AUTO_INCREMENT,
  `amenityCode` VARCHAR(20) NOT NULL,
  `amenityName` VARCHAR(45) NOT NULL,
  `amenityDesc` VARCHAR(150) DEFAULT NULL,
  `amenityRate` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`amenityID`),
  UNIQUE KEY `amenityID_UNIQUE` (`amenityID`)
) ;

/*Table structure for table `bills` */

DROP TABLE IF EXISTS `bills`;

CREATE TABLE `bills` (
  `bill_id` INT(11) NOT NULL AUTO_INCREMENT,
  `book_id` INT(11) NOT NULL COMMENT 'booking/reservation id',
  `date_billed` DATETIME NOT NULL,
  `billno` VARCHAR(15) NOT NULL DEFAULT '0',
  `status` TINYINT(4) DEFAULT NULL,
  `date_checked` DATE DEFAULT NULL,
  `reservation_id` INT(11) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `guestid` INT(11) DEFAULT NULL,
  `notes` TEXT NULL,
  `flags` INT DEFAULT 0 NULL,
  PRIMARY KEY (`bill_id`)
)   COMMENT='InnoDB free: 120832 kB; InnoDB free: 120832 kB; InnoDB free:';

/*Table structure for table `booking` */

DROP TABLE IF EXISTS `booking`;

CREATE TABLE `booking` (
  `book_id` INT(11) NOT NULL AUTO_INCREMENT,
  `guestid` INT(11) NOT NULL,
  `reservation_id` INT(11) NOT NULL COMMENT 'direct/agent',
  `bill_id` INT(11) NOT NULL COMMENT 'bo/bb/hb/fb',
  `no_adults` TINYINT(2) DEFAULT NULL,
  `no_child6_12` TINYINT(2) DEFAULT NULL,
  `no_child1_5` TINYINT(2) DEFAULT NULL,
  `no_babies` TINYINT(2) DEFAULT NULL,
  `checkindate` DATETIME NOT NULL,
  `checkoutdate` DATETIME NOT NULL,
  `roomid` INT(11) DEFAULT NULL COMMENT 'country_code',
  `roomtypeid` INT(11) DEFAULT NULL,
  `rates_id` INT(11) DEFAULT NULL,
  `instructions` TEXT,
  `voucher_no` VARCHAR(15) DEFAULT NULL,
  `checkedin_by` INT(5) DEFAULT NULL,
  `checkedin_date` DATETIME DEFAULT NULL,
  `checkedout_by` INT(11) DEFAULT NULL,
  `checkedout_date` DATETIME DEFAULT NULL,
  `cctype` VARCHAR(4) DEFAULT NULL,
  `CCnum` VARCHAR(19) DEFAULT NULL,
  `expiry` VARCHAR(4) DEFAULT NULL,
  `CVV` VARCHAR(5) DEFAULT NULL,
  `book_status` INT(11) DEFAULT NULL,
  `res_det_id` INT(255) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `id` (`book_id`)
)   COMMENT='InnoDB free: 121856 kB; InnoDB free: 115712 kB; InnoDB free:';

/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `countryid` SMALLINT(6) NOT NULL AUTO_INCREMENT,
  `country` VARCHAR(150) NOT NULL,
  `countrycode` CHAR(10) NOT NULL,
  `subscriber` CHAR(19) DEFAULT NULL,
  `nationality` VARCHAR(150) DEFAULT NULL,
  `currency` VARCHAR(45) DEFAULT NULL,
  PRIMARY KEY (`countryid`),
  UNIQUE KEY `countrycode` (`countrycode`),
  KEY `country` (`country`)
)   COMMENT='InnoDB free: 121856 kB; InnoDB free: 121856 kB; InnoDB free:';

/*Table structure for table `details` */

DROP TABLE IF EXISTS `details`;

CREATE TABLE `details` (
  `itemid` INT(11) NOT NULL AUTO_INCREMENT,
  `item` VARCHAR(35) NOT NULL,
  `description` VARCHAR(150) DEFAULT NULL,
  `sale` TINYINT(1) DEFAULT NULL,
  `expense` TINYINT(1) DEFAULT NULL,
  `itype` INT(11) DEFAULT NULL,
  PRIMARY KEY (`itemid`)
)   COMMENT='InnoDB free: 120832 kB; InnoDB free: 120832 kB; InnoDB free:';

/*Table structure for table `doctypes` */

DROP TABLE IF EXISTS `doctypes`;

CREATE TABLE `doctypes` (
  `doc_id` INT(11) NOT NULL AUTO_INCREMENT,
  `doc_code` VARCHAR(5) NOT NULL,
  `doc_type` VARCHAR(25) NOT NULL,
  `remarks` LONGTEXT,
  `accounts` TINYINT(4) DEFAULT NULL,
  `cooperative` TINYINT(4) DEFAULT NULL,
  `payroll` TINYINT(4) DEFAULT NULL,
  PRIMARY KEY (`doc_id`)
)   COMMENT='InnoDB free: 120832 kB; InnoDB free: 120832 kB; InnoDB free:';

/*Table structure for table `documents` */

DROP TABLE IF EXISTS `documents`;

CREATE TABLE `documents` (
  `propertyid` INT(11) NOT NULL,
  `receiptno` VARCHAR(15) DEFAULT NULL,
  `invoiceno` VARCHAR(15) DEFAULT NULL,
  `voucherno` VARCHAR(15) DEFAULT NULL
) ;

/*Table structure for table `guestbook` */

DROP TABLE IF EXISTS `guestbook`;

CREATE TABLE `guestbook` (
  `gb_index` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `date` DATETIME DEFAULT NULL,
  `message` MEDIUMTEXT,
  `reply` MEDIUMTEXT,
  PRIMARY KEY (`gb_index`)
) ENGINE=INNODB ;

/*Table structure for table `guests` */

DROP TABLE IF EXISTS `guests`;

CREATE TABLE `guests` (
  `guestid` INT(11) NOT NULL AUTO_INCREMENT,
  `lastname` VARCHAR(64) NOT NULL,
  `firstname` VARCHAR(64) DEFAULT NULL,
  `middlename` VARCHAR(64) DEFAULT NULL,
  `salutation` INT(11) DEFAULT NULL,
  `pp_no` VARCHAR(32) DEFAULT NULL,
  `idno` INT(11) UNSIGNED DEFAULT NULL,
  `countrycode` CHAR(2) NOT NULL,
  `street_no` VARCHAR(10) DEFAULT NULL,
  `street_name` VARCHAR(250) DEFAULT NULL,
  `town` VARCHAR(64) DEFAULT NULL,
  `postal_code` VARCHAR(16) DEFAULT NULL,
  `access` VARCHAR(45) DEFAULT NULL,
  `areacode` VARCHAR(45) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(128) DEFAULT NULL,
  `mobilephone` VARCHAR(45) DEFAULT NULL,
  `eBridgeID` VARCHAR(250) DEFAULT NULL,
  `IM` VARCHAR(250) DEFAULT NULL,
  `nationality` CHAR(2) DEFAULT NULL,
  `useAdvProfile` TINYINT(1) DEFAULT '0',
  PRIMARY KEY (`guestid`),
  UNIQUE KEY `id` (`guestid`)
)   COMMENT='InnoDB free: 121856 kB; InnoDB free: 115712 kB; InnoDB free:';

/*Table structure for table `hoteldetails` */

DROP TABLE IF EXISTS `hoteldetails`;

CREATE TABLE `hoteldetails` (
  `hotelname` CHAR(1) DEFAULT NULL,
  `property_code` INT(11) DEFAULT NULL,
  `property_name` CHAR(1) DEFAULT NULL,
  `postal_zipcode` INT(11) DEFAULT NULL,
  `state_code` CHAR(11) DEFAULT NULL,
  `book_id` INT(11) DEFAULT NULL,
  `reservation_id` INT(11) DEFAULT NULL,
  `userid` SMALLINT(6) DEFAULT NULL,
  `groupcode` INT(11) DEFAULT NULL
) ;

/*Table structure for table `hotelgallery` */

DROP TABLE IF EXISTS `hotelgallery`;

CREATE TABLE `hotelgallery` (
  `PicID` INT(11) NOT NULL AUTO_INCREMENT,
  `Title` CHAR(100) DEFAULT NULL,
  `Description` CHAR(100) DEFAULT NULL,
  `URL` CHAR(100) DEFAULT NULL,
  `page` INT NOT NULL DEFAULT '0' COMMENT '0 gallery 1 promo',
  `imgtype` INT NOT NULL DEFAULT '0' COMMENT '0 img 1 video',
  PRIMARY KEY (`PicID`)
)  ;

/*Table structure for table `hotelsetup` */

DROP TABLE IF EXISTS `hotelsetup`;

CREATE TABLE `hotelsetup` (
  `HotelName` VARCHAR(250) NOT NULL,
  `AltHotelName` VARCHAR(250) NOT NULL,
  `CompanyName` VARCHAR(250) NOT NULL,
  `Street` VARCHAR(250) NOT NULL,
  `State` VARCHAR(250) NOT NULL,
  `CityCode` VARCHAR(4) NOT NULL,
  `City` VARCHAR(250) NOT NULL,
  `Country` VARCHAR(100) NOT NULL,
  `CountryCode` VARCHAR(3) NOT NULL,
  `PostCode` VARCHAR(50) NOT NULL,
  `Telephone` VARCHAR(100) NOT NULL,
  `Fax` VARCHAR(100) NOT NULL,
  `Email` VARCHAR(200) NOT NULL,
  `Web` VARCHAR(250) NOT NULL,
  `Registration` VARCHAR(100) NOT NULL,
  `TaxID1` VARCHAR(100) NOT NULL,
  `TaxID2` VARCHAR(100) NOT NULL,
  `OTA_URL` VARCHAR(200) NOT NULL,
  `lang` VARCHAR(5) NOT NULL,
  `LogoFileURL` VARCHAR(500) NOT NULL,
  `ChainCode` VARCHAR(200) NOT NULL,
  `Latitude` VARCHAR(15) NOT NULL,
  `Longitude` VARCHAR(15) NOT NULL,
  `eBridgeID` VARCHAR(250) NOT NULL,
  `IM` VARCHAR(250) NOT NULL,
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
)  ;

/*Table structure for table `inventory_group` */

DROP TABLE IF EXISTS `inventory_group`;

CREATE TABLE `inventory_group` (
  `groupcode` INT(11) NOT NULL,
  `groupname` VARCHAR(40) DEFAULT NULL,
  `userid` SMALLINT(6) DEFAULT NULL,
  `inventory_count` INT(11) DEFAULT NULL,
  PRIMARY KEY (`groupcode`)
) ;

/*Table structure for table `languages` */

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `lang` VARCHAR(10) NOT NULL,
  `Description` VARCHAR(100) NOT NULL,
  `LocalDescription` VARCHAR(100) NOT NULL,
  `active` INT(1) NOT NULL
) ;

/*Table structure for table `ota_bedtype` */

DROP TABLE IF EXISTS `ota_bedtype`;

CREATE TABLE `ota_bedtype` (
  `OTA_BedID` INT(11) NOT NULL AUTO_INCREMENT,
  `OTA_Number` INT(11) NOT NULL,
  `lang` CHAR(6) CHARACTER SET ASCII NOT NULL,
  `Description` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`OTA_BedID`)
)  ;

/*Table structure for table `ota_roomamenity` */

DROP TABLE IF EXISTS `ota_roomamenity`;

CREATE TABLE `ota_roomamenity` (
  `OTA_RoomAmenityID` INT(11) NOT NULL AUTO_INCREMENT,
  `OTA_Number` INT(11) NOT NULL,
  `lang` CHAR(6) NOT NULL,
  `Description` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`OTA_RoomAmenityID`)
)  ;

/*Table structure for table `paygateway_list` */

DROP TABLE IF EXISTS `paygateway_list`;

CREATE TABLE `paygateway_list` (
  `pgid` INT(11) NOT NULL AUTO_INCREMENT,
  `payment_gateway` VARCHAR(50) NOT NULL,
  `paymentform` VARCHAR(50) NOT NULL COMMENT '@see FOP Form of payments',
  PRIMARY KEY (`pgid`)
)  ;

/*Table structure for table `payment_gateways` */

DROP TABLE IF EXISTS `payment_gateways`;

CREATE TABLE `payment_gateways` (
  `gateid` INT(11) NOT NULL AUTO_INCREMENT,
  `paymentgateway` VARCHAR(50) DEFAULT NULL,
  `accname` VARCHAR(50) NOT NULL,
  `accid` VARCHAR(50) DEFAULT NULL,
  `swiftcode` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`gateid`)
)  ;

/*Table structure for table `payment_mode` */

DROP TABLE IF EXISTS `payment_mode`;

CREATE TABLE `payment_mode` (
  `paymentid` INT(11) NOT NULL AUTO_INCREMENT,
  `payment_option` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`paymentid`)
)   COMMENT='InnoDB free: 120832 kB';

/*Table structure for table `php_session` */

DROP TABLE IF EXISTS `php_session`;

CREATE TABLE `php_session` (
  `session_id` VARCHAR(32) NOT NULL DEFAULT '',
  `user_id` VARCHAR(16) DEFAULT NULL,
  `date_created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_data` LONGTEXT,
  PRIMARY KEY (`session_id`),
  KEY `last_updated` (`last_updated`)
) ;

/*Table structure for table `policy` */

DROP TABLE IF EXISTS `policy`;

CREATE TABLE `policy` (
  `idpolicy` INT(11) NOT NULL AUTO_INCREMENT,
  `ID` VARCHAR(45) DEFAULT NULL,
  `rateid` VARCHAR(45) DEFAULT NULL,
  `title` VARCHAR(45) DEFAULT NULL,
  `language` VARCHAR(15) DEFAULT NULL,
  `description` BLOB,
  PRIMARY KEY (`idpolicy`)
)  ;

/*Table structure for table `rateitems` */

DROP TABLE IF EXISTS `rateitems`;

CREATE TABLE `rateitems` (
  `rateitemid` INT(11) NOT NULL AUTO_INCREMENT,
  `ratesid` INT(11) DEFAULT NULL,
  `itemid` INT(11) DEFAULT NULL,
  `discounttype` INT(11) DEFAULT NULL,
  `validperiod` INT(11) DEFAULT NULL,
  `service` INT(11) DEFAULT NULL,
  `tax` INT(11) DEFAULT NULL,
  `discountvalue` FLOAT DEFAULT NULL,
  `maxcount` INT(11) DEFAULT NULL,
  PRIMARY KEY (`rateitemid`)
)  ;

/*Table structure for table `rateroomtypes` */

DROP TABLE IF EXISTS `rateroomtypes`;

CREATE TABLE `rateroomtypes` (
  `ratesid` INT(11) DEFAULT NULL,
  `typeid` INT(11) DEFAULT NULL,
  `typeitemid` INT(11) DEFAULT NULL
) ;

/*Table structure for table `rates` */

DROP TABLE IF EXISTS `rates`;

CREATE TABLE `rates` (
  `ratesid` INT(11) NOT NULL AUTO_INCREMENT,
  `ratecode` VARCHAR(20) DEFAULT NULL,
  `description` VARCHAR(100) DEFAULT NULL,
  `bookingtype` INT(1) NOT NULL COMMENT 'Direct/Agent/Web/OTA',
  `occupancy` CHAR(1) NOT NULL COMMENT 'single/double/family',
  `rate_type` INT(1) NOT NULL COMMENT 'resident/non-resident',
  `currency` VARCHAR(45) NOT NULL,
  `date_started` DATE NOT NULL,
  `date_stopped` DATE NOT NULL,
  `max_stay` INT(11) DEFAULT NULL,
  `min_stay` INT(11) DEFAULT NULL,
  `max_people` INT(11) DEFAULT NULL,
  `min_people` INT(11) DEFAULT NULL,
  `min_advanced_booking` INT(1) DEFAULT NULL,
  PRIMARY KEY (`ratesid`)
)   COMMENT='InnoDB free: 120832 kB; InnoDB free: 120832 kB; InnoDB free:';


/*Table structure for table `receipts` */

DROP TABLE IF EXISTS `receipts`;

CREATE TABLE `receipts` (
  `receipt_id` INT(11) NOT NULL AUTO_INCREMENT,
  `bill_id` INT(11) DEFAULT NULL,
  `book_id` INT(11) DEFAULT NULL,
  `reservation_id` INT(11) DEFAULT NULL,
  `rcpt_no` VARCHAR(15) DEFAULT NULL,
  `rcpt_date` DATETIME DEFAULT NULL,
  `fop` TINYINT(4) DEFAULT NULL,
  `cctype` VARCHAR(4) DEFAULT NULL,
  `CCnum` VARCHAR(25) DEFAULT NULL,
  `expiry` VARCHAR(10) DEFAULT NULL COMMENT 'cheque or CC number',
  `cvv` VARCHAR(5) DEFAULT NULL,
  `auth` VARCHAR(200) DEFAULT NULL,
  `name` VARCHAR(64) DEFAULT NULL COMMENT 'name on card/cheque',
  `amount` DECIMAL(10,2) DEFAULT NULL COMMENT 'This is the payed amount recorded in source currency, to get the actual amount paid multiply by the exchange rate and use the target currency',
  `status` TINYINT(4) DEFAULT NULL,
  `add_by` INT(11) DEFAULT NULL,
  `add_date` DATETIME DEFAULT NULL,
  `exrate` DECIMAL(10,6) DEFAULT NULL COMMENT 'exchange rate for charged currency from base currency',
  `srcCurrency` VARCHAR(5) DEFAULT NULL,
  `tgtCurrency` VARCHAR(5) DEFAULT NULL,
  PRIMARY KEY (`receipt_id`)
)  ;


/*Table structure for table `reservation` */

DROP TABLE IF EXISTS `reservation`;

CREATE TABLE `reservation` (
  `reservation_id` INT(11) NOT NULL AUTO_INCREMENT,
  `src` VARCHAR(15) NOT NULL,
  `guestid` INT(40) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `reservation_by` VARCHAR(300) DEFAULT NULL,
  `reservation_by_phone` VARCHAR(50) DEFAULT NULL,
  `checkindate` DATETIME NOT NULL,
  `checkoutdate` DATETIME DEFAULT NULL,
  `no_adults` TINYINT(2) DEFAULT NULL,
  `no_child1_5` TINYINT(2) DEFAULT NULL,
  `no_child6_12` TINYINT(2) DEFAULT NULL,
  `no_babies` TINYINT(2) DEFAULT NULL,
  `roomid` INT(11) DEFAULT NULL,
  `roomtypeid` INT(11) DEFAULT NULL,
  `ratesid` INT(11) DEFAULT NULL,
  `instructions` TEXT,
  `agentid` INT(11) DEFAULT NULL,
  `voucher_no` VARCHAR(15) DEFAULT NULL,
  `reserved_by` INT(11) NOT NULL,
  `reserved_date` DATETIME DEFAULT NULL,
  `confirmed_by` INT(11) DEFAULT NULL,
  `confirmed_date` DATETIME DEFAULT NULL,
  `reserve_time` DATETIME DEFAULT NULL,
  `cctype` VARCHAR(4) DEFAULT NULL,
  `CCnum` VARCHAR(19) DEFAULT NULL,
  `expiry` VARCHAR(4) DEFAULT NULL,
  `CVV` VARCHAR(5) DEFAULT NULL,
  `book_id` INT(11) DEFAULT NULL,
  `status` INT(11) DEFAULT NULL,
  `bill_id` INT(11) DEFAULT NULL,
  `fop` INT(11) DEFAULT NULL,
  `amt` DECIMAL(10,2) DEFAULT '0',
  `booked_by_ebridgeid` VARCHAR(100) DEFAULT NULL COMMENT 'The ebridge id from which the booking informaation is received',
  `cancelled_by_ebridgeid` VARCHAR(100) DEFAULT NULL COMMENT 'The ebridge id from which the cancellation request is received',
  `cancelled_date` DATETIME DEFAULT NULL COMMENT 'The cancelled date and time',
  PRIMARY KEY (`reservation_id`),
  UNIQUE KEY `id` (`reservation_id`)
)   COMMENT='InnoDB free: 121856 kB; InnoDB free: 115712 kB; InnoDB free:';

/*Table structure for table `room_amenities` */

DROP TABLE IF EXISTS `room_amenities`;

CREATE TABLE `room_amenities` (
  `room_id` INT(11) DEFAULT NULL,
  `OTA_number` INT(11) DEFAULT NULL
) ;

/*Table structure for table `rooms` */

DROP TABLE IF EXISTS `rooms`;

CREATE TABLE `rooms` (
  `roomid` INT(11) NOT NULL AUTO_INCREMENT,
  `roomno` MEDIUMINT(3) NOT NULL DEFAULT '0',
  `roomtypeid` INT(11) DEFAULT NULL,
  `roomname` VARCHAR(35) DEFAULT NULL,
  `noofrooms` TINYINT(3) DEFAULT NULL,
  `occupancy` TINYINT(2) DEFAULT NULL,
  `bedtype1` INT(11) DEFAULT NULL,
  `bedtype2` INT(11) DEFAULT NULL,
  `bedtype3` INT(11) DEFAULT NULL,
  `bedtype4` INT(11) DEFAULT NULL,
  `bedcount` INT(11) DEFAULT NULL,
  `status` CHAR(1) DEFAULT NULL COMMENT '(V)acant/(R)eserverd/(B)ooked/(L)ocked',
  `photo` LONGBLOB,
  `filetype` VARCHAR(50) DEFAULT NULL,
  `rateid` INT(11) DEFAULT NULL,
  PRIMARY KEY (`roomid`,`roomno`)
)   COMMENT='InnoDB free: 119808 kB; InnoDB free: 119808 kB; InnoDB free:';

/*Table structure for table `roomtype` */

DROP TABLE IF EXISTS `roomtype`;

CREATE TABLE `roomtype` (
  `roomtypeid` INT(11) NOT NULL AUTO_INCREMENT,
  `roomtype` VARCHAR(15) NOT NULL,
  `description` VARCHAR(100) DEFAULT NULL,
  `rateid` INT(11) DEFAULT NULL,
  `roomurl` VARCHAR(150) DEFAULT NULL COMMENT 'URL of the room image',
  PRIMARY KEY (`roomtypeid`)
)  ;

/*Table structure for table `salutation` */

DROP TABLE IF EXISTS `salutation`;

CREATE TABLE `salutation` (
  `saluteid` INT(11) NOT NULL AUTO_INCREMENT,
  `salute` INT(11) DEFAULT NULL,
  `lang` VARCHAR(6) DEFAULT NULL,
  `Description` VARCHAR(10) DEFAULT NULL,
  PRIMARY KEY (`saluteid`)
)  ;

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id_session` VARCHAR(32) NOT NULL DEFAULT '',
  `moment` BIGINT(20) NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `data` TEXT NOT NULL
) ;

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `transno` INT(11) NOT NULL AUTO_INCREMENT,
  `bill_id` INT(11) NOT NULL,
  `item_id` INT(11) NOT NULL COMMENT 'Receipt/Invoice/Chit/Bill',
  `details` VARCHAR(65) NOT NULL,
  `std_amount` DECIMAL(10,2) DEFAULT NULL,
  `std_svc` DECIMAL(10,2) DEFAULT NULL,
  `std_tax` DECIMAL(10,2) DEFAULT NULL,
  `trans_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` DECIMAL(10,2) DEFAULT NULL,
  `svc` DECIMAL(10,2) DEFAULT NULL,
  `tax` DECIMAL(10,2) DEFAULT NULL,
  `quantity` INT(11) DEFAULT NULL,
  `grossamount` DECIMAL(10,2) DEFAULT NULL,
  `ratesid` INT(11) DEFAULT NULL,
  `add_by` INT(11) DEFAULT NULL,
  `add_date` DATETIME DEFAULT NULL,
  `status` TINYINT(4) DEFAULT '0',
  `currency` VARCHAR(5) DEFAULT NULL,
  `XOID` INT(11) DEFAULT NULL,
  PRIMARY KEY (`transno`)
)   COMMENT='Bill Postings; InnoDB free: 120832 kB; InnoDB free: 120832 k';

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userid` SMALLINT(6) NOT NULL AUTO_INCREMENT,
  `fname` VARCHAR(25) NOT NULL,
  `sname` VARCHAR(25) NOT NULL,
  `loginname` VARCHAR(15) NOT NULL,
  `pass` VARCHAR(32) NOT NULL,
  `phone` INT(25) DEFAULT NULL,
  `mobile` INT(11) DEFAULT NULL,
  `fax` INT(11) DEFAULT NULL,
  `email` VARCHAR(65) DEFAULT NULL,
  `dateregistered` DATE DEFAULT NULL,
  `countrycode` SMALLINT(6) DEFAULT NULL,
  `admin` TINYINT(1) NOT NULL DEFAULT '0',
  `guest` TINYINT(1) NOT NULL DEFAULT '0',
  `reservation` TINYINT(1) NOT NULL DEFAULT '0',
  `booking` TINYINT(1) NOT NULL DEFAULT '0',
  `agents` TINYINT(1) NOT NULL DEFAULT '0',
  `rooms` TINYINT(1) NOT NULL DEFAULT '0',
  `billing` TINYINT(1) NOT NULL DEFAULT '0',
  `rates` TINYINT(1) NOT NULL DEFAULT '0',
  `lookup` TINYINT(1) NOT NULL DEFAULT '0',
  `reports` TINYINT(1) NOT NULL DEFAULT '0',
  `policy` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `loginname` (`loginname`),
  KEY `names` (`fname`,`sname`)
)   COMMENT='InnoDB free: 121856 kB; InnoDB free: 121856 kB; InnoDB free:';

/*Table structure for table `users` */

DROP TABLE IF EXISTS `user_keys`;

CREATE TABLE `user_keys` ( 
	`userid` SMALLINT(6), 
	`pubkey` VARCHAR(8096), 
	`privkey` VARCHAR(8096), 
	`secret` VARCHAR(8096) 
);

/*Table structure for table `users_online` */

DROP TABLE IF EXISTS `users_online`;

CREATE TABLE `users_online` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `timestamp` INT(15) NOT NULL DEFAULT '0',
  `ip` VARCHAR(40) NOT NULL DEFAULT '',
  `file` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `file` (`file`),
  KEY `timestamp` (`timestamp`)
)  ;

/*Table structure for table `advaddresses` */

DROP TABLE IF EXISTS `advaddresses`;

CREATE TABLE `advaddresses` (
  `addressid` INT(225) NOT NULL AUTO_INCREMENT COMMENT 'Database index for address',
  `parentid` INT(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` INT(225) NOT NULL COMMENT 'Database profile id',
  `addresstype` INT(11) DEFAULT NULL COMMENT 'Address types see OTA AUT types',
  `clt` INT(11) DEFAULT NULL COMMENT 'Communication location type',
  `blk` VARCHAR(10) DEFAULT NULL COMMENT 'Blk/House number',
  `building` VARCHAR(64) DEFAULT NULL COMMENT 'Building name',
  `floor` VARCHAR(30) DEFAULT NULL COMMENT 'Floor number or name',
  `unit` VARCHAR(30) DEFAULT NULL COMMENT 'Unit number',
  `street` VARCHAR(64) DEFAULT NULL COMMENT 'Street name',
  `city` VARCHAR(64) DEFAULT NULL COMMENT 'City Name',
  `postcode` VARCHAR(16) DEFAULT NULL COMMENT 'Postal Code or Zip code',
  `countrycode` VARCHAR(2) DEFAULT NULL COMMENT '2 letter country code see countries table',
  `state` VARCHAR(64) DEFAULT NULL COMMENT 'State or province',
  `createdate` DATETIME DEFAULT NULL,
  PRIMARY KEY (`addressid`)
) ;

/*Table structure for table `advdocuments` */

DROP TABLE IF EXISTS `advdocuments`;

CREATE TABLE `advdocuments` (
  `documentid` INT(225) NOT NULL AUTO_INCREMENT COMMENT 'Document id',
  `parentid` INT(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` INT(225) NOT NULL COMMENT 'Profile id',
  `doctype` INT(11) DEFAULT NULL COMMENT 'OTA DOC type',
  `validfrom` DATETIME DEFAULT NULL COMMENT 'Valid start date time',
  `validto` DATETIME DEFAULT NULL COMMENT 'Valid until date time',
  `issuer` VARCHAR(50) DEFAULT NULL COMMENT 'Issuer',
  `issuecountrycode` VARCHAR(5) DEFAULT NULL COMMENT 'Issuer Country Code',
  `nationality` VARCHAR(5) DEFAULT NULL COMMENT 'Nationality- 2 letter Country code',
  `name` VARCHAR(210) DEFAULT NULL COMMENT 'Name on document',
  `docnumber` VARCHAR(50) DEFAULT NULL COMMENT 'Document number',
  `issuelocation` VARCHAR(64) DEFAULT NULL COMMENT 'Issuing location',
  `createdate` DATETIME DEFAULT NULL,
  PRIMARY KEY (`documentid`)
) ;

/*Table structure for table `advemails` */

DROP TABLE IF EXISTS `advemails`;

CREATE TABLE `advemails` (
  `emailid` INT(225) NOT NULL AUTO_INCREMENT,
  `parentid` INT(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` INT(225) NOT NULL COMMENT 'Database profile id',
  `addresstype` INT(11) DEFAULT NULL COMMENT 'Email or IM 0 email, 1 IM',
  `EAT` INT(11) DEFAULT '0' COMMENT 'Email Address Type from OTA see EAT',
  `addr` VARCHAR(128) DEFAULT NULL COMMENT 'Email or IM address',
  `IMT` VARCHAR(100) DEFAULT NULL COMMENT 'Instant Messenger type string - Skype, MSN, Yahoo etc',
  `createdate` DATETIME DEFAULT NULL,
  PRIMARY KEY (`emailid`)
) ;

/*Table structure for table `advphones` */

DROP TABLE IF EXISTS `advphones`;

CREATE TABLE `advphones` (
  `telephoneid` INT(225) NOT NULL AUTO_INCREMENT COMMENT 'Database phone id',
  `parentid` INT(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` INT(225) NOT NULL COMMENT 'Database profile id',
  `ptt` INT(11) DEFAULT NULL COMMENT 'Phone technology type see PTT',
  `put` INT(11) DEFAULT NULL COMMENT 'Phone usage type see PUT',
  `plt` INT(11) DEFAULT NULL COMMENT 'Phone location type see PLT',
  `countrycode` VARCHAR(3) DEFAULT NULL,
  `areacode` VARCHAR(8) DEFAULT NULL,
  `phonenumber` VARCHAR(32) DEFAULT NULL,
  `ext` VARCHAR(8) DEFAULT NULL COMMENT 'Phone Extension Code',
  `createdate` DATETIME DEFAULT NULL,
  PRIMARY KEY (`telephoneid`)
) ;

/*Table structure for table `advprofile` */

DROP TABLE IF EXISTS `advprofile`;

CREATE TABLE `advprofile` (
  `profileid` INT(225) NOT NULL AUTO_INCREMENT COMMENT 'profile ID',
  `parentid` INT(225) DEFAULT NULL COMMENT 'parent detail ID for linked detail set',
  `salutation` VARCHAR(10) DEFAULT NULL COMMENT 'Mr Mrs Miss Dr Sir',
  `firstname` VARCHAR(64) DEFAULT NULL,
  `middlename` VARCHAR(64) DEFAULT NULL,
  `lastname` VARCHAR(64) DEFAULT NULL,
  `dob` DATE DEFAULT NULL COMMENT 'Date of birth',
  `gender` VARCHAR(1) DEFAULT NULL COMMENT 'M/F',
  `lang` VARCHAR(10) DEFAULT NULL COMMENT 'language code - en',
  `altlang` VARCHAR(10) DEFAULT NULL COMMENT 'alternate language code',
  `ebridgeid` VARCHAR(64) DEFAULT NULL COMMENT 'e-Bridge ID',  
  `comments` BLOB,
  `createdate` DATETIME DEFAULT NULL,
  PRIMARY KEY (`profileid`)
) ;

/*Table structure for table `adv_cards` */

DROP TABLE IF EXISTS `adv_cards`;


CREATE TABLE `adv_cards` (
  `cardid` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Document id',
  `profileid` BIGINT(20) NOT NULL COMMENT 'Profile id of e-Bridge profile',
  `programid` VARCHAR(50) DEFAULT NULL COMMENT 'Rewards Program ID',
  `membershipid` VARCHAR(50) DEFAULT NULL COMMENT 'Membership ID',
  `loyaltylevel` VARCHAR(50) DEFAULT NULL COMMENT 'Loyalty Level',
  `loyaltylevelcode` VARCHAR(10) DEFAULT NULL COMMENT 'Loyalty Level Code',
  `singleprogram` BOOL DEFAULT TRUE COMMENT 'Loyalty Program for single supplier, not group or affliated membership',
  `tvs` INT(11) DEFAULT NULL COMMENT 'OTA TVS Travel Sector value',
  `signupdate` DATETIME DEFAULT NULL COMMENT 'Signup start date time',
  `effectivedate` DATETIME DEFAULT NULL COMMENT 'Valid start date time',
  `expiredate` DATETIME DEFAULT NULL COMMENT 'Valid until date time',
  `customertype` VARCHAR(50) DEFAULT NULL COMMENT 'Customer Type',
  `allianceloyaltylevelname` VARCHAR(100) DEFAULT NULL COMMENT 'Alliance Loyalty Level Name - eg Sapphire One World',
  `customervalue` VARCHAR(50) DEFAULT NULL COMMENT 'Customer loyalty value eg 500 (points) or A***',
  `password` VARCHAR(50) DEFAULT NULL COMMENT 'PIN or Password',
  `vendorcode` VARCHAR(10) DEFAULT NULL COMMENT 'Vendor code',
  PRIMARY KEY (`cardid`)
) ;

/*Table structure for table `reservation_details` */

DROP TABLE IF EXISTS `reservation_details`;

CREATE TABLE `reservation_details`(
     `id` INT(255) NOT NULL AUTO_INCREMENT ,
     `reservation_id` INT(255) NOT NULL ,
     `roomid` INT(255) ,
     `roomtypeid` INT(255) ,
     `ratesid` INT(255) ,
     `quantity` INT(255) ,
	 `status` INT(11) DEFAULT 1,
     PRIMARY KEY (`id`)
 ) ;
 
/*Table structure for table `emailsetup` */

DROP TABLE IF EXISTS `emailsetup`;

 CREATE TABLE `emailsetup`(
`mailServer` VARCHAR(100) ,
`secureprotocol` VARCHAR(10) ,
`port` VARCHAR(10),
`emailaddress` VARCHAR(255) ,
`authenticate` TINYINT(1) ,
`username` VARCHAR(50) ,
`password` VARCHAR(50) 
 )  ;
 
 /*Table structure for table `holidays` */

DROP TABLE IF EXISTS `holidays`;

CREATE TABLE `holidays` (
  `HolidayID` INT(11) NOT NULL AUTO_INCREMENT,
  `Description` VARCHAR(250) DEFAULT NULL,
  `CountryCode` VARCHAR(2) DEFAULT NULL,
  `Holiday` DATETIME DEFAULT NULL,
  PRIMARY KEY (`HolidayID`)
);	
 /*Table structure for table `agent_bookref` */

DROP TABLE IF EXISTS `agent_bookref`;

CREATE TABLE `agent_bookref`( 
	`agent_bookrefid` INT(255) NOT NULL AUTO_INCREMENT, 
	`reservation_id` INT(11) NOT NULL, 
	`agentid` INT(11) NOT NULL, 
	`refno` CHAR(20), 
	PRIMARY KEY (`agent_bookrefid`) 
); 
/*Table structure for table `version` */

DROP TABLE IF EXISTS `version`;

CREATE TABLE `version` (
  `Major` INT(11) NOT NULL,
  `Minor` INT(11) NOT NULL,
  `Patch` INT(11) NOT NULL
) ;

DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts`( 
	`userid` SMALLINT NOT NULL, 
	`startshift` DATETIME NOT NULL, 
	`endshift` DATETIME NOT NULL, 
	`notes` TEXT NOT NULL 
);

 

DROP TABLE IF EXISTS `otadata`;

CREATE TABLE `otadata` (
  `otaid` INT(11) NOT NULL AUTO_INCREMENT,
  `agentid` INT(11) NOT NULL,
  `label` VARCHAR(100) DEFAULT NULL,
  `otatype` INT(11) DEFAULT NULL COMMENT 'Agoda, expedia, etc',
  `key1` MEDIUMBLOB COMMENT 'key data by ota type',
  `key2` MEDIUMBLOB COMMENT 'key data by ota type',
  `key3` MEDIUMBLOB COMMENT 'key data by ota type',
  `key4` MEDIUMBLOB COMMENT 'key data by ota type',
  `key5` MEDIUMBLOB COMMENT 'key data by ota type',
  PRIMARY KEY (`otaid`)
) ;

/*Table structure for table `otasync` */

DROP TABLE IF EXISTS `otasync`;

CREATE TABLE `otasync`( 
  `syncid` INT NOT NULL AUTO_INCREMENT, 
  `ratesid` INT COMMENT 'Rate ID', 
  `roomtypeid` INT COMMENT 'Room type id', 
  `start` DATE, `end` DATE, 
  `syncdatetime` DATETIME, 
  PRIMARY KEY (`syncid`) 
);


/*Table structure for table `otasynclog` */

DROP TABLE IF EXISTS `otasynclog`;

CREATE TABLE `otasynclog` (
  `syncid` INT(11) DEFAULT NULL,
  `otatype` INT(11) DEFAULT 0,
  `synctime` DATETIME DEFAULT NULL,
  `syncxml` MEDIUMTEXT
) ;


DROP TABLE IF EXISTS `otaalloc`;
CREATE TABLE `otaalloc`(
	`allocationid` INT NOT NULL AUTO_INCREMENT, 
	`ratesid` INT(11), 
	`roomid` INT(11) DEFAULT 0, 
	`roomtypeid` INT(11) DEFAULT 0, 
	`roomcount` INT(11) DEFAULT 0, 
	`guaranteecount` INT(11) DEFAULT 0, 
	PRIMARY KEY (`allocationid`) 
);

DROP TABLE IF EXISTS `otacloseout`;

CREATE TABLE `otacloseout`(
	`otacloseid` INT(11) NOT NULL AUTO_INCREMENT,
	`ratesid` INT(11), 
	`roomtypeid` INT(11), 
	`closeregular` BOOL DEFAULT FALSE, 
	`closeguarantee` BOOL DEFAULT FALSE, 
	`totalrooms` INT DEFAULT 0, 
	`guaranteerooms` INT DEFAULT 0, 
	`closedate` DATE DEFAULT NULL,
	`ratevalue` FLOAT DEFAULT 0,
	PRIMARY KEY (`otacloseid`)
);


DROP TABLE IF EXISTS `customfields`;
CREATE TABLE `customfields`( 
	`customfldid` INT(11) NOT NULL AUTO_INCREMENT, 
	`pageid` INT(11), 
	`fieldreq` TINYINT DEFAULT 0, 
	`fieldtype` INT DEFAULT 0, 
	`label` VARCHAR(100), 
	PRIMARY KEY (`customfldid`) 
); 

DROP TABLE IF EXISTS `customvalues`;
CREATE TABLE `customvalues`( 
	`valueid` INT(11) NOT NULL AUTO_INCREMENT, 
	`customfldid` INT(11), 
	`pageid` INT(11), 
	`transid` INT(11), 
	`customvalue` MEDIUMBLOB, 
	PRIMARY KEY (`valueid`) 
); 

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*Data for the table `countries` */

INSERT  INTO `countries`(`countryid`,`country`,`countrycode`,`subscriber`,`nationality`,`currency`) VALUES (1,'ANDORRA,PRINCIPALITYOF','AD','',NULL,NULL),(2,'UNITEDARABEMIRATES','AE','971',NULL,NULL),(3,'AFGHANISTAN,ISLAMICSTATEOF','AF','',NULL,NULL),(4,'ANTIGUAANDBARBUDA','AG','',NULL,NULL),(5,'ANGUILLA','AI','+1-264*',NULL,NULL),(6,'ALBANIA','AL','355',NULL,NULL),(7,'ARMENIA','AM','374',NULL,NULL),(8,'NETHERLANDSANTILLES','AN','599',NULL,NULL),(9,'ANGOLA','AO','244',NULL,NULL),(10,'ANTARCTICA','AQ','672',NULL,NULL),(11,'ARGENTINA','AR','54',NULL,NULL),(12,'AMERICANSAMOA','AS','684',NULL,NULL),(13,'AUSTRIA','AT','43',NULL,NULL),(14,'AUSTRALIA','AU','61',NULL,NULL),(15,'ARUBA','AW','297',NULL,NULL),(16,'AZERBAIDJAN','AZ','',NULL,NULL),(17,'BOSNIA-HERZEGOVINA','BA','',NULL,NULL),(18,'BARBADOS','BB','+1-246*',NULL,NULL),(19,'BANGLADESH','BD','880',NULL,NULL),(20,'BELGIUM','BE','32',NULL,NULL),(21,'BURKINAFASO','BF','226',NULL,NULL),(22,'BULGARIA','BG','359',NULL,NULL),(23,'BAHRAIN','BH','973',NULL,NULL),(24,'BURUNDI','BI','257',NULL,NULL),(25,'BENIN','BJ','229',NULL,NULL),(26,'BERMUDA','BM','+1-441*',NULL,NULL),(27,'BRUNEIDARUSSALAM','BN','673',NULL,NULL),(28,'BOLIVIA','BO','591',NULL,NULL),(29,'BRAZIL','BR','55',NULL,NULL),(30,'BAHAMAS','BS','+1-242*',NULL,NULL),(31,'BHUTAN','BT','975',NULL,NULL),(32,'BOUVETISLAND','BV','',NULL,NULL),(33,'BOTSWANA','BW','267',NULL,NULL),(34,'BELARUS','BY','375',NULL,NULL),(35,'BELIZE','BZ','501',NULL,NULL),(36,'CANADA','CA','1',NULL,NULL),(37,'COCOS(KEELING)ISLANDS','CC','',NULL,NULL),(38,'CENTRALAFRICANREPUBLIC','CF','236',NULL,NULL),(39,'CONGO,THEDEMOCRATICREPUBLICOFTHE','CD','',NULL,NULL),(40,'CONGO','CG','242',NULL,NULL),(41,'SWITZERLAND','CH','41',NULL,NULL),(42,'IVORYCOAST(COTEDIVOIRE)','CI','',NULL,NULL),(43,'COOKISLANDS','CK','682',NULL,NULL),(44,'CHILE','CL','56',NULL,NULL),(45,'CAMEROON','CM','237',NULL,NULL),(46,'CHINA','CN','',NULL,NULL),(47,'COLOMBIA','CO','57',NULL,NULL),(48,'COSTARICA','CR','506',NULL,NULL),(49,'FORMERCZECHOSLOVAKIA','CS','',NULL,NULL),(50,'CUBA','CU','53',NULL,NULL),(51,'CAPEVERDE','CV','',NULL,NULL),(52,'CHRISTMASISLAND','CX','53',NULL,NULL),(53,'CYPRUS','CY','357',NULL,NULL),(54,'CZECHREPUBLIC','CZ','420',NULL,NULL),(55,'GERMANY','DE','49',NULL,NULL),(56,'DJIBOUTI','DJ','253',NULL,NULL),(57,'DENMARK','DK','45',NULL,NULL),(58,'DOMINICA','DM','+1-767*',NULL,NULL),(59,'DOMINICANREPUBLIC','DO','+1-809*',NULL,NULL),(60,'ALGERIA','DZ','213',NULL,NULL),(61,'ECUADOR','EC','593',NULL,NULL),(62,'ESTONIA','EE','372',NULL,NULL),(63,'EGYPT','EG','20',NULL,NULL),(64,'WESTERNSAHARA','EH','',NULL,NULL),(65,'ERITREA','ER','291',NULL,NULL),(66,'SPAIN','ES','34',NULL,NULL),(67,'ETHIOPIA','ET','251',NULL,NULL),(68,'FINLAND','FI','358',NULL,NULL),(69,'FIJI','FJ','',NULL,NULL),(70,'FALKLANDISLANDS','FK','',NULL,NULL),(71,'MICRONESIA','FM','',NULL,NULL),(72,'FAROEISLANDS','FO','298',NULL,NULL),(73,'FRANCE','FR','33',NULL,NULL),(74,'FRANCE(EUROPEANTERRITORY)','FX','',NULL,NULL),(75,'GABON','GA','',NULL,NULL),(76,'GREATBRITAIN','GB','',NULL,NULL),(77,'GRENADA','GD','+1-473*',NULL,NULL),(78,'GEORGIA','GE','995',NULL,NULL),(79,'FRENCHGUYANA','GF','',NULL,NULL),(80,'GHANA','GH','233',NULL,NULL),(81,'GIBRALTAR','GI','350',NULL,NULL),(82,'GREENLAND','GL','299',NULL,NULL),(83,'GAMBIA','GM','220',NULL,NULL),(84,'GUINEA','GN','224',NULL,NULL),(85,'USAGOVERNMENT','GOV','',NULL,NULL),(86,'GUADELOUPE(FRENCH)','GP','',NULL,NULL),(87,'EQUATORIALGUINEA','GQ','240',NULL,NULL),(88,'GREECE','GR','30',NULL,NULL),(89,'S.GEORGIA&S.SANDWICHISLS.','GS','',NULL,NULL),(90,'GUATEMALA','GT','502',NULL,NULL),(91,'GUAM(USA)','GU','',NULL,NULL),(92,'GUINEABISSAU','GW','',NULL,NULL),(93,'GUYANA','GY','592',NULL,NULL),(94,'HONGKONG','HK','852',NULL,NULL),(95,'HEARDANDMCDONALDISLANDS','HM','',NULL,NULL),(96,'HONDURAS','HN','504',NULL,NULL),(97,'CROATIA','HR','385',NULL,NULL),(98,'HAITI','HT','509',NULL,NULL),(99,'HUNGARY','HU','36',NULL,NULL),(100,'INDONESIA','ID','62',NULL,NULL),(101,'IRELAND','IE','353',NULL,NULL),(102,'ISRAEL','IL','972',NULL,NULL),(103,'INDIA','IN','91',NULL,NULL),(104,'BRITISHINDIANOCEANTERRITORY','IO','',NULL,NULL),(105,'IRAQ','IQ','964',NULL,NULL),(106,'IRAN','IR','98',NULL,NULL),(107,'ICELAND','IS','354',NULL,NULL),(108,'ITALY','IT','39',NULL,NULL),(109,'JAMAICA','JM','+1-876*',NULL,NULL),(110,'JORDAN','JO','962',NULL,NULL),(111,'JAPAN','JP','81',NULL,NULL),(112,'KENYA','KE','254',NULL,NULL),(113,'KYRGYZREPUBLIC(KYRGYZSTAN)','KG','',NULL,NULL),(114,'CAMBODIA,KINGDOMOF','KH','',NULL,NULL),(115,'KIRIBATI','KI','686',NULL,NULL),(116,'COMOROS','KM','269',NULL,NULL),(117,'SAINTKITTS&NEVISANGUILLA','KN','',NULL,NULL),(118,'NORTHKOREA','KP','',NULL,NULL),(119,'SOUTHKOREA','KR','',NULL,NULL),(120,'KUWAIT','KW','965',NULL,NULL),(121,'CAYMANISLANDS','KY','+1-345*',NULL,NULL),(122,'KAZAKHSTAN','KZ','7',NULL,NULL),(123,'LAOS','LA','856',NULL,NULL),(124,'LEBANON','LB','961',NULL,NULL),(125,'SAINTLUCIA','LC','',NULL,NULL),(126,'LIECHTENSTEIN','LI','423',NULL,NULL),(127,'SRILANKA','LK','94',NULL,NULL),(128,'LIBERIA','LR','231',NULL,NULL),(129,'LESOTHO','LS','266',NULL,NULL),(130,'LITHUANIA','LT','370',NULL,NULL),(131,'LUXEMBOURG','LU','352',NULL,NULL),(132,'LATVIA','LV','371',NULL,NULL),(133,'LIBYA','LY','218',NULL,NULL),(134,'MOROCCO','MA','212',NULL,NULL),(135,'MONACO','MC','377',NULL,NULL),(136,'MOLDAVIA','MD','',NULL,NULL),(137,'MADAGASCAR','MG','261',NULL,NULL),(138,'MARSHALLISLANDS','MH','692',NULL,NULL),(139,'MACEDONIA','MK','',NULL,NULL),(140,'MALI','ML','',NULL,NULL),(141,'MYANMAR','MM','95',NULL,NULL),(142,'MONGOLIA','MN','976',NULL,NULL),(143,'MACAU','MO','',NULL,NULL),(144,'NORTHERNMARIANAISLANDS','MP','',NULL,NULL),(145,'MARTINIQUE(FRENCH)','MQ','',NULL,NULL),(146,'MAURITANIA','MR','222',NULL,NULL),(147,'MONTSERRAT','MS','+1-664*',NULL,NULL),(148,'MALTA','MT','356',NULL,NULL),(149,'MAURITIUS','MU','230',NULL,NULL),(150,'MALDIVES','MV','960',NULL,NULL),(151,'MALAWI','MW','265',NULL,NULL),(152,'MEXICO','MX','52',NULL,NULL),(153,'MALAYSIA','MY','60',NULL,'MYR'),(154,'MOZAMBIQUE','MZ','258',NULL,NULL),(155,'NAMIBIA','NA','264',NULL,NULL),(156,'NEWCALEDONIA(FRENCH)','NC','',NULL,NULL),(157,'NIGER','NE','227',NULL,NULL),(158,'NORFOLKISLAND','NF','672',NULL,NULL),(159,'NIGERIA','NG','234',NULL,NULL),(160,'NICARAGUA','NI','505',NULL,NULL),(161,'NETHERLANDS','NL','31',NULL,NULL),(162,'NORWAY','NO','47',NULL,NULL),(163,'NEPAL','NP','977',NULL,NULL),(164,'NAURU','NR','674',NULL,NULL),(165,'NIUE','NU','683',NULL,NULL),(166,'NEWZEALAND','NZ','64',NULL,'NZD'),(167,'OMAN','OM','968',NULL,NULL),(168,'PANAMA','PA','507',NULL,NULL),(169,'PERU','PE','51',NULL,NULL),(170,'POLYNESIA(FRENCH)','PF','',NULL,NULL),(171,'PAPUANEWGUINEA','PG','675',NULL,NULL),(172,'PHILIPPINES','PH','63',NULL,'PHP'),(173,'PAKISTAN','PK','92',NULL,NULL),(174,'POLAND','PL','48',NULL,NULL),(175,'SAINTPIERREANDMIQUELON','PM','',NULL,NULL),(176,'PITCAIRNISLAND','PN','',NULL,NULL),(177,'PUERTORICO','PR','+1-787*or+1-939*',NULL,NULL),(178,'PORTUGAL','PT','351',NULL,NULL),(179,'PALAU','PW','680',NULL,NULL),(180,'PARAGUAY','PY','595',NULL,NULL),(181,'QATAR','QA','974',NULL,NULL),(182,'REUNION(FRENCH)','RE','',NULL,NULL),(183,'ROMANIA','RO','40',NULL,NULL),(184,'RUSSIANFEDERATION','RU','',NULL,NULL),(185,'RWANDA','RW','',NULL,NULL),(186,'SAUDIARABIA','SA','966',NULL,NULL),(187,'SOLOMONISLANDS','SB','677',NULL,NULL),(188,'SEYCHELLES','SC','',NULL,NULL),(189,'SUDAN','SD','249',NULL,NULL),(190,'SWEDEN','SE','46',NULL,NULL),(191,'SINGAPORE','SG','65',NULL,'SGD'),(192,'SAINTHELENA','SH','',NULL,NULL),(193,'SLOVENIA','SI','386',NULL,NULL),(194,'SVALBARDANDJANMAYENISLANDS','SJ','',NULL,NULL),(195,'SLOVAKREPUBLIC','SK','421',NULL,NULL),(196,'SIERRALEONE','SL','232',NULL,NULL),(197,'SANMARINO','SM','378',NULL,NULL),(198,'SENEGAL','SN','221',NULL,NULL),(199,'SOMALIA','SO','',NULL,NULL),(200,'SURINAME','SR','597',NULL,NULL),(201,'SAINTTOME(SAOTOME)ANDPRINCIPE','ST','',NULL,NULL),(202,'FORMERUSSR','SU','',NULL,NULL),(203,'ELSALVADOR','SV','503',NULL,NULL),(204,'SYRIA','SY','963',NULL,NULL),(205,'SWAZILAND','SZ','268',NULL,NULL),(206,'TURKSANDCAICOSISLANDS','TC','+1-649*',NULL,NULL),(207,'CHAD','TD','235',NULL,NULL),(208,'FRENCHSOUTHERNTERRITORIES','TF','',NULL,NULL),(209,'TOGO','TG','',NULL,NULL),(210,'THAILAND','TH','66',NULL,NULL),(211,'TADJIKISTAN','TJ','',NULL,NULL),(212,'TOKELAU','TK','690',NULL,NULL),(213,'TURKMENISTAN','TM','993',NULL,NULL),(214,'TUNISIA','TN','216',NULL,NULL),(215,'TONGA','TO','',NULL,NULL),(216,'EASTTIMOR','TP','670',NULL,NULL),(217,'TURKEY','TR','90',NULL,NULL),(218,'TRINIDADANDTOBAGO','TT','',NULL,NULL),(219,'TUVALU','TV','688',NULL,NULL),(220,'TAIWAN','TW','886',NULL,NULL),(221,'TANZANIA','TZ','255',NULL,NULL),(222,'UKRAINE','UA','380',NULL,NULL),(223,'UGANDA','UG','256',NULL,NULL),(224,'UNITEDKINGDOM','UK','44',NULL,NULL),(225,'USAMINOROUTLYINGISLANDS','UM','',NULL,NULL),(226,'UNITEDSTATES','US','',NULL,NULL),(227,'URUGUAY','UY','598',NULL,NULL),(228,'UZBEKISTAN','UZ','998',NULL,NULL),(229,'HOLYSEE(VATICANCITYSTATE)','VA','',NULL,NULL),(230,'SAINTVINCENT&GRENADINES','VC','',NULL,NULL),(231,'VENEZUELA','VE','58',NULL,NULL),(232,'VIRGINISLANDS(BRITISH)','VG','',NULL,NULL),(233,'VIRGINISLANDS(USA)','VI','',NULL,NULL),(234,'VIETNAM','VN','84',NULL,NULL),(235,'VANUATU','VU','678',NULL,NULL),(236,'WALLISANDFUTUNAISLANDS','WF','681',NULL,NULL),(237,'SAMOA','WS','',NULL,NULL),(238,'YEMEN','YE','967',NULL,NULL),(239,'MAYOTTE','YT','',NULL,NULL),(240,'YUGOSLAVIA','YU','',NULL,NULL),(241,'SOUTHAFRICA','ZA','27',NULL,NULL),(242,'ZAMBIA','ZM','260',NULL,NULL),(243,'ZAIRE','ZR','',NULL,NULL),(244,'ZIMBABWE','ZW','263',NULL,NULL);
/*Data for the table `details` */

INSERT  INTO `details`(`itemid`,`item`,`description`,`sale`,`expense`,`itype`) VALUES (1,'Room','Accommodation',1,0,1),(2,'Bar','Bar',1,0,3),(3,'Conference','Conference',1,0,7),(4,'Restaurant','Restaurant',1,0,2),(5,'Laundry','Laundry',1,0,7),(6,'Other','Other',1,0,7),(7,'Transfer','Hotel Transfer',1,0,4),(8,'Movie','Internal Movies',1,0,7),(9,'Phone','Phone Calls',1,0,6),(10,'Taxes','Taxes',1,0,8),(11,'Fee','Service Fee',1,0,5),(12,'Internet','Internet',1,0,7),(13,'Breakfast','Breakfast Buffet',1,0,2),(14,'Cancel','Cancellation Fee',1,0,5),(15,'Guarantee','Guarantee Fee',1,0,5),(16,'ExtraPerson','Extra Person',1,0,5),(17,'ExtraBed','Extra Bed',1,0,5);
/*Data for the table `doctypes` */

INSERT  INTO `doctypes`(`doc_id`,`doc_code`,`doc_type`,`remarks`,`accounts`,`cooperative`,`payroll`) VALUES (1,'RECEI','RECEIPT                  ',NULL,1,1,NULL),(2,'INVOI','INVOICE                  ',NULL,1,1,NULL),(3,'ADVAN','ADVANCE                  ',NULL,1,NULL,1),(4,'SPADV','SPECIAL ADVANCE          ',NULL,1,NULL,1),(5,'LOANS','LOANS                    ',NULL,NULL,1,1),(6,'SHARE','SHARES                   ',NULL,NULL,1,1),(7,'OVEDE','OVER DEDUCTION           ',NULL,NULL,1,1),(8,'UNDED','UNDER DEDUCTION          ',NULL,1,1,1),(9,'CSHPV','CASH PAYMENT VOUCHER     ',NULL,NULL,1,NULL),(10,'CSHRV','CASH RECEIVED VOUCHER    ',NULL,NULL,1,NULL),(11,'CHQRV','CHEQUE RECEIVED VOUCHER  ',NULL,NULL,1,NULL),(12,'CRVCH','CREDIT VOUCHER           ',NULL,1,1,1),(13,'DRVCH','DEBIT VOUCHER            ',NULL,1,1,1),(14,'CDPVC','CASH DEPOSIT VOUCHER     ',NULL,NULL,NULL,NULL),(15,'CHDVC','CHEQUE DEPOSIT VOUCHER   ',NULL,1,1,NULL),(16,'PCSVC','PETTY CASH VOUCER        ',NULL,NULL,NULL,NULL),(17,'WTVCH','WITHDRAWAL VOUCHER       ',NULL,1,1,NULL),(18,'CRADV','CREDIT ADVICE            ',NULL,1,1,0),(19,'DRADV','DEBIT ADVICE             ',NULL,1,1,NULL),(20,'IMPVC','IMPREST VOUCHER          ',NULL,1,1,NULL),(21,'chit','chits','for credit sales',1,1,1);
/*Data for the table `documents` */

INSERT  INTO `documents`(`propertyid`,`receiptno`,`invoiceno`,`voucherno`) VALUES (1,'RCPT1000','INV1000','VCH1000');
/*Data for the table `hotelgallery` */

INSERT  INTO `hotelgallery`(`PicID`,`Title`,`Description`,`URL`) VALUES (1,'Front View','Pic taken by front view','http://www.patiopacificboracay.com/wordpress/wp-content/gallery/photo-gallery/cove.jpg'),(2,'Overall View','Pic taken by overall view','http://www.patiopacificboracay.com/wordpress/wp-content/gallery/photo-gallery/redhot-paraw.jpg'),(3,'Front','Pic Front','http://www.patiopacificboracay.com/wordpress/wp-content/gallery/photo-gallery/new-facade.jpg');
/*Data for the table `inventory_group` */

INSERT  INTO `inventory_group`(`groupcode`,`groupname`,`userid`,`inventory_count`) VALUES (1,'Widgets Annual Sales Meeting Group',3,30),(2,'Digits Customer Forum Group',5,15),(3,'RRRR Group',2,25),(4,'Gizmos',4,20);
/*Data for the table `languages` */

INSERT  INTO `languages`(`lang`,`Description`,`LocalDescription`,`active`) VALUES ('af','Afrikaans','Afrikaans',0),('ar-sa','Arabic (Saudi Arabia)','Arabic (Saudi Arabia)',0),('am','Amharic','Amharic',1),('ar-eg','Arabic (Egypt)','Arabic (Egypt)',0),('ar-dz','Arabic (Algeria)','Arabic (Algeria)',0),('ar-tn','Arabic (Tunisia)','Arabic (Tunisia)',0),('ar-ye','Arabic (Yemen)','Arabic (Yemen)',0),('ar-jo','Arabic (Jordan)','Arabic (Jordan)',0),('ar-kw','Arabic (Kuwait)','Arabic (Kuwait)',0),('ar-bh','Arabic (Bahrain)','Arabic (Bahrain)',0),('en-us','English (United States)','English (United States)',1),('en-au','English (Australia)','English (Australia)',1),('en-nz','English (New Zealand)','English (New Zealand)',1),('en-za','English (South Africa)','English (South Africa)',1),('en','English','English',1),('en-gb','English (United Kingdom)','English (United Kingdom)',1),('en-tt','English (Trinidad)','English (Trinidad)',1),('en-ca','English (Canada)','English (Canada)',1),('en-ie','English (Ireland)','English (Ireland)',1),('en-jm','English (Jamaica)','English (Jamaica)',1),('en-bz','English (Belize)','English (Belize)',1),('ko','Korean','&#54620;&#44397;&#50612;',1),('zh-tw','Chinese (Taiwan)','&#20013;&#22283;(&#21488;&#28771)',1),('zh-hk','Chinese (Hong Kong)','&#20013;&#22283;(&#39321;&#28207;)',1),('zh-cn','Chinese (PRC)','&#20013;&#25991;(&#20013;&#22269;)',1),('zh-sg','Chinese (Singapore)','&#20013;&#25991;(&#26032;&#21152;&#22369;)',1),('th','Thai','&#3616;&#3634;&#3625;&#3634;&#3652;&#3607;&#3618;',1),('eu','Basque','Basque',0),('be','Belarusian','Belarusian',0),('hr','Croatian','Croatian',0),('da','Danish','Danish',0),('nl-be','Dutch (Belgium)','Dutch (Belgium)',0),('fo','Faeroese','Faeroese',0),('fi','Finnish','Finnish',0),('fr-be','French (Belgium)','French (Belgium)',0),('fr','French (Standard)','fran&#231;aise',0),('fr-ca','French (Canada)','French (Canada)',0),('fr-ch','French (Switzerland)','French (Switzerland)',0),('fr-lu','French (Luxembourg)','French (Luxembourg)',0),('gd','Gaelic (Scotland)','Gaelic (Scotland)',0),('ga','Irish','Irish',0),('de','German (Standard)','German (Standard)',0),('de-ch','German (Switzerland)','German (Switzerland)',0),('de-at','German (Austria)','German (Austria)',0),('de-li','German (Liechtenstein)','German (Liechtenstein)',0),('de-lu','German (Luxembourg)','German (Luxembourg)',0),('el','Greek','&#904;&#955;&#955;&#951;&#957;&#949;&#962;',0),('he','Hebrew','&#1506;&#1489;&#1512;&#1497;&#1514;',0),('hi','Hindi','Hindi',0),('hu','Hungarian','Hungarian',0),('is','Icelandic','Icelandic',0),('id','Indonesian','Indonesian',0),('it-ch','Italian (Switzerland)','Italian (Switzerland)',0),('ms','Malaysian','Malaysia',0),('vi','Vietnamese','Vietnamese',0),('vt','Vietnamese','Vi&#7879;t',1),('my','Myanmar (Burmese)','Myanmar (Burmese)',0);
/*Data for the table `ota_bedtype` */

INSERT  INTO `ota_bedtype`(`OTA_BedID`,`OTA_Number`,`lang`,`Description`) VALUES (1,1,'en-us','Double'),(2,2,'en-us','Futon'),(3,3,'en-us','King'),(4,4,'en-us','Murphy Bed'),(5,5,'en-us','Queen'),(6,6,'en-us','Sofa Bed'),(7,7,'en-us','Tatami mats'),(8,8,'en-us','Twin'),(9,9,'en-us','Single'),(10,1,'en','Double'),(11,2,'en','Futon'),(12,3,'en','King'),(13,4,'en','Wall Bed'),(14,5,'en','Queen'),(15,6,'en','Sofa Bed'),(16,7,'en','Tatami Mats'),(17,8,'en','Twin Beds'),(18,9,'en','Single'),(19,1,'kr','&#45908;&#48660; &#52840;&#45824;'),(20,2,'kr','&#51060;&#48520; &#52840;&#45824;'),(21,3,'kr','&#53433; &#48288;&#46300;'),(22,4,'kr','&#52840;&#45824; &#50500;&#47000;&#47196; &#45817;&#44200;'),(23,5,'kr','&#53304; &#52840;&#45824;'),(24,6,'kr','&#49548;&#54028; &#48288;&#46300;'),(25,7,'kr','&#45796;&#45796;&#48120; &#47588;&#53944;'),(26,8,'kr','&#53944;&#50952; &#52840;&#45824;'),(27,9,'kr','&#49905;&#44544; &#52840;&#45824;'),(28,1,'th','&#3648;&#3605;&#3637;&#3618;&#3591;&#3588;&#3641;&'),(29,2,'th','&#3614;&#3619;&#3657;&#3629;&#3617;&#3615;&#3641;&'),(30,3,'th','&#3648;&#3605;&#3637;&#3618;&#3591;'),(31,4,'th','&#3604;&#3638;&#3591;&#3621;&#3591;&#3648;&#3605;&#3637;&#3618;&#3591;'),(32,5,'th','&#3648;&#3605;&#3637;&#3618;&#3591; Queen'),(33,6,'th','&#3648;&#3605;&#3637;&#3618;&#3591;&#3609;&#3629;&#3609;&#3650;&#3595;&#3615;&#3634;'),(34,7,'th','&#3648;&#3626;&#3639;&#3656;&#3629;&#3605;&#3634;&#3605;&#3634;&#3617;&#3636;'),(35,8,'th','&#3648;&#3605;&#3637;&#3618;&#3591;&#3588;&#3641;&#3656;'),(36,9,'th','&#3648;&#3605;&#3637;&#3618;&#3591;&#3648;&#3604;&#3637;&#3656;&#3618;&#3623;'),(37,1,'vt','Double'),(48,2,'zh-cn','&#24202;&#34987;&#35109;'),(38,2,'vt','Futon'),(39,3,'vt','King'),(40,4,'vt','Murphy Bed'),(41,5,'vt','Queen'),(42,6,'vt','Sofa Bed'),(43,7,'vt','Tatami Mats'),(44,8,'vt','Twin'),(45,9,'vt','Single'),(46,1,'zh-cn','&#21452;&#20154;&#24202;'),(49,3,'zh-cn','&#22823;&#24202;'),(50,4,'zh-cn','&#19979;&#25289;&#24202;'),(51,5,'zh-cn','&#22823;&#24202;'),(52,6,'zh-cn','&#27801;&#21457;&#24202;'),(53,7,'zh-cn','&#27067;&#27067;&#31859;'),(54,8,'zh-cn','&#21452;&#24202;'),(55,9,'zh-cn','&#21333;&#24202;'),(56,1,'zh-hk','Double'),(57,2,'zh-hk','Futon'),(58,3,'zh-hk','King'),(59,4,'zh-hk','Murphy Bed'),(60,5,'zh-hk','Queen'),(61,6,'zh-hk','Sofa Bed'),(62,7,'zh-hk','Tatami Mats'),(63,8,'zh-hk','Twin'),(64,9,'zh-hk','Single'),(65,1,'zh-tw','Double'),(66,2,'zh-tw','Futon'),(67,3,'zh-tw','King'),(68,4,'zh-tw','Murphy Bed'),(69,5,'zh-tw','Queen'),(70,6,'zh-tw','Sofa Bed'),(71,7,'zh-tw','Tatami Mats'),(72,8,'zh-tw','Twin'),(73,9,'zh-tw','Single'),(74,1,'zh-sg','Double'),(75,2,'zh-sg','Futon'),(76,3,'zh-sg','King'),(77,4,'zh-sg','Murphy Bed'),(78,5,'zh-sg','Queen'),(79,6,'zh-sg','Sofa Bed'),(80,7,'zh-sg','Tatami Mats'),(81,8,'zh-sg','Twin'),(82,9,'zh-sg','Single');
/*Data for the table `ota_roomamenity` */

INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (1,1,'en-us','Adjoining rooms'),(2,2,'en-us','Air conditioning'),(3,3,'en-us','Alarm clock'),(4,13,'en-us','Bathtub'),(5,14,'en-us','Bathtub only'),(6,15,'en-us','Bathtub/shower combination'),(7,19,'en-us','Coffee/Tea maker'),(8,20,'en-us','Color television'),(9,21,'en-us','Computer'),(10,28,'en-us','Desk'),(11,38,'en-us','Fax machine'),(12,51,'en-us','High speed internet connection'),(13,52,'en-us','Interactive web TV'),(14,53,'en-us','International direct dialing'),(15,54,'en-us','Internet access'),(16,61,'en-us','Kitchenette'),(17,64,'en-us','Large desk'),(18,68,'en-us','Microwave'),(19,69,'en-us','Minibar'),(20,83,'en-us','Prayer mats'),(21,92,'en-us','Safe'),(22,97,'en-us','Shower only'),(23,101,'en-us','Smoking'),(24,126,'en-us','Air conditioning individually controlled in room'),(25,123,'en-us','Wireless internet connection'),(26,133,'en-us','Desk with electrical outlet'),(27,142,'en-us','Shower'),(28,155,'en-us','Separate tub and shower'),(29,164,'en-us','Mini-refrigerator'),(30,163,'en-us','DVD player'),(31,169,'en-us','Collect calls'),(32,170,'en-us','International calls'),(33,171,'en-us','Carrier calls'),(34,172,'en-us','Interstate calls'),(35,173,'en-us','Intrastate calls'),(36,174,'en-us','Local calls'),(37,175,'en-us','Long distance calls'),(38,176,'en-us','Operator-assisted calls'),(39,177,'en-us','Credit card access calls'),(40,178,'en-us','Calling card calls'),(41,179,'en-us','Toll free calls'),(42,213,'en-us','DVR player'),(43,214,'en-us','iPod docking station'),(44,226,'en-us','High speed wireless'),(45,225,'en-us','High speed internet access fee'),(46,246,'en-us','High Definition (HD) Flat Panel Television  - 32 i'),(47,1,'en','Adjoining rooms'),(48,2,'en','Air conditioning'),(49,3,'en','Alarm clock'),(50,13,'en','Bathtub'),(51,14,'en','Bathtub only'),(52,15,'en','Bathtub/shower combination'),(53,19,'en','Coffee/Tea maker'),(54,20,'en','Color television'),(55,21,'en','Computer'),(56,28,'en','Desk'),(57,38,'en','Fax machine'),(58,51,'en','High speed internet connection'),(59,52,'en','Interactive web TV'),(60,53,'en','International direct dialing'),(61,54,'en','Internet access'),(62,61,'en','Kitchenette'),(63,64,'en','Large desk'),(64,68,'en','Microwave'),(65,69,'en','Minibar'),(66,83,'en','Prayer mats'),(67,92,'en','Safe'),(68,97,'en','Shower only'),(69,101,'en','Smoking'),(70,126,'en','Air conditioning individually controlled in room'),(71,123,'en','Wireless internet connection'),(72,133,'en','Desk with electrical outlet'),(73,142,'en','Shower'),(74,155,'en','Separate tub and shower'),(75,164,'en','Mini-refrigerator'),(76,163,'en','DVD player'),(77,169,'en','Collect calls'),(78,170,'en','International calls'),(79,171,'en','Carrier calls'),(80,172,'en','Interstate calls'),(81,173,'en','Intrastate calls'),(82,174,'en','Local calls'),(83,175,'en','Long distance calls'),(84,176,'en','Operator-assisted calls'),(85,177,'en','Credit card access calls'),(86,178,'en','Calling card calls'),(87,179,'en','Toll free calls'),(88,213,'en','DVR player'),(89,214,'en','iPod docking station'),(90,226,'en','High speed wireless'),(91,225,'en','High speed internet access fee'),(92,246,'en','High Definition (HD) Flat Panel Television  - 32 i'),(93,1,'kr','Adjoining rooms'),(94,2,'kr','Air conditioning'),(95,3,'kr','Alarm clock'),(96,13,'kr','Bathtub'),(97,14,'kr','Bathtub only'),(98,15,'kr','Bathtub/shower combination'),(99,19,'kr','Coffee/Tea maker'),(100,20,'kr','Color television'),(101,21,'kr','Computer'),(102,28,'kr','Desk'),(103,38,'kr','Fax machine'),(104,51,'kr','High speed internet connection'),(105,52,'kr','Interactive web TV'),(106,53,'kr','International direct dialing'),(107,54,'kr','Internet access'),(108,61,'kr','Kitchkrette'),(109,64,'kr','Large desk'),(110,68,'kr','Microwave'),(111,69,'kr','Minibar'),(112,83,'kr','Prayer mats'),(113,92,'kr','Safe'),(114,97,'kr','Shower only'),(115,101,'kr','Smoking'),(116,126,'kr','Air conditioning individually controlled in room'),(117,123,'kr','Wireless internet connection'),(118,133,'kr','Desk with electrical outlet'),(119,142,'kr','Shower'),(120,155,'kr','Separate tub and shower'),(121,164,'kr','Mini-refrigerator'),(122,163,'kr','DVD player'),(123,169,'kr','Collect calls'),(124,170,'kr','International calls'),(125,171,'kr','Carrier calls'),(126,172,'kr','Interstate calls'),(127,173,'kr','Intrastate calls'),(128,174,'kr','Local calls'),(129,175,'kr','Long distance calls'),(130,176,'kr','Operator-assisted calls'),(131,177,'kr','Credit card access calls'),(132,178,'kr','Calling card calls'),(133,179,'kr','Toll free calls'),(134,213,'kr','DVR player'),(135,214,'kr','iPod docking station'),(136,226,'kr','High speed wireless'),(137,225,'kr','High speed internet access fee'),(138,246,'kr','High Definition (HD) Flat Panel Television  - 32 i'),(139,1,'th','Adjoining rooms'),(140,2,'th','Air conditioning'),(141,3,'th','Alarm clock'),(142,13,'th','Bathtub'),(143,14,'th','Bathtub only'),(144,15,'th','Bathtub/shower combination'),(145,19,'th','Coffee/Tea maker'),(146,20,'th','Color television'),(147,21,'th','Computer'),(148,28,'th','Desk'),(149,38,'th','Fax machine'),(150,51,'th','High speed internet connection'),(151,52,'th','Interactive web TV'),(152,53,'th','International direct dialing'),(153,54,'th','Internet access'),(154,61,'th','Kitchthette'),(155,64,'th','Large desk'),(156,68,'th','Microwave'),(157,69,'th','Minibar'),(158,83,'th','Prayer mats'),(159,92,'th','Safe'),(160,97,'th','Shower only'),(161,101,'th','Smoking'),(162,126,'th','Air conditioning individually controlled in room'),(163,123,'th','Wireless internet connection'),(164,133,'th','Desk with electrical outlet'),(165,142,'th','Shower'),(166,155,'th','Separate tub and shower'),(167,164,'th','Mini-refrigerator'),(168,163,'th','DVD player'),(169,169,'th','Collect calls'),(170,170,'th','International calls'),(171,171,'th','Carrier calls'),(172,172,'th','Interstate calls'),(173,173,'th','Intrastate calls'),(174,174,'th','Local calls'),(175,175,'th','Long distance calls'),(176,176,'th','Operator-assisted calls'),(177,177,'th','Credit card access calls'),(178,178,'th','Calling card calls'),(179,179,'th','Toll free calls'),(180,213,'th','DVR player'),(181,214,'th','iPod docking station'),(182,226,'th','High speed wireless'),(183,225,'th','High speed internet access fee'),(184,246,'th','High Definition (HD) Flat Panel Television  - 32 i'),(185,1,'vt','Adjoining rooms'),(186,2,'vt','Air conditioning'),(187,3,'vt','Alarm clock'),(188,13,'vt','Bathtub'),(189,14,'vt','Bathtub only'),(190,15,'vt','Bathtub/shower combination'),(191,19,'vt','Coffee/Tea maker'),(192,20,'vt','Color television'),(193,21,'vt','Computer'),(194,28,'vt','Desk'),(195,38,'vt','Fax machine'),(196,51,'vt','High speed internet connection'),(197,52,'vt','Interactive web TV'),(198,53,'vt','International direct dialing'),(199,54,'vt','Internet access'),(200,61,'vt','Kitchenette'),(201,64,'vt','Large desk'),(202,68,'vt','Microwave'),(203,69,'vt','Minibar'),(204,83,'vt','Prayer mats'),(205,92,'vt','Safe'),(206,97,'vt','Shower only'),(207,101,'vt','Smoking'),(208,126,'vt','Air conditioning individually controlled in room'),(209,123,'vt','Wireless internet connection'),(210,133,'vt','Desk with electrical outlet'),(211,142,'vt','Shower'),(212,155,'vt','Separate tub and shower'),(213,164,'vt','Mini-refrigerator'),(214,163,'vt','DVD player'),(215,169,'vt','Collect calls'),(216,170,'vt','International calls'),(217,171,'vt','Carrier calls'),(218,172,'vt','Interstate calls'),(219,173,'vt','Intrastate calls'),(220,174,'vt','Local calls'),(221,175,'vt','Long distance calls'),(222,176,'vt','Operator-assisted calls'),(223,177,'vt','Credit card access calls'),(224,178,'vt','Calling card calls'),(225,179,'vt','Toll free calls'),(226,213,'vt','DVR player'),(227,214,'vt','iPod docking station'),(228,226,'vt','High speed wireless'),(229,225,'vt','High speed internet access fee'),(230,246,'vt','High Definition (HD) Flat Panel Television  - 32 i'),(231,1,'zh-cn','Adjoining rooms'),(232,2,'zh-cn','&#31354;&#35843;'),(233,3,'zh-cn','Alarm clock'),(234,13,'zh-cn','Bathtub'),(235,14,'zh-cn','Bathtub only'),(236,15,'zh-cn','Bathtub/shower combination'),(237,19,'zh-cn','Coffee/Tea maker'),(238,20,'zh-cn','Color television'),(239,21,'zh-cn','&#30005;&#33041;'),(240,28,'zh-cn','Desk'),(241,38,'zh-cn','Fax machine'),(242,51,'zh-cn','High speed internet connection'),(243,52,'zh-cn','Interactive web TV'),(244,53,'zh-cn','International direct dialing'),(245,54,'zh-cn','Internet access'),(246,61,'zh-cn','Kitchenette'),(247,64,'zh-cn','Large desk'),(248,68,'zh-cn','Microwave'),(249,69,'zh-cn','Minibar'),(250,83,'zh-cn','Prayer mats'),(251,92,'zh-cn','Safe'),(252,97,'zh-cn','Shower only'),(253,101,'zh-cn','Smoking'),(254,126,'zh-cn','Air conditioning individually controlled in room'),(255,123,'zh-cn','Wireless internet connection'),(256,133,'zh-cn','Desk with electrical outlet'),(257,142,'zh-cn','Shower'),(258,155,'zh-cn','Separate tub and shower'),(259,164,'zh-cn','Mini-refrigerator'),(260,163,'zh-cn','DVD player'),(261,169,'zh-cn','Collect calls'),(262,170,'zh-cn','International calls'),(263,171,'zh-cn','Carrier calls'),(264,172,'zh-cn','Interstate calls'),(265,173,'zh-cn','Intrastate calls'),(266,174,'zh-cn','Local calls'),(267,175,'zh-cn','Long distance calls'),(268,176,'zh-cn','Operator-assisted calls'),(269,177,'zh-cn','Credit card access calls'),(270,178,'zh-cn','Calling card calls'),(271,179,'zh-cn','Toll free calls'),(272,213,'zh-cn','DVR player'),(273,214,'zh-cn','iPod docking station'),(274,226,'zh-cn','High speed wireless'),(275,225,'zh-cn','High speed internet access fee'),(276,246,'zh-cn','High Definition (HD) Flat Panel Television  - 32 i'),(277,1,'zh-hk','Adjoining rooms'),(278,2,'zh-hk','&#31354;&#35519;'),(279,3,'zh-hk','Alarm clock'),(280,13,'zh-hk','Bathtub'),(281,14,'zh-hk','Bathtub only'),(282,15,'zh-hk','Bathtub/shower combination'),(283,19,'zh-hk','Coffee/Tea maker'),(284,20,'zh-hk','Color television'),(285,21,'zh-hk','&#38651;&#33126;'),(286,28,'zh-hk','Desk'),(287,38,'zh-hk','Fax machine'),(288,51,'zh-hk','High speed internet connection'),(289,52,'zh-hk','Interactive web TV'),(290,53,'zh-hk','International direct dialing'),(291,54,'zh-hk','Internet access'),(292,61,'zh-hk','Kitchenette'),(293,64,'zh-hk','Large desk'),(294,68,'zh-hk','Microwave'),(295,69,'zh-hk','Minibar'),(296,83,'zh-hk','Prayer mats'),(297,92,'zh-hk','Safe'),(298,97,'zh-hk','Shower only'),(299,101,'zh-hk','Smoking'),(300,126,'zh-hk','Air conditioning individually controlled in room'),(301,123,'zh-hk','Wireless internet connection'),(302,133,'zh-hk','Desk with electrical outlet'),(303,142,'zh-hk','Shower'),(304,155,'zh-hk','Separate tub and shower'),(305,164,'zh-hk','Mini-refrigerator'),(306,163,'zh-hk','DVD player'),(307,169,'zh-hk','Collect calls'),(308,170,'zh-hk','International calls'),(309,171,'zh-hk','Carrier calls'),(310,172,'zh-hk','Interstate calls'),(311,173,'zh-hk','Intrastate calls'),(312,174,'zh-hk','Local calls'),(313,175,'zh-hk','Long distance calls'),(314,176,'zh-hk','Operator-assisted calls'),(315,177,'zh-hk','Credit card access calls'),(316,178,'zh-hk','Calling card calls'),(317,179,'zh-hk','Toll free calls'),(318,213,'zh-hk','DVR player'),(319,214,'zh-hk','iPod docking station'),(320,226,'zh-hk','High speed wireless'),(321,225,'zh-hk','High speed internet access fee'),(322,246,'zh-hk','High Definition (HD) Flat Panel Television  - 32 i'),(323,1,'zh-tw','Adjoining rooms'),(324,2,'zh-tw','&#31354;&#35519;'),(325,3,'zh-tw','Alarm clock'),(326,13,'zh-tw','Bathtub'),(327,14,'zh-tw','Bathtub only'),(328,15,'zh-tw','Bathtub/shower combination'),(329,19,'zh-tw','Coffee/Tea maker'),(330,20,'zh-tw','Color television'),(331,21,'zh-tw','&#38651;&#33126;'),(332,28,'zh-tw','Desk'),(333,38,'zh-tw','Fax machine'),(334,51,'zh-tw','High speed internet connection'),(335,52,'zh-tw','Interactive web TV'),(336,53,'zh-tw','International direct dialing'),(337,54,'zh-tw','Internet access'),(338,61,'zh-tw','Kitchenette'),(339,64,'zh-tw','Large desk'),(340,68,'zh-tw','Microwave'),(341,69,'zh-tw','Minibar'),(342,83,'zh-tw','Prayer mats'),(343,92,'zh-tw','Safe'),(344,97,'zh-tw','Shower only'),(345,101,'zh-tw','Smoking'),(346,126,'zh-tw','Air conditioning individually controlled in room'),(347,123,'zh-tw','Wireless internet connection'),(348,133,'zh-tw','Desk with electrical outlet'),(349,142,'zh-tw','Shower'),(350,155,'zh-tw','Separate tub and shower'),(351,164,'zh-tw','Mini-refrigerator'),(352,163,'zh-tw','DVD player'),(353,169,'zh-tw','Collect calls'),(354,170,'zh-tw','International calls'),(355,171,'zh-tw','Carrier calls'),(356,172,'zh-tw','Interstate calls'),(357,173,'zh-tw','Intrastate calls'),(358,174,'zh-tw','Local calls'),(359,175,'zh-tw','Long distance calls'),(360,176,'zh-tw','Operator-assisted calls'),(361,177,'zh-tw','Credit card access calls'),(362,178,'zh-tw','Calling card calls'),(363,179,'zh-tw','Toll free calls'),(364,213,'zh-tw','DVR player'),(365,214,'zh-tw','iPod docking station'),(366,226,'zh-tw','High speed wireless'),(367,225,'zh-tw','High speed internet access fee'),(368,246,'zh-tw','High Definition (HD) Flat Panel Television  - 32 i'),(369,1,'zh-sg','Adjoining rooms'),(370,2,'zh-sg','&#31354;&#35519;'),(371,3,'zh-sg','Alarm clock'),(372,13,'zh-sg','Bathtub'),(373,14,'zh-sg','Bathtub only'),(374,15,'zh-sg','Bathtub/shower combination'),(375,19,'zh-sg','Coffee/Tea maker'),(376,20,'zh-sg','Color television'),(377,21,'zh-sg','&#38651;&#33126;'),(378,28,'zh-sg','Desk'),(379,38,'zh-sg','Fax machine'),(380,51,'zh-sg','High speed internet connection'),(381,52,'zh-sg','Interactive web TV'),(382,53,'zh-sg','International direct dialing'),(383,54,'zh-sg','Internet access'),(384,61,'zh-sg','Kitchenette'),(385,64,'zh-sg','Large desk'),(386,68,'zh-sg','Microwave'),(387,69,'zh-sg','Minibar'),(388,83,'zh-sg','Prayer mats'),(389,92,'zh-sg','Safe'),(390,97,'zh-sg','Shower only'),(391,101,'zh-sg','Smoking'),(392,126,'zh-sg','Air conditioning individually controlled in room'),(393,123,'zh-sg','Wireless internet connection'),(394,133,'zh-sg','Desk with electrical outlet'),(395,142,'zh-sg','Shower'),(396,155,'zh-sg','Separate tub and shower'),(397,164,'zh-sg','Mini-refrigerator'),(398,163,'zh-sg','DVD player'),(399,169,'zh-sg','Collect calls'),(400,170,'zh-sg','International calls'),(401,171,'zh-sg','Carrier calls'),(402,172,'zh-sg','Interstate calls'),(403,173,'zh-sg','Intrastate calls'),(404,174,'zh-sg','Local calls'),(405,175,'zh-sg','Long distance calls'),(406,176,'zh-sg','Operator-assisted calls'),(407,177,'zh-sg','Credit card access calls'),(408,178,'zh-sg','Calling card calls'),(409,179,'zh-sg','Toll free calls'),(410,213,'zh-sg','DVR player'),(411,214,'zh-sg','iPod docking station'),(412,226,'zh-sg','High speed wireless'),(413,225,'zh-sg','High speed internet access fee'),(414,246,'zh-sg','High Definition (HD) Flat Panel Television  - 32 i');

INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (415,4,'en-us','All news channel'),(416,5,'en-us','AM/FM radio'),(417,6,'en-us','Baby listening device'),(418,7,'en-us','Balcony/Lanai/Terrace'),(419,8,'en-us','Barbeque grills'),(420,9,'en-us','Bath tub with spray jets'),(421,10,'en-us','Bathrobe'),(422,11,'en-us','Bathroom amenities'),(423,12,'en-us','Bathroom telephone'),(424,16,'en-us','Bidet'),(425,17,'en-us','Bottled water'),(426,18,'en-us','Cable television'),(427,22,'en-us','Connecting rooms'),(428,23,'en-us','Converters/ Voltage adaptors'),(429,24,'en-us','Copier'),(430,25,'en-us','Cordless phone'),(431,26,'en-us','Cribs'),(432,27,'en-us','Data port'),(433,29,'en-us','Desk with lamp'),(434,30,'en-us','Dining guide'),(435,31,'en-us','Direct dial phone number'),(436,32,'en-us','Dishwasher'),(437,33,'en-us','Double beds'),(438,34,'en-us','Dual voltage outlet'),(439,35,'en-us','Electrical current voltage'),(440,36,'en-us','Ergonomic chair'),(441,37,'en-us','Extended phone cord'),(442,39,'en-us','Fire alarm'),(443,40,'en-us','Fire alarm with light'),(444,41,'en-us','Fireplace'),(445,42,'en-us','Free toll free calls'),(446,43,'en-us','Free calls'),(447,44,'en-us','Free credit card access calls'),(448,45,'en-us','Free local calls'),(449,46,'en-us','Free movies/video'),(450,47,'en-us','Full kitchen'),(451,48,'en-us','Grab bars in bathroom'),(452,49,'en-us','Grecian tub'),(453,50,'en-us','Hairdryer'),(454,55,'en-us','Iron'),(455,56,'en-us','Ironing board'),(456,57,'en-us','Whirpool'),(457,58,'en-us','King bed'),(458,59,'en-us','Kitchen'),(459,60,'en-us','Kitchen supplies'),(460,62,'en-us','Knock light'),(461,63,'en-us','Laptop'),(462,65,'en-us','Large work area'),(463,66,'en-us','Laundry basket/clothes hamper'),(464,67,'en-us','Loft'),(465,70,'en-us','Modem'),(466,71,'en-us','Modem jack'),(467,72,'en-us','Multi-line phone'),(468,73,'en-us','Newspaper'),(469,74,'en-us','Non-smoking'),(470,75,'en-us','Notepads'),(471,76,'en-us','Office supplies'),(472,77,'en-us','Oven'),(473,78,'en-us','Pay per view movies on TV'),(474,79,'en-us','Pens'),(475,80,'en-us','Phone in bathroom'),(476,81,'en-us','Plates and bowls'),(477,82,'en-us','Pots and pans'),(478,84,'en-us','Printer'),(479,85,'en-us','Private bathroom'),(480,86,'en-us','Queen bed'),(481,87,'en-us','Recliner'),(482,88,'en-us','Refrigerator'),(483,89,'en-us','Refrigerator with ice maker'),(484,90,'en-us','Remote control television'),(485,91,'en-us','Rollaway bed'),(486,93,'en-us','Scanner'),(487,94,'en-us','Separate closet'),(488,95,'en-us','Separate modem line available'),(489,96,'en-us','Shoe polisher'),(490,98,'en-us','Silverware/utensils'),(491,99,'en-us','Sitting area'),(492,100,'en-us','Smoke detectors'),(493,102,'en-us','Sofa bed'),(494,103,'en-us','Speaker phone'),(495,104,'en-us','Stereo'),(496,105,'en-us','Stove'),(497,106,'en-us','Tape recorder'),(498,107,'en-us','Telephone'),(499,108,'en-us','Telephone for hearing impaired'),(500,109,'en-us','Telephones with message light'),(501,110,'en-us','Toaster oven'),(502,111,'en-us','Trouser/Pant press'),(503,112,'en-us','Turn down service'),(504,113,'en-us','Twin bed'),(505,114,'en-us','Vaulted ceilings'),(506,115,'en-us','VCR movies'),(507,116,'en-us','VCR player'),(508,117,'en-us','Video games'),(509,118,'en-us','Voice mail'),(510,119,'en-us','Wake-up calls'),(511,120,'en-us','Water closet'),(512,121,'en-us','Water purification system'),(513,122,'en-us','Wet bar'),(514,124,'en-us','Wireless keyboard'),(515,125,'en-us','Adaptor available for telephone PC use'),(516,127,'en-us','Bathtub &whirlpool separate'),(517,128,'en-us','Telephone with data ports'),(518,129,'en-us','CD  player'),(519,130,'en-us','Complimentary local calls time limit'),(520,131,'en-us','Extra person charge for rollaway use'),(521,132,'en-us','Down/feather pillows'),(522,134,'en-us','ESPN available'),(523,135,'en-us','Foam pillows'),(524,136,'en-us','HBO available'),(525,137,'en-us','High ceilings'),(526,138,'en-us','Marble bathroom'),(527,139,'en-us','List of movie channels available'),(528,140,'en-us','Pets allowed'),(529,141,'en-us','Oversized bathtub'),(530,143,'en-us','Sink in-room'),(531,144,'en-us','Soundproofed room'),(532,145,'en-us','Storage space'),(533,146,'en-us','Tables and chairs'),(534,147,'en-us','Two-line phone'),(535,148,'en-us','Walk-in closet'),(536,149,'en-us','Washer/dryer'),(537,150,'en-us','Weight scale'),(538,151,'en-us','Welcome gift'),(539,152,'en-us','Spare electrical outlet available at desk'),(540,153,'en-us','Non-refundable charge for pets'),(541,154,'en-us','Refundable deposit for pets'),(542,156,'en-us','Entrance type to guest room'),(543,157,'en-us','Ceiling fan'),(544,158,'en-us','CNN available'),(545,159,'en-us','Electrical adaptors available'),(546,160,'en-us','Buffet breakfast'),(547,161,'en-us','Accessible room'),(548,162,'en-us','Closets in room'),(549,165,'en-us','Separate line billing for multi-line phone'),(550,166,'en-us','Self-controlled heating/cooling system'),(551,167,'en-us','Toaster'),(552,168,'en-us','Analog data port'),(553,171,'en-us','Carrier access'),(554,180,'en-us','Universal AC/DC adaptors'),(555,181,'en-us','Bathtub seat'),(556,182,'en-us','Canopy/poster bed'),(557,183,'en-us','Cups/glassware'),(558,184,'en-us','Entertainment center'),(559,185,'en-us','Family/oversized room'),(560,186,'en-us','Hypoallergenic bed'),(561,187,'en-us','Hypoallergenic pillows'),(562,188,'en-us','Lamp'),(563,189,'en-us','Meal included - breakfast'),(564,190,'en-us','Meal included - continental breakfast'),(565,191,'en-us','Meal included - dinner'),(566,192,'en-us','Meal included - lunch'),(567,193,'en-us','Shared bathroom'),(568,194,'en-us','Telephone TDD/Textphone'),(569,195,'en-us','Water bed'),(570,196,'en-us','Extra adult charge'),(571,197,'en-us','Extra child charge'),(572,198,'en-us','Extra child charge for rollaway use'),(573,199,'en-us','Meal included:  full American breakfast'),(574,200,'en-us','Futon'),(575,201,'en-us','Murphy bed'),(576,202,'en-us','Tatami mats'),(577,203,'en-us','Single bed'),(578,204,'en-us','Annex room'),(579,205,'en-us','Free newspaper'),(580,206,'en-us','Honeymoon suites'),(581,207,'en-us','Complimentary high speed internet in room'),(582,208,'en-us','Maid service'),(583,209,'en-us','PC hook-up in room'),(584,210,'en-us','Satellite television'),(585,211,'en-us','VIP rooms'),(586,212,'en-us','Cell phone recharger'),(587,215,'en-us','Media center'),(588,216,'en-us','Plug & play panel'),(589,217,'en-us','Satellite radio'),(590,218,'en-us','Video on demand'),(591,219,'en-us','Exterior corridors'),(592,220,'en-us','Gulf view'),(593,222,'en-us','Interior corridors'),(594,223,'en-us','Mountain view'),(595,224,'en-us','Ocean view'),(596,227,'en-us','Premium movie channels'),(597,228,'en-us','Slippers'),(598,229,'en-us','First nighters\' kit'),(599,230,'en-us','Chair provided with desk'),(600,231,'en-us','Pillow top mattress'),(601,232,'en-us','Feather bed'),(602,233,'en-us','Duvet'),(603,234,'en-us','Luxury linen type'),(604,235,'en-us','International channels'),(605,236,'en-us','Pantry'),(606,237,'en-us','Dish-cleaning supplies'),(607,238,'en-us','Double vanity'),(608,239,'en-us','Lighted makeup mirror'),(609,240,'en-us','Upgraded bathroom amenities'),(610,241,'en-us','VCR player available at front desk'),(611,242,'en-us','Instant hot water'),(612,243,'en-us','Outdoor space'),(613,244,'en-us','Hinoki tub'),(614,245,'en-us','Private pool'),(615,247,'en-us','Room windows open'),(616,248,'en-us','Bedding type unknown or unspecified'),(617,249,'en-us','Full bed'),(618,250,'en-us','Round bed'),(619,251,'en-us','TV'),(620,252,'en-us','Child rollaway'),(621,253,'en-us','DVD player available at front desk'),(622,254,'en-us','Video game player:'),(623,255,'en-us','Video game player available at front desk'),(624,256,'en-us','Dining room seats'),(625,257,'en-us','Full size mirror'),(626,258,'en-us','Mobile/cellular phones'),(627,259,'en-us','Movies'),(628,260,'en-us','Multiple closets'),(629,261,'en-us','Plates/glassware'),(630,262,'en-us','Safe large enough to accommodate a laptop'),(631,263,'en-us','Bed linen thread count'),(632,264,'en-us','Blackout curtain'),(633,265,'en-us','Bluray player'),(634,266,'en-us','Device with mp3'),(635,267,'en-us','No adult channels or adult channel lock'),(636,268,'en-us','Non-allergenic room'),(637,269,'en-us','Pillow type'),(638,270,'en-us','Seating area with sofa/chair'),(639,271,'en-us','Separate toilet area'),(640,272,'en-us','Web enabled'),(641,273,'en-us','Widescreen TV'),(642,274,'en-us','Other data connection'),(643,275,'en-us','Phoneline billed separately'),(644,276,'en-us','Separate tub or shower'),(645,278,'en-us','Roof ventilator'),(646,279,'en-us','Children\'s playpen'),(647,280,'en-us','Plunge pool'),(648,4,'en','All news channel'),(649,5,'en','AM/FM radio'),(650,6,'en','Baby listening device'),(651,7,'en','Balcony/Lanai/Terrace'),(652,8,'en','Barbeque grills'),(653,9,'en','Bath tub with spray jets'),(654,10,'en','Bathrobe'),(655,11,'en','Bathroom amenities'),(656,12,'en','Bathroom telephone'),(657,16,'en','Bidet'),(658,17,'en','Bottled water'),(659,18,'en','Cable television'),(660,22,'en','Connecting rooms'),(661,23,'en','Converters/ Voltage adaptors'),(662,24,'en','Copier'),(663,25,'en','Cordless phone'),(664,26,'en','Cribs'),(665,27,'en','Data port'),(666,29,'en','Desk with lamp'),(667,30,'en','Dining guide'),(668,31,'en','Direct dial phone number'),(669,32,'en','Dishwasher'),(670,33,'en','Double beds'),(671,34,'en','Dual voltage outlet'),(672,35,'en','Electrical current voltage'),(673,36,'en','Ergonomic chair'),(674,37,'en','Extended phone cord'),(675,39,'en','Fire alarm'),(676,40,'en','Fire alarm with light'),(677,41,'en','Fireplace'),(678,42,'en','Free toll free calls'),(679,43,'en','Free calls'),(680,44,'en','Free credit card access calls'),(681,45,'en','Free local calls'),(682,46,'en','Free movies/video'),(683,47,'en','Full kitchen'),(684,48,'en','Grab bars in bathroom'),(685,49,'en','Grecian tub'),(686,50,'en','Hairdryer'),(687,55,'en','Iron'),(688,56,'en','Ironing board'),(689,57,'en','Whirpool'),(690,58,'en','King bed'),(691,59,'en','Kitchen'),(692,60,'en','Kitchen supplies'),(693,62,'en','Knock light'),(694,63,'en','Laptop'),(695,65,'en','Large work area'),(696,66,'en','Laundry basket/clothes hamper'),(697,67,'en','Loft'),(698,70,'en','Modem'),(699,71,'en','Modem jack'),(700,72,'en','Multi-line phone'),(701,73,'en','Newspaper'),(702,74,'en','Non-smoking'),(703,75,'en','Notepads'),(704,76,'en','Office supplies'),(705,77,'en','Oven'),(706,78,'en','Pay per view movies on TV'),(707,79,'en','Pens'),(708,80,'en','Phone in bathroom'),(709,81,'en','Plates and bowls'),(710,82,'en','Pots and pans'),(711,84,'en','Printer'),(712,85,'en','Private bathroom'),(713,86,'en','Queen bed'),(714,87,'en','Recliner'),(715,88,'en','Refrigerator'),(716,89,'en','Refrigerator with ice maker'),(717,90,'en','Remote control television'),(718,91,'en','Rollaway bed'),(719,93,'en','Scanner'),(720,94,'en','Separate closet'),(721,95,'en','Separate modem line available'),(722,96,'en','Shoe polisher'),(723,98,'en','Silverware/utensils'),(724,99,'en','Sitting area'),(725,100,'en','Smoke detectors'),(726,102,'en','Sofa bed'),(727,103,'en','Speaker phone'),(728,104,'en','Stereo'),(729,105,'en','Stove'),(730,106,'en','Tape recorder'),(731,107,'en','Telephone'),(732,108,'en','Telephone for hearing impaired'),(733,109,'en','Telephones with message light'),(734,110,'en','Toaster oven'),(735,111,'en','Trouser/Pant press'),(736,112,'en','Turn down service'),(737,113,'en','Twin bed'),(738,114,'en','Vaulted ceilings'),(739,115,'en','VCR movies'),(740,116,'en','VCR player'),(741,117,'en','Video games'),(742,118,'en','Voice mail'),(743,119,'en','Wake-up calls'),(744,120,'en','Water closet'),(745,121,'en','Water purification system'),(746,122,'en','Wet bar'),(747,124,'en','Wireless keyboard'),(748,125,'en','Adaptor available for telephone PC use'),(749,127,'en','Bathtub &whirlpool separate'),(750,128,'en','Telephone with data ports'),(751,129,'en','CD  player'),(752,130,'en','Complimentary local calls time limit'),(753,131,'en','Extra person charge for rollaway use'),(754,132,'en','Down/feather pillows'),(755,134,'en','ESPN available'),(756,135,'en','Foam pillows'),(757,136,'en','HBO available'),(758,137,'en','High ceilings'),(759,138,'en','Marble bathroom'),(760,139,'en','List of movie channels available'),(761,140,'en','Pets allowed'),(762,141,'en','Oversized bathtub'),(763,143,'en','Sink in-room'),(764,144,'en','Soundproofed room'),(765,145,'en','Storage space'),(766,146,'en','Tables and chairs'),(767,147,'en','Two-line phone'),(768,148,'en','Walk-in closet'),(769,149,'en','Washer/dryer'),(770,150,'en','Weight scale'),(771,151,'en','Welcome gift'),(772,152,'en','Spare electrical outlet available at desk'),(773,153,'en','Non-refundable charge for pets'),(774,154,'en','Refundable deposit for pets'),(775,156,'en','Entrance type to guest room'),(776,157,'en','Ceiling fan'),(777,158,'en','CNN available'),(778,159,'en','Electrical adaptors available'),(779,160,'en','Buffet breakfast'),(780,161,'en','Accessible room'),(781,162,'en','Closets in room'),(782,165,'en','Separate line billing for multi-line phone'),(783,166,'en','Self-controlled heating/cooling system'),(784,167,'en','Toaster'),(785,168,'en','Analog data port'),(786,171,'en','Carrier access'),(787,180,'en','Universal AC/DC adaptors'),(788,181,'en','Bathtub seat'),(789,182,'en','Canopy/poster bed'),(790,183,'en','Cups/glassware'),(791,184,'en','Entertainment center'),(792,185,'en','Family/oversized room'),(793,186,'en','Hypoallergenic bed'),(794,187,'en','Hypoallergenic pillows'),(795,188,'en','Lamp'),(796,189,'en','Meal included - breakfast'),(797,190,'en','Meal included - continental breakfast'),(798,191,'en','Meal included - dinner'),(799,192,'en','Meal included - lunch'),(800,193,'en','Shared bathroom'),(801,194,'en','Telephone TDD/Textphone'),(802,195,'en','Water bed'),(803,196,'en','Extra adult charge'),(804,197,'en','Extra child charge'),(805,198,'en','Extra child charge for rollaway use'),(806,199,'en','Meal included:  full American breakfast'),(807,200,'en','Futon'),(808,201,'en','Murphy bed'),(809,202,'en','Tatami mats'),(810,203,'en','Single bed'),(811,204,'en','Annex room'),(812,205,'en','Free newspaper'),(813,206,'en','Honeymoon suites'),(814,207,'en','Complimentary high speed internet in room'),(815,208,'en','Maid service'),(816,209,'en','PC hook-up in room'),(817,210,'en','Satellite television'),(818,211,'en','VIP rooms'),(819,212,'en','Cell phone recharger'),(820,215,'en','Media center'),(821,216,'en','Plug & play panel'),(822,217,'en','Satellite radio'),(823,218,'en','Video on demand'),(824,219,'en','Exterior corridors'),(825,220,'en','Gulf view'),(826,222,'en','Interior corridors'),(827,223,'en','Mountain view'),(828,224,'en','Ocean view'),(829,227,'en','Premium movie channels'),(830,228,'en','Slippers'),(831,229,'en','First nighters\' kit'),(832,230,'en','Chair provided with desk'),(833,231,'en','Pillow top mattress'),(834,232,'en','Feather bed'),(835,233,'en','Duvet'),(836,234,'en','Luxury linen type'),(837,235,'en','International channels'),(838,236,'en','Pantry'),(839,237,'en','Dish-cleaning supplies'),(840,238,'en','Double vanity'),(841,239,'en','Lighted makeup mirror'),(842,240,'en','Upgraded bathroom amenities'),(843,241,'en','VCR player available at front desk'),(844,242,'en','Instant hot water'),(845,243,'en','Outdoor space'),(846,244,'en','Hinoki tub'),(847,245,'en','Private pool'),(848,247,'en','Room windows open'),(849,248,'en','Bedding type unknown or unspecified'),(850,249,'en','Full bed'),(851,250,'en','Round bed'),(852,251,'en','TV'),(853,252,'en','Child rollaway'),(854,253,'en','DVD player available at front desk'),(855,254,'en','Video game player:'),(856,255,'en','Video game player available at front desk'),(857,256,'en','Dining room seats'),(858,257,'en','Full size mirror'),(859,258,'en','Mobile/cellular phones'),(860,259,'en','Movies'),(861,260,'en','Multiple closets'),(862,261,'en','Plates/glassware'),(863,262,'en','Safe large enough to accommodate a laptop'),(864,263,'en','Bed linen thread count'),(865,264,'en','Blackout curtain'),(866,265,'en','Bluray player'),(867,266,'en','Device with mp3'),(868,267,'en','No adult channels or adult channel lock'),(869,268,'en','Non-allergenic room'),(870,269,'en','Pillow type'),(871,270,'en','Seating area with sofa/chair'),(872,271,'en','Separate toilet area'),(873,272,'en','Web enabled'),(874,273,'en','Widescreen TV'),(875,274,'en','Other data connection'),(876,275,'en','Phoneline billed separately'),(877,276,'en','Separate tub or shower'),(878,278,'en','Roof ventilator'),(879,279,'en','Children\'s playpen'),(880,280,'en','Plunge pool'); 
INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (881,4,'kr','All news channel'),(882,5,'kr','AM/FM radio'),(883,6,'kr','Baby listening device'),(884,7,'kr','Balcony/Lanai/Terrace'),(885,8,'kr','Barbeque grills'),(886,9,'kr','Bath tub with spray jets'),(887,10,'kr','Bathrobe'),(888,11,'kr','Bathroom amenities'),(889,12,'kr','Bathroom telephone'),(890,16,'kr','Bidet'),(891,17,'kr','Bottled water'),(892,18,'kr','Cable television'),(893,22,'kr','Connecting rooms'),(894,23,'kr','Converters/ Voltage adaptors'),(895,24,'kr','Copier'),(896,25,'kr','Cordless phone'),(897,26,'kr','Cribs'),(898,27,'kr','Data port'),(899,29,'kr','Desk with lamp'),(900,30,'kr','Dining guide'),(901,31,'kr','Direct dial phone number'),(902,32,'kr','Dishwasher'),(903,33,'kr','Double beds'),(904,34,'kr','Dual voltage outlet'),(905,35,'kr','Electrical current voltage'),(906,36,'kr','Ergonomic chair'),(907,37,'kr','Extended phone cord'),(908,39,'kr','Fire alarm'),(909,40,'kr','Fire alarm with light'),(910,41,'kr','Fireplace'),(911,42,'kr','Free toll free calls'),(912,43,'kr','Free calls'),(913,44,'kr','Free credit card access calls'),(914,45,'kr','Free local calls'),(915,46,'kr','Free movies/video'),(916,47,'kr','Full kitchen'),(917,48,'kr','Grab bars in bathroom'),(918,49,'kr','Grecian tub'),(919,50,'kr','Hairdryer'),(920,55,'kr','Iron'),(921,56,'kr','Ironing board'),(922,57,'kr','Whirpool'),(923,58,'kr','King bed'),(924,59,'kr','Kitchen'),(925,60,'kr','Kitchen supplies'),(926,62,'kr','Knock light'),(927,63,'kr','Laptop'),(928,65,'kr','Large work area'),(929,66,'kr','Laundry basket/clothes hamper'),(930,67,'kr','Loft'),(931,70,'kr','Modem'),(932,71,'kr','Modem jack'),(933,72,'kr','Multi-line phone'),(934,73,'kr','Newspaper'),(935,74,'kr','Non-smoking'),(936,75,'kr','Notepads'),(937,76,'kr','Office supplies'),(938,77,'kr','Oven'),(939,78,'kr','Pay per view movies on TV'),(940,79,'kr','Pens'),(941,80,'kr','Phone in bathroom'),(942,81,'kr','Plates and bowls'),(943,82,'kr','Pots and pans'),(944,84,'kr','Printer'),(945,85,'kr','Private bathroom'),(946,86,'kr','Queen bed'),(947,87,'kr','Recliner'),(948,88,'kr','Refrigerator'),(949,89,'kr','Refrigerator with ice maker'),(950,90,'kr','Remote control television'),(951,91,'kr','Rollaway bed'),(952,93,'kr','Scanner'),(953,94,'kr','Separate closet'),(954,95,'kr','Separate modem line available'),(955,96,'kr','Shoe polisher'),(956,98,'kr','Silverware/utensils'),(957,99,'kr','Sitting area'),(958,100,'kr','Smoke detectors'),(959,102,'kr','Sofa bed'),(960,103,'kr','Speaker phone'),(961,104,'kr','Stereo'),(962,105,'kr','Stove'),(963,106,'kr','Tape recorder'),(964,107,'kr','Telephone'),(965,108,'kr','Telephone for hearing impaired'),(966,109,'kr','Telephones with message light'),(967,110,'kr','Toaster oven'),(968,111,'kr','Trouser/Pant press'),(969,112,'kr','Turn down service'),(970,113,'kr','Twin bed'),(971,114,'kr','Vaulted ceilings'),(972,115,'kr','VCR movies'),(973,116,'kr','VCR player'),(974,117,'kr','Video games'),(975,118,'kr','Voice mail'),(976,119,'kr','Wake-up calls'),(977,120,'kr','Water closet'),(978,121,'kr','Water purification system'),(979,122,'kr','Wet bar'),(980,124,'kr','Wireless keyboard'),(981,125,'kr','Adaptor available for telephone PC use'),(982,127,'kr','Bathtub &whirlpool separate'),(983,128,'kr','Telephone with data ports'),(984,129,'kr','CD  player'),(985,130,'kr','Complimentary local calls time limit'),(986,131,'kr','Extra person charge for rollaway use'),(987,132,'kr','Down/feather pillows'),(988,134,'kr','ESPN available'),(989,135,'kr','Foam pillows'),(990,136,'kr','HBO available'),(991,137,'kr','High ceilings'),(992,138,'kr','Marble bathroom'),(993,139,'kr','List of movie channels available'),(994,140,'kr','Pets allowed'),(995,141,'kr','Oversized bathtub'),(996,143,'kr','Sink in-room'),(997,144,'kr','Soundproofed room'),(998,145,'kr','Storage space'),(999,146,'kr','Tables and chairs'),(1000,147,'kr','Two-line phone'),(1001,148,'kr','Walk-in closet'),(1002,149,'kr','Washer/dryer'),(1003,150,'kr','Weight scale'),(1004,151,'kr','Welcome gift'),(1005,152,'kr','Spare electrical outlet available at desk'),(1006,153,'kr','Non-refundable charge for pets'),(1007,154,'kr','Refundable deposit for pets'),(1008,156,'kr','Entrance type to guest room'),(1009,157,'kr','Ceiling fan'),(1010,158,'kr','CNN available'),(1011,159,'kr','Electrical adaptors available'),(1012,160,'kr','Buffet breakfast'),(1013,161,'kr','Accessible room'),(1014,162,'kr','Closets in room'),(1015,165,'kr','Separate line billing for multi-line phone'),(1016,166,'kr','Self-controlled heating/cooling system'),(1017,167,'kr','Toaster'),(1018,168,'kr','Analog data port'),(1019,171,'kr','Carrier access'),(1020,180,'kr','Universal AC/DC adaptors'),(1021,181,'kr','Bathtub seat'),(1022,182,'kr','Canopy/poster bed'),(1023,183,'kr','Cups/glassware'),(1024,184,'kr','Entertainment center'),(1025,185,'kr','Family/oversized room'),(1026,186,'kr','Hypoallergenic bed'),(1027,187,'kr','Hypoallergenic pillows'),(1028,188,'kr','Lamp'),(1029,189,'kr','Meal included - breakfast'),(1030,190,'kr','Meal included - continental breakfast'),(1031,191,'kr','Meal included - dinner'),(1032,192,'kr','Meal included - lunch'),(1033,193,'kr','Shared bathroom'),(1034,194,'kr','Telephone TDD/Textphone'),(1035,195,'kr','Water bed'),(1036,196,'kr','Extra adult charge'),(1037,197,'kr','Extra child charge'),(1038,198,'kr','Extra child charge for rollaway use'),(1039,199,'kr','Meal included:  full American breakfast'),(1040,200,'kr','Futon'),(1041,201,'kr','Murphy bed'),(1042,202,'kr','Tatami mats'),(1043,203,'kr','Single bed'),(1044,204,'kr','Annex room'),(1045,205,'kr','Free newspaper'),(1046,206,'kr','Honeymoon suites'),(1047,207,'kr','Complimentary high speed internet in room'),(1048,208,'kr','Maid service'),(1049,209,'kr','PC hook-up in room'),(1050,210,'kr','Satellite television'),(1051,211,'kr','VIP rooms'),(1052,212,'kr','Cell phone recharger'),(1053,215,'kr','Media center'),(1054,216,'kr','Plug & play panel'),(1055,217,'kr','Satellite radio'),(1056,218,'kr','Video on demand'),(1057,219,'kr','Exterior corridors'),(1058,220,'kr','Gulf view'),(1059,222,'kr','Interior corridors'),(1060,223,'kr','Mountain view'),(1061,224,'kr','Ocean view'),(1062,227,'kr','Premium movie channels'),(1063,228,'kr','Slippers'),(1064,229,'kr','First nighters\' kit'),(1065,230,'kr','Chair provided with desk'),(1066,231,'kr','Pillow top mattress'),(1067,232,'kr','Feather bed'),(1068,233,'kr','Duvet'),(1069,234,'kr','Luxury linen type'),(1070,235,'kr','International channels'),(1071,236,'kr','Pantry'),(1072,237,'kr','Dish-cleaning supplies'),(1073,238,'kr','Double vanity'),(1074,239,'kr','Lighted makeup mirror'),(1075,240,'kr','Upgraded bathroom amenities'),(1076,241,'kr','VCR player available at front desk'),(1077,242,'kr','Instant hot water'),(1078,243,'kr','Outdoor space'),(1079,244,'kr','Hinoki tub'),(1080,245,'kr','Private pool'),(1081,247,'kr','Room windows open'),(1082,248,'kr','Bedding type unknown or unspecified'),(1083,249,'kr','Full bed'),(1084,250,'kr','Round bed'),(1085,251,'kr','TV'),(1086,252,'kr','Child rollaway'),(1087,253,'kr','DVD player available at front desk'),(1088,254,'kr','Video game player:'),(1089,255,'kr','Video game player available at front desk'),(1090,256,'kr','Dining room seats'),(1091,257,'kr','Full size mirror'),(1092,258,'kr','Mobile/cellular phones'),(1093,259,'kr','Movies'),(1094,260,'kr','Multiple closets'),(1095,261,'kr','Plates/glassware'),(1096,262,'kr','Safe large enough to accommodate a laptop'),(1097,263,'kr','Bed linen thread count'),(1098,264,'kr','Blackout curtain'),(1099,265,'kr','Bluray player'),(1100,266,'kr','Device with mp3'),(1101,267,'kr','No adult channels or adult channel lock'),(1102,268,'kr','Non-allergenic room'),(1103,269,'kr','Pillow type'),(1104,270,'kr','Seating area with sofa/chair'),(1105,271,'kr','Separate toilet area'),(1106,272,'kr','Web enabled'),(1107,273,'kr','Widescreen TV'),(1108,274,'kr','Other data connection'),(1109,275,'kr','Phoneline billed separately'),(1110,276,'kr','Separate tub or shower'),(1111,278,'kr','Roof ventilator'),(1112,279,'kr','Children\'s playpen'),(1113,280,'kr','Plunge pool'),(1114,4,'th','All news channel'),(1115,5,'th','AM/FM radio'),(1116,6,'th','Baby listening device'),(1117,7,'th','Balcony/Lanai/Terrace'),(1118,8,'th','Barbeque grills'),(1119,9,'th','Bath tub with spray jets'),(1120,10,'th','Bathrobe'),(1121,11,'th','Bathroom amenities'),(1122,12,'th','Bathroom telephone'),(1123,16,'th','Bidet'),(1124,17,'th','Bottled water'),(1125,18,'th','Cable television'),(1126,22,'th','Connecting rooms'),(1127,23,'th','Converters/ Voltage adaptors'),(1128,24,'th','Copier'),(1129,25,'th','Cordless phone'),(1130,26,'th','Cribs'),(1131,27,'th','Data port'),(1132,29,'th','Desk with lamp'),(1133,30,'th','Dining guide'),(1134,31,'th','Direct dial phone number'),(1135,32,'th','Dishwasher'),(1136,33,'th','Double beds'),(1137,34,'th','Dual voltage outlet'),(1138,35,'th','Electrical current voltage'),(1139,36,'th','Ergonomic chair'),(1140,37,'th','Extended phone cord'),(1141,39,'th','Fire alarm'),(1142,40,'th','Fire alarm with light'),(1143,41,'th','Fireplace'),(1144,42,'th','Free toll free calls'),(1145,43,'th','Free calls'),(1146,44,'th','Free credit card access calls'),(1147,45,'th','Free local calls'),(1148,46,'th','Free movies/video'),(1149,47,'th','Full kitchen'),(1150,48,'th','Grab bars in bathroom'),(1151,49,'th','Grecian tub'),(1152,50,'th','Hairdryer'),(1153,55,'th','Iron'),(1154,56,'th','Ironing board'),(1155,57,'th','Whirpool'),(1156,58,'th','King bed'),(1157,59,'th','Kitchen'),(1158,60,'th','Kitchen supplies'),(1159,62,'th','Knock light'),(1160,63,'th','Laptop'),(1161,65,'th','Large work area'),(1162,66,'th','Laundry basket/clothes hamper'),(1163,67,'th','Loft'),(1164,70,'th','Modem'),(1165,71,'th','Modem jack'),(1166,72,'th','Multi-line phone'),(1167,73,'th','Newspaper'),(1168,74,'th','Non-smoking'),(1169,75,'th','Notepads'),(1170,76,'th','Office supplies'),(1171,77,'th','Oven'),(1172,78,'th','Pay per view movies on TV'),(1173,79,'th','Pens'),(1174,80,'th','Phone in bathroom'),(1175,81,'th','Plates and bowls'),(1176,82,'th','Pots and pans'),(1177,84,'th','Printer'),(1178,85,'th','Private bathroom'),(1179,86,'th','Queen bed'),(1180,87,'th','Recliner'),(1181,88,'th','Refrigerator'),(1182,89,'th','Refrigerator with ice maker'),(1183,90,'th','Remote control television'),(1184,91,'th','Rollaway bed'),(1185,93,'th','Scanner'),(1186,94,'th','Separate closet'),(1187,95,'th','Separate modem line available'),(1188,96,'th','Shoe polisher'),(1189,98,'th','Silverware/utensils'),(1190,99,'th','Sitting area'),(1191,100,'th','Smoke detectors'),(1192,102,'th','Sofa bed'),(1193,103,'th','Speaker phone'),(1194,104,'th','Stereo'),(1195,105,'th','Stove'),(1196,106,'th','Tape recorder'),(1197,107,'th','Telephone'),(1198,108,'th','Telephone for hearing impaired'),(1199,109,'th','Telephones with message light'),(1200,110,'th','Toaster oven'),(1201,111,'th','Trouser/Pant press'),(1202,112,'th','Turn down service'),(1203,113,'th','Twin bed'),(1204,114,'th','Vaulted ceilings'),(1205,115,'th','VCR movies'),(1206,116,'th','VCR player'),(1207,117,'th','Video games'),(1208,118,'th','Voice mail'),(1209,119,'th','Wake-up calls'),(1210,120,'th','Water closet'),(1211,121,'th','Water purification system'),(1212,122,'th','Wet bar'),(1213,124,'th','Wireless keyboard'),(1214,125,'th','Adaptor available for telephone PC use'),(1215,127,'th','Bathtub &whirlpool separate'),(1216,128,'th','Telephone with data ports'),(1217,129,'th','CD  player'),(1218,130,'th','Complimentary local calls time limit'),(1219,131,'th','Extra person charge for rollaway use'),(1220,132,'th','Down/feather pillows'),(1221,134,'th','ESPN available'),(1222,135,'th','Foam pillows'),(1223,136,'th','HBO available'),(1224,137,'th','High ceilings'),(1225,138,'th','Marble bathroom'),(1226,139,'th','List of movie channels available'),(1227,140,'th','Pets allowed'),(1228,141,'th','Oversized bathtub'),(1229,143,'th','Sink in-room'),(1230,144,'th','Soundproofed room'),(1231,145,'th','Storage space'),(1232,146,'th','Tables and chairs'),(1233,147,'th','Two-line phone'),(1234,148,'th','Walk-in closet'),(1235,149,'th','Washer/dryer'),(1236,150,'th','Weight scale'),(1237,151,'th','Welcome gift'),(1238,152,'th','Spare electrical outlet available at desk'),(1239,153,'th','Non-refundable charge for pets'),(1240,154,'th','Refundable deposit for pets'),(1241,156,'th','Entrance type to guest room'),(1242,157,'th','Ceiling fan'),(1243,158,'th','CNN available'),(1244,159,'th','Electrical adaptors available'),(1245,160,'th','Buffet breakfast'),(1246,161,'th','Accessible room'),(1247,162,'th','Closets in room'),(1248,165,'th','Separate line billing for multi-line phone'),(1249,166,'th','Self-controlled heating/cooling system'),(1250,167,'th','Toaster'),(1251,168,'th','Analog data port'),(1252,171,'th','Carrier access'),(1253,180,'th','Universal AC/DC adaptors'),(1254,181,'th','Bathtub seat'),(1255,182,'th','Canopy/poster bed'),(1256,183,'th','Cups/glassware'),(1257,184,'th','Entertainment center'),(1258,185,'th','Family/oversized room'),(1259,186,'th','Hypoallergenic bed'),(1260,187,'th','Hypoallergenic pillows'),(1261,188,'th','Lamp'),(1262,189,'th','Meal included - breakfast'),(1263,190,'th','Meal included - continental breakfast'),(1264,191,'th','Meal included - dinner'),(1265,192,'th','Meal included - lunch'),(1266,193,'th','Shared bathroom'),(1267,194,'th','Telephone TDD/Textphone'),(1268,195,'th','Water bed'),(1269,196,'th','Extra adult charge'),(1270,197,'th','Extra child charge'),(1271,198,'th','Extra child charge for rollaway use'),(1272,199,'th','Meal included:  full American breakfast'),(1273,200,'th','Futon'),(1274,201,'th','Murphy bed'),(1275,202,'th','Tatami mats'),(1276,203,'th','Single bed'),(1277,204,'th','Annex room'),(1278,205,'th','Free newspaper'),(1279,206,'th','Honeymoon suites'),(1280,207,'th','Complimentary high speed internet in room'),(1281,208,'th','Maid service'),(1282,209,'th','PC hook-up in room'),(1283,210,'th','Satellite television'),(1284,211,'th','VIP rooms'),(1285,212,'th','Cell phone recharger'),(1286,215,'th','Media center'),(1287,216,'th','Plug & play panel'),(1288,217,'th','Satellite radio'),(1289,218,'th','Video on demand'),(1290,219,'th','Exterior corridors'),(1291,220,'th','Gulf view'),(1292,222,'th','Interior corridors'),(1293,223,'th','Mountain view'),(1294,224,'th','Ocean view'),(1295,227,'th','Premium movie channels'),(1296,228,'th','Slippers'),(1297,229,'th','First nighters\' kit'),(1298,230,'th','Chair provided with desk'),(1299,231,'th','Pillow top mattress'),(1300,232,'th','Feather bed'),(1301,233,'th','Duvet'),(1302,234,'th','Luxury linen type'),(1303,235,'th','International channels'),(1304,236,'th','Pantry'),(1305,237,'th','Dish-cleaning supplies'),(1306,238,'th','Double vanity'),(1307,239,'th','Lighted makeup mirror'),(1308,240,'th','Upgraded bathroom amenities'),(1309,241,'th','VCR player available at front desk'),(1310,242,'th','Instant hot water'),(1311,243,'th','Outdoor space'),(1312,244,'th','Hinoki tub'),(1313,245,'th','Private pool'),(1314,247,'th','Room windows open'),(1315,248,'th','Bedding type unknown or unspecified'),(1316,249,'th','Full bed'),(1317,250,'th','Round bed'),(1318,251,'th','TV'),(1319,252,'th','Child rollaway'),(1320,253,'th','DVD player available at front desk'),(1321,254,'th','Video game player:'),(1322,255,'th','Video game player available at front desk'),(1323,256,'th','Dining room seats'),(1324,257,'th','Full size mirror'),(1325,258,'th','Mobile/cellular phones'),(1326,259,'th','Movies'),(1327,260,'th','Multiple closets'),(1328,261,'th','Plates/glassware'),(1329,262,'th','Safe large enough to accommodate a laptop'),(1330,263,'th','Bed linen thread count'),(1331,264,'th','Blackout curtain'),(1332,265,'th','Bluray player'),(1333,266,'th','Device with mp3'),(1334,267,'th','No adult channels or adult channel lock'),(1335,268,'th','Non-allergenic room'),(1336,269,'th','Pillow type'),(1337,270,'th','Seating area with sofa/chair'),(1338,271,'th','Separate toilet area'),(1339,272,'th','Web enabled'),(1340,273,'th','Widescreen TV'),(1341,274,'th','Other data connection'),(1342,275,'th','Phoneline billed separately'),(1343,276,'th','Separate tub or shower'),(1344,278,'th','Roof ventilator'),(1345,279,'th','Children\'s playpen'),(1346,280,'th','Plunge pool');
INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (1347,4,'vt','All news channel'),(1348,5,'vt','AM/FM radio'),(1349,6,'vt','Baby listening device'),(1350,7,'vt','Balcony/Lanai/Terrace'),(1351,8,'vt','Barbeque grills'),(1352,9,'vt','Bath tub with spray jets'),(1353,10,'vt','Bathrobe'),(1354,11,'vt','Bathroom amenities'),(1355,12,'vt','Bathroom telephone'),(1356,16,'vt','Bidet'),(1357,17,'vt','Bottled water'),(1358,18,'vt','Cable television'),(1359,22,'vt','Connecting rooms'),(1360,23,'vt','Converters/ Voltage adaptors'),(1361,24,'vt','Copier'),(1362,25,'vt','Cordless phone'),(1363,26,'vt','Cribs'),(1364,27,'vt','Data port'),(1365,29,'vt','Desk with lamp'),(1366,30,'vt','Dining guide'),(1367,31,'vt','Direct dial phone number'),(1368,32,'vt','Dishwasher'),(1369,33,'vt','Double beds'),(1370,34,'vt','Dual voltage outlet'),(1371,35,'vt','Electrical current voltage'),(1372,36,'vt','Ergonomic chair'),(1373,37,'vt','Extended phone cord'),(1374,39,'vt','Fire alarm'),(1375,40,'vt','Fire alarm with light'),(1376,41,'vt','Fireplace'),(1377,42,'vt','Free toll free calls'),(1378,43,'vt','Free calls'),(1379,44,'vt','Free credit card access calls'),(1380,45,'vt','Free local calls'),(1381,46,'vt','Free movies/video'),(1382,47,'vt','Full kitchen'),(1383,48,'vt','Grab bars in bathroom'),(1384,49,'vt','Grecian tub'),(1385,50,'vt','Hairdryer'),(1386,55,'vt','Iron'),(1387,56,'vt','Ironing board'),(1388,57,'vt','Whirpool'),(1389,58,'vt','King bed'),(1390,59,'vt','Kitchen'),(1391,60,'vt','Kitchen supplies'),(1392,62,'vt','Knock light'),(1393,63,'vt','Laptop'),(1394,65,'vt','Large work area'),(1395,66,'vt','Laundry basket/clothes hamper'),(1396,67,'vt','Loft'),(1397,70,'vt','Modem'),(1398,71,'vt','Modem jack'),(1399,72,'vt','Multi-line phone'),(1400,73,'vt','Newspaper'),(1401,74,'vt','Non-smoking'),(1402,75,'vt','Notepads'),(1403,76,'vt','Office supplies'),(1404,77,'vt','Oven'),(1405,78,'vt','Pay per view movies on TV'),(1406,79,'vt','Pens'),(1407,80,'vt','Phone in bathroom'),(1408,81,'vt','Plates and bowls'),(1409,82,'vt','Pots and pans'),(1410,84,'vt','Printer'),(1411,85,'vt','Private bathroom'),(1412,86,'vt','Queen bed'),(1413,87,'vt','Recliner'),(1414,88,'vt','Refrigerator'),(1415,89,'vt','Refrigerator with ice maker'),(1416,90,'vt','Remote control television'),(1417,91,'vt','Rollaway bed'),(1418,93,'vt','Scanner'),(1419,94,'vt','Separate closet'),(1420,95,'vt','Separate modem line available'),(1421,96,'vt','Shoe polisher'),(1422,98,'vt','Silverware/utensils'),(1423,99,'vt','Sitting area'),(1424,100,'vt','Smoke detectors'),(1425,102,'vt','Sofa bed'),(1426,103,'vt','Speaker phone'),(1427,104,'vt','Stereo'),(1428,105,'vt','Stove'),(1429,106,'vt','Tape recorder'),(1430,107,'vt','Telephone'),(1431,108,'vt','Telephone for hearing impaired'),(1432,109,'vt','Telephones with message light'),(1433,110,'vt','Toaster oven'),(1434,111,'vt','Trouser/Pant press'),(1435,112,'vt','Turn down service'),(1436,113,'vt','Twin bed'),(1437,114,'vt','Vaulted ceilings'),(1438,115,'vt','VCR movies'),(1439,116,'vt','VCR player'),(1440,117,'vt','Video games'),(1441,118,'vt','Voice mail'),(1442,119,'vt','Wake-up calls'),(1443,120,'vt','Water closet'),(1444,121,'vt','Water purification system'),(1445,122,'vt','Wet bar'),(1446,124,'vt','Wireless keyboard'),(1447,125,'vt','Adaptor available for telephone PC use'),(1448,127,'vt','Bathtub &whirlpool separate'),(1449,128,'vt','Telephone with data ports'),(1450,129,'vt','CD  player'),(1451,130,'vt','Complimentary local calls time limit'),(1452,131,'vt','Extra person charge for rollaway use'),(1453,132,'vt','Down/feather pillows'),(1454,134,'vt','ESPN available'),(1455,135,'vt','Foam pillows'),(1456,136,'vt','HBO available'),(1457,137,'vt','High ceilings'),(1458,138,'vt','Marble bathroom'),(1459,139,'vt','List of movie channels available'),(1460,140,'vt','Pets allowed'),(1461,141,'vt','Oversized bathtub'),(1462,143,'vt','Sink in-room'),(1463,144,'vt','Soundproofed room'),(1464,145,'vt','Storage space'),(1465,146,'vt','Tables and chairs'),(1466,147,'vt','Two-line phone'),(1467,148,'vt','Walk-in closet'),(1468,149,'vt','Washer/dryer'),(1469,150,'vt','Weight scale'),(1470,151,'vt','Welcome gift'),(1471,152,'vt','Spare electrical outlet available at desk'),(1472,153,'vt','Non-refundable charge for pets'),(1473,154,'vt','Refundable deposit for pets'),(1474,156,'vt','Entrance type to guest room'),(1475,157,'vt','Ceiling fan'),(1476,158,'vt','CNN available'),(1477,159,'vt','Electrical adaptors available'),(1478,160,'vt','Buffet breakfast'),(1479,161,'vt','Accessible room'),(1480,162,'vt','Closets in room'),(1481,165,'vt','Separate line billing for multi-line phone'),(1482,166,'vt','Self-controlled heating/cooling system'),(1483,167,'vt','Toaster'),(1484,168,'vt','Analog data port'),(1485,171,'vt','Carrier access'),(1486,180,'vt','Universal AC/DC adaptors'),(1487,181,'vt','Bathtub seat'),(1488,182,'vt','Canopy/poster bed'),(1489,183,'vt','Cups/glassware'),(1490,184,'vt','Entertainment center'),(1491,185,'vt','Family/oversized room'),(1492,186,'vt','Hypoallergenic bed'),(1493,187,'vt','Hypoallergenic pillows'),(1494,188,'vt','Lamp'),(1495,189,'vt','Meal included - breakfast'),(1496,190,'vt','Meal included - continental breakfast'),(1497,191,'vt','Meal included - dinner'),(1498,192,'vt','Meal included - lunch'),(1499,193,'vt','Shared bathroom'),(1500,194,'vt','Telephone TDD/Textphone'),(1501,195,'vt','Water bed'),(1502,196,'vt','Extra adult charge'),(1503,197,'vt','Extra child charge'),(1504,198,'vt','Extra child charge for rollaway use'),(1505,199,'vt','Meal included:  full American breakfast'),(1506,200,'vt','Futon'),(1507,201,'vt','Murphy bed'),(1508,202,'vt','Tatami mats'),(1509,203,'vt','Single bed'),(1510,204,'vt','Annex room'),(1511,205,'vt','Free newspaper'),(1512,206,'vt','Honeymoon suites'),(1513,207,'vt','Complimentary high speed internet in room'),(1514,208,'vt','Maid service'),(1515,209,'vt','PC hook-up in room'),(1516,210,'vt','Satellite television'),(1517,211,'vt','VIP rooms'),(1518,212,'vt','Cell phone recharger'),(1519,215,'vt','Media center'),(1520,216,'vt','Plug & play panel'),(1521,217,'vt','Satellite radio'),(1522,218,'vt','Video on demand'),(1523,219,'vt','Exterior corridors'),(1524,220,'vt','Gulf view'),(1525,222,'vt','Interior corridors'),(1526,223,'vt','Mountain view'),(1527,224,'vt','Ocean view'),(1528,227,'vt','Premium movie channels'),(1529,228,'vt','Slippers'),(1530,229,'vt','First nighters\' kit'),(1531,230,'vt','Chair provided with desk'),(1532,231,'vt','Pillow top mattress'),(1533,232,'vt','Feather bed'),(1534,233,'vt','Duvet'),(1535,234,'vt','Luxury linen type'),(1536,235,'vt','International channels'),(1537,236,'vt','Pantry'),(1538,237,'vt','Dish-cleaning supplies'),(1539,238,'vt','Double vanity'),(1540,239,'vt','Lighted makeup mirror'),(1541,240,'vt','Upgraded bathroom amenities'),(1542,241,'vt','VCR player available at front desk'),(1543,242,'vt','Instant hot water'),(1544,243,'vt','Outdoor space'),(1545,244,'vt','Hinoki tub'),(1546,245,'vt','Private pool'),(1547,247,'vt','Room windows open'),(1548,248,'vt','Bedding type unknown or unspecified'),(1549,249,'vt','Full bed'),(1550,250,'vt','Round bed'),(1551,251,'vt','TV'),(1552,252,'vt','Child rollaway'),(1553,253,'vt','DVD player available at front desk'),(1554,254,'vt','Video game player:'),(1555,255,'vt','Video game player available at front desk'),(1556,256,'vt','Dining room seats'),(1557,257,'vt','Full size mirror'),(1558,258,'vt','Mobile/cellular phones'),(1559,259,'vt','Movies'),(1560,260,'vt','Multiple closets'),(1561,261,'vt','Plates/glassware'),(1562,262,'vt','Safe large enough to accommodate a laptop'),(1563,263,'vt','Bed linen thread count'),(1564,264,'vt','Blackout curtain'),(1565,265,'vt','Bluray player'),(1566,266,'vt','Device with mp3'),(1567,267,'vt','No adult channels or adult channel lock'),(1568,268,'vt','Non-allergenic room'),(1569,269,'vt','Pillow type'),(1570,270,'vt','Seating area with sofa/chair'),(1571,271,'vt','Separate toilet area'),(1572,272,'vt','Web enabled'),(1573,273,'vt','Widescreen TV'),(1574,274,'vt','Other data connection'),(1575,275,'vt','Phoneline billed separately'),(1576,276,'vt','Separate tub or shower'),(1577,278,'vt','Roof ventilator'),(1578,279,'vt','Children\'s playpen'),(1579,280,'vt','Plunge pool'),(1580,4,'zh-cn','All news channel'),(1581,5,'zh-cn','AM/FM radio'),(1582,6,'zh-cn','Baby listening device'),(1583,7,'zh-cn','Balcony/Lanai/Terrace'),(1584,8,'zh-cn','Barbeque grills'),(1585,9,'zh-cn','Bath tub with spray jets'),(1586,10,'zh-cn','Bathrobe'),(1587,11,'zh-cn','Bathroom amenities'),(1588,12,'zh-cn','Bathroom telephone'),(1589,16,'zh-cn','Bidet'),(1590,17,'zh-cn','Bottled water'),(1591,18,'zh-cn','Cable television'),(1592,22,'zh-cn','Connecting rooms'),(1593,23,'zh-cn','Converters/ Voltage adaptors'),(1594,24,'zh-cn','Copier'),(1595,25,'zh-cn','Cordless phone'),(1596,26,'zh-cn','Cribs'),(1597,27,'zh-cn','Data port'),(1598,29,'zh-cn','Desk with lamp'),(1599,30,'zh-cn','Dining guide'),(1600,31,'zh-cn','Direct dial phone number'),(1601,32,'zh-cn','Dishwasher'),(1602,33,'zh-cn','Double beds'),(1603,34,'zh-cn','Dual voltage outlet'),(1604,35,'zh-cn','Electrical current voltage'),(1605,36,'zh-cn','Ergonomic chair'),(1606,37,'zh-cn','Extended phone cord'),(1607,39,'zh-cn','Fire alarm'),(1608,40,'zh-cn','Fire alarm with light'),(1609,41,'zh-cn','Fireplace'),(1610,42,'zh-cn','Free toll free calls'),(1611,43,'zh-cn','Free calls'),(1612,44,'zh-cn','Free credit card access calls'),(1613,45,'zh-cn','Free local calls'),(1614,46,'zh-cn','Free movies/video'),(1615,47,'zh-cn','Full kitchen'),(1616,48,'zh-cn','Grab bars in bathroom'),(1617,49,'zh-cn','Grecian tub'),(1618,50,'zh-cn','Hairdryer'),(1619,55,'zh-cn','Iron'),(1620,56,'zh-cn','Ironing board'),(1621,57,'zh-cn','Whirpool'),(1622,58,'zh-cn','King bed'),(1623,59,'zh-cn','Kitchen'),(1624,60,'zh-cn','Kitchen supplies'),(1625,62,'zh-cn','Knock light'),(1626,63,'zh-cn','Laptop'),(1627,65,'zh-cn','Large work area'),(1628,66,'zh-cn','Laundry basket/clothes hamper'),(1629,67,'zh-cn','Loft'),(1630,70,'zh-cn','Modem'),(1631,71,'zh-cn','Modem jack'),(1632,72,'zh-cn','Multi-line phone'),(1633,73,'zh-cn','Newspaper'),(1634,74,'zh-cn','Non-smoking'),(1635,75,'zh-cn','Notepads'),(1636,76,'zh-cn','Office supplies'),(1637,77,'zh-cn','Oven'),(1638,78,'zh-cn','Pay per view movies on TV'),(1639,79,'zh-cn','Pens'),(1640,80,'zh-cn','Phone in bathroom'),(1641,81,'zh-cn','Plates and bowls'),(1642,82,'zh-cn','Pots and pans'),(1643,84,'zh-cn','Printer'),(1644,85,'zh-cn','Private bathroom'),(1645,86,'zh-cn','Queen bed'),(1646,87,'zh-cn','Recliner'),(1647,88,'zh-cn','Refrigerator'),(1648,89,'zh-cn','Refrigerator with ice maker'),(1649,90,'zh-cn','Remote control television'),(1650,91,'zh-cn','Rollaway bed'),(1651,93,'zh-cn','Scanner'),(1652,94,'zh-cn','Separate closet'),(1653,95,'zh-cn','Separate modem line available'),(1654,96,'zh-cn','Shoe polisher'),(1655,98,'zh-cn','Silverware/utensils'),(1656,99,'zh-cn','Sitting area'),(1657,100,'zh-cn','Smoke detectors'),(1658,102,'zh-cn','Sofa bed'),(1659,103,'zh-cn','Speaker phone'),(1660,104,'zh-cn','Stereo'),(1661,105,'zh-cn','Stove'),(1662,106,'zh-cn','Tape recorder'),(1663,107,'zh-cn','Telephone'),(1664,108,'zh-cn','Telephone for hearing impaired'),(1665,109,'zh-cn','Telephones with message light'),(1666,110,'zh-cn','Toaster oven'),(1667,111,'zh-cn','Trouser/Pant press'),(1668,112,'zh-cn','Turn down service'),(1669,113,'zh-cn','Twin bed'),(1670,114,'zh-cn','Vaulted ceilings'),(1671,115,'zh-cn','VCR movies'),(1672,116,'zh-cn','VCR player'),(1673,117,'zh-cn','Video games'),(1674,118,'zh-cn','Voice mail'),(1675,119,'zh-cn','Wake-up calls'),(1676,120,'zh-cn','Water closet'),(1677,121,'zh-cn','Water purification system'),(1678,122,'zh-cn','Wet bar'),(1679,124,'zh-cn','Wireless keyboard'),(1680,125,'zh-cn','Adaptor available for telephone PC use'),(1681,127,'zh-cn','Bathtub &whirlpool separate'),(1682,128,'zh-cn','Telephone with data ports'),(1683,129,'zh-cn','CD  player'),(1684,130,'zh-cn','Complimentary local calls time limit'),(1685,131,'zh-cn','Extra person charge for rollaway use'),(1686,132,'zh-cn','Down/feather pillows'),(1687,134,'zh-cn','ESPN available'),(1688,135,'zh-cn','Foam pillows'),(1689,136,'zh-cn','HBO available'),(1690,137,'zh-cn','High ceilings'),(1691,138,'zh-cn','Marble bathroom'),(1692,139,'zh-cn','List of movie channels available'),(1693,140,'zh-cn','Pets allowed'),(1694,141,'zh-cn','Oversized bathtub'),(1695,143,'zh-cn','Sink in-room'),(1696,144,'zh-cn','Soundproofed room'),(1697,145,'zh-cn','Storage space'),(1698,146,'zh-cn','Tables and chairs'),(1699,147,'zh-cn','Two-line phone'),(1700,148,'zh-cn','Walk-in closet'),(1701,149,'zh-cn','Washer/dryer'),(1702,150,'zh-cn','Weight scale'),(1703,151,'zh-cn','Welcome gift'),(1704,152,'zh-cn','Spare electrical outlet available at desk'),(1705,153,'zh-cn','Non-refundable charge for pets'),(1706,154,'zh-cn','Refundable deposit for pets'),(1707,156,'zh-cn','Entrance type to guest room'),(1708,157,'zh-cn','Ceiling fan'),(1709,158,'zh-cn','CNN available'),(1710,159,'zh-cn','Electrical adaptors available'),(1711,160,'zh-cn','Buffet breakfast'),(1712,161,'zh-cn','Accessible room'),(1713,162,'zh-cn','Closets in room'),(1714,165,'zh-cn','Separate line billing for multi-line phone'),(1715,166,'zh-cn','Self-controlled heating/cooling system'),(1716,167,'zh-cn','Toaster'),(1717,168,'zh-cn','Analog data port'),(1718,171,'zh-cn','Carrier access'),(1719,180,'zh-cn','Universal AC/DC adaptors'),(1720,181,'zh-cn','Bathtub seat'),(1721,182,'zh-cn','Canopy/poster bed'),(1722,183,'zh-cn','Cups/glassware'),(1723,184,'zh-cn','Entertainment center'),(1724,185,'zh-cn','Family/oversized room'),(1725,186,'zh-cn','Hypoallergenic bed'),(1726,187,'zh-cn','Hypoallergenic pillows'),(1727,188,'zh-cn','Lamp'),(1728,189,'zh-cn','Meal included - breakfast'),(1729,190,'zh-cn','Meal included - continental breakfast'),(1730,191,'zh-cn','Meal included - dinner'),(1731,192,'zh-cn','Meal included - lunch'),(1732,193,'zh-cn','Shared bathroom'),(1733,194,'zh-cn','Telephone TDD/Textphone'),(1734,195,'zh-cn','Water bed'),(1735,196,'zh-cn','Extra adult charge'),(1736,197,'zh-cn','Extra child charge'),(1737,198,'zh-cn','Extra child charge for rollaway use'),(1738,199,'zh-cn','Meal included:  full American breakfast'),(1739,200,'zh-cn','Futon'),(1740,201,'zh-cn','Murphy bed'),(1741,202,'zh-cn','Tatami mats'),(1742,203,'zh-cn','Single bed'),(1743,204,'zh-cn','Annex room'),(1744,205,'zh-cn','Free newspaper'),(1745,206,'zh-cn','Honeymoon suites'),(1746,207,'zh-cn','Complimentary high speed internet in room'),(1747,208,'zh-cn','Maid service'),(1748,209,'zh-cn','PC hook-up in room'),(1749,210,'zh-cn','Satellite television'),(1750,211,'zh-cn','VIP rooms'),(1751,212,'zh-cn','Cell phone recharger'),(1752,215,'zh-cn','Media center'),(1753,216,'zh-cn','Plug & play panel'),(1754,217,'zh-cn','Satellite radio'),(1755,218,'zh-cn','Video on demand'),(1756,219,'zh-cn','Exterior corridors'),(1757,220,'zh-cn','Gulf view'),(1758,222,'zh-cn','Interior corridors'),(1759,223,'zh-cn','Mountain view'),(1760,224,'zh-cn','Ocean view'),(1761,227,'zh-cn','Premium movie channels'),(1762,228,'zh-cn','Slippers'),(1763,229,'zh-cn','First nighters\' kit'),(1764,230,'zh-cn','Chair provided with desk'),(1765,231,'zh-cn','Pillow top mattress'),(1766,232,'zh-cn','Feather bed'),(1767,233,'zh-cn','Duvet'),(1768,234,'zh-cn','Luxury linen type'),(1769,235,'zh-cn','International channels'),(1770,236,'zh-cn','Pantry'),(1771,237,'zh-cn','Dish-cleaning supplies'),(1772,238,'zh-cn','Double vanity'),(1773,239,'zh-cn','Lighted makeup mirror'),(1774,240,'zh-cn','Upgraded bathroom amenities'),(1775,241,'zh-cn','VCR player available at front desk'),(1776,242,'zh-cn','Instant hot water'),(1777,243,'zh-cn','Outdoor space'),(1778,244,'zh-cn','Hinoki tub'),(1779,245,'zh-cn','Private pool'),(1780,247,'zh-cn','Room windows open'),(1781,248,'zh-cn','Bedding type unknown or unspecified'),(1782,249,'zh-cn','Full bed'),(1783,250,'zh-cn','Round bed'),(1784,251,'zh-cn','TV'),(1785,252,'zh-cn','Child rollaway'),(1786,253,'zh-cn','DVD player available at front desk'),(1787,254,'zh-cn','Video game player:'),(1788,255,'zh-cn','Video game player available at front desk'),(1789,256,'zh-cn','Dining room seats'),(1790,257,'zh-cn','Full size mirror'),(1791,258,'zh-cn','Mobile/cellular phones'),(1792,259,'zh-cn','Movies'),(1793,260,'zh-cn','Multiple closets'),(1794,261,'zh-cn','Plates/glassware'),(1795,262,'zh-cn','Safe large enough to accommodate a laptop'),(1796,263,'zh-cn','Bed linen thread count'),(1797,264,'zh-cn','Blackout curtain'),(1798,265,'zh-cn','Bluray player'),(1799,266,'zh-cn','Device with mp3'),(1800,267,'zh-cn','No adult channels or adult channel lock'),(1801,268,'zh-cn','Non-allergenic room'),(1802,269,'zh-cn','Pillow type'),(1803,270,'zh-cn','Seating area with sofa/chair'),(1804,271,'zh-cn','Separate toilet area'),(1805,272,'zh-cn','Web enabled'),(1806,273,'zh-cn','Widescreen TV'),(1807,274,'zh-cn','Other data connection'),(1808,275,'zh-cn','Phoneline billed separately'),(1809,276,'zh-cn','Separate tub or shower'),(1810,278,'zh-cn','Roof ventilator'),(1811,279,'zh-cn','Children\'s playpen'),(1812,280,'zh-cn','Plunge pool');  
INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (1813,4,'zh-hk','All news channel'),(1814,5,'zh-hk','AM/FM radio'),(1815,6,'zh-hk','Baby listening device'),(1816,7,'zh-hk','Balcony/Lanai/Terrace'),(1817,8,'zh-hk','Barbeque grills'),(1818,9,'zh-hk','Bath tub with spray jets'),(1819,10,'zh-hk','Bathrobe'),(1820,11,'zh-hk','Bathroom amenities'),(1821,12,'zh-hk','Bathroom telephone'),(1822,16,'zh-hk','Bidet'),(1823,17,'zh-hk','Bottled water'),(1824,18,'zh-hk','Cable television'),(1825,22,'zh-hk','Connecting rooms'),(1826,23,'zh-hk','Converters/ Voltage adaptors'),(1827,24,'zh-hk','Copier'),(1828,25,'zh-hk','Cordless phone'),(1829,26,'zh-hk','Cribs'),(1830,27,'zh-hk','Data port'),(1831,29,'zh-hk','Desk with lamp'),(1832,30,'zh-hk','Dining guide'),(1833,31,'zh-hk','Direct dial phone number'),(1834,32,'zh-hk','Dishwasher'),(1835,33,'zh-hk','Double beds'),(1836,34,'zh-hk','Dual voltage outlet'),(1837,35,'zh-hk','Electrical current voltage'),(1838,36,'zh-hk','Ergonomic chair'),(1839,37,'zh-hk','Extended phone cord'),(1840,39,'zh-hk','Fire alarm'),(1841,40,'zh-hk','Fire alarm with light'),(1842,41,'zh-hk','Fireplace'),(1843,42,'zh-hk','Free toll free calls'),(1844,43,'zh-hk','Free calls'),(1845,44,'zh-hk','Free credit card access calls'),(1846,45,'zh-hk','Free local calls'),(1847,46,'zh-hk','Free movies/video'),(1848,47,'zh-hk','Full kitchen'),(1849,48,'zh-hk','Grab bars in bathroom'),(1850,49,'zh-hk','Grecian tub'),(1851,50,'zh-hk','Hairdryer'),(1852,55,'zh-hk','Iron'),(1853,56,'zh-hk','Ironing board'),(1854,57,'zh-hk','Whirpool'),(1855,58,'zh-hk','King bed'),(1856,59,'zh-hk','Kitchen'),(1857,60,'zh-hk','Kitchen supplies'),(1858,62,'zh-hk','Knock light'),(1859,63,'zh-hk','Laptop'),(1860,65,'zh-hk','Large work area'),(1861,66,'zh-hk','Laundry basket/clothes hamper'),(1862,67,'zh-hk','Loft'),(1863,70,'zh-hk','Modem'),(1864,71,'zh-hk','Modem jack'),(1865,72,'zh-hk','Multi-line phone'),(1866,73,'zh-hk','Newspaper'),(1867,74,'zh-hk','Non-smoking'),(1868,75,'zh-hk','Notepads'),(1869,76,'zh-hk','Office supplies'),(1870,77,'zh-hk','Oven'),(1871,78,'zh-hk','Pay per view movies on TV'),(1872,79,'zh-hk','Pens'),(1873,80,'zh-hk','Phone in bathroom'),(1874,81,'zh-hk','Plates and bowls'),(1875,82,'zh-hk','Pots and pans'),(1876,84,'zh-hk','Printer'),(1877,85,'zh-hk','Private bathroom'),(1878,86,'zh-hk','Queen bed'),(1879,87,'zh-hk','Recliner'),(1880,88,'zh-hk','Refrigerator'),(1881,89,'zh-hk','Refrigerator with ice maker'),(1882,90,'zh-hk','Remote control television'),(1883,91,'zh-hk','Rollaway bed'),(1884,93,'zh-hk','Scanner'),(1885,94,'zh-hk','Separate closet'),(1886,95,'zh-hk','Separate modem line available'),(1887,96,'zh-hk','Shoe polisher'),(1888,98,'zh-hk','Silverware/utensils'),(1889,99,'zh-hk','Sitting area'),(1890,100,'zh-hk','Smoke detectors'),(1891,102,'zh-hk','Sofa bed'),(1892,103,'zh-hk','Speaker phone'),(1893,104,'zh-hk','Stereo'),(1894,105,'zh-hk','Stove'),(1895,106,'zh-hk','Tape recorder'),(1896,107,'zh-hk','Telephone'),(1897,108,'zh-hk','Telephone for hearing impaired'),(1898,109,'zh-hk','Telephones with message light'),(1899,110,'zh-hk','Toaster oven'),(1900,111,'zh-hk','Trouser/Pant press'),(1901,112,'zh-hk','Turn down service'),(1902,113,'zh-hk','Twin bed'),(1903,114,'zh-hk','Vaulted ceilings'),(1904,115,'zh-hk','VCR movies'),(1905,116,'zh-hk','VCR player'),(1906,117,'zh-hk','Video games'),(1907,118,'zh-hk','Voice mail'),(1908,119,'zh-hk','Wake-up calls'),(1909,120,'zh-hk','Water closet'),(1910,121,'zh-hk','Water purification system'),(1911,122,'zh-hk','Wet bar'),(1912,124,'zh-hk','Wireless keyboard'),(1913,125,'zh-hk','Adaptor available for telephone PC use'),(1914,127,'zh-hk','Bathtub &whirlpool separate'),(1915,128,'zh-hk','Telephone with data ports'),(1916,129,'zh-hk','CD  player'),(1917,130,'zh-hk','Complimentary local calls time limit'),(1918,131,'zh-hk','Extra person charge for rollaway use'),(1919,132,'zh-hk','Down/feather pillows'),(1920,134,'zh-hk','ESPN available'),(1921,135,'zh-hk','Foam pillows'),(1922,136,'zh-hk','HBO available'),(1923,137,'zh-hk','High ceilings'),(1924,138,'zh-hk','Marble bathroom'),(1925,139,'zh-hk','List of movie channels available'),(1926,140,'zh-hk','Pets allowed'),(1927,141,'zh-hk','Oversized bathtub'),(1928,143,'zh-hk','Sink in-room'),(1929,144,'zh-hk','Soundproofed room'),(1930,145,'zh-hk','Storage space'),(1931,146,'zh-hk','Tables and chairs'),(1932,147,'zh-hk','Two-line phone'),(1933,148,'zh-hk','Walk-in closet'),(1934,149,'zh-hk','Washer/dryer'),(1935,150,'zh-hk','Weight scale'),(1936,151,'zh-hk','Welcome gift'),(1937,152,'zh-hk','Spare electrical outlet available at desk'),(1938,153,'zh-hk','Non-refundable charge for pets'),(1939,154,'zh-hk','Refundable deposit for pets'),(1940,156,'zh-hk','Entrance type to guest room'),(1941,157,'zh-hk','Ceiling fan'),(1942,158,'zh-hk','CNN available'),(1943,159,'zh-hk','Electrical adaptors available'),(1944,160,'zh-hk','Buffet breakfast'),(1945,161,'zh-hk','Accessible room'),(1946,162,'zh-hk','Closets in room'),(1947,165,'zh-hk','Separate line billing for multi-line phone'),(1948,166,'zh-hk','Self-controlled heating/cooling system'),(1949,167,'zh-hk','Toaster'),(1950,168,'zh-hk','Analog data port'),(1951,171,'zh-hk','Carrier access'),(1952,180,'zh-hk','Universal AC/DC adaptors'),(1953,181,'zh-hk','Bathtub seat'),(1954,182,'zh-hk','Canopy/poster bed'),(1955,183,'zh-hk','Cups/glassware'),(1956,184,'zh-hk','Entertainment center'),(1957,185,'zh-hk','Family/oversized room'),(1958,186,'zh-hk','Hypoallergenic bed'),(1959,187,'zh-hk','Hypoallergenic pillows'),(1960,188,'zh-hk','Lamp'),(1961,189,'zh-hk','Meal included - breakfast'),(1962,190,'zh-hk','Meal included - continental breakfast'),(1963,191,'zh-hk','Meal included - dinner'),(1964,192,'zh-hk','Meal included - lunch'),(1965,193,'zh-hk','Shared bathroom'),(1966,194,'zh-hk','Telephone TDD/Textphone'),(1967,195,'zh-hk','Water bed'),(1968,196,'zh-hk','Extra adult charge'),(1969,197,'zh-hk','Extra child charge'),(1970,198,'zh-hk','Extra child charge for rollaway use'),(1971,199,'zh-hk','Meal included:  full American breakfast'),(1972,200,'zh-hk','Futon'),(1973,201,'zh-hk','Murphy bed'),(1974,202,'zh-hk','Tatami mats'),(1975,203,'zh-hk','Single bed'),(1976,204,'zh-hk','Annex room'),(1977,205,'zh-hk','Free newspaper'),(1978,206,'zh-hk','Honeymoon suites'),(1979,207,'zh-hk','Complimentary high speed internet in room'),(1980,208,'zh-hk','Maid service'),(1981,209,'zh-hk','PC hook-up in room'),(1982,210,'zh-hk','Satellite television'),(1983,211,'zh-hk','VIP rooms'),(1984,212,'zh-hk','Cell phone recharger'),(1985,215,'zh-hk','Media center'),(1986,216,'zh-hk','Plug & play panel'),(1987,217,'zh-hk','Satellite radio'),(1988,218,'zh-hk','Video on demand'),(1989,219,'zh-hk','Exterior corridors'),(1990,220,'zh-hk','Gulf view'),(1991,222,'zh-hk','Interior corridors'),(1992,223,'zh-hk','Mountain view'),(1993,224,'zh-hk','Ocean view'),(1994,227,'zh-hk','Premium movie channels'),(1995,228,'zh-hk','Slippers'),(1996,229,'zh-hk','First nighters\' kit'),(1997,230,'zh-hk','Chair provided with desk'),(1998,231,'zh-hk','Pillow top mattress'),(1999,232,'zh-hk','Feather bed'),(2000,233,'zh-hk','Duvet'),(2001,234,'zh-hk','Luxury linen type'),(2002,235,'zh-hk','International channels'),(2003,236,'zh-hk','Pantry'),(2004,237,'zh-hk','Dish-cleaning supplies'),(2005,238,'zh-hk','Double vanity'),(2006,239,'zh-hk','Lighted makeup mirror'),(2007,240,'zh-hk','Upgraded bathroom amenities'),(2008,241,'zh-hk','VCR player available at front desk'),(2009,242,'zh-hk','Instant hot water'),(2010,243,'zh-hk','Outdoor space'),(2011,244,'zh-hk','Hinoki tub'),(2012,245,'zh-hk','Private pool'),(2013,247,'zh-hk','Room windows open'),(2014,248,'zh-hk','Bedding type unknown or unspecified'),(2015,249,'zh-hk','Full bed'),(2016,250,'zh-hk','Round bed'),(2017,251,'zh-hk','TV'),(2018,252,'zh-hk','Child rollaway'),(2019,253,'zh-hk','DVD player available at front desk'),(2020,254,'zh-hk','Video game player:'),(2021,255,'zh-hk','Video game player available at front desk'),(2022,256,'zh-hk','Dining room seats'),(2023,257,'zh-hk','Full size mirror'),(2024,258,'zh-hk','Mobile/cellular phones'),(2025,259,'zh-hk','Movies'),(2026,260,'zh-hk','Multiple closets'),(2027,261,'zh-hk','Plates/glassware'),(2028,262,'zh-hk','Safe large enough to accommodate a laptop'),(2029,263,'zh-hk','Bed linen thread count'),(2030,264,'zh-hk','Blackout curtain'),(2031,265,'zh-hk','Bluray player'),(2032,266,'zh-hk','Device with mp3'),(2033,267,'zh-hk','No adult channels or adult channel lock'),(2034,268,'zh-hk','Non-allergenic room'),(2035,269,'zh-hk','Pillow type'),(2036,270,'zh-hk','Seating area with sofa/chair'),(2037,271,'zh-hk','Separate toilet area'),(2038,272,'zh-hk','Web enabled'),(2039,273,'zh-hk','Widescreen TV'),(2040,274,'zh-hk','Other data connection'),(2041,275,'zh-hk','Phoneline billed separately'),(2042,276,'zh-hk','Separate tub or shower'),(2043,278,'zh-hk','Roof ventilator'),(2044,279,'zh-hk','Children\'s playpen'),(2045,280,'zh-hk','Plunge pool'),(2046,4,'zh-tw','All news channel'),(2047,5,'zh-tw','AM/FM radio'),(2048,6,'zh-tw','Baby listening device'),(2049,7,'zh-tw','Balcony/Lanai/Terrace'),(2050,8,'zh-tw','Barbeque grills'),(2051,9,'zh-tw','Bath tub with spray jets'),(2052,10,'zh-tw','Bathrobe'),(2053,11,'zh-tw','Bathroom amenities'),(2054,12,'zh-tw','Bathroom telephone'),(2055,16,'zh-tw','Bidet'),(2056,17,'zh-tw','Bottled water'),(2057,18,'zh-tw','Cable television'),(2058,22,'zh-tw','Connecting rooms'),(2059,23,'zh-tw','Converters/ Voltage adaptors'),(2060,24,'zh-tw','Copier'),(2061,25,'zh-tw','Cordless phone'),(2062,26,'zh-tw','Cribs'),(2063,27,'zh-tw','Data port'),(2064,29,'zh-tw','Desk with lamp'),(2065,30,'zh-tw','Dining guide'),(2066,31,'zh-tw','Direct dial phone number'),(2067,32,'zh-tw','Dishwasher'),(2068,33,'zh-tw','Double beds'),(2069,34,'zh-tw','Dual voltage outlet'),(2070,35,'zh-tw','Electrical current voltage'),(2071,36,'zh-tw','Ergonomic chair'),(2072,37,'zh-tw','Extended phone cord'),(2073,39,'zh-tw','Fire alarm'),(2074,40,'zh-tw','Fire alarm with light'),(2075,41,'zh-tw','Fireplace'),(2076,42,'zh-tw','Free toll free calls'),(2077,43,'zh-tw','Free calls'),(2078,44,'zh-tw','Free credit card access calls'),(2079,45,'zh-tw','Free local calls'),(2080,46,'zh-tw','Free movies/video'),(2081,47,'zh-tw','Full kitchen'),(2082,48,'zh-tw','Grab bars in bathroom'),(2083,49,'zh-tw','Grecian tub'),(2084,50,'zh-tw','Hairdryer'),(2085,55,'zh-tw','Iron'),(2086,56,'zh-tw','Ironing board'),(2087,57,'zh-tw','Whirpool'),(2088,58,'zh-tw','King bed'),(2089,59,'zh-tw','Kitchen'),(2090,60,'zh-tw','Kitchen supplies'),(2091,62,'zh-tw','Knock light'),(2092,63,'zh-tw','Laptop'),(2093,65,'zh-tw','Large work area'),(2094,66,'zh-tw','Laundry basket/clothes hamper'),(2095,67,'zh-tw','Loft'),(2096,70,'zh-tw','Modem'),(2097,71,'zh-tw','Modem jack'),(2098,72,'zh-tw','Multi-line phone'),(2099,73,'zh-tw','Newspaper'),(2100,74,'zh-tw','Non-smoking'),(2101,75,'zh-tw','Notepads'),(2102,76,'zh-tw','Office supplies'),(2103,77,'zh-tw','Oven'),(2104,78,'zh-tw','Pay per view movies on TV'),(2105,79,'zh-tw','Pens'),(2106,80,'zh-tw','Phone in bathroom'),(2107,81,'zh-tw','Plates and bowls'),(2108,82,'zh-tw','Pots and pans'),(2109,84,'zh-tw','Printer'),(2110,85,'zh-tw','Private bathroom'),(2111,86,'zh-tw','Queen bed'),(2112,87,'zh-tw','Recliner'),(2113,88,'zh-tw','Refrigerator'),(2114,89,'zh-tw','Refrigerator with ice maker'),(2115,90,'zh-tw','Remote control television'),(2116,91,'zh-tw','Rollaway bed'),(2117,93,'zh-tw','Scanner'),(2118,94,'zh-tw','Separate closet'),(2119,95,'zh-tw','Separate modem line available'),(2120,96,'zh-tw','Shoe polisher'),(2121,98,'zh-tw','Silverware/utensils'),(2122,99,'zh-tw','Sitting area'),(2123,100,'zh-tw','Smoke detectors'),(2124,102,'zh-tw','Sofa bed'),(2125,103,'zh-tw','Speaker phone'),(2126,104,'zh-tw','Stereo'),(2127,105,'zh-tw','Stove'),(2128,106,'zh-tw','Tape recorder'),(2129,107,'zh-tw','Telephone'),(2130,108,'zh-tw','Telephone for hearing impaired'),(2131,109,'zh-tw','Telephones with message light'),(2132,110,'zh-tw','Toaster oven'),(2133,111,'zh-tw','Trouser/Pant press'),(2134,112,'zh-tw','Turn down service'),(2135,113,'zh-tw','Twin bed'),(2136,114,'zh-tw','Vaulted ceilings'),(2137,115,'zh-tw','VCR movies'),(2138,116,'zh-tw','VCR player'),(2139,117,'zh-tw','Video games'),(2140,118,'zh-tw','Voice mail'),(2141,119,'zh-tw','Wake-up calls'),(2142,120,'zh-tw','Water closet'),(2143,121,'zh-tw','Water purification system'),(2144,122,'zh-tw','Wet bar'),(2145,124,'zh-tw','Wireless keyboard'),(2146,125,'zh-tw','Adaptor available for telephone PC use'),(2147,127,'zh-tw','Bathtub &whirlpool separate'),(2148,128,'zh-tw','Telephone with data ports'),(2149,129,'zh-tw','CD  player'),(2150,130,'zh-tw','Complimentary local calls time limit'),(2151,131,'zh-tw','Extra person charge for rollaway use'),(2152,132,'zh-tw','Down/feather pillows'),(2153,134,'zh-tw','ESPN available'),(2154,135,'zh-tw','Foam pillows'),(2155,136,'zh-tw','HBO available'),(2156,137,'zh-tw','High ceilings'),(2157,138,'zh-tw','Marble bathroom'),(2158,139,'zh-tw','List of movie channels available'),(2159,140,'zh-tw','Pets allowed'),(2160,141,'zh-tw','Oversized bathtub'),(2161,143,'zh-tw','Sink in-room'),(2162,144,'zh-tw','Soundproofed room'),(2163,145,'zh-tw','Storage space'),(2164,146,'zh-tw','Tables and chairs'),(2165,147,'zh-tw','Two-line phone'),(2166,148,'zh-tw','Walk-in closet'),(2167,149,'zh-tw','Washer/dryer'),(2168,150,'zh-tw','Weight scale'),(2169,151,'zh-tw','Welcome gift'),(2170,152,'zh-tw','Spare electrical outlet available at desk'),(2171,153,'zh-tw','Non-refundable charge for pets'),(2172,154,'zh-tw','Refundable deposit for pets'),(2173,156,'zh-tw','Entrance type to guest room'),(2174,157,'zh-tw','Ceiling fan'),(2175,158,'zh-tw','CNN available'),(2176,159,'zh-tw','Electrical adaptors available'),(2177,160,'zh-tw','Buffet breakfast'),(2178,161,'zh-tw','Accessible room'),(2179,162,'zh-tw','Closets in room'),(2180,165,'zh-tw','Separate line billing for multi-line phone'),(2181,166,'zh-tw','Self-controlled heating/cooling system'),(2182,167,'zh-tw','Toaster'),(2183,168,'zh-tw','Analog data port'),(2184,171,'zh-tw','Carrier access'),(2185,180,'zh-tw','Universal AC/DC adaptors'),(2186,181,'zh-tw','Bathtub seat'),(2187,182,'zh-tw','Canopy/poster bed'),(2188,183,'zh-tw','Cups/glassware'),(2189,184,'zh-tw','Entertainment center'),(2190,185,'zh-tw','Family/oversized room'),(2191,186,'zh-tw','Hypoallergenic bed'),(2192,187,'zh-tw','Hypoallergenic pillows'),(2193,188,'zh-tw','Lamp'),(2194,189,'zh-tw','Meal included - breakfast'),(2195,190,'zh-tw','Meal included - continental breakfast'),(2196,191,'zh-tw','Meal included - dinner'),(2197,192,'zh-tw','Meal included - lunch'),(2198,193,'zh-tw','Shared bathroom'),(2199,194,'zh-tw','Telephone TDD/Textphone'),(2200,195,'zh-tw','Water bed'),(2201,196,'zh-tw','Extra adult charge'),(2202,197,'zh-tw','Extra child charge'),(2203,198,'zh-tw','Extra child charge for rollaway use'),(2204,199,'zh-tw','Meal included:  full American breakfast'),(2205,200,'zh-tw','Futon'),(2206,201,'zh-tw','Murphy bed'),(2207,202,'zh-tw','Tatami mats'),(2208,203,'zh-tw','Single bed'),(2209,204,'zh-tw','Annex room'),(2210,205,'zh-tw','Free newspaper'),(2211,206,'zh-tw','Honeymoon suites'),(2212,207,'zh-tw','Complimentary high speed internet in room'),(2213,208,'zh-tw','Maid service'),(2214,209,'zh-tw','PC hook-up in room'),(2215,210,'zh-tw','Satellite television'),(2216,211,'zh-tw','VIP rooms'),(2217,212,'zh-tw','Cell phone recharger'),(2218,215,'zh-tw','Media center'),(2219,216,'zh-tw','Plug & play panel'),(2220,217,'zh-tw','Satellite radio'),(2221,218,'zh-tw','Video on demand'),(2222,219,'zh-tw','Exterior corridors'),(2223,220,'zh-tw','Gulf view'),(2224,222,'zh-tw','Interior corridors'),(2225,223,'zh-tw','Mountain view'),(2226,224,'zh-tw','Ocean view'),(2227,227,'zh-tw','Premium movie channels'),(2228,228,'zh-tw','Slippers'),(2229,229,'zh-tw','First nighters\' kit'),(2230,230,'zh-tw','Chair provided with desk'),(2231,231,'zh-tw','Pillow top mattress'),(2232,232,'zh-tw','Feather bed'),(2233,233,'zh-tw','Duvet'),(2234,234,'zh-tw','Luxury linen type'),(2235,235,'zh-tw','International channels'),(2236,236,'zh-tw','Pantry'),(2237,237,'zh-tw','Dish-cleaning supplies'),(2238,238,'zh-tw','Double vanity'),(2239,239,'zh-tw','Lighted makeup mirror'),(2240,240,'zh-tw','Upgraded bathroom amenities'),(2241,241,'zh-tw','VCR player available at front desk'),(2242,242,'zh-tw','Instant hot water'),(2243,243,'zh-tw','Outdoor space'),(2244,244,'zh-tw','Hinoki tub'),(2245,245,'zh-tw','Private pool'),(2246,247,'zh-tw','Room windows open'),(2247,248,'zh-tw','Bedding type unknown or unspecified'),(2248,249,'zh-tw','Full bed'),(2249,250,'zh-tw','Round bed'),(2250,251,'zh-tw','TV'),(2251,252,'zh-tw','Child rollaway'),(2252,253,'zh-tw','DVD player available at front desk'),(2253,254,'zh-tw','Video game player:'),(2254,255,'zh-tw','Video game player available at front desk'),(2255,256,'zh-tw','Dining room seats'),(2256,257,'zh-tw','Full size mirror'),(2257,258,'zh-tw','Mobile/cellular phones'),(2258,259,'zh-tw','Movies'),(2259,260,'zh-tw','Multiple closets'),(2260,261,'zh-tw','Plates/glassware'),(2261,262,'zh-tw','Safe large enough to accommodate a laptop'),(2262,263,'zh-tw','Bed linen thread count'),(2263,264,'zh-tw','Blackout curtain'),(2264,265,'zh-tw','Bluray player'),(2265,266,'zh-tw','Device with mp3'),(2266,267,'zh-tw','No adult channels or adult channel lock'),(2267,268,'zh-tw','Non-allergenic room'),(2268,269,'zh-tw','Pillow type'),(2269,270,'zh-tw','Seating area with sofa/chair'),(2270,271,'zh-tw','Separate toilet area'),(2271,272,'zh-tw','Web enabled'),(2272,273,'zh-tw','Widescreen TV'),(2273,274,'zh-tw','Other data connection'),(2274,275,'zh-tw','Phoneline billed separately'),(2275,276,'zh-tw','Separate tub or shower'),(2276,278,'zh-tw','Roof ventilator'),(2277,279,'zh-tw','Children\'s playpen'),(2278,280,'zh-tw','Plunge pool'); 
INSERT  INTO `ota_roomamenity`(`OTA_RoomAmenityID`,`OTA_Number`,`lang`,`Description`) VALUES (2279,4,'zh-sg','All news channel'),(2280,5,'zh-sg','AM/FM radio'),(2281,6,'zh-sg','Baby listening device'),(2282,7,'zh-sg','Balcony/Lanai/Terrace'),(2283,8,'zh-sg','Barbeque grills'),(2284,9,'zh-sg','Bath tub with spray jets'),(2285,10,'zh-sg','Bathrobe'),(2286,11,'zh-sg','Bathroom amenities'),(2287,12,'zh-sg','Bathroom telephone'),(2288,16,'zh-sg','Bidet'),(2289,17,'zh-sg','Bottled water'),(2290,18,'zh-sg','Cable television'),(2291,22,'zh-sg','Connecting rooms'),(2292,23,'zh-sg','Converters/ Voltage adaptors'),(2293,24,'zh-sg','Copier'),(2294,25,'zh-sg','Cordless phone'),(2295,26,'zh-sg','Cribs'),(2296,27,'zh-sg','Data port'),(2297,29,'zh-sg','Desk with lamp'),(2298,30,'zh-sg','Dining guide'),(2299,31,'zh-sg','Direct dial phone number'),(2300,32,'zh-sg','Dishwasher'),(2301,33,'zh-sg','Double beds'),(2302,34,'zh-sg','Dual voltage outlet'),(2303,35,'zh-sg','Electrical current voltage'),(2304,36,'zh-sg','Ergonomic chair'),(2305,37,'zh-sg','Extended phone cord'),(2306,39,'zh-sg','Fire alarm'),(2307,40,'zh-sg','Fire alarm with light'),(2308,41,'zh-sg','Fireplace'),(2309,42,'zh-sg','Free toll free calls'),(2310,43,'zh-sg','Free calls'),(2311,44,'zh-sg','Free credit card access calls'),(2312,45,'zh-sg','Free local calls'),(2313,46,'zh-sg','Free movies/video'),(2314,47,'zh-sg','Full kitchen'),(2315,48,'zh-sg','Grab bars in bathroom'),(2316,49,'zh-sg','Grecian tub'),(2317,50,'zh-sg','Hairdryer'),(2318,55,'zh-sg','Iron'),(2319,56,'zh-sg','Ironing board'),(2320,57,'zh-sg','Whirpool'),(2321,58,'zh-sg','King bed'),(2322,59,'zh-sg','Kitchen'),(2323,60,'zh-sg','Kitchen supplies'),(2324,62,'zh-sg','Knock light'),(2325,63,'zh-sg','Laptop'),(2326,65,'zh-sg','Large work area'),(2327,66,'zh-sg','Laundry basket/clothes hamper'),(2328,67,'zh-sg','Loft'),(2329,70,'zh-sg','Modem'),(2330,71,'zh-sg','Modem jack'),(2331,72,'zh-sg','Multi-line phone'),(2332,73,'zh-sg','Newspaper'),(2333,74,'zh-sg','Non-smoking'),(2334,75,'zh-sg','Notepads'),(2335,76,'zh-sg','Office supplies'),(2336,77,'zh-sg','Oven'),(2337,78,'zh-sg','Pay per view movies on TV'),(2338,79,'zh-sg','Pens'),(2339,80,'zh-sg','Phone in bathroom'),(2340,81,'zh-sg','Plates and bowls'),(2341,82,'zh-sg','Pots and pans'),(2342,84,'zh-sg','Printer'),(2343,85,'zh-sg','Private bathroom'),(2344,86,'zh-sg','Queen bed'),(2345,87,'zh-sg','Recliner'),(2346,88,'zh-sg','Refrigerator'),(2347,89,'zh-sg','Refrigerator with ice maker'),(2348,90,'zh-sg','Remote control television'),(2349,91,'zh-sg','Rollaway bed'),(2350,93,'zh-sg','Scanner'),(2351,94,'zh-sg','Separate closet'),(2352,95,'zh-sg','Separate modem line available'),(2353,96,'zh-sg','Shoe polisher'),(2354,98,'zh-sg','Silverware/utensils'),(2355,99,'zh-sg','Sitting area'),(2356,100,'zh-sg','Smoke detectors'),(2357,102,'zh-sg','Sofa bed'),(2358,103,'zh-sg','Speaker phone'),(2359,104,'zh-sg','Stereo'),(2360,105,'zh-sg','Stove'),(2361,106,'zh-sg','Tape recorder'),(2362,107,'zh-sg','Telephone'),(2363,108,'zh-sg','Telephone for hearing impaired'),(2364,109,'zh-sg','Telephones with message light'),(2365,110,'zh-sg','Toaster oven'),(2366,111,'zh-sg','Trouser/Pant press'),(2367,112,'zh-sg','Turn down service'),(2368,113,'zh-sg','Twin bed'),(2369,114,'zh-sg','Vaulted ceilings'),(2370,115,'zh-sg','VCR movies'),(2371,116,'zh-sg','VCR player'),(2372,117,'zh-sg','Video games'),(2373,118,'zh-sg','Voice mail'),(2374,119,'zh-sg','Wake-up calls'),(2375,120,'zh-sg','Water closet'),(2376,121,'zh-sg','Water purification system'),(2377,122,'zh-sg','Wet bar'),(2378,124,'zh-sg','Wireless keyboard'),(2379,125,'zh-sg','Adaptor available for telephone PC use'),(2380,127,'zh-sg','Bathtub &whirlpool separate'),(2381,128,'zh-sg','Telephone with data ports'),(2382,129,'zh-sg','CD  player'),(2383,130,'zh-sg','Complimentary local calls time limit'),(2384,131,'zh-sg','Extra person charge for rollaway use'),(2385,132,'zh-sg','Down/feather pillows'),(2386,134,'zh-sg','ESPN available'),(2387,135,'zh-sg','Foam pillows'),(2388,136,'zh-sg','HBO available'),(2389,137,'zh-sg','High ceilings'),(2390,138,'zh-sg','Marble bathroom'),(2391,139,'zh-sg','List of movie channels available'),(2392,140,'zh-sg','Pets allowed'),(2393,141,'zh-sg','Oversized bathtub'),(2394,143,'zh-sg','Sink in-room'),(2395,144,'zh-sg','Soundproofed room'),(2396,145,'zh-sg','Storage space'),(2397,146,'zh-sg','Tables and chairs'),(2398,147,'zh-sg','Two-line phone'),(2399,148,'zh-sg','Walk-in closet'),(2400,149,'zh-sg','Washer/dryer'),(2401,150,'zh-sg','Weight scale'),(2402,151,'zh-sg','Welcome gift'),(2403,152,'zh-sg','Spare electrical outlet available at desk'),(2404,153,'zh-sg','Non-refundable charge for pets'),(2405,154,'zh-sg','Refundable deposit for pets'),(2406,156,'zh-sg','Entrance type to guest room'),(2407,157,'zh-sg','Ceiling fan'),(2408,158,'zh-sg','CNN available'),(2409,159,'zh-sg','Electrical adaptors available'),(2410,160,'zh-sg','Buffet breakfast'),(2411,161,'zh-sg','Accessible room'),(2412,162,'zh-sg','Closets in room'),(2413,165,'zh-sg','Separate line billing for multi-line phone'),(2414,166,'zh-sg','Self-controlled heating/cooling system'),(2415,167,'zh-sg','Toaster'),(2416,168,'zh-sg','Analog data port'),(2417,171,'zh-sg','Carrier access'),(2418,180,'zh-sg','Universal AC/DC adaptors'),(2419,181,'zh-sg','Bathtub seat'),(2420,182,'zh-sg','Canopy/poster bed'),(2421,183,'zh-sg','Cups/glassware'),(2422,184,'zh-sg','Entertainment center'),(2423,185,'zh-sg','Family/oversized room'),(2424,186,'zh-sg','Hypoallergenic bed'),(2425,187,'zh-sg','Hypoallergenic pillows'),(2426,188,'zh-sg','Lamp'),(2427,189,'zh-sg','Meal included - breakfast'),(2428,190,'zh-sg','Meal included - continental breakfast'),(2429,191,'zh-sg','Meal included - dinner'),(2430,192,'zh-sg','Meal included - lunch'),(2431,193,'zh-sg','Shared bathroom'),(2432,194,'zh-sg','Telephone TDD/Textphone'),(2433,195,'zh-sg','Water bed'),(2434,196,'zh-sg','Extra adult charge'),(2435,197,'zh-sg','Extra child charge'),(2436,198,'zh-sg','Extra child charge for rollaway use'),(2437,199,'zh-sg','Meal included:  full American breakfast'),(2438,200,'zh-sg','Futon'),(2439,201,'zh-sg','Murphy bed'),(2440,202,'zh-sg','Tatami mats'),(2441,203,'zh-sg','Single bed'),(2442,204,'zh-sg','Annex room'),(2443,205,'zh-sg','Free newspaper'),(2444,206,'zh-sg','Honeymoon suites'),(2445,207,'zh-sg','Complimentary high speed internet in room'),(2446,208,'zh-sg','Maid service'),(2447,209,'zh-sg','PC hook-up in room'),(2448,210,'zh-sg','Satellite television'),(2449,211,'zh-sg','VIP rooms'),(2450,212,'zh-sg','Cell phone recharger'),(2451,215,'zh-sg','Media center'),(2452,216,'zh-sg','Plug & play panel'),(2453,217,'zh-sg','Satellite radio'),(2454,218,'zh-sg','Video on demand'),(2455,219,'zh-sg','Exterior corridors'),(2456,220,'zh-sg','Gulf view'),(2457,222,'zh-sg','Interior corridors'),(2458,223,'zh-sg','Mountain view'),(2459,224,'zh-sg','Ocean view'),(2460,227,'zh-sg','Premium movie channels'),(2461,228,'zh-sg','Slippers'),(2462,229,'zh-sg','First nighters\' kit'),(2463,230,'zh-sg','Chair provided with desk'),(2464,231,'zh-sg','Pillow top mattress'),(2465,232,'zh-sg','Feather bed'),(2466,233,'zh-sg','Duvet'),(2467,234,'zh-sg','Luxury linen type'),(2468,235,'zh-sg','International channels'),(2469,236,'zh-sg','Pantry'),(2470,237,'zh-sg','Dish-cleaning supplies'),(2471,238,'zh-sg','Double vanity'),(2472,239,'zh-sg','Lighted makeup mirror'),(2473,240,'zh-sg','Upgraded bathroom amenities'),(2474,241,'zh-sg','VCR player available at front desk'),(2475,242,'zh-sg','Instant hot water'),(2476,243,'zh-sg','Outdoor space'),(2477,244,'zh-sg','Hinoki tub'),(2478,245,'zh-sg','Private pool'),(2479,247,'zh-sg','Room windows open'),(2480,248,'zh-sg','Bedding type unknown or unspecified'),(2481,249,'zh-sg','Full bed'),(2482,250,'zh-sg','Round bed'),(2483,251,'zh-sg','TV'),(2484,252,'zh-sg','Child rollaway'),(2485,253,'zh-sg','DVD player available at front desk'),(2486,254,'zh-sg','Video game player:'),(2487,255,'zh-sg','Video game player available at front desk'),(2488,256,'zh-sg','Dining room seats'),(2489,257,'zh-sg','Full size mirror'),(2490,258,'zh-sg','Mobile/cellular phones'),(2491,259,'zh-sg','Movies'),(2492,260,'zh-sg','Multiple closets'),(2493,261,'zh-sg','Plates/glassware'),(2494,262,'zh-sg','Safe large enough to accommodate a laptop'),(2495,263,'zh-sg','Bed linen thread count'),(2496,264,'zh-sg','Blackout curtain'),(2497,265,'zh-sg','Bluray player'),(2498,266,'zh-sg','Device with mp3'),(2499,267,'zh-sg','No adult channels or adult channel lock'),(2500,268,'zh-sg','Non-allergenic room'),(2501,269,'zh-sg','Pillow type'),(2502,270,'zh-sg','Seating area with sofa/chair'),(2503,271,'zh-sg','Separate toilet area'),(2504,272,'zh-sg','Web enabled'),(2505,273,'zh-sg','Widescreen TV'),(2506,274,'zh-sg','Other data connection'),(2507,275,'zh-sg','Phoneline billed separately'),(2508,276,'zh-sg','Separate tub or shower'),(2509,278,'zh-sg','Roof ventilator'),(2510,279,'zh-sg','Children\'s playpen'),(2511,280,'zh-sg','Plunge pool');
 
/*Data for the table `paygateway_list` */

INSERT  INTO `paygateway_list`(`pgid`,`payment_gateway`,`paymentform`) VALUES (1,'Paypal','5'),(2,'VI','2'),(3,'CA','2'),(4,'AX','2'),(5,'DC','2'),(6,'JC','2'),(7,'CB','2'),(8,'BC','2'),(9,'DS','2'),(10,'T','2'),(11,'R','2'),(12,'N','2'),(13,'L','2'),(14,'E','2'),(15,'TO','2'),(16,'S','2');
/*Data for the table `payment_gateways` */

INSERT  INTO `payment_gateways`(`gateid`,`paymentgateway`,`accname`,`accid`,`swiftcode`) VALUES (1,'Paypal','Paypal','Pro_1334301561_biz@gmail.com','');
/*Data for the table `payment_mode` */

INSERT  INTO `payment_mode`(`paymentid`,`payment_option`) VALUES (1,'Cash'),(2,'Credit Card'),(3,'Cheque'),(4,'Company'),(5,'Money Order'),(6,'Western Union');
/*Data for the table `salutation` */

INSERT  INTO `salutation`(`saluteid`,`salute`,`lang`,`Description`) VALUES (1,1,'en-us','Mr.'),(2,2,'en-us','Ms.'),(3,3,'en-us','Miss'),(4,4,'en-us','Mrs.'),(5,5,'en-us','Dr.'),(6,6,'en-us','Prof.'),(7,7,'en-us','Sir.'),(8,8,'en-us','Co.'),(9,9,'en-us','GRP');

/*Data for the table `users` */

INSERT  INTO `users`(`userid`,`fname`,`sname`,`loginname`,`pass`,`phone`,`mobile`,`fax`,`email`,`dateregistered`,`countrycode`,`admin`,`guest`,`reservation`,`booking`,`agents`,`rooms`,`billing`,`rates`,`lookup`,`reports`,`policy`) VALUES (2,'admin','','admin','5f4dcc3b5aa765d61d8327deb882cf99',NULL,NULL,NULL,NULL,'2006-07-07',NULL,1,1,1,1,1,1,1,1,1,1,1);

/*Data for the table `version` */

INSERT  INTO `version`(`Major`,`Minor`,`Patch`) VALUES (2,1,1597);
