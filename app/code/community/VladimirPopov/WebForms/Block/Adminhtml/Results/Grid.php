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

class VladimirPopov_WebForms_Block_Adminhtml_Results_Grid
	extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('webformsGrid');
		$this->setDefaultSort('created_time');
		$this->setDefaultDir('desc');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('product_filter');
	}
	
	public function getGridUrl()
	{
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
	protected function _filterCustomerCondition($collection,$column){
		if (!$value = trim($column->getFilter()->getValue())) {
			return;
		}
		while(strstr($value,"  ")){
			$value = str_replace("  "," ",$value);
		}
		$customers_array = array();
		$name = explode(" ",$value);
		$firstname = $name[0];
		$lastname = $name[count($name)-1];
		$customers = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToFilter('firstname',$firstname);
		if(count($name)==2)
			$customers->addAttributeToFilter('lastname',$lastname);
		foreach($customers as $customer){
			$customers_array[]= $customer->getId();
		}
		$collection->addFieldToFilter('customer_id', array('in' => $customers_array));
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('webforms/results')->getCollection()->addFilter('webform_id',$this->getRequest()->getParam('webform_id'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('id',array(
			'header' => Mage::helper('webforms')->__('Id'),
			'align'	=> 'right',
			'width'	=> '50px',
			'index'	=> 'id',
		));
		
		$fields = Mage::getModel('webforms/fields')->getCollection()
			->addFilter('webform_id',$this->getRequest()->getParam('webform_id'));
		
		if((float)substr(Mage::getVersion(),0,3)>1)
			$fields->addOrder('position','asc');
		
		$maxlength = Mage::getStoreConfig('webforms/results/fieldname_display_limit');
		foreach($fields as $field){
			$field_name = $field->getName();
			if($field->getResultLabel()){
				$field_name = $field->getResultLabel();
			}
			if(strlen($field_name)>$maxlength && $maxlength>0){
				$field_name = substr($field_name,0,$maxlength).'...';
			}
			$this->addColumn('field_'.$field->getId(), array(
				'header' => $field_name,
				'index' => 'field_'.$field->getId(),
				'sortable' => false,
				'filter' => false,
				'renderer' => 'VladimirPopov_WebForms_Block_Adminhtml_Results_Renderer_Value'
			));
		}
				
		$this->addColumn('customer_id',array(
			'header' => Mage::helper('webforms')->__('Customer'),
			'align' => 'left',
			'index' => 'customer_id',
			'renderer' => 'VladimirPopov_WebForms_Block_Adminhtml_Results_Renderer_Customer',
			'filter_condition_callback' => array($this, '_filterCustomerCondition'),
			'sortable' => false
		));
		
		if(Mage::registry('webform_data')->getApprove()){
			$this->addColumn('approved', array(
				'header'    => Mage::helper('webforms')->__('Approved'),
				'index'     => 'approved',
				'type'      => 'options',
				'options'   => Array("1"=>$this->__('Yes'),"0"=>$this->__('No')),
			));
			
		}
		
		$this->addColumn('ip',array(
			'header' => Mage::helper('webforms')->__('IP'),
			'index' => 'ip',
		));
		
		
		$this->addColumn('created_time', array(
			'header'    => Mage::helper('webforms')->__('Date Created'),
			'index'     => 'created_time',
			'type'      => 'datetime',
		));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('webforms')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('webforms')->__('Excel XML'));

		return parent::_prepareColumns();
	}
	
	protected function _filterStoreCondition($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addStoreFilter($value);
	}
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('id');
		
		$forms = Mage::getModel('webforms/webforms')->getCollection()->count();
		$exc = false;
		if($forms>3){
			$exc = true;
			$CEurl = '';
			$CEconfirm = Mage::helper('webforms')->__("You have exceeded Community Edition limit!\nCommunity Edition allows you to manage only 3 web-forms.\nUpgrade to Professional Edition.");
		}
		
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$this->getRequest()->getParam('webform_id'))->count();
		if($fields>10){
			$exc = true;
			$CEurl = '';
			$CEconfirm = Mage::helper('webforms')->__("You have exceeded Community Edition limit!\nCommunity Edition allows you to have only 10 fields in web-form.\nUpgrade to Professional Edition.");
		}
		
		if(Mage::registry('webform_data')->getApprove()){
			$url = $this->getUrl('*/*/massApprove',array('webform_id'=>$this->getRequest()->getParam('webform_id')));
			$confirm = Mage::helper('webforms')->__('Approve selected results?');
			if($exc){
				$url = $CEurl;
				$confirm = $CEconfirm;
			}
			$this->getMassactionBlock()->addItem('approve', array(
				 'label'=> Mage::helper('webforms')->__('Approve'),
				 'url'  => $url,
				 'confirm' => $confirm,
			));
			
			$url = $this->getUrl('*/*/massDisapprove',array('webform_id'=>$this->getRequest()->getParam('webform_id')));
			$confirm = Mage::helper('webforms')->__('Disapprove selected results?');
			if($exc){
				$url = $CEurl;
				$confirm = $CEconfirm;
			}
			
			$this->getMassactionBlock()->addItem('disapprove', array(
				 'label'=> Mage::helper('webforms')->__('Disapprove'),
				 'url'  => $url,
				 'confirm' => $confirm,
			));
		}
		
		$url = $this->getUrl('*/*/massEmail',array('webform_id'=>$this->getRequest()->getParam('webform_id')));
		$confirm = Mage::helper('webforms')->__('Send selected results by e-mail?');
		if($exc){
			$url = $CEurl;
			$confirm = $CEconfirm;
		}
		$this->getMassactionBlock()->addItem('email', array(
			 'label'=> Mage::helper('webforms')->__('Send by e-mail'),
			 'url'  => $url,
			 'confirm' => $confirm,
		));
		
		$url = $this->getUrl('*/*/massDelete',array('webform_id'=>$this->getRequest()->getParam('webform_id')));
		$confirm = Mage::helper('webforms')->__('Are you sure to delete selected results?');
		if($exc){
			$url = $CEurl;
			$confirm = $CEconfirm;
		}
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'=> Mage::helper('webforms')->__('Delete'),
			 'url'  => $url,
			 'confirm' => $confirm,
		));
		
		return $this;
	}
}
?>
