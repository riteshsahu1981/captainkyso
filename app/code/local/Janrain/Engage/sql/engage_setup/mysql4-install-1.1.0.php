<?php

$installer = $this;

$installer->startSetup();
$installer->run("
CREATE TABLE `{$installer->getTable('engage_identifiers')}` (
  `engage_identifier_id` int(11) NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) NOT NULL,
  `customer_id` int(11) unsigned NOT NULL,
  `profile_name` varchar(100) NOT NULL,
  `provider` varchar(50) NOT NULL,
  PRIMARY KEY (`engage_identifier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");
$installer->endSetup();
