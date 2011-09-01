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

class VladimirPopov_WebForms_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Page_Menu
{
	public function getMenuArray()
	{
		//Load standard menu
		$parentArr = parent::getMenuArray();
		
		$collection = Mage::getModel('webforms/webforms')->getCollection()
			->addFilter('is_active','1');
			
		$collection->getSelect()->order('name asc')->limit(3);
		
		$totalCount= count($collection);
		
		//Update all previous menu items 
		if($totalCount){
			foreach($parentArr['webforms']['children'] as $i=>$item){
				$parentArr['webforms']['children'][$i]['last'] = false;
			}
		}
		
		foreach($collection as $webform){
			$menuitem = array(
				'label' => $webform->getName(),
				'active' => false ,
				'sort_order' => $i++ * 10,
				'level' => 1,
				'url' => $this->getUrl('webforms/adminhtml_results',array('webform_id'=>$webform->getId()))
			);
			$parentArr['webforms']['children'][]= $menuitem;
		}
		
		$configItem    = array(
			'label' => $this->__('Forms Settings'),
			'active' => false ,
			'sort_order' => $i++ * 10,
			'level' => 1,
			'url' => $this->getUrl('adminhtml/system_config/edit/section/webforms'),
			'last' => true
		);
		
		$parentArr['webforms']['children'][]= $configItem;
	
		return $parentArr;
	}
}