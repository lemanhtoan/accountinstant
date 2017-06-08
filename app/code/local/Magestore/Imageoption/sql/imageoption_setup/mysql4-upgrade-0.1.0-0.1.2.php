<?php

$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('imageoption')} 
ADD `image_width` int(11) unsigned NOT NULL default 60  AFTER `image` ;
  
 ");

$installer->endSetup(); 