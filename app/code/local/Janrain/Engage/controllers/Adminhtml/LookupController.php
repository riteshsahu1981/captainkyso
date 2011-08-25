<?php

class Janrain_Engage_Adminhtml_LookupController extends Mage_Adminhtml_Controller_action {

	public function rpAction() {
		if(Mage::helper('engage/rpxcall')->rpxLookupSave())
			Mage::getSingleton('core/session')->addSuccess('Engage account data successfully retrieved');
		else
			Mage::getSingleton('core/session')->addError('Engage account data could not be updated');
		$this->_redirect('adminhtml/system_config/edit/section/engage');
	}

}