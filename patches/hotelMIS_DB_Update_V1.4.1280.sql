USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=4,`Patch`=1280;

CREATE TABLE `customfields`( 
	`customfldid` INT(11) NOT NULL AUTO_INCREMENT, 
	`pageid` INT(11), 
	`fieldreq` TINYINT DEFAULT 0, 
	`fieldtype` INT DEFAULT 0, 
	`label` VARCHAR(100), 
	PRIMARY KEY (`customfldid`) 
);

CREATE TABLE `customvalues`( 
	`valueid` INT(11) NOT NULL AUTO_INCREMENT, 
	`customfldid` INT(11), 
	`pageid` INT(11), 
	`transid` INT(11), 
	`customvalue` MEDIUMBLOB, 
	PRIMARY KEY (`valueid`) 
); 

CREATE TABLE `otadata` (
  `otaid` int(11) NOT NULL AUTO_INCREMENT,
  `agentid` int(11) NOT NULL,
  `label` varchar(100) DEFAULT NULL,
  `otatype` int(11) DEFAULT NULL COMMENT 'Agoda, expedia, etc',
  `key1` mediumblob COMMENT 'key data by ota type',
  `key2` mediumblob COMMENT 'key data by ota type',
  `key3` mediumblob COMMENT 'key data by ota type',
  `key4` mediumblob COMMENT 'key data by ota type',
  `key5` mediumblob COMMENT 'key data by ota type',
  PRIMARY KEY (`otaid`)
) ;


CREATE TABLE `otasync` (
  `syncid` int(11) NOT NULL AUTO_INCREMENT,
  `otaid` int(11) DEFAULT NULL,
  `ratesid` int(11) DEFAULT NULL,
  `lastsync` datetime DEFAULT NULL COMMENT 'lastsync should be after last modified indicating no sync required',
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`syncid`)
);


CREATE TABLE `otasynclog` (
  `syncid` int(11) DEFAULT NULL,
  `synctime` datetime DEFAULT NULL,
  `syncxml` mediumtext
) ;

 

CREATE TABLE `otaalloc`(
	`allocationid` INT NOT NULL AUTO_INCREMENT, 
	`ratesid` INT(11), 
	`roomid` INT(11) DEFAULT 0, 
	`roomtypeid` INT(11) DEFAULT 0, 
	`roomcount` INT(11) DEFAULT 0, 
	PRIMARY KEY (`allocationid`) 
);