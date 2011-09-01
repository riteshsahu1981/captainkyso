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

class VladimirPopov_WebForms_Model_Mysql4_Results
	extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('webforms/results','id');
	}
	
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		
		if (! $object->getId() && $object->getCreatedTime() == "") {
			$object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
		}
		
		$object->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());
		
		return $this;
	}
	
	public function _afterSave(Mage_Core_Model_Abstract $object){
		//insert field values
		if(count($object->getData('field'))>0){
			foreach($object->getData('field') as $field_id=>$value){
				if(is_array($value)){
					$value = implode("",$value);
				}
				$this->_getWriteAdapter()->insert($this->getTable('webforms/results_values'),array(
					"result_id" => $object->getId(),
					"field_id" => $field_id,
					"value" => $value,
				));
			}
		}
		
		return parent::_afterSave($object);
	}
	
	public function _afterLoad(Mage_Core_Model_Abstract $object){
		$webform = Mage::getModel('webforms/webforms')->load($object->getData('webform_id'));
		$fields_to_fieldsets = $webform->getFieldsToFieldsets();

		$select = $this->_getReadAdapter()->select()
			->from($this->getTable('webforms/results_values'))
			->where('result_id = ?', $object->getId());	
		$values = $this->_getReadAdapter()->fetchAll($select);
		
		foreach($values as $val){
			$object->setData('field_'.$val['field_id'],$val['value']);
		}
		
		$object->setData('ip',long2ip($object->getCustomerIp()));
		if($object->getCustomerId()){
			$object->setData('customer',Mage::getModel('customer/customer')->load($object->getCustomerId())->getName());
		} else {
			$object->setData('customer',Mage::helper('webforms')->__('Guest'));
		}
		return parent::_afterLoad($object);
	}
	
	public function _afterDelete(Mage_Core_Model_Abstract $object){
		//delete values
		$values = $this->_getReadAdapter()->delete($this->getTable('webforms/results_values'),
			'result_id = '. $object->getId()
		);	
		
		return parent::_afterDelete($object);
	}
}  
?>
