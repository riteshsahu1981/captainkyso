<?php

class Janrain_Engage_Model_Mysql4_Identifiers extends Mage_Core_Model_Mysql4_Abstract {

	protected function _construct() {
		$this->_init('engage/identifiers', 'engage_identifier_id');
	}

}