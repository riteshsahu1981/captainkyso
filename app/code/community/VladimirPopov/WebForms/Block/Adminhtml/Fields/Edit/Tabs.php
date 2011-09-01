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

class VladimirPopov_WebForms_Block_Adminhtml_Fields_Edit_Tabs
	extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('webforms_fields_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('webforms')->__('Field Information'));
	}
	
	protected function _beforeToHtml()
	{
		
		$this->addTab('form_information',array(
			'label' => Mage::helper('webforms')->__('Information'),
			'title' => Mage::helper('webforms')->__('Information'),
			'content' => $this->getLayout()->createBlock('webforms/adminhtml_fields_edit_tab_information')->toHtml(),
		));
		
		$this->addTab('form_fields',array(
			'label' => Mage::helper('webforms')->__('Design'),
			'title' => Mage::helper('webforms')->__('Design'),
			'content' => $this->getLayout()->createBlock('webforms/adminhtml_fields_edit_tab_design')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
?>
