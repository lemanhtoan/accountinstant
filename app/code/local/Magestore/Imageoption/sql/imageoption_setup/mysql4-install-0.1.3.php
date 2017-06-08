<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('imageoption')};
CREATE TABLE {$this->getTable('imageoption')} (
  `imageoption_id` int(11) unsigned NOT NULL auto_increment,
  `option_type_id` int(10) unsigned NOT NULL ,
  `option_id` int(10) unsigned NOT NULL ,
  `product_id` int(10) unsigned NOT NULL ,
  `image` varchar(255) NOT NULL default '',
  `image_width` int(11) unsigned NOT NULL default 60,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  `is_template` tinyint(1) default '0',
  UNIQUE ( `option_type_id`, `option_id`,`product_id` ),
  INDEX (`product_id`),
  INDEX (`option_id`),
  INDEX (`option_type_id`),
  FOREIGN KEY ( `option_id` ) REFERENCES {$this->getTable('catalog/product_option')} ( `option_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,    
  FOREIGN KEY ( `option_type_id` ) REFERENCES {$this->getTable('catalog_product_option_type_value')} ( `option_type_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,    
  FOREIGN KEY ( `product_id` ) REFERENCES {$this->getTable('catalog/product')} ( `entity_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,    

  PRIMARY KEY (`imageoption_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
DROP TABLE IF EXISTS {$this->getTable('imageoption_template')};
CREATE TABLE {$this->getTable('imageoption_template')} (
  `template_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `short_descrip` varchar(255) NOT NULL default '',
  `description` text NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL ,
  `created_time` datetime NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('imageoption_template_option')};
CREATE TABLE {$this->getTable('imageoption_template_option')} (
  `template_option_id` int(11) unsigned NOT NULL auto_increment,
  `template_id` int(11) unsigned NOT NULL,
  `option_id` int(10) unsigned NOT NULL,
  `created_time` datetime NULL,
  UNIQUE (`template_id` ,`option_id`),
  INDEX ( `template_id` ),
  INDEX ( `option_id` ),
  FOREIGN KEY ( `template_id` ) REFERENCES {$this->getTable('imageoption_template')} ( `template_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,  
  FOREIGN KEY ( `option_id` ) REFERENCES {$this->getTable('catalog/product_option')} ( `option_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,  
  PRIMARY KEY (`template_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('imageoption_template_product')};
CREATE TABLE {$this->getTable('imageoption_template_product')} (
  `template_product_id` int(11) unsigned NOT NULL auto_increment,
  `template_id` int(11) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `applied` tinyint(1) default '0',
  `created_time` datetime NULL,
  UNIQUE (`template_id` ,`product_id`), 
  FOREIGN KEY ( `template_id` ) REFERENCES {$this->getTable('imageoption_template')} ( `template_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,    
  FOREIGN KEY ( `product_id` ) REFERENCES {$this->getTable('catalog/product')} ( `entity_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,    
  PRIMARY KEY (`template_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('imageoption_template_product_optionsmap')};
CREATE TABLE {$this->getTable('imageoption_template_product_optionsmap')} (
  `optionmap_id` int(11) unsigned NOT NULL auto_increment,
  `template_option_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_option_id` int(10) unsigned NOT NULL DEFAULT '0',
  `template_product_id` int(11) unsigned NOT NULL,
  UNIQUE (`template_option_id` ,`product_option_id`), 
  PRIMARY KEY (`optionmap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('imageoption_template_product_optiontypesmap')};
CREATE TABLE {$this->getTable('imageoption_template_product_optiontypesmap')} (
  `optiontypemap_id` int(11) unsigned NOT NULL auto_increment,
  `template_option_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_option_type_id` int(10) unsigned NOT NULL DEFAULT '0',
  `optionmap_id` int(11) unsigned NOT NULL,
  UNIQUE (`template_option_type_id` ,`product_option_type_id`), 
  INDEX ( `optionmap_id` ),
  FOREIGN KEY ( `optionmap_id` ) REFERENCES {$this->getTable('imageoption_template_product_optionsmap')} ( `optionmap_id` ) ON DELETE CASCADE ON UPDATE CASCADE ,      
  PRIMARY KEY (`optiontypemap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM {$this->getTable('catalog/product')} WHERE `entity_id` = '0';

INSERT INTO {$this->getTable('catalog/product')} VALUES(0,1,1,'simple',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,0);
 
UPDATE {$this->getTable('catalog/product')} SET entity_id = '0' WHERE entity_type_id='1' and attribute_set_id = '1';
 
    ");	

$installer->endSetup(); 