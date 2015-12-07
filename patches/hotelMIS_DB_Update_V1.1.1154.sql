USE `hotelmis`;

UPDATE `version` SET `Major`=1,`Minor`=1,`Patch`=1154;

CREATE TABLE `agent_bookref`( 
	`agent_bookrefid` INT(255) NOT NULL AUTO_INCREMENT, 
	`reservation_id` INT(11) NOT NULL, 
	`agentid` INT(11) NOT NULL, 
	`refno` CHAR(20), 
	PRIMARY KEY (`agent_bookrefid`) 
); 

