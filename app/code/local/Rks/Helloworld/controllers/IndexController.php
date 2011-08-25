<?php
class Rks_Helloworld_IndexController extends Mage_Core_Controller_Front_Action{

	public function indexAction()
	{
		echo "I am in Index action in Index Controller in Helloworld Module";
		$this->loadLayout();
		$this->renderLayout();
	}

	public function helloAction()
	{
		$this->loadLayout();
		
		//echo "I am in hello action in Index Controller in Helloworld Module";
		///$params=$this->getRequest()->getParams();
		//echo "<pre>";
		//print_r($params);
		$this->renderLayout();
		
	}



















	public function programAction()
	{
		//Get current layout state
		$this->loadLayout();
		 
		$block = $this->getLayout()->createBlock(
		'Mage_Core_Block_Template',
		'my_block_name_here',
		array('template' => 'helloworld/index.phtml')
		);
		 
		$this->getLayout()->getBlock('content')->append($block);
		 

		$this->renderLayout();
	}

}