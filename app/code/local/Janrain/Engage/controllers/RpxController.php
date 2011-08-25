<?php

require_once("Mage/Customer/controllers/AccountController.php");

class Janrain_Engage_RpxController extends Mage_Customer_AccountController {

	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
	 *
	 * This is a clone of the one in Mage_Customer_AccountController
	 * with two added action names to the preg_match regex to prevent
	 * redirects back to customer/account/login when using Engage
	 * authentication links. Rather than calling parent::preDispatch()
	 * we explicitly call Mage_Core_Controller_Front_Action to prevent the
	 * original preg_match test from breaking our auth process.
	 * 
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        Mage_Core_Controller_Front_Action::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(addIdentifier|token_url_add|token_url|authenticate|duplicate|create|login|logoutSuccess|forgotpassword|forgotpasswordpost|confirm|confirmation)/i', $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }

	public function indexAction() {
		$this->_redirect('customer/account/index');
	}

	/**
	 * RPX Callback
	 */
	public function token_urlAction() {
		$session = $this->_getSession();

		// Redirect if user is already authenticated
		if ($session->isLoggedIn()) {
			$this->_redirect('customer/account');
			return;
		}

		if ($this->getRequest()->isPost()) {
			$token = $this->getRequest()->getPost('token');

			if($token){
				// Store token in session under random key
				$key = Mage::helper('engage')->rand_str(12);
				Mage::getSingleton('engage/session')->setData($key, $token);

				// Redirect user to $this->authAction method passing $key as ses
				// $_GET variable (Magento style)
				$this->_redirect("janrain-engage/rpx/authenticate", array("ses" => $key));
				return;
			} else {
				$session->addError('Authentication token not received. Please try again.');
			}
		}

		$this->_redirect('customer/account/login');
	}

	/**
	 * RPX Callback for Additional Identifiers
	 */
	public function token_url_addAction(){
		$session = $this->_getSession();

		// Redirect if user isn't already authenticated
		if (!$session->isLoggedIn()) {
			$this->_redirect('customer/account/login');
			return;
		}

		if ($this->getRequest()->isPost()) {
			$token = $this->getRequest()->getPost('token');

			// Store token in session under random key
			$key = Mage::helper('engage')->rand_str(12);
			Mage::getSingleton('engage/session')->setData($key, $token);

			// Redirect user to $this->authAction method passing $key as ses
			// $_GET variable (Magento style)
			$this->_redirect("janrain-engage/rpx/addidentifier", array("ses" => $key));
		}
	}

	public function authenticateAction() {
		$session = $this->_getSession();

		$key = $this->getRequest()->getParam('ses');
		$token = Mage::getSingleton('engage/session')->getData($key);
		$auth_info = Mage::helper('engage/rpxcall')->rpxAuthInfoCall($token);
		if(isset($auth_info->stat) && $auth_info->stat=='ok') {
			$customer = Mage::helper('engage/identifiers')->get_customer($auth_info->profile->identifier);

			if ($customer === false) {
				$this->loadLayout();
				$block = Mage::getSingleton('core/layout')->getBlock('customer_form_register');
				if($block !== false) {
					$form_data = $block->getFormData();

					if(isset($auth_info->profile) && isset($auth_info->profile->verifiedEmail))
						$email = $auth_info->profile->verifiedEmail;
					else if(isset($auth_info->profile) && isset($auth_info->profile->email))
						$email = $auth_info->profile->email;
					else
						$email = '';

					$firstName = Mage::helper('engage/rpxcall')->getFirstName($auth_info);
					$lastName = Mage::helper('engage/rpxcall')->getLastName($auth_info);

					$form_data->setEmail($email);
					$form_data->setFirstname($firstName);
					$form_data->setLastname($lastName);
				}
				$profile = Mage::helper('engage')->buildProfile($auth_info);
				Mage::getSingleton('engage/session')->setIdentifier($profile);

				$this->renderLayout();
				return;
			} else {
				Mage::getSingleton('engage/session')->setLoginRequest(true);
				$session->login($customer->getEmail(), 'REQUIRED_SECOND_PARAM');
				$this->_loginPostRedirect();
			}
		} else {
			$session->addWarning('Could not retrieve account info. Please try again.');
			$this->_redirect('customer/account/login');
		}
	}

	public function addIdentifierAction() {
		$session = $this->_getSession();

		$key = $this->getRequest()->getParam('ses');
		$token = Mage::getSingleton('engage/session')->getData($key);
		$auth_info = Mage::helper('engage/rpxcall')->rpxAuthInfoCall($token);

		$customer = Mage::helper('engage/identifiers')->get_customer($auth_info->profile->identifier);

		if ($customer===false) {
			$customer_id = $session->getCustomer()->getId();
			$profile = Mage::helper('engage')->buildProfile($auth_info);

			Mage::helper('engage/identifiers')
					->save_identifier($customer_id, $profile);

			$session->addSuccess('New provider successfully added.');

		} else {
			$session->addWarning('Could not add Provider. This account is already associated with a user.');
		}

		$this->_redirect('customer/account');
	}

	public function createPostAction() {
		$session = $this->_getSession();
		parent::createPostAction();

		$messages = $session->getMessages();
		$isError = false;

		foreach ($messages->getItems() as $message) {
			if ($message->getType() == 'error') {
				$isError = true;
			}
		}

		if ($isError) {
			$email = $this->getRequest()->getPost('email');
			$firstname = $this->getRequest()->getPost('firstname');
			$lastname = $this->getRequest()->getPost('lastname');
			Mage::getSingleton('engage/session')
				->setEmail($email)
				->setFirstname($firstname)
				->setLastname($lastname);
			$this->_redirect('engage/rpx/duplicate');
		}

		return;
	}

	public function duplicateAction() {
		$session = $this->_getSession();

		// Redirect if user is already authenticated
		if ($session->isLoggedIn()) {
			$this->_redirect('customer/account');
			return;
		}

		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$block = Mage::getSingleton('core/layout')->getBlock('customer_form_register');
		$block->setUsername(Mage::getSingleton('engage/session')->getEmail());
		$block->getFormData()->setEmail(Mage::getSingleton('engage/session')->getEmail());
		$block->getFormData()->setFirstname(Mage::getSingleton('engage/session')->getFirstname());
		$block->getFormData()->setLastname(Mage::getSingleton('engage/session')->getLastname());
		$this->renderLayout();
	}

	public function loginPostAction() {
		parent::loginPostAction();
	}

	protected function _loginPostRedirect() {
		$session = $this->_getSession();
		if ($session->isLoggedIn()) {
			if ($profile = Mage::getSingleton('engage/session')->getIdentifier()) {
				$customer = $session->getCustomer();
				Mage::helper('engage/identifiers')
						->save_identifier($customer->getId(), $profile);
				Mage::getSingleton('engage/session')->setIdentifier(false);
			}
		}

		parent::_loginPostRedirect();
	}

	public function removeIdAction() {
		$session = $this->_getSession();
		$id = $this->getRequest()->getParam('identifier');

		Mage::helper('engage/identifiers')
				->delete_identifier($id);
		$session->addSuccess('Provider removed');
		$this->_redirect('customer/account');
	}

}