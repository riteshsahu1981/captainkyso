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

class VladimirPopov_WebForms_Block_Adminhtml_Webforms extends Mage_Adminhtml_Block_Widget_Grid_Container{
	public function __construct(){
		$this->_controller = 'adminhtml_webforms';
		$this->_blockGroup = 'webforms';
		$this->_headerText = Mage::helper('webforms')->__('Manage Forms');
		$this->_addButtonLabel = Mage::helper('webforms')->__('Add New Web-form');
		parent::__construct();
		$forms = Mage::getModel('webforms/webforms')->getCollection()->count();
		if($forms>=3){
			$this->_removeButton('add');
			$this->_addButton('add',array(
				'label' => Mage::helper('webforms')->__('Add New Web-form'),
				'onclick' => 'alert(\''.Mage::helper('webforms')->__('You have reached Community Edition limit!\nCommunity Edition allows you to manage only 3 web-forms.\nUpgrade to Professional Edition.').'\')',
			));
		}
	}
}  
?>
