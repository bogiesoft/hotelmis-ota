USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=0,`Patch`=962;

/*Table structure for table `holidays` */

DROP TABLE IF EXISTS `holidays`;

CREATE TABLE `holidays` (
  `HolidayID` INT(11) NOT NULL AUTO_INCREMENT,
  `Description` VARCHAR(250) DEFAULT NULL,
  `CountryCode` VARCHAR(2) DEFAULT NULL,
  `Holiday` DATETIME DEFAULT NULL,
  PRIMARY KEY (`HolidayID`)
);	
 
ALTER TABLE `booking` ADD COLUMN `res_det_id` INT(255) DEFAULT 0 NULL AFTER `book_status`;
