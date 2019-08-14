<?php 
/**
 * Magmodules.eu - http://www.magmodules.eu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magmodules.eu so we can send you a copy immediately.
 *
 * @category	Magmodules
 * @package		Magmodules_Kiyoh
 * @author		Magmodules <info@magmodules.eu)
 * @copyright	Copyright (c) 2015 (http://www.magmodules.eu)
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Magmodules_Kiyoh_Model_Api extends Mage_Core_Model_Abstract {

	public function processFeed($storeid = 0, $type) {
		if($feed = $this->getFeed($storeid, $type)) {
			$results			= Mage::getModel('kiyoh/reviews')->processFeed($feed, $storeid, $type);
			$results['stats']	= Mage::getModel('kiyoh/stats')->processFeed($feed, $storeid);
			return $results;
		} else {
			return false;
		}
	}

	public function getFeed($storeid, $type = '') {
		$api_id	= Mage::getStoreConfig('kiyoh/general/api_id', $storeid);
		$api_key = Mage::getStoreConfig('kiyoh/general/api_key', $storeid);
		$api_url = Mage::getStoreConfig('kiyoh/general/api_url', $storeid);
		
		if($type == 'stats') {		
			$api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10';
		} 
		if($type == 'reviews') {
			$api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10';
		}
		if($type == 'history') {
			$api_url = 'https://' . $api_url . '/xml/recent_company_reviews.xml?connectorcode=' . $api_key . '&company_id=' . $api_id . '&reviewcount=10000';
		}
		
		
		if($api_id) {
			$xml = simplexml_load_file($api_url);	
			if($xml) {
				return $xml;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function sendInvitation($order) {
		
		$store_id 		= $order->getStoreId();
		$start_time 	= microtime(true);
		$crontype 		= 'orderupdate';
		$order_id 		= $order->getIncrementId(); 
		$api_id 		= Mage::getStoreConfig('kiyoh/general/api_id', $store_id);
		$api_key 		= Mage::getStoreConfig('kiyoh/general/api_key', $store_id);
		$api_url 		= Mage::getStoreConfig('kiyoh/general/api_url', $store_id);
		$api_email 		= Mage::getStoreConfig('kiyoh/invitation/company_email', $store_id);
		$delay			= Mage::getStoreConfig('kiyoh/invitation/delay', $store_id);
		$min_order		= Mage::getStoreConfig('kiyoh/invitation/min_order_total', $store_id);
		$inv_status		= Mage::getStoreConfig('kiyoh/invitation/status', $store_id);
		$email			= strtolower($order->getCustomerEmail());
	    $order_total 	= $order->getGrandTotal();

		if($order_total >= $min_order || $min_order == 0) {
			if($order->getStatus() == $inv_status) {			
				$http = new Varien_Http_Adapter_Curl();
				$http->setConfig(array('timeout' => 30, 'maxredirects' => 0));

				$url = 'https://' . $api_url. '/set.php';								
				$request = "action=sendInvitation&connector=" . $api_key . "&targetMail=" . $email . "&delay=" . $delay . "&user=" . $api_email;
				
				$http->write(Zend_Http_Client::POST, $url, '1.1', array(), $request);
				$result = $http->read();

				if($result) {
					$lines = explode("\n", $result);
					$response_html = $lines[0];
					$lines = array_reverse($lines);
					$response_html .=  ' - ' . $lines[0];
				} else {
					$response_html = 'No response from ' . $url; 
				}
		
				// Write to log
				$writelog = Mage::getModel('kiyoh/log')->addToLog('invitation', $order->getStoreId(), '', $response_html, (microtime(true) - $start_time), $crontype, $url . '?' . $request, $order->getId());
				return true;
			}
		} else {
			return false;	
		}
	}

	public function getStoreIds() {
		$store_ids =  array(); $api_ids =  array();
		$stores = Mage::getModel('core/store')->getCollection();
		foreach ($stores as $store) {		
			if($store->getIsActive()) {
				$api_id	= Mage::getStoreConfig('kiyoh/general/api_id', $store->getId());
				if(!in_array($api_id, $api_ids)) {
					$api_ids[] = $api_id; $store_ids[] = $store->getId();
				}		
			}
		}
		return $store_ids;		
	}
	
}