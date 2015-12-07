USE `hotelmis`;
ALTER TABLE `guests` CHANGE `phone` `phone` VARCHAR(50) NULL; 
ALTER TABLE `guests` CHANGE `area` `areacode` INT(4) NULL;
CREATE TABLE `paygateway_list` (
  `pgid` int(11) NOT NULL AUTO_INCREMENT,
  `payment_gateway` varchar(50) NOT NULL,
  `paymentform` varchar(50) NOT NULL COMMENT '@see FOP Form of payments',
  PRIMARY KEY (`pgid`)
);
CREATE TABLE `payment_gateways` (
  `gateid` int(11) NOT NULL AUTO_INCREMENT,
  `paymentgateway` varchar(50) DEFAULT NULL,
  `accname` varchar(50) NOT NULL,
  `accid` varchar(50) DEFAULT NULL,
  `swiftcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`gateid`)
) ;
CREATE TABLE `policy` (
  `idpolicy` int(11) NOT NULL AUTO_INCREMENT,
  `ID` varchar(45) DEFAULT NULL,
  `rateid` varchar(45) DEFAULT NULL,
  `title` varchar(45) DEFAULT NULL,
  `language` varchar(15) DEFAULT NULL,
  `description` blob,
  PRIMARY KEY (`idpolicy`)
)
UPDATE `version` SET `Major`=0,`Minor`=6,`Patch`=637;