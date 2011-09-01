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

class VladimirPopov_WebForms_IndexController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction()
	{
		Mage::register('show_form_name',true);
		$this->loadLayout();
		$this->renderLayout();
	}
	
}  
?>