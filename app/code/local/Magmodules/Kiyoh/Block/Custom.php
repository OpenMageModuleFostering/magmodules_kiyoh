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
 
class Magmodules_Kiyoh_Block_Custom extends Mage_Core_Block_Template {

	protected function _construct() {
		parent::_construct();	

		$this->addData(array(
            'cache_lifetime'    => 7200,
            'cache_tags'        => array(Mage_Cms_Model_Block::CACHE_TAG, Magmodules_Kiyoh_Model_Reviews::CACHE_TAG),
            'cache_key'         => 'kiyoh-custom-block',                   
        ));

		if(Mage::getStoreConfig('kiyoh/general/enabled')) {
			$this->setTemplate('magmodules/kiyoh/widget/custom.phtml');
		}        		
	}

	public function getKiyohData() {
		return $this->helper('kiyoh')->getTotalScore();
	}

}