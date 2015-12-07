USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=5,`Patch`=1335;

ALTER TABLE `otaalloc` ADD COLUMN `guaranteecount` INT(11) DEFAULT 0 NULL AFTER `roomcount`;
CREATE TABLE `otacloseout`( `otacloseid` INT(11) NOT NULL AUTO_INCREMENT, `ratesid` INT(11), `roomtypeid` INT(11), `closeregular` BOOL DEFAULT FALSE, `closeguarantee` BOOL DEFAULT FALSE, `totalrooms` INT DEFAULT 0, `guaranteerooms` INT DEFAULT 0, `closedate` DATE DEFAULT NULL, PRIMARY KEY (`otacloseid`) ); 
ALTER TABLE `otasynclog` ADD COLUMN `otatype` INT(11) DEFAULT 0 NULL AFTER `syncxml`;