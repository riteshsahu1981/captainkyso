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

class VladimirPopov_WebForms_Block_Adminhtml_Fieldsets_Edit_Form
	extends Mage_Adminhtml_Block_Widget_Form
{
	
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		
		$model = Mage::getModel('webforms/fieldsets');
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
		));
		$fieldset = $form->addFieldset('webforms_form',array(
			'legend' => Mage::helper('webforms')->__('Information')
		));
		
		$fieldset->addField('name','text',array(
			'label' => Mage::helper('webforms')->__('Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'name'
		));
		
		$fieldset->addField('position','text',array(
			'label' => Mage::helper('webforms')->__('Position'),
			'required' => true,
			'name' => 'position',
			'note' => Mage::helper('webforms')->__('Fieldset position in the form'),
		));
		
		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('webforms')->__('Status'),
			'title'     => Mage::helper('webforms')->__('Status'),
			'name'      => 'is_active',
			'required'  => true,
			'options'   => Mage::getModel('webforms/webforms')->getAvailableStatuses(),
		));
		
		$fieldset->addField('webform_id', 'hidden', array(
			'name'      => 'webform_id',
			'value'   => 1,
		));
		
		
		if (!$model->getId()) {
			$model->setData('is_active', '0');
		}
		
		if(Mage::getSingleton('adminhtml/session')->getWebFormsData())
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getWebFormsData());
			Mage::getSingleton('adminhtml/session')->setWebFormsData(null);
		} elseif(Mage::registry('fieldsets_data')){
			$form->setValues(Mage::registry('fieldsets_data')->getData());
		} 
		
		// set default field values
		if(!Mage::registry('fieldsets_data')->getId()){
			$form->setValues(array(
				'webform_id' => $this->getRequest()->getParam('webform_id'),
				'position' => 10
			));
		}
		$form->setUseContainer(true);
		$this->setForm($form);
		
	}
}  
?>
