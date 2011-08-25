<?php

class Janrain_Engage_Helper_Rpxcall extends Mage_Core_Helper_Abstract {

	public function getEngageApiKey() {
		return Mage::getStoreConfig('engage/options/apikey');
	}

	public function rpxLookupSave() {
		try {
			$lookup_rp = $this->rpxLookupRpCall();

			Mage::getModel('core/config')
				->saveConfig('engage/vars/realm', $lookup_rp->realm)
				->saveConfig('engage/vars/realmscheme', $lookup_rp->realmScheme)
				->saveConfig('engage/vars/appid', $lookup_rp->appId)
				->saveConfig('engage/vars/adminurl', $lookup_rp->adminUrl)
				->saveConfig('engage/vars/socialpub', $lookup_rp->socialPub)
				->saveConfig('engage/vars/enabled_providers', $lookup_rp->signinProviders)
				->saveConfig('engage/vars/apikey', Mage::getStoreConfig('engage/options/apikey'));
			Mage::getConfig()->reinit();

			return true;

		} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addWarning('Could not retrieve account info. Please try again');
		}
		
		return false;
	}

    public function rpxLookupRpCall() {
        $version = Mage::getConfig()->getModuleConfig("Janrain_Engage")->version;

        $postParams = array();
        $postParams["apiKey"] = $this->getEngageApiKey();
        $postParams["pluginName"] = "magento";
        $postParams["pluginVersion"] = $version;

        $result = "rpxLookupRpCall: no result";
        try {
            $result = $this->rpxPost("lookup_rp", $postParams);
        }
        catch (Exception $e) {
            throw Mage::exception('Mage_Core', $e);
        }

        return $result;

    }
    
    public function rpxAuthInfoCall($token) {

        $postParams = array();

        $postParams["token"] = $token;
        $postParams["apiKey"] = $this->getEngageApiKey();

        $result = "rpxAuthInfoCall: no result";
        try {
            $result = $this->rpxPost("auth_info", $postParams);
        }
        catch (Exception $e) {
            throw Mage::exception('Mage_Core', $e);
        }

        return $result;

    }


    public function rpxActivityCall($identifier, $activity_message, $url) {

        $postParams = array();
		$activity = new stdClass();

		$activity->action = $activity_message;
		$activity->url = $url;
        
		$activity_json = json_encode($activity);

        $postParams["activity"] = $activity_json;
        $postParams["identifier"] = $identifier;
        $postParams["apiKey"] = $this->getEngageApiKey();

        $result = "rpxActivityCall: no result";
        try {
            $result = $this->rpxPost("activity", $postParams);
        }
        catch (Exception $e) {
            throw Mage::exception('Mage_Core', $e);
        }

        return $result;

    }


    private function rpxPost($method, $postParams) {

        $rpxbase = "https://rpxnow.com";

        if ($method == "auth_info") {
            $method_fragment = "api/v2/auth_info";
        }
        elseif ($method == "activity") {
            $method_fragment = "api/v2/activity";
        }
        elseif ($method == "lookup_rp") {
            $method_fragment = "plugin/lookup_rp";
        }
        else {
            throw Mage::exception('Mage_Core', "method [$method] not understood");
        }
		
		$url = "$rpxbase/$method_fragment";
		$method = 'POST';
        $postParams["format"] = 'json';

        return $this->rpxCall($url, $method, $postParams);
		
    }

    private function rpxCall($url, $method='GET', $postParams=null) {

        $result = "rpxCallUrl: no result yet";

        try {

            $http = new Varien_Http_Client($url);
            $http->setHeaders( array( "Accept-encoding" => "identity"  ) );
			if($method=='POST')
				$http->setParameterPost($postParams);
            $response = $http->request($method);

            $body = $response->getBody();

            try {
                $result = json_decode($body);
            }
            catch (Exception $e) {
                throw Mage::exception('Mage_Core', $e);
            }

            if ($result) {
                return $result;
            }
            else {
                throw Mage::exception('Mage_Core', "something went wrong");
            }

        }
        catch (Exception $e) {
            throw Mage::exception('Mage_Core', $e);
        }

    }

	public function getFirstName($auth_info) {
		if (isset($auth_info->profile->name->givenName))
			return $auth_info->profile->name->givenName;
        
        if (!isset($auth_info->profile->name->formatted))
            return '';

		$name = str_replace(",", "", $auth_info->profile->name->formatted);

        if (!$name)
            return '';
        
        $split = explode(" ", $name);

		$fName = isset($split[0]) ? $split[0] : '';
		return $fName;
	}

	public function getLastName($auth_info) {
		if (isset($auth_info->profile->name->familyName))
			return $auth_info->profile->name->familyName;
        
        if (!isset($auth_info->profile->name->formatted))
            return '';

		$name = str_replace(",", "", $auth_info->profile->name->formatted);
        
        if (!$name)
            return '';
        
		$split = explode(" ", $name);
		$key = sizeof($split) - 1;

		$lName = isset($split[$key]) ? $split[$key] : '';
		return $lName;
	}

}
?>