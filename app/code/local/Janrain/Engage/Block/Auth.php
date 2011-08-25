<?php

class Janrain_Engage_Block_Auth extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

	function rpx_small_buttons() {
        $size = $this->getSize();
        if ($size == 'inline') {
            $iframe = '<iframe src="'
                . ((Mage::getStoreConfig('engage/vars/realmscheme') == 'https') ? 'https' : 'http')
                . '://' . Mage::getStoreConfig('engage/vars/realm')
                . '/openid/embed?token_url=' . urlencode(Mage::getUrl('engage/rpx/token_url'))
                . '" scrolling="no" frameBorder="no" allowtransparency="true" style="width:400px;height:240px"></iframe>';
            return $iframe;
        } else {
            $providers = Mage::helper('engage')->getRpxProviders();
            if (is_array($providers)) {
                $size = ($size == 'small') ? "16" : "30";
                $wrap_open = '<a class="rpxnow rpx_link_wrap" onclick="return false;" href="'
                        . Mage::helper('engage')->getRpxAuthUrl()
                        . '">';
                $wrap_close = '</a>';

                $labelText = $this->getLabelText();
                if(empty($labelText))
                    $labelText = 'Or log in with';

                $label = '<div class="rpx_label">' . $labelText . '</div>';
                $rpx_buttons = '';
                foreach ($providers as $val) {
                    $rpx_buttons .= '<div class="jn-icon jn-size' . $size . ' jn-' . $val . '" title="' . htmlentities($val) . '"></div>';
                }
                $buttons = '<div class="rpx_button">' . $rpx_buttons . '</div><div class="rpx_clear"></div>';

                return $wrap_open . $label . $buttons . $wrap_close;
            }
        }
	}

	protected function _toHtml() {
		$content = '';
		if (Mage::getSingleton('customer/session')->isLoggedIn() == false)
			$content = $this->rpx_small_buttons();
		return $content;
	}

	protected function _prepareLayout() {

		/*
		 * Doesn't work on inline widgets because layout isn't loaded until
		 * after the head has been written to the page. Fix.
		 *
		if($this->getLayout()->getBlock('janrain_engage_styles')==false) {
			$block = $this->getLayout()
				->createBlock('core/template', 'janrain_engage_styles')
				->setTemplate('janrain/engage/styles.phtml');
			$this->getLayout()->getBlock('head')->insert($block);
		}
		*/

		if($this->getLayout()->getBlock('janrain_engage_scripts')==false) {
			$block = $this->getLayout()
				->createBlock('core/template', 'janrain_engage_scripts')
				->setTemplate('janrain/engage/scripts.phtml');
			$this->getLayout()->getBlock('before_body_end')->insert($block);
		}

		parent::_prepareLayout();
	}

}