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

class VladimirPopov_WebForms_Model_Webforms extends Mage_Core_Model_Abstract{

	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	public function _construct(){
		parent::_construct();
		$this->_init('webforms/webforms');
	}
	
	public function getAvailableStatuses(){
		$statuses = new Varien_Object(array(
			self::STATUS_ENABLED => Mage::helper('webforms')->__('Enabled'),
			self::STATUS_DISABLED => Mage::helper('webforms')->__('Disabled'),
		));

		Mage::dispatchEvent('webforms_statuses', array('statuses' => $statuses));

		return $statuses->getData();
		
	}
	
	/**
	 * Provide available options as a value/label array
	 *
	 * @return array
	 */
	public function toOptionArray(){
		$collection = $this->getCollection()->addFilter('is_active',self::STATUS_ENABLED)->addOrder('name','asc');
		$option_array = array();
		foreach($collection as $webform)
			$option_array[]= array('value'=>$webform->getId(), 'label' => $webform->getName());
		return $option_array;
	}
	
	public function getFieldsetsOptionsArray(){
		$collection = Mage::getModel('webforms/fieldsets')->getCollection()->addFilter('webform_id',$this->getId());
		if((float)substr(Mage::getVersion(),0,3)>1)
			$collection->addOrder('position','asc');
		else
			$collection->getSelect()->order('position asc');
			
		$options = array(0 =>'...');
		foreach($collection as $o){
			$options[$o->getId()]= $o->getName();
		}
		return $options;
	}
	
	public function getEmailSettings(){
		$settings["email_enable"] = $this->getSendEmail();
		$settings["email"] = Mage::getStoreConfig('webforms/email/email');
		if($this->getEmail())
			$settings["email"] = $this->getEmail();
		return $settings;
	}
	
	public function getFieldsToFieldsets(){
		//get form fieldsets
		$fieldsets = Mage::getModel('webforms/fieldsets')->getCollection()
			->addFilter('webform_id',$this->getId())
			->addFilter('is_active', self::STATUS_ENABLED);
		if((float)substr(Mage::getVersion(),0,3)>1)
			$fieldsets->addOrder('position','asc');
		else $fieldsets->getSelect()->order('position asc');
		
		//get form fields
		$fields = Mage::getModel('webforms/fields')->getCollection()
			->addFilter('webform_id',$this->getId())
			->addFilter('is_active', self::STATUS_ENABLED);
		if((float)substr(Mage::getVersion(),0,3)>1)
			$fields->addOrder('position','asc');
		else
			$fields->getSelect()->order('position asc');
		
		//fields to fieldsets
		//make zero fieldset
		$fields_to_fieldsets = array();
		foreach($fields as $field){
			if($field->getFieldsetId() == 0){
				$fields_to_fieldsets[0]['fields'][] = $field;
			}
		}
		
		foreach($fieldsets as $fieldset){
			$fields_to_fieldsets[$fieldset->getId()]['name'] = $fieldset->getName();
			foreach($fields as $field){
				if($field->getFieldsetId() == $fieldset->getId())
					$fields_to_fieldsets[$fieldset->getId()]['fields'][] = $field;
			}
		}
		
		return $fields_to_fieldsets;
		
	}
}
?>
