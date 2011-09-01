<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Pager extends Mage_Page_Block_Html_Pager 
{
	/**
	 * Return the URL for a certain page of the collection
	 *
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$pathInfo = explode('/', $this->_getPathInfo());
		$parts = array();
		
		for ($it = 1; $it < count($pathInfo); $it = $it+2) {
			if (isset($pathInfo[$it+1])) {
				$key = (binary) $pathInfo[$it];
				$parts[$key] = $pathInfo[$it+1];
			}
		}
		
		foreach($params as $key => $param) {
			$parts[$key] = $param;
		}

		return Mage::helper('wordpress')->getUrl(null, $parts);
	}

	/**
	 * Gets the path info from the request object
	 *
	 * @return string
	 */
	protected function _getPathInfo()
	{
		return trim(Mage::app()->getRequest()->getPathInfo(), '/');;
	}
}
