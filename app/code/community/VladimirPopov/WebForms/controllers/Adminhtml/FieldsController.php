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

class VladimirPopov_WebForms_Adminhtml_FieldsController
	extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('webforms/webforms');
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function gridAction()
	{
		if(!Mage::registry('webforms_data')){
			Mage::register('webforms_data',Mage::getModel('webforms/webforms')->load($this->getRequest()->getParam('id')));
		}
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tab_fields')->toHtml()
		);
	}	
	
	public function editAction(){
		if((float)substr(Mage::getVersion(),0,3) > 1.3)
			$this->_title($this->__('Web-forms'))->_title($this->__('Edit Field'));
		$fieldsId = $this->getRequest()->getParam('id');
		$webformsId = $this->getRequest()->getParam('webform_id');
		$fieldsModel = Mage::getModel('webforms/fields')->load($fieldsId);
		if($fieldsModel->getWebformId()){
			$webformsId = $fieldsModel->getWebformId();
		}
		$webformsModel = Mage::getModel('webforms/webforms')->load($webformsId);
		
		if($fieldsModel->getId() || $fieldsId == 0){
			Mage::register('webforms_data',$webformsModel);
			Mage::register('fields_data',$fieldsModel);
			
			$this->loadLayout();
			$this->_setActiveMenu('webforms/webforms');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('WebForms'),Mage::helper('adminhtml')->__('Web-forms'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('webforms/adminhtml_fields_edit'))
				->_addLeft($this->getLayout()->createBlock('webforms/adminhtml_fields_edit_tabs'));
				
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webforms')->__('Field does not exist'));
			$this->_redirect('*/adminhtml_webforms/edit',array('id' => $webformsId));
		}
	}
	
	public function newAction()
	{
		$this->_forward('edit');
	}
	
	public function saveAction()
	{
		if( $this->getRequest()->getPost()){
			try{
				$postData = $this->getRequest()->getPost();
				$fieldsModel = Mage::getModel('webforms/fields');
				
				$fieldsModel->setData($postData)
					->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
					->setId($this->getRequest()->getParam('id'))
					->save();

				if( $this->getRequest()->getParam('id') <= 0 )
					$fieldsModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Field was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setWebFormsData(false);
				$this->_redirect('*/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields'));
				return;
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setWebFormsData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit',array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			
		}
		$this->_redirect('*/adminhtml_webforms/edit',array('id' => $this->getRequest()->getParam('webform_id'),'tab' => 'form_fields'));
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0){
			try{
				$fieldsModel = Mage::getModel('webforms/fields')->load($this->getRequest()->getParam('id'));
				$webform_id = $fieldsModel->getWebformId();
				$fieldsModel->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Field was successfully deleted'));
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/adminhtml_webforms/edit',array('id' => $webform_id,'tab' => 'form_fields'));
	}
	
}
?>