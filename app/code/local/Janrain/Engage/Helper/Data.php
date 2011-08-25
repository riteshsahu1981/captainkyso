<?php

class Janrain_Engage_Helper_Data extends Mage_Core_Helper_Abstract {

	private $providers = array(
		'Facebook'		=> 'facebook',
		'Google'		=> 'google',
		'LinkedIn'		=> 'linkedin',
		'MySpace'		=> 'myspace',
		'Twitter'		=> 'twitter',
		'Windows Live'	=> 'live_id',
		'Yahoo!'		=> 'yahoo',
		'AOL'			=> 'aol',
		'Blogger'		=> 'blogger',
		'Flickr'		=> 'flickr',
		'Hyves'			=> 'hyves',
		'Livejournal'	=> 'livejournal',
		'MyOpenID'		=> 'myopenid',
		'Netlog'		=> 'netlog',
		'OpenID'		=> 'openid',
		'Verisign'		=> 'verisign',
		'Wordpress'		=> 'wordpress',
		'PayPal'		=> 'paypal'
	);

	/**
	 * Returns whether the Enabled config variable is set to true
	 *
	 * @return bool
	 */
	public function isEngageEnabled() {
		if(Mage::getStoreConfig('engage/options/enable')=='1' && strlen(Mage::getStoreConfig('engage/options/apikey')) > 1)
			return true;

		return false;
	}

	/**
	 * Returns random alphanumber string
	 *
	 * @param int $length
	 * @param string $chars
	 * @return string
	 */
	public function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {
		$chars_length = (strlen($chars) - 1);

		$string = $chars{rand(0, $chars_length)};

		for ($i = 1; $i < $length; $i = strlen($string)) {
			$r = $chars{rand(0, $chars_length)};

			if ($r != $string{$i - 1})
				$string .= $r;
		}

		return $string;
	}

	/**
	 * Returns the url of skin directory containing scripts and styles
	 *
	 * @return string
	 */
	public function _baseSkin() {
		return Mage::getBaseUrl('skin') . "frontend/janrain";
	}

	/**
	 * Returns the full Auth URL for the Engage Sign In Widget
	 *
	 * @return string
	 */
	public function getRpxAuthUrl($add=false) {
		$action = $add ? 'engage/rpx/token_url_add' : 'engage/rpx/token_url';
		$rpx_callback = urlencode(Mage::getUrl($action));
		$link = (Mage::getStoreConfig('engage/vars/realmscheme') == 'https') ? 'https' : 'http';
		$link.= '://' . Mage::getStoreConfig('engage/vars/realm');
		$link.= '/openid/v2/signin?token_url=' . $rpx_callback;

		return $link;
	}

	/**
	 * Returns an unserialized array of available providers
	 * or Null if empty (Invalid or missing API Key)
	 *
	 * @return string
	 */
	public function getRpxProviders() {
		$providers = Mage::getStoreConfig('engage/vars/enabled_providers');
		if($providers)
			return explode(",", $providers);
		else
			return false;
	}

	public function buildProfile($auth_info) {
		$profile_name = false;

		if(isset($auth_info->profile->preferredUsername))
			$profile_name = $auth_info->profile->preferredUsername;

		else if(isset($auth_info->profile->email))
			$profile_name = $auth_info->profile->email;
		
		else if(isset($auth_info->profile->displayName))
			$profile_name = $auth_info->profile->displayName;
		
		else if(isset($auth_info->profile->name->formatted))
			$profile_name = $auth_info->profile->name->formatted;
		
		else
			$profile_name = $auth_info->profile->providerName;

		return array('provider' => $this->providers[$auth_info->profile->providerName], 'identifier' => $auth_info->profile->identifier, 'profile_name' => $profile_name);
	}

}