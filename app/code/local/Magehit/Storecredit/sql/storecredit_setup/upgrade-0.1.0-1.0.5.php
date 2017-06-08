<?php
    $installer  = $this;
    $installer->startSetup();
    $sql = "ALTER TABLE {$installer->getTable('catalogrule')} ADD `store_credit`  float(11) unsigned NOT NULL;";

    $installer->run($sql);
    $installer->endSetup();
?>