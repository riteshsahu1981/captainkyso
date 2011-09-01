<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 * @license    	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VladimirPopov_WebForms_Block_Adminhtml_Webforms_Edit_Tab_Fields
	extends Mage_Adminhtml_Block_Widget_Grid
{
	
	public function _prepareLayout(){
		parent::_prepareLayout();
	}
	
	/**
	 * Set grid params
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('form_fields_grid');
		$this->setDefaultSort('position');
		$this->setDefaultDir('asc');
		$this->setUseAjax(true);
		$this->setFilterVisibility(false);
	}
	
	public function getGridUrl(){
		return $this->getUrl('*/adminhtml_fields/grid',array('id'=> $this->getRequest()->getParam('id')));
	}
	
	public function getRowUrl($row){
		return $this->getUrl('*/adminhtml_fields/edit', array('id' => $row->getId(), 'webform_id' => $this->getRequest()->getParam('id')));
	}
	
	public function _prepareCollection(){
		$collection = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id', $this->getRequest()->getParam('id'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * Add columns to grid
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header'    => Mage::helper('webforms')->__('ID'),
			'width'     => 60,
			'index'     => 'id'
		));

		$this->addColumn('name', array(
			'header'    => Mage::helper('webforms')->__('Name'),
			'index'     => 'name'
		));
		
		$fieldsetsOptions  = Mage::registry('webforms_data')->getFieldsetsOptionsArray();
		if(count($fieldsetsOptions)>1) {
			$this->addColumn('fieldset_id', array(
				'header'    => Mage::helper('webforms')->__('Field Set'),
				'index'     => 'fieldset_id',
				'type'      => 'options',
				'options'   => $fieldsetsOptions,
			));
		}
		
		$this->addColumn('type', array(
			'header'    => Mage::helper('webforms')->__('Type'),
			'width'     => 150,
			'index'     => 'type',
			'type'      => 'options',
			'options'   => Mage::getModel('webforms/fields')->getFieldTypes(),
		));
		
		$this->addColumn('required', array(
			'header'    => Mage::helper('webforms')->__('Required'),
			'width'     => 100,
			'index'     => 'required',
			'type'      => 'options',
			'options'   => array("1"=>$this->__("Yes"),"0"=>$this->__("No")),
		));

		$this->addColumn('is_active', array(
			'header'    => Mage::helper('webforms')->__('Status'),
			'index'     => 'is_active',
			'type'      => 'options',
			'options'   => Mage::getModel('webforms/webforms')->getAvailableStatuses(),
		));

		$this->addColumn('position', array(
			'header'            => Mage::helper('webforms')->__('Position'),
			'name'              => 'position',
			'type'              => 'number',
			'validate_class'    => 'validate-number',
			'index'             => 'position',
			'width'             => 60,
		));
		return parent::_prepareColumns();
	}
	
}  
?>
