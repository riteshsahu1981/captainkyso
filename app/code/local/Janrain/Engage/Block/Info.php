<?php

class Janrain_Engage_Block_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {

        $html = $this->_getHeaderHtml($element);

        $html.= $this->_getFieldHtml($element);

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldHtml($fieldset) {
        $content = '<p>Janrain Engage for Magento ' . Mage::getConfig()->getModuleConfig("Janrain_Engage")->version . '</p>';
        $content.= '<p>This extension is developed by <a href="http://janrain.com/" target="_blank">Janrain</a>. Please refer to our <a href="http://documentation.janrain.com/engage/plugins-modules/magento" target="_blank">Documentation</a> on how to install and configure this extension.</p>';
        $content.= '<p>Copyright &copy ' . date("Y") . ' <a href="http://janrain.com/" target="_blank">Janrain, Inc.</a></p>';
		
        return $content;
    }

}
