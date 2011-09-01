<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Observer_DatabaseSetup
{
	public function initConnection($observer = null)
	{
		try {
			if (!Mage::helper('wordpress/db')->hasBeenInitialised()) {
				Mage::register('wordpress_db_init', 1, true);
				Mage::helper('wordpress/db')->mapTables();
				$this->_initConnection();
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
		}
	}
	
	/**
	 * Initialise the database connection
	 *
	 * @return bool
	 */
	protected function _initConnection()
	{
		if (Mage::helper('wordpress')->isSeparateDatabase()) {
			try {
				if (!$connection = $this->_createConnection()) {
					throw new Exception('There was an error connecting to the WordPress database');
				}
			}
			catch (Exception $e) {
				Mage::helper('wordpress')->log('initDatabaseConnection: ' . $e->getMessage());
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Creates the WordPress database connections
	 * This function only need be called if Mage/WP are installed in different DB's
	 *
	 */
	protected function _createConnection()
	{
		$configs = array('model' => 'mysql4', 'active' => '1');
		$keys = array('host' => '', 'username' => '', 'password' => '', 'dbname' => '', 'charset' => 'utf8');
		
		foreach($keys as $key => $defaultValue) {
			if ($value = Mage::getStoreConfig('wordpress/database/' . $key)) {
				$configs[$key] = $value;
			}
			else {
				$configs[$key] = $defaultValue;
			}
		}

		foreach(array('username', 'password', 'dbname') as $field) {
			if (isset($configs[$field])) {
				$configs[$field] = Mage::helper('core')->decrypt($configs[$field]);
			}
		}

		if ($configs['host']) {
			return Mage::getSingleton('core/resource')->createConnection('wordpress', 'pdo_mysql', $configs);
		}
		
		return false;
	}
	
}
