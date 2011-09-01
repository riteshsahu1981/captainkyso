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

class VladimirPopov_WebForms_Block_Adminhtml_Fields_Edit_Tab_Information
	extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareLayout(){
		
		parent::_prepareLayout();
	}	
	
	protected function _prepareForm()
	{
		$model = Mage::getModel('webforms/fields');
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('webforms_form',array(
			'legend' => Mage::helper('webforms')->__('Information')
		));
		
		$fieldset->addField('name','text',array(
			'label' => Mage::helper('webforms')->__('Name'),
			'class' => 'required-entry',
			'required' => true,
			'name' => 'name'
		));
		
		$fieldset->addField('result_label','text',array(
			'label' => Mage::helper('webforms')->__('Result label'),
			'required' => false,
			'name' => 'result_label',
			'note' => Mage::helper('webforms')->__('Result label will be used on results page')
		));
		
		$fieldset->addField('code','text',array(
			'label' => Mage::helper('webforms')->__('Code'),
			'name' => 'code',
			'note' => Mage::helper('webforms')->__('Code is used to help identify this field in scripts'),
		));
		
		$fieldset->addField('type', 'select', array(
			'label'     => Mage::helper('webforms')->__('Type'),
			'title'     => Mage::helper('webforms')->__('Type'),
			'name'      => 'type',
			'required'  => false,
			'options'   => $model->getFieldTypes(),
		));
	
		$fieldsetsOptions  = Mage::registry('webforms_data')->getFieldsetsOptionsArray();
		if(count($fieldsetsOptions)>1){
			$fieldset->addField('fieldset_id', 'select', array(
				'label'     => Mage::helper('webforms')->__('Field set'),
				'title'     => Mage::helper('webforms')->__('Field set'),
				'name'      => 'fieldset_id',
				'required'  => false,
				'options'   => $fieldsetsOptions,
			));
		}
		
		$fieldset->addField('value','textarea',array(
			'label' => Mage::helper('webforms')->__('Field value(s)'),
			'required' => false,
			'name' => 'value',
			'note' => Mage::helper('webforms')->__('Values for select type field should be separated with new line (the first listed value is default).<br />You can use following variables for default values:<br />{{firstname}} - logged in customer first name <br />{{lastname}} - logged in customer last name <br />{{email}} - logged in customer e-mail address'),
		));
		
		$fieldset->addField('email_subject', 'select', array(
			'label'     => Mage::helper('webforms')->__('Use as e-mail subject'),
			'title'     => Mage::helper('webforms')->__('Use as e-mail subject'),
			'name'      => 'email_subject',
			'note'		=> Mage::helper('webforms')->__('This field will be used as a subject in notification e-mail'),
			'required'  => false,
			'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('position','text',array(
			'label' => Mage::helper('webforms')->__('Position'),
			'required' => true,
			'name' => 'position',
			'note' => Mage::helper('webforms')->__('Field position in the form relative to field set'),
		));
		
		$fieldset->addField('required', 'select', array(
			'label'     => Mage::helper('webforms')->__('Required'),
			'title'     => Mage::helper('webforms')->__('Required'),
			'name'      => 'required',
			'required'  => false,
			'values'   => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('is_active', 'select', array(
			'label'     => Mage::helper('webforms')->__('Status'),
			'title'     => Mage::helper('webforms')->__('Status'),
			'name'      => 'is_active',
			'note'		=> Mage::helper('webforms')->__('If assigned field set is not active the field won`t be displayed'),
			'required'  => false,
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
		} elseif(Mage::registry('fields_data')){
			$form->setValues(Mage::registry('fields_data')->getData());
		} 
		
		// set default field values
		if(!Mage::registry('fields_data')->getId()){
			$form->setValues(array(
				'webform_id' => $this->getRequest()->getParam('webform_id'),
				'position' => 10
			));
		}
		
		return parent::_prepareForm();
	}
}  
?>
