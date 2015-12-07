USE `hotelmis`;

UPDATE `version` SET `Major`=0,`Minor`=16,`Patch`=961;

/*Table structure for table `emailsetup` */

DROP TABLE IF EXISTS `emailsetup`;

 CREATE TABLE `emailsetup`(
`mailServer` VARCHAR(100) ,
`secureprotocol` VARCHAR(10) ,
`port` VARCHAR(10),
`emailaddress` VARCHAR(255) ,
`authenticate` TINYINT(1) ,
`username` VARCHAR(50) ,
`password` VARCHAR(50) 
 );

