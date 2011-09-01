<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Archive_ViewController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		parent::_init();

		if ($archive = $this->_initArchive()) {
			$this->_title($archive->getName())
				->_addCrumb('archive', array('link' => $archive->getUrl(), 'label' => $archive->getName()))
				->_addCanonicalLink($archive->getUrl());
		}
		else {
			$this->throwInvalidObjectException('author');
		}
	}

	/**
	 * Loads an archive model based on the URI
	 *
	 * @return Fishpig_Wordpress_Model_Archive
	 */
	protected function _initArchive()
	{
		if ($archive = Mage::getModel('wordpress/archive')->load(Mage::helper('wordpress/router')->getBlogUri())) {
			if ($archive->hasPosts()) {
				Mage::register('wordpress_archive', $archive);
				return $archive;
			}
		}

		return false;
	}
}
