<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_CategoryController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		if ($category = $this->_loadCategoryBasedOnUrl()) {
			if (!Mage::helper('wordpress')->isLegacyMagento()) {
				$this->_addCustomLayoutHandles(array('wordpress_category_index', 'WORDPRESS_CATEGORY_'.$category->getId()));
			}
			
			// Add base breacrumbs and title
			parent::_init();
			
			$this->_title($category->getName());
			$this->_addCrumb('category', array('link' => $category->getUrl(), 'label' => $category->getName()));
			$this->_addCanonicalLink($category->getUrl());
			
			return $this;
		}
		else {
			$this->throwInvalidObjectException('category');
		}
	}
	
	/**
	 * Load the category based on the slug stored in the param 'category'
	 *
	 * @return Fishpig_Wordpress_Model_Post_Categpry
	 */
	protected function _loadCategoryBasedOnUrl()
	{
		$uri = Mage::helper('wordpress/router')->getBlogUri();

		if (!Mage::helper('wordpress')->isPluginEnabled('No Category Base')) {
			$base = Mage::helper('wordpress/router')->getCategoryBase();
			$slug = trim(substr($uri, strlen($base)), '/');
		}
		else {
			$slug = $uri;
		}

		$category = Mage::getModel('wordpress/post_category')->loadBySlug($slug);
			
		if ($category && $category->getId()) {
			Mage::register('wordpress_category', $category);
			return $category;
		}

		return false;
	}
}
