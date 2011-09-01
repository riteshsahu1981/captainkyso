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

class VladimirPopov_WebForms_Block_Results
	extends VladimirPopov_WebForms_Block_Webforms
	implements Mage_Widget_Block_Interface
{
	protected function _construct(){
		parent::_construct();
		$this->setData('results',1);
	}
}
  
?>
