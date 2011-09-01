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

class VladimirPopov_WebForms_Model_Mysql4_Results_Collection
	extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function _construct(){
		parent::_construct();
		$this->_init('webforms/results');
	}
	
	protected function _afterLoad()
	{
		parent::_afterLoad();
		foreach ($this as $item) {
			$query = $this->getConnection()->select()
				->from($this->getTable('webforms/results_values'))
				->where($this->getTable('webforms/results_values').'.result_id = '.$item->getId())
				;	
			$results = $this->getConnection()->fetchAll($query);
			foreach($results as $result){
				$item->setData('field_'.$result['field_id'],$result['value']);
			}
			
			$item->setData('ip',long2ip($item->getCustomerIp()));
			
			if($item->getCustomerId()){
				$item->setData('customer',Mage::getModel('customer/customer')->load($item->getCustomerId())->getName());
			} else {
				$item->setData('customer',Mage::helper('webforms')->__('Guest'));
			}
		}
		return $this;
	}
}  
?>
