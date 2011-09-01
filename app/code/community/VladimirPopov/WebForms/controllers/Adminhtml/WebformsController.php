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

class VladimirPopov_WebForms_Adminhtml_WebFormsController
	extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('webforms/webforms');
		
		if((float)substr(Mage::getVersion(),0,3) > 1.3)
			$this->_title($this->__('Web-forms'))->_title($this->__('Manage Forms'));
		return $this;
	}
	
	public function indexAction(){
		$this->_initAction();
		$this->renderLayout();
	}
	
	public function gridAction()
	{
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('webforms/adminhtml_webforms_grid')->toHtml()
		);
	}	
	
	public function editAction(){
		if((float)substr(Mage::getVersion(),0,3) > 1.3)
			$this->_title($this->__('Web-forms'))->_title($this->__('Edit Form'));
		
		$webformsId = $this->getRequest()->getParam('id');
		$webformsModel = Mage::getModel('webforms/webforms')->load($webformsId);
		
		if($webformsModel->getId() || $webformsId ==0 ){
			Mage::register('webforms_data',$webformsModel);
			$this->loadLayout();
			$this->_setActiveMenu('webforms/webforms');
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Web-forms'),Mage::helper('adminhtml')->__('Web-forms'));
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('webforms/adminhtml_webforms_edit'))
				->_addLeft($this->getLayout()->createBlock('webforms/adminhtml_webforms_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('webforms')->__('Web-form does not exist'));
			$this->_redirect('*/*/');
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
				$webformsModel = Mage::getModel('webforms/webforms');
				
//				Zend_Debug::dump($this->getRequest()->getParam('name'));  
				
				$webformsModel->setData($postData)
					->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
					->setId($this->getRequest()->getParam('id'))
					->save();

				if( $this->getRequest()->getParam('id') <= 0 )
					$webformsModel->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Web-form was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setWebFormsData(false);
				
				// check if 'Save and Continue'
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $webformsModel->getId()));
					return;
				}
				
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setWebFormsData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit',array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			
		}
		$this->_redirect('*/*/');
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0){
			try{
				$webformsModel = Mage::getModel('webforms/webforms');
				$webformsModel->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Web-form was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	

	public function massStatusAction(){
		$Ids = (array)$this->getRequest()->getParam('id');
		$status = (int)$this->getRequest()->getParam('status');
		
		try {
			foreach($Ids as $id){
				$result = Mage::getModel('webforms/webforms')->load($id);
				$result->setData('is_active',$status);
				$result->save();
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been updated.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
		}

		$this->_redirect('*/*/');
			
		
	}
	
	public function massDeleteAction(){
		$Ids = (array)$this->getRequest()->getParam('id');
		
		try {
			foreach($Ids as $id){
				$result = Mage::getModel('webforms/webforms')->load($id);
				$result->delete();
			}

			$this->_getSession()->addSuccess(
				$this->__('Total of %d record(s) have been deleted.', count($Ids))
			);
		}
		catch (Mage_Core_Model_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}
		catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('An error occurred while updating records.'));
		}

		$this->_redirect('*/*/');
		
	}
}
?>
