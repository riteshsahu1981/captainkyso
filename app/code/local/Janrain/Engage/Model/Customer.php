<?php

class Janrain_Engage_Model_Customer extends Mage_Customer_Model_Customer {

	/**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
		// TODO Make a more secure method of bypassing password (PSE-27)
		if(Mage::getSingleton('engage/session')->getLoginRequest()===true){
			Mage::getSingleton('engage/session')->setLoginRequest(false);
			return true;
		}
        return parent::validatePassword($password);
    }

}