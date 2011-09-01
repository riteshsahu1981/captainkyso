<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Abstract extends Mage_Core_Helper_Abstract
{
	/**
	 * Internal cache variable
	 *
	 * @var array
	 */
	protected $_cache = array();
	
	/**
	  * Returns the URL used to access your Wordpress frontend
	  *
	  */
	public function getUrl($extra = null, $params = array())
	{
		if ($this->isFullyIntegrated()) {
			$blogRoute = $this->getBlogRoute();
			
			if (Mage::getStoreConfigFlag('web/url/use_store')) {
				$blogRoute = Mage::app()->getStore()->getCode() . '/' . $blogRoute;
			}
			
			if (Mage::getStoreConfigFlag('web/seo/use_rewrites')) {
				$url = Mage::getBaseUrl('web', false) . $blogRoute;
			}
			else {
				$url = Mage::getBaseUrl('link', false) . $blogRoute;
			}
		}
		else {
			$url = $this->getCachedWpOption('home');
		}

		$url = $url . '/' . $extra;
		
		if (count($params) > 0) {
			$url = rtrim($url, '/') . '/';
			
			foreach($params as $field => $value) {
				$url .= "{$field}/{$value}/";
			}
		}

		return htmlspecialchars($url);
	}
	
	public function getHomeUrl()
	{
		return $this->getCachedWpOption('home');
	}
	
	/**
	  * Returns the URL Wordpress is installed on
	  *
	  * @param string $extra
	  * @return string
	  */
	public function getBaseUrl($extra = '')
	{
		return rtrim($this->getCachedWpOption('siteurl'), '/') . '/' . $extra;
	}
	
	/**
	  * Get Wordpress Admin URL
	  *
	  */
	public function getAdminUrl($extra = null)
	{
		return $this->getBaseUrl('wp-admin/' . $extra);
	}
	
	/**
	  * Returns the blog route selected in the Magento config
	  *
	  * Returns null if full integration is disbaled
	  *
	  */
	public function getBlogRoute()
	{
		if ($this->isFullyIntegrated()) {
			return Mage::getStoreConfig('wordpress/integration/route');
		}

		return null;
	}

	/**
	 * Returns the pretty version of the blog route
	 *
	 * @return string
	 */
	public function getPrettyBlogRoute()
	{
		if ($route = $this->getBlogRoute()) {
			$route = str_replace('-', ' ', $route);
			return ucwords($route);
		}
	
		return null;	
	}

	/**
	 * Returns true if Magento/Wordpress are installed in different DB
	 * This can be configured in the Magento admin
	 *
	 * @return bool
	 */	
	public function isSeparateDatabase()
	{
		return Mage::getStoreConfigFlag('wordpress/database/is_different_db');
	}
	
	/**
	 * Returns true if Magento/Wordpress are installed in the same DB
	 * This can be configured in the Magento admin
	 *
	 * @return bool
	 */
	public function isSameDatabase()
	{
		return !$this->isSeparateDatabase();
	}
	
	/**
	  * Returns true if full integration is enabled
	  *
	  */
	public function isFullyIntegrated()
	{
		return Mage::getStoreConfigFlag('wordpress/integration/full');
	}
	
	/**
	  * Returns true if semi-integration is enabled
	  * Function will always return the opposite of isFullyIntegrated()
	  *
	  */
	public function isSemiIntegrated()
	{
		return !$this->isFullyIntegrated();
	}
	
	/**
	  * Gets a Wordpress option based on it's name
	  *
	  * If the value isn't found in the cache, it is fetched and added
	  *
	  */
	public function getCachedWpOption($optionName, $default = null)
	{
		if (!isset($this->_cache['option'][$optionName])) {
			$this->_cache['option'][$optionName] = $this->getWpOption($optionName, $default);
		}

		return $this->_cache['option'][$optionName];
	}
	
	/**
	  * Gets a Wordpress option based on it's name
	  *
	  */
	public function getWpOption($optionName, $default = null)
	{
		$option = Mage::getModel('wordpress/option')->load($optionName, 'option_name');
			
		if ($option->getOptionValue()) {
			return $option->getOptionValue();
		}

		return $default;
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatDate($date, $format = null, $f = false)
	{
		if ($format == null) {
			$format = $this->getDefaultDateFormat();
		}
		
		/**
		 * This allows you to translate month names rather than whole date strings
		 * eg. "March","Mars"
		 *
		 */
		$len = strlen($format);
		$out = '';
		
		for( $i = 0; $i < $len; $i++) {	
			$out .= $this->__(Mage::getModel('core/date')->date($format[$i], strtotime($date)));
		}
		
		return $out;
	}
	
	/**
	  * Formats a Wordpress date string
	  *
	  */
	public function formatTime($time, $format = null)
	{
		if ($format == null) {
			$format = $this->getDefaultTimeFormat();
		}
		
		return $this->formatDate($time, $format);
	}
	
	/**
	  * Return the default date formatting
	  *
	  */
	public function getDefaultDateFormat()
	{
		return $this->getCachedWpOption('date_format', 'F jS, Y');
	}
	
	/**
	  * Return the default time formatting
	  *
	  */
	public function getDefaultTimeFormat()
	{
		return $this->getCachedWpOption('time_format', 'g:ia');
	}
	
	/**
	  * Logs an error to the Wordpress error log
	  *
	  */
	public function log($message, $level = null, $file = 'wordpress.log')
	{
		if (Mage::getStoreConfigFlag('wordpress/debug/log_enabled')) {
			if ($message = trim($message)) {
				return Mage::log($message, $level, $file, true);
			}
		}
	}
	
	/**
	 * Retrieve the local path to file cache path
	 *
	 * @return string
	 */
	public function getFileCachePath()
	{
		return Mage::getBaseDir('var') . DS . 'wordpress' . DS;
	}
	
	/**
	 * Returns true if the current Magento version is below 1.4
	 *
	 * @return bool
	 */
	public function isLegacyMagento()
	{
		return version_compare(Mage::getVersion(), '1.4.0.0', '<');
	}

	/**
	 * Determine whether the Magento is the Enterprise edition
	 *
	 * @return bool
	 */
	public function isEnterpriseMagento()
	{
		return is_file(Mage::getBaseDir('code') . DS . implode(DS, array('Enterprise', 'Enterprise', 'etc')) . DS . 'config.xml');
	}
	
	/**
	 * Shortcut to get Param
	 *
	 * @param string $field
	 * @param null|mixed $default
	 * @return mixed
	 */
	public function getParam($field, $default = null)
	{
		return Mage::app()->getRequest()->getParam($field, $default);
	}
	
	/**
	 * Retrieve the path for the WordPress installation
	 * The main use of this is to include the phpass class file for Customer Synchronisation
	 *
	 * @return string
	 */
	public function getWordPressPath()
	{
		$path = Mage::getStoreConfig('wordpress/misc/path');

		if (!$path) {
			$mUrlParts = parse_url(Mage::getBaseUrl());
			$wUrlParts = parse_url($this->getBaseUrl());

			$basePath = Mage::getBaseDir();
			
			if (isset($mUrlParts['path']) && !empty($mUrlParts['path'])) {
				$basePath = substr($basePath, 0, -(strlen($mUrlParts['path'])-1));
			}
			

			$path = $basePath . $wUrlParts['path'];
		}
		
		return rtrim($path, DS) . DS;
	}
	
	/**
	 * Retrieve the WordPress domain by getting the URL and the path
	 *
	 * @return string
	 */
	public function getDomain()
	{	
		$url = parse_url(Mage::getStoreConfig('web/unsecure/base_url'));
		$host = $url['host'];
		
		if (strpos($host, 'www.') !== false) {
			$host = substr($host, 4);
		}
	
		return $host;
	}
	
	public function integrationIsEnabled()
	{
		$read = Mage::helper('wordpress/db')->getWordpressRead();
		$select = $read->select()->from(Mage::helper('wordpress/db')->getTableName('posts'), 'ID')->limit(1);
			
		try {
			$read->fetchOne($select);
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}
}
