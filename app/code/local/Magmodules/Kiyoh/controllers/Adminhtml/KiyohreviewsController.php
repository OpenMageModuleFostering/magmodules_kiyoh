<?php 
/**
 * Magmodules.eu
 * http://www.magmodules.eu
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
 
class Magmodules_Kiyoh_Adminhtml_KiyohreviewsController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()->_setActiveMenu('kiyoh/kiyohreviews')->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	public function indexAction() {
		$this->_initAction()->renderLayout();
	}

	public function processAction() {
		$storeids = Mage::getModel('kiyoh/api')->getStoreIds();
		$start_time = microtime(true);

		foreach($storeids as $storeid)  {
			$msg = '';
			$api_id = Mage::getStoreConfig('kiyoh/general/api_id', $storeid);
			$result = Mage::getModel('kiyoh/api')->processFeed($storeid, 'history');		
			$log = Mage::getModel('kiyoh/log')->addToLog('reviews', $storeid, $result, '', (microtime(true) - $start_time), '', '');

			if(($result['review_new'] > 0) || ($result['review_updates'] > 0) || ($result['stats'] == true)) {
				$msg = Mage::helper('kiyoh')->__('Webwinkel ID %s:', $api_id) . ' '; 
				$msg .= Mage::helper('kiyoh')->__('%s new review(s)', $result['review_new']) . ', '; 
				$msg .= Mage::helper('kiyoh')->__('%s review(s) updated', $result['review_updates']) . ' & '; 
				$msg .= Mage::helper('kiyoh')->__('and total score updated.');
			}

			if($msg) {
				Mage::getSingleton('adminhtml/session')->addSuccess($msg);
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kiyoh')->__('Webwinkel ID %s: no updates found, feed is empty or not found!', $api_id));
			}
		}
		Mage::getModel('kiyoh/stats')->processOverall();
		$this->_redirect('adminhtml/system_config/edit/section/kiyoh');
	}

	public function massDisableAction() {
		$reviewIds = $this->getRequest()->getParam('reviewids');
		if(!is_array($reviewIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kiyoh')->__('Please select item(s)'));
		} else {
			try {
				foreach ($reviewIds as $review_id) {
					$reviews = Mage::getModel('kiyoh/reviews')->load($review_id);
					$reviews->setStatus(0)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('kiyoh')->__('Total of %d review(s) were disabled.', count($reviewIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massEnableAction() {
	$reviewIds = $this->getRequest()->getParam('reviewids');
		if(!is_array($reviewIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kiyoh')->__('Please select item(s)'));
		} else {
			try {
				foreach ($reviewIds as $review_id) {
					$reviews = Mage::getModel('kiyoh/reviews')->load($review_id);
					$reviews->setStatus(1)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('kiyoh')->__('Total of %d review(s) were enabled.', count($reviewIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massEnableSidebarAction() {
		$reviewIds = $this->getRequest()->getParam('reviewids');
		if(!is_array($reviewIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kiyoh')->__('Please select item(s)'));
		} else {
			try {
				foreach ($reviewIds as $review_id) {
					$reviews = Mage::getModel('kiyoh/reviews')->load($review_id);
					$reviews->setSidebar(1)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('kiyoh')->__('Total of %d review(s) were added to the sidebar.', count($reviewIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massDisableSidebarAction() {
		$reviewIds = $this->getRequest()->getParam('reviewids');
		if(!is_array($reviewIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('kiyoh')->__('Please select item(s)'));
		} else {
			try {
				foreach ($reviewIds as $review_id) {
					$reviews = Mage::getModel('kiyoh/reviews')->load($review_id);
					$reviews->setSidebar(0)->save();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('kiyoh')->__('Total of %d review(s) were removed from the sidebar.', count($reviewIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	} 

	public function truncateAction() {
		$i = 0;
		$collection = Mage::getModel('kiyoh/reviews')->getCollection();
		foreach ($collection as $item) {
			$item->delete();
			$i++;
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('kiyoh')->__('Succefully deleted all %s saved review(s).', $i));
		$this->_redirect('*/*/index');	
	}

}