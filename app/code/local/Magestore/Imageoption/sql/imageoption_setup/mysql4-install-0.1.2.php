<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('imageoption')};
CREATE TABLE {$this->getTable('imageoption')} (
  `imageoption_id` int(11) unsigned NOT NULL auto_increment,
  `option_type_id` int(11) unsigned NOT NULL ,
  `option_id` int(11) unsigned NOT NULL ,
  `product_id` int(11) unsigned NOT NULL ,
  `image` varchar(255) NOT NULL default '',
  `image_width` int(11) unsigned NOT NULL default 60,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`imageoption_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 