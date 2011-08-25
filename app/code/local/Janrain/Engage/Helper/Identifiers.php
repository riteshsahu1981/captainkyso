<?php
class Janrain_Engage_Helper_Identifiers extends Mage_Core_Helper_Abstract {

	/**
	 * Assigns a new identifier to a customer
	 *
	 * @param int $customer_id
	 * @param string $identifier
	 */
	public function save_identifier($customer_id, $profile) {

		/**
		 * Make sure we have a valid customer_id
		 *
		 */
		$customer = Mage::getModel('customer/customer')
			->getCollection()
			->addFieldToFilter('entity_id', $customer_id)
			->getFirstItem();
		if(!$customer->getId())
			Mage::throwException('Invalid Customer ID');

		/**
		 * Make the save
		 *
		 */
		try {
			Mage::getModel('engage/identifiers')
					->setIdentifier($profile['identifier'])
					->setProvider($profile['provider'])
					->setProfileName($profile['profile_name'])
					->setCustomerId($customer_id)
					->save();
		}
		catch (Exception $e) {
			echo "Could not save: " . $e->getMessage() . "\n";
		}

	}

	/**
	 * Gets a customer by identifier
	 *
	 * @param string $identifier
	 * @return Mage_Customer_Model_Customer
	 */
	public function get_customer($identifier) {
		$customer_id = Mage::getModel('engage/identifiers')
						->getCollection()
						->addFieldToFilter('identifier', $identifier)
						->getFirstItem();

		$customer_id = $customer_id->getCustomerId();
		if((int) $customer_id > 0) {
			$customer = Mage::getModel('customer/customer')
						->getCollection()
						->addFieldToFilter('entity_id', $customer_id)
						->getFirstItem();
			return $customer;
		}

		return false;
	}

	public function get_identifiers($customer_id) {
		if((int) $customer_id > 0){
			$identifiers = Mage::getModel('engage/identifiers')
							->getCollection()
							->addFieldToFilter('customer_id', $customer_id);

			return $identifiers;
		}

		return false;
	}

	public function delete_identifier($id) {
		$customer_id = Mage::getSingleton('customer/session')
				->getCustomer()
				->getId();

		$identifier = Mage::getModel('engage/identifiers')
							->getCollection()
							->addFieldToFilter('engage_identifier_id', $id)
							->getFirstItem();
		if($identifier->getCustomerId() == $customer_id) {
			try {
				$identifier->delete();
			} catch (Exception $e) {
				echo "Could not delete: $e";
			}
		}
		
	}

}