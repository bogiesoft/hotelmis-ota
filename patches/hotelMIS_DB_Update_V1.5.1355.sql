USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=5,`Patch`=1355;

ALTER TABLE `transactions` ADD COLUMN `XOID` INT(11) DEFAULT NULL AFTER `currency`;