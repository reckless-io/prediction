<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('prediction/promotions')}
    ADD COLUMN(
        `aov_global` decimal(12,4) DEFAULT NULL COMMENT 'Average Order Value',
        `ltv_global` decimal(12,4) DEFAULT NULL COMMENT 'Life time Value',
        `total_orders` smallint(5) unsigned DEFAULT '0' COMMENT 'Total orders')
;
");

$installer->endSetup();
