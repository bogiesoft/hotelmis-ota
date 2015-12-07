USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=2,`Patch`=1210;

ALTER TABLE `reservation` CHANGE `amt` `amt` DECIMAL(10,2) DEFAULT '0';
ALTER TABLE `hotelgallery` ADD COLUMN `page` INT NOT NULL DEFAULT '0' COMMENT '0 gallery 1 promo' AFTER `URL`, ADD COLUMN `imgtype` INT NOT NULL DEFAULT '0' COMMENT '0 img 1 video' AFTER `page`;