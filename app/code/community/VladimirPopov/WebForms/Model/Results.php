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

class VladimirPopov_WebForms_Model_Results extends Mage_Core_Model_Abstract{

	public function _construct(){
		parent::_construct();
		$this->_init('webforms/results');
	}
	
	public function sendEmail($recipient='admin'){
		$webform = Mage::getModel('webforms/webforms')->load($this->getWebformId());
		if(!Mage::registry('webform'))
			Mage::register('webform',$webform);
		
		$emailSettings = $webform->getEmailSettings();
		
		// for admin
		$sender = Array(
			'name'  => $this->getCustomer(),
			'email' => $this->getReplyTo($recipient),
		);
		// for customer
		if($recipient == 'customer'){
			$sender['name'] = Mage::app()->getStore($this->getStoreId())->getFrontendName();
		}
		
		$subject = $this->getEmailSubject($recipient);
		
		$email = $emailSettings['email'];
		
		//for customer
		if($recipient == 'customer'){
			$email = $this->getCustomerEmail();
		}
		
		$name = '';

		$vars = Array(
			'webform_subject'=>$subject,
			'webform_name'=>$webform->getName(),
			'webform_result' =>$this->toHtml($recipient),
			'result' => $this->getTemplateResultVar(),
		);
		
		if($recipient == 'customer'){
			$vars['noreply'] = Mage::helper('webforms')->__('Please, don`t reply to this e-mail!');
		}

		$storeId = Mage::app()->getStore($this->getStoreId())->getId(); 
		$templateId = 'webforms_results';
		if($recipient == 'customer')
			$templateId = 'webforms_results_customer';
		$success = Mage::getModel('core/email_template')
			->setTemplateSubject($subject)
			->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
		return $success;
	}
	
	public function getEmailSubject($recipient='admin'){
		$webform_name = Mage::registry('webform')->getName();
		$store_name = Mage::app()->getStore($this->getStoreId())->getFrontendName();
		
		//get subject for customer
		if($recipient == 'customer'){
			return Mage::helper('webforms')->__("You have submitted '%s' form on %s website",$webform_name,$store_name);
		}
		
		//get default subject for admin
		$subject = Mage::helper('webforms')->__("Web-form '%s' submitted",$webform_name);
		
		//iterate through fields and build subject
		$subject_array = array();
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$this->getWebformId())->addFilter('email_subject',1);
		foreach($fields as $field){
			foreach($this->getData() as $key=>$value){
				if($key == 'field_'.$field->getId() && $value){
					$subject_array[]= $value;
				}
			}
		}
		if(count($subject_array)>0){
			$subject = implode(" / ",$subject_array);
		}
		return $subject;
	}
	
	public function getTemplateResultVar(){
		$result = new Varien_Object();
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$this->getWebformId());
		foreach($fields as $field){
			foreach($this->getData() as $key=>$value){
				if($key == 'field_'.$field->getId() && $value){
					$data = new Varien_Object( array(
						'value' => nl2br($value),
						'name' => $field->getName(),
						'result_label' => $field->getResultLabel(),
					) );
					$result->setData($field->getId(),$data);
					if($field->getCode()){
						$result->setData($field->getCode(),$data);
					}
				}
			}
		}
		return $result;
	}
	
	public function getReplyTo($recipient='admin'){
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$this->getWebformId())->addFilter('type','email');
		
		foreach($this->getData() as $key=>$value){
			if($key == 'field_'.$fields->getFirstItem()->getId()) {
				$reply_to = $value;
			}
		}
		if(!$reply_to){
			if(Mage::helper('customer')->isLoggedIn()){
				$reply_to = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			}
			else{
				$reply_to = Mage::getStoreConfig('trans_email/ident_general/email');
			}
		}
		if($recipient == 'customer'){
			if(Mage::getStoreConfig('webforms/email/email_reply_to'))
				$reply_to = Mage::getStoreConfig('webforms/email/email_reply_to');
			else
				$reply_to = Mage::getStoreConfig('trans_email/ident_general/email');
		}
		return $reply_to;
	}
	
	public function getCustomerEmail(){
		$fields = Mage::getModel('webforms/fields')->getCollection()->addFilter('webform_id',$this->getWebformId())->addFilter('type','email');
		
		foreach($this->getData() as $key=>$value){
			if($key == 'field_'.$fields->getFirstItem()->getId()) {
				$customer_email = $value;
			}
		}
		if(!$customer_email){
			if(Mage::helper('customer')->isLoggedIn()){
				$customer_email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
			}
		}
		return $customer_email;
	}
	
	public function toHtml($recipient = 'admin'){
		$store_group = Mage::app()->getStore($this->getStoreId())->getFrontendName();
		$store_name = Mage::app()->getStore($this->getStoreId())->getName();
		
		$html = "";
		if($recipient == 'admin'){
			if($store_group)
				$html .= Mage::helper('webforms')->__('Store group').": ".$store_group."<br />";
			if($store_name)
				$html .= Mage::helper('webforms')->__('Store name').": ".$store_name."<br />";
			$html .= Mage::helper('webforms')->__('Customer').": ".$this->getCustomer()."<br />";
			$html .= Mage::helper('webforms')->__('IP').": ".$this->getIp()."<br />";
		}
		$format = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
		$html .= Mage::helper('webforms')->__('Date').": ". Mage::app()->getLocale()->date($this->getCreatedTime())->toString($format)."<br />";
		$html .= "<br />";
		
		$head_html = $html;
		
		$html = "";
		
		$fields_to_fieldsets = Mage::getModel('webforms/webforms')->load($this->getWebformId())->getFieldsToFieldsets();
		
		foreach($fields_to_fieldsets as $fieldset){
			$k=false;
			$field_html="";
			foreach($fieldset['fields'] as $field){
				$value= trim($this->getData('field_'.$field->getId()));
				if(strlen($value)){
					$value = nl2br(htmlspecialchars($value));
					$field_html.= '<b>'.$field->getName().'</b><br/>';
					$field_html.=$value."<br /><br />";
					$k=true;
				}
			}
			if($fieldset['name'] && $k)
				$field_html= '<h2>'.$fieldset['name'].'</h2>'.$field_html;
			$html.=$field_html;
		}
		return $head_html.$html;
		
	}
}
?>
