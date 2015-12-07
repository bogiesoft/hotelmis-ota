USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=3,`Patch`=1240;

CREATE TABLE `shifts`( `userid` SMALLINT NOT NULL, `startshift` DATETIME NOT NULL, `endshift` DATETIME NOT NULL, `notes` TEXT NOT NULL ); 