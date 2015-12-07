USE `hotelmis`;

UPDATE `version` SET `Major`=2,`Minor`=1,`Patch`=1597;
ALTER TABLE otacloseout ADD ratevalue FLOAT DEFAULT 0;
ALTER TABLE `bills` ADD COLUMN `notes` TEXT NULL;
ALTER TABLE `bills` ADD COLUMN `flags` INT DEFAULT 0 NULL; 

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

drop table otasync;
CREATE TABLE `otasync`( 
  `syncid` INT NOT NULL AUTO_INCREMENT, 
  `ratesid` INT COMMENT 'Rate ID', 
  `roomtypeid` INT COMMENT 'Room type id', 
  `start` DATE, `end` DATE, 
  `syncdatetime` DATETIME, 
  PRIMARY KEY (`syncid`) 
);

