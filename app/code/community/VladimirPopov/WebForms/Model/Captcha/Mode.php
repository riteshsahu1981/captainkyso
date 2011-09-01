<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Captcha_Mode extends Mage_Core_Model_Abstract{

	public function _construct()
	{
		parent::_construct();
		$this->_init('webforms/captcha_mode');
	}
	
	public function toOptionArray(){
		return array(
			array('value' => 'default' , 'label' => Mage::helper('webforms')->__('Hidden for logged in customers')),
			array('value' => 'always' , 'label' => Mage::helper('webforms')->__('Always on')),
		);
	}
	
}
?>
