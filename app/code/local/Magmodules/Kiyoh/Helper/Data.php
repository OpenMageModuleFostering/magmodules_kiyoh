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
 
class Magmodules_Kiyoh_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getTotalScore() {
		$qty = 0; $rating = 0;
		$shop_id = Mage::getStoreConfig('kiyoh/general/api_id');					
		$review_stats = Mage::getModel('kiyoh/stats')->load($shop_id, 'shop_id');				
		if($review_stats->getScore() > 0) {
			$review_stats->setPercentage($review_stats->getScore());
			$review_stats->setStarsQty(number_format(($review_stats->getScore() / 10), 1, ',', ''));			
			return $review_stats;
		} else {
			return false;
		}		
	}

	function getExternalLink() {			
		if(Mage::getStoreConfig('kiyoh/general/url')) {
			return Mage::helper('kiyoh')->__('on') . ' <a href="' . Mage::getStoreConfig('kiyoh/general/url'). '" target="_blank">Kiyoh.nl</a>';
		} else {
			return false; 
		}		
	}

	function getHtmlStars($rating) {	
		$perc  = $rating;
		$html  = '<div class="rating-box">';
		$html .= '	<div class="rating" style="width:' . $perc . '%"></div>';
		$html .= '</div>';
		return $html;
	}
		
}