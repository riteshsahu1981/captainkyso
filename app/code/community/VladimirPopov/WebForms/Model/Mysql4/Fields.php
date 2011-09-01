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

class VladimirPopov_WebForms_Model_Mysql4_Fields
	extends Mage_Core_Model_Mysql4_Abstract
{

	public function _construct(){
		$this->_init('webforms/fields','id');
	}
	
	public function _afterDelete(Mage_Core_Model_Abstract $object){
		//delete values
		$values = $this->_getReadAdapter()->delete($this->getTable('webforms/results_values'),'field_id ='. $object->getId());	
		return parent::_afterDelete($object);
	}
}  
?>
