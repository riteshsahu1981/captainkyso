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

class VladimirPopov_WebForms_Block_Webforms extends Mage_Core_Block_Template
{
	protected function _toHtml()
	{
		if((float)substr(Mage::getVersion(),0,3) <= 1.3){
			if($this->getTemplate() == 'webforms/default.phtml' && $this->getData('nolegacy')!='0'){
				$this->setTemplate('webforms/legacy.phtml');
			}
		}
		$note = "<p style='font-size:80%; color:#999; text-align:center'>Powered by <a href='http://mageme.com'>WebForms</a></p>";
		return parent::_toHtml().$note;
	}
	
	public function getFormData(){
		$data = $this->getRequest()->getParams();
		if(isset($data['id'])){
			$data['webform_id'] = $data['id'];
		}
		if($this->getData('webform_id')){
			$data['webform_id'] = $this->getData('webform_id');
		}
		return $data;
	}
	
	protected function _prepareLayout()
	{
		if((float)substr(Mage::getVersion(),0,3)<=1.4)
			error_reporting(E_ERROR);
		$show_success = false;
		$data = $this->getFormData();
		//get form data
		$webform = Mage::getModel('webforms/webforms')->load($data['webform_id']);
		if(!Mage::registry('webform')) Mage::register('webform',$webform);
		
		if(intval($this->getData('results')) == 1)
			$this->getResults();
		
		if($webform->getSurvey()){
			$collection = Mage::getModel('webforms/results')->getCollection();
			
			if(Mage::helper('customer')->isLoggedIn())
				$collection->addFilter('webform_id',$data['webform_id'])->addFilter('customer_id',Mage::getSingleton('customer/session')->getCustomerId());
			else{
				$session_validator = Mage::getSingleton('customer/session')->getData('_session_validator_data');
				$collection->addFilter('customer_ip',ip2long($session_validator['remote_addr']));
			}
			$count = $collection->count();
			if($count>0){
				$show_success = true;
			}
		}
		
		if(Mage::getSingleton('core/session')->getWebformsSuccess() == $data['webform_id'] || $show_success){
			Mage::register('show_success',true);
			Mage::getSingleton('core/session')->setWebformsSuccess();
		}
		
		if($webform->getRegisteredOnly() && !Mage::helper('customer')->isLoggedIn()){
			Mage::getSingleton('customer/session')->setBeforeAuthUrl($this->getRequest()->getRequestUri());
			Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl(),301);
		}
		
		Mage::register('fields_to_fieldsets',$webform->getFieldsToFieldsets());
		
		//use captcha
		if(!Mage::helper('customer')->isLoggedIn()){
			$pubKey = Mage::getStoreConfig('webforms/captcha/public_key');
			$privKey = Mage::getStoreConfig('webforms/captcha/private_key');
			if($this->captchaAvailable())
				Mage::register('use_captcha',true);
		}
		
		//proccess the result
		if($this->getRequest()->getParam('submitWebform_'.$data['webform_id'])){
			//validate captcha
			if(Mage::registry('use_captcha')){
				if($this->getRequest()->getParam('recaptcha_response_field')) {
					$verify = $this->getCaptcha()->verify($this->getRequest()->getParam('recaptcha_challenge_field'),$this->getRequest()->getParam('recaptcha_response_field'));
					if($verify->isValid()){
						$success = $this->saveResult();
					} else {
						Mage::getSingleton('core/session')->addError($this->__('Verification code was not correct. Please try again.'));
						Mage::register('captcha_invalid',true);
					}
				} else {
					Mage::getSingleton('core/session')->addError($this->__('Verification code was not correct. Please try again.'));
					Mage::register('captcha_invalid',true);
				}
			} else {
				$success = $this->saveResult();
			}
			if($success){
				Mage::getSingleton('core/session')->setWebformsSuccess($data['webform_id']);
			}
			//redirect after successful submission
			$url = Mage::helper('core/url')->getCurrentUrl();
			if($webform->getRedirectUrl()){
				if(strstr($webform->getRedirectUrl(),'://'))	
					$url = $webform->getRedirectUrl();
				else
					$url = $this->getUrl($webform->getRedirectUrl());
			}
			if($success)
				Mage::app()->getFrontController()->getResponse()->setRedirect($url);
		}
		parent::_prepareLayout();
		$this->getLayout()->getBlock('head')->setTitle(Mage::registry('webform')->getName());
		
	}
	
	public function captchaAvailable(){
		if(class_exists('Zend_Service_ReCaptcha')
			&& Mage::getStoreConfig('webforms/captcha/public_key')
			&& Mage::getStoreConfig('webforms/captcha/private_key')
		) return true;
		return false;
	}
	
	public function getCaptcha(){
		$pubKey = Mage::getStoreConfig('webforms/captcha/public_key');
		$privKey = Mage::getStoreConfig('webforms/captcha/private_key');
		if($pubKey && $privKey)
			$recaptcha = new Zend_Service_ReCaptcha($pubKey, $privKey);
		return $recaptcha;
	}
	
	public function saveResult(){
		if(!Mage::registry('webform')) return false;
		try{
			$postData = $this->getRequest()->getPost();
			$result = Mage::getModel('webforms/results');
			
			$session_validator = Mage::getSingleton('customer/session')->getData('_session_validator_data');
			$iplong = ip2long($session_validator['remote_addr']);
			if((float)substr(Mage::getVersion(),0,3)<=1){
				$iplong = ip2long($this->getRealIp());
			}
			$result->setData($postData)
				->setWebformId(Mage::registry('webform')->getId())
				->setStoreId(Mage::app()->getStore()->getId())
				->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
				->setCustomerIp($iplong)
				->save();
				
			Mage::dispatchEvent('webforms_result_submit',array('result'=>$result,'webform'=>Mage::registry('webform')));
			
			$emailSettings = Mage::registry('webform')->getEmailSettings();
			
			if($emailSettings['email_enable']){
				
				$result = Mage::getModel('webforms/results')->load($result->getId());
				$result->sendEmail();
				if(Mage::registry('webform')->getDuplicateEmail()){
					$result->sendEmail('customer');
				}
			}
			return true;
		} catch (Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
			return false;
		}
	}
	
	public function getRealIp()
	{
		 $ip = false;
		 if(!empty($_SERVER['HTTP_CLIENT_IP']))
		 {
			  $ip = $_SERVER['HTTP_CLIENT_IP'];
		 }
		 if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		 {
			  $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			  if($ip)
			  {
				   array_unshift($ips, $ip);
				   $ip = false;
			  }
			  for($i = 0; $i < count($ips); $i++)
			  {
				   if(!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]))
				   {
						if(version_compare(phpversion(), "5.0.0", ">="))
						{
							 if(ip2long($ips[$i]) != false)
							 {
								  $ip = $ips[$i];
								  break;
							 }
						}
						else
						{
							 if(ip2long($ips[$i]) != - 1)
							 {
								  $ip = $ips[$i];
								  break;
							 }
						}
				   }
			  }
		 }
		 return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}  
	
	public function getResults(){
		$data = $this->getData();
		
		$webform = Mage::registry('webform');
		
		//get results
		$page_size = $data["page_size"];
		$current_page = (int)$this->getRequest()->getParam('p');
		if(!$current_page) $current_page = 1;
		$from = $current_page*$page_size;
		$results = Mage::getModel('webforms/results')->getCollection()
			->addFilter('webform_id',$webform->getId())
			->addFilter('approved',1)
			->setPageSize($page_size)
			->setCurPage($current_page)
			;
		$results->getSelect()->order('created_time desc');

		
		$last_page = $results->getLastPageNumber();
		
		$page_url = $this->getUrl(Mage::getSingleton('cms/page')->getData('identifier'));
		echo get_class($page_url);
		if($current_page<$last_page){
			$prev_url= $page_url."?p=".($current_page+1);
		}
		if($current_page>1){
			$next_url= $page_url."?p=".($current_page-1);
		}
		
		Mage::register('prev_url',$prev_url);
		Mage::register('next_url',$next_url);
		Mage::register('current_page',$current_page);
		Mage::register('results',$results);
		
	}
}
?>
