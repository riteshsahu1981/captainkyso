<?php
class Rks_Helloworld_Block_Hello extends Mage_Core_Block_Template
{
	public function hello()
	{echo "hello";
		return "I am in Block";
	}
}