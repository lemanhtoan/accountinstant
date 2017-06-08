<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('thienha_webmoney')};
CREATE TABLE {$this->getTable('thienha_webmoney')} (
	`transaction_id` int(11) unsigned NOT NULL auto_increment,
	`state` enum('INI','RES','SUC','INV','FAL') NOT NULL default 'INI',
	`timestamp` datetime NOT NULL default '0000-00-00 00:00:00',
	`order_id` varchar(50) NOT NULL default '',
	`method` varchar(255) default NULL,
	`rnd` varchar(8) default NULL,
	`lmi_sys_invs_no` int(11) default NULL,
	`lmi_sys_trans_no` int(11) default NULL,
	`lmi_sys_trans_date` varchar(17) default NULL,
	`lmi_payer_purse` varchar(13) default NULL,
	`lmi_payer_wm` varchar(12) default NULL,
	PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();