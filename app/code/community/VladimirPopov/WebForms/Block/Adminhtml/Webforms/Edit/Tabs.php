<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 * @license    	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VladimirPopov_WebForms_Block_Adminhtml_WebForms_Edit_Tabs
	extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('webforms_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('webforms')->__('Form Information'));
	}
	
	protected function _beforeToHtml()
	{
		
		$this->addTab('form_information',array(
			'label' => Mage::helper('webforms')->__('Information'),
			'title' => Mage::helper('webforms')->__('Information'),
			'content' => $this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_information')->toHtml(),
		));
		
		$this->addTab('form_settings',array(
			'label' => Mage::helper('webforms')->__('Settings'),
			'title' => Mage::helper('webforms')->__('Settings'),
			'content' => $this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_settings')->toHtml(),
		));
		
		if(Mage::registry('webforms_data') && Mage::registry('webforms_data')->getId() ){
			$this->addTab('form_fieldsets',array(
				'label' => Mage::helper('webforms')->__('Field Sets'),
				'title' => Mage::helper('webforms')->__('Field Sets'),
				'content' => $this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_fieldsets')->toHtml(),
			));
			
			$this->addTab('form_fields',array(
				'label' => Mage::helper('webforms')->__('Fields'),
				'title' => Mage::helper('webforms')->__('Fields'),
				'content' => $this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_fields')->toHtml(),
			));
		}
		
		if($this->getRequest()->getParam('tab')){
			$this->setActiveTab($this->getRequest()->getParam('tab'));
		}
		return parent::_beforeToHtml();
	}
}
?>
