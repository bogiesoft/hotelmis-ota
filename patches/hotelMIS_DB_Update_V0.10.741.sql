USE `hotelmis`;
UPDATE `version` SET `Major`=0,`Minor`=10,`Patch`=741;
ALTER TABLE `reservation` ADD COLUMN `fop` INT(11) NULL AFTER `bill_id`, ADD COLUMN `amt` INT(11) DEFAULT 0 NULL AFTER `fop`; 
ALTER TABLE `transactions` ADD COLUMN `currency` VARCHAR(5) NULL AFTER `status`;
ALTER TABLE `receipts` ADD COLUMN `exrate` DECIMAL(10,6) NULL COMMENT 'exchange rate for charged currency from base currency' AFTER `add_date`,  ADD COLUMN `srcCurrency` VARCHAR(5) NULL AFTER `exrate`, ADD COLUMN `tgtCurrency` VARCHAR(5) NULL AFTER `srcCurrency`;
ALTER TABLE `receipts` CHANGE `amount` `amount` DECIMAL(10,2) NULL  COMMENT 'This is the payed amount recorded in source currency, to get the actual amount paid multiply by the exchange rate and use the target currency';
