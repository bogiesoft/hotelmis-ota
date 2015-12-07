USE `hotelmis`;
ALTER TABLE `booking` CHANGE `CCnum` `CCnum` VARCHAR(19) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;

ALTER TABLE `guests`     
CHANGE `lastname` `lastname` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,     
CHANGE `firstname` `firstname` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `middlename` `middlename` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `pp_no` `pp_no` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `street_name` `street_name` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,    
CHANGE `town` `town` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `postal_code` `postal_code` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,   
CHANGE `access` `access` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,  
CHANGE `areacode` `areacode` VARCHAR(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `email` `email` VARCHAR(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `mobilephone` `mobilephone` VARCHAR(45) NULL ;

ALTER TABLE `receipts`     
CHANGE `auth` `auth` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ,     
CHANGE `name` `name` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL  COMMENT 'name on card/cheque';

ALTER TABLE `reservation`     
CHANGE `CCnum` `CCnum` VARCHAR(19) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
CHANGE `reservation_by` `reservation_by` VARCHAR(300) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL ;

UPDATE `salutation` SET `saluteid`='1',`salute`='1',`lang`='en-us',`Description`='Mr.' WHERE `saluteid`='1';
UPDATE `salutation` SET `saluteid`='2',`salute`='2',`lang`='en-us',`Description`='Ms.' WHERE `saluteid`='2';
UPDATE `salutation` SET `saluteid`='4',`salute`='4',`lang`='en-us',`Description`='Mrs.' WHERE `saluteid`='4';
UPDATE `salutation` SET `saluteid`='5',`salute`='5',`lang`='en-us',`Description`='Dr.' WHERE `saluteid`='5';
UPDATE `salutation` SET `saluteid`='7',`salute`='7',`lang`='en-us',`Description`='Sir.' WHERE `saluteid`='7';
UPDATE `salutation` SET `saluteid`='6',`salute`='6',`lang`='en-us',`Description`='Prof.' WHERE `saluteid`='6';
DELETE FROM `salutation` WHERE `saluteid`='8';
DELETE FROM `salutation` WHERE `saluteid`='9';
UPDATE `version` SET `Major`=0,`Minor`=7,`Patch`=650;
