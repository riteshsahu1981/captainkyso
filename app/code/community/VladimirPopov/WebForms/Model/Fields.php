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

class VladimirPopov_WebForms_Model_Fields extends Mage_Core_Model_Abstract{

	public function _construct()
	{
		parent::_construct();
		$this->_init('webforms/fields');
	}
	
	public function getFieldTypes()
	{
		$types = new Varien_Object(array(
			"text" => Mage::helper('webforms')->__('Text'),
			"email" => Mage::helper('webforms')->__('Text / E-mail'),
			"number" => Mage::helper('webforms')->__('Text / Number'),
			"textarea" => Mage::helper('webforms')->__('Textarea'),
			"select" => Mage::helper('webforms')->__('Select'),
			"select/radio" => Mage::helper('webforms')->__('Select / Radio'),
			"select/checkbox" => Mage::helper('webforms')->__('Select / Checkbox'),
			
		));
		
		// add more field types
		Mage::dispatchEvent('webforms_fields_types', array('types' => $types));

		return $types->getData();
		
	}
	
	public function getSizeTypes(){
		$types = new Varien_Object(array(
			"standard" => Mage::helper('webforms')->__('Standard'),
			"wide" => Mage::helper('webforms')->__('Wide'),
		));
		
		// add more size types
		Mage::dispatchEvent('webforms_fields_size_types', array('types' => $types));

		return $types->getData();
		
	}
	
	public function toHtml()
	{
		$html="";
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$filter = new Varien_Filter_Template_Simple();
		$filter->setData('firstname',$customer->getFirstname());
		$filter->setData('lastname',$customer->getLastname());
		$filter->setData('email',$customer->getEmail());
		
		// apply custom filter
		Mage::dispatchEvent('webforms_fields_tohtml_filter',array('filter'=>$filter));
		
		$field_id="field[".$this->getId()."]";
		$field_name = $field_id;
		$field_value = $filter->filter($this->getValue());
		$field_type = $this->getType();
		$field_class="input-text";
		if($this->getRequired())
			$field_class.=" required-entry";
		if($field_type == "email")
			$field_class.= " validate-email";
		if($field_type == "number")
			$field_class.= " validate-number";
		if($this->getCssClass()){
			$field_class.=' '.$this->getCssClass();
		}
		if($this->getCssStyle()){
			$field_style = $this->getCssStyle();
		}
		switch($field_type){
			case 'textarea': 
				$html = "<textarea name='$field_name' id='$field_id' class='$field_class' style='$field_style'>$field_value</textarea>";
				break;
			case 'select': 
				$options = explode("\n",$field_value);
				$html = "<select name='$field_name' id='$field_id' class='$field_class' style='$field_style'>";
				foreach($options as $option){
					if(trim($option))
						$html.= "<option value='$option'>$option</option>";
				}
				$html.="</select>";
				break;
			case 'select/radio':
				$options = explode("\n",$field_value);
				$html= "<ul style='padding:10px'>";
				$field_class=  $this->getCssClass();
				foreach($options as $i=>$option){
					if($this->getRequired() && $i==(count($options)-1)){
						$validate = "validate-one-required-by-name";
					}
					if(trim($option))
						$html.= "<li class='control'><input style='float:left' type='radio' name='".$field_name."[]' id='$field_id.$i' value='$option' class='radio $validate'/><label for='$field_id.$i' class='$field_class' style='$field_style'>$option</label></li>";
				}
				$html.="</ul>";
				break;
			case 'select/checkbox':
				$options = explode("\n",$field_value);
				$html= "<ul style='padding:10px'>";
				$field_class=  $this->getCssClass();
				foreach($options as $i=>$option){
					if($this->getRequired() && $i==(count($options)-1)){
						$validate = "validate-one-required-by-name";
					}
	
					if(trim($option))
						$html.= "<li class='control'><input style='float:left' type='checkbox' name='".$field_name."[]' id='$field_id.$i' value='$option' class='checkbox $validate'/><label for='$field_id.$i' class='$field_class' style='$field_style'>&nbsp;$option</label></li>";
				}
				$html.="</ul>";
				break;
			default: 
				$html ="<input type='text' name='$field_name' id='$field_id' class='$field_class' style='$field_style' value='$field_value'/>";
				break;
		}
		
		// apply custom field type
		Mage::dispatchEvent('webforms_fields_tohtml_html',array('field'=>$this,'html'=>$html));
		
		return $html;
	}
	
}
?>