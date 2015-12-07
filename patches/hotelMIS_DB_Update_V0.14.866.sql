USE `hotelmis`;

UPDATE `version` SET `Major`=0,`Minor`=14,`Patch`=866;

/*Table structure for table `advaddresses` */

DROP TABLE IF EXISTS `advaddresses`;

CREATE TABLE `advaddresses` (
  `addressid` int(225) NOT NULL AUTO_INCREMENT COMMENT 'Database index for address',
  `parentid` int(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` int(225) NOT NULL COMMENT 'Database profile id',
  `addresstype` int(11) DEFAULT NULL COMMENT 'Address types see OTA AUT types',
  `clt` int(11) DEFAULT NULL COMMENT 'Communication location type',
  `blk` varchar(10) DEFAULT NULL COMMENT 'Blk/House number',
  `building` varchar(64) DEFAULT NULL COMMENT 'Building name',
  `floor` varchar(30) DEFAULT NULL COMMENT 'Floor number or name',
  `unit` varchar(30) DEFAULT NULL COMMENT 'Unit number',
  `street` varchar(64) DEFAULT NULL COMMENT 'Street name',
  `city` varchar(64) DEFAULT NULL COMMENT 'City Name',
  `postcode` varchar(16) DEFAULT NULL COMMENT 'Postal Code or Zip code',
  `countrycode` varchar(2) DEFAULT NULL COMMENT '2 letter country code see countries table',
  `state` varchar(64) DEFAULT NULL COMMENT 'State or province',
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`addressid`)
) DEFAULT CHARSET=latin1;

/*Table structure for table `advdocuments` */

DROP TABLE IF EXISTS `advdocuments`;

CREATE TABLE `advdocuments` (
  `documentid` int(225) NOT NULL AUTO_INCREMENT COMMENT 'Document id',
  `parentid` int(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` int(225) NOT NULL COMMENT 'Profile id',
  `doctype` int(11) DEFAULT NULL COMMENT 'OTA DOC type',
  `validfrom` datetime DEFAULT NULL COMMENT 'Valid start date time',
  `validto` datetime DEFAULT NULL COMMENT 'Valid until date time',
  `issuer` varchar(50) DEFAULT NULL COMMENT 'Issuer',
  `issuecountrycode` varchar(5) DEFAULT NULL COMMENT 'Issuer Country Code',
  `nationality` varchar(5) DEFAULT NULL COMMENT 'Nationality- 2 letter Country code',
  `name` varchar(210) DEFAULT NULL COMMENT 'Name on document',
  `docnumber` varchar(50) DEFAULT NULL COMMENT 'Document number',
  `issuelocation` varchar(64) DEFAULT NULL COMMENT 'Issuing location',
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`documentid`)
) DEFAULT CHARSET=latin1;

/*Table structure for table `advemails` */

DROP TABLE IF EXISTS `advemails`;

CREATE TABLE `advemails` (
  `emailid` int(225) NOT NULL AUTO_INCREMENT,
  `parentid` int(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` int(225) NOT NULL COMMENT 'Database profile id',
  `addresstype` int(11) DEFAULT NULL COMMENT 'Email or IM 0 email, 1 IM',
  `EAT` int(11) DEFAULT '0' COMMENT 'Email Address Type from OTA see EAT',
  `addr` varchar(128) DEFAULT NULL COMMENT 'Email or IM address',
  `IMT` varchar(100) DEFAULT NULL COMMENT 'Instant Messenger type string - Skype, MSN, Yahoo etc',
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`emailid`)
) DEFAULT CHARSET=latin1;

/*Table structure for table `advphones` */

DROP TABLE IF EXISTS `advphones`;

CREATE TABLE `advphones` (
  `telephoneid` int(225) NOT NULL AUTO_INCREMENT COMMENT 'Database phone id',
  `parentid` int(225) NOT NULL COMMENT 'Parent Detail for linked detail set',
  `profileid` int(225) NOT NULL COMMENT 'Database profile id',
  `ptt` int(11) DEFAULT NULL COMMENT 'Phone technology type see PTT',
  `put` int(11) DEFAULT NULL COMMENT 'Phone usage type see PUT',
  `plt` int(11) DEFAULT NULL COMMENT 'Phone location type see PLT',
  `countrycode` varchar(3) DEFAULT NULL,
  `areacode` varchar(8) DEFAULT NULL,
  `phonenumber` varchar(32) DEFAULT NULL,
  `ext` varchar(8) DEFAULT NULL COMMENT 'Phone Extension Code',
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`telephoneid`)
) DEFAULT CHARSET=latin1;

/*Table structure for table `advprofile` */

DROP TABLE IF EXISTS `advprofile`;

CREATE TABLE `advprofile` (
  `profileid` int(225) NOT NULL AUTO_INCREMENT COMMENT 'profile ID',
  `parentid` int(225) DEFAULT NULL COMMENT 'parent detail ID for linked detail set',
  `salutation` varchar(10) DEFAULT NULL COMMENT 'Mr Mrs Miss Dr Sir',
  `firstname` varchar(64) DEFAULT NULL,
  `middlename` varchar(64) DEFAULT NULL,
  `lastname` varchar(64) DEFAULT NULL,
  `dob` date DEFAULT NULL COMMENT 'Date of birth',
  `gender` varchar(1) DEFAULT NULL COMMENT 'M/F',
  `lang` varchar(10) DEFAULT NULL COMMENT 'language code - en',
  `altlang` varchar(10) DEFAULT NULL COMMENT 'alternate language code',
  `ebridgeid` varchar(64) DEFAULT NULL COMMENT 'e-Bridge ID',
  `comments` blob,
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`profileid`)
) DEFAULT CHARSET=latin1;

ALTER TABLE `reservation` 
ADD COLUMN `booked_by_ebridgeid` VARCHAR(100) NULL COMMENT 'The ebridge id from which the booking informaation is received' AFTER `amt`, 
ADD COLUMN `cancelled_by_ebridgeid` VARCHAR(100) NULL COMMENT 'The ebridge id from which the cancellation request is received' AFTER `booked_by_ebridgeid`, 
ADD COLUMN `cancelled_date` DATETIME NULL COMMENT 'The cancelled date and time' AFTER `cancelled_by_ebridgeid`;

ALTER TABLE `guests`
ADD COLUMN `useAdvProfile` TINYINT(1) DEFAULT '0' NULL AFTER `nationality`;