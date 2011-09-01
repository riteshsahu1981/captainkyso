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

class VladimirPopov_WebForms_Block_Adminhtml_Fields_Edit 
	extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'webforms';
		$this->_controller = 'adminhtml_fields';
		
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',Mage::registry('webforms_data')->getId())->count();
		if($fields>10){
			$this->_removeButton('save');
			$this->_addButton('save',array(
				'label' => $this->__('Save'),
				'onclick' => 'alert(\''.Mage::helper('webforms')->__('You have exceeded Community Edition limit!\nCommunity Edition allows you to have only 10 fields in web-form.\nUpgrade to Professional Edition.').'\')',
			));
		}
	}
	
	public function getSaveUrl()
	{
		return $this->getUrl('*/adminhtml_webforms/save',array('webform_id'=>Mage::registry('webforms_data')->getId()));
	}
	
	public function getBackUrl(){
		return $this->getUrl('*/adminhtml_webforms/edit',array('id'=>Mage::registry('webforms_data')->getId()));
	}
	
	public function getHeaderText(){
		if( Mage::registry('fields_data') && Mage::registry('fields_data')->getId() ) {
			return Mage::helper('webforms')->__("Edit '%s' Field - %s", $this->htmlEscape(Mage::registry('fields_data')->getName()), $this->htmlEscape($this->htmlEscape(Mage::registry('webforms_data')->getName())));
		} else {
			return Mage::helper('webforms')->__('Add Field - %s',$this->htmlEscape(Mage::registry('webforms_data')->getName()));
		}
	}

}  
?>
