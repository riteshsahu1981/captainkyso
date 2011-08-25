<?php

class Janrain_Engage_Model_Observer {

	public function addIdentifier($observer) {
		if($profile = Mage::getSingleton('engage/session')->getIdentifier()) {
			Mage::helper('engage/identifiers')
				->save_identifier($observer->getCustomer()->getId(), $profile);
			Mage::getSingleton('engage/session')->setIdentifier(false);
		}
	}

	public function onConfigSave($observer) {
		if(Mage::getStoreConfig('engage/vars/apikey') != Mage::getStoreConfig('engage/options/apikey') || strlen(Mage::getStoreConfig('engage/vars/appid'))<1){
			Mage::helper('engage/rpxcall')->rpxLookupSave();
		}
	}

}