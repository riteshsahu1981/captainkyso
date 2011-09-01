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

class VladimirPopov_WebForms_Block_Adminhtml_Fields_Edit_Tab_Design
	extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout(){
		
		parent::_prepareLayout();
	}	
	
	protected function _prepareForm()
	{
		$model = Mage::getModel('webforms/webforms');
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('webforms_form',array(
			'legend' => Mage::helper('webforms')->__('Design')
		));
		
		
		$fieldset->addField('size','select',array(
			'label' => Mage::helper('webforms')->__('Size'),
			'name' => 'size',
			'values'   => Mage::getModel('webforms/fields')->getSizeTypes(),
			'note' => Mage::helper('webforms')->__('Standard - two neighbour fields will be merged in one row<br />Wide - field will be wide and single in a row')
		));
		
		$fieldset->addField('css_class','text',array(
			'label' => Mage::helper('webforms')->__('Additional CSS class'),
			'name' => 'css_class',
			'note' => Mage::helper('webforms')->__('You can also use it for additional field validation (see Prototype validation classes)')
		));
		
		$fieldset->addField('css_style','text',array(
			'label' => Mage::helper('webforms')->__('Additional CSS style'),
			'name' => 'css_style'
		));
		
		if(Mage::getSingleton('adminhtml/session')->getWebFormsData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getWebFormsData());
			Mage::getSingleton('adminhtml/session')->setWebFormsData(null);
		} elseif(Mage::registry('fields_data')){
			$form->setValues(Mage::registry('fields_data')->getData());
		}
		return parent::_prepareForm();
	}
}  
?>
