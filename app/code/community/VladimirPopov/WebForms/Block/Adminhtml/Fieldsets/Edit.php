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

class VladimirPopov_WebForms_Block_Adminhtml_Fieldsets_Edit
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'webforms';
		$this->_controller = 'adminhtml_fieldsets';

	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/adminhtml_webforms/save',array('webform_id'=>Mage::registry('webforms_data')->getId()));
	}
	
	public function getBackUrl(){
		return $this->getUrl('*/adminhtml_webforms/edit',array('id'=>Mage::registry('webforms_data')->getId()));
	}

	public function getHeaderText()
	{
		if(!is_null(Mage::registry('fieldsets_data')->getId())) {
			return Mage::helper('webforms')->__("Edit Field Set '%s'", $this->htmlEscape(Mage::registry('fieldsets_data')->getName()));
		} else {
			return Mage::helper('webforms')->__('New Field Set');
		}
	}

}  
?>
