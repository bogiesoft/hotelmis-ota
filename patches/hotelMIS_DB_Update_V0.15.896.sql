USE `hotelmis`;

UPDATE `version` SET `Major`=0,`Minor`=15,`Patch`=896;

/*Table structure for table `reservation_details` */

DROP TABLE IF EXISTS `reservation_details`;

CREATE TABLE `reservation_details`(
     `id` INT(255) NOT NULL AUTO_INCREMENT ,
     `reservation_id` INT(255) NOT NULL ,
     `roomid` INT(255) ,
     `roomtypeid` INT(255) ,
     `ratesid` INT(255) ,
     `quantity` INT(255) ,
	 `status` int(11) DEFAULT 1,
     PRIMARY KEY (`id`)
 ) DEFAULT CHARSET=latin1;
 
 /* Fixed YEAR def 0x0FFF0000 */
 UPDATE rateitems SET validperiod = ((validperiod & 65535) | 268369920) WHERE  validperiod & 16773120 = 16773120;