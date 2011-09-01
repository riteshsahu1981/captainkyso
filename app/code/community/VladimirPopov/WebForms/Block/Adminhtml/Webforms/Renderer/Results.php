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

class VladimirPopov_WebForms_Block_Adminhtml_Webforms_Renderer_Results extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
 
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		return $value.' [ <a href="#" style="text-decoration:none" onclick="setLocation(\''.$this->getAddFieldUrl($row).'\')">'.Mage::helper('webforms')->__('View').'</a> ]';
	 
	 }
	 
	 public function getAddFieldUrl(Varien_Object $row)
	 {
		 return $this->getUrl('*/adminhtml_results',array('webform_id'=>$row->getId()));
	 }
 
}
?>