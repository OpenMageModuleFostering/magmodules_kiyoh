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

class Magmodules_Kiyoh_Model_Reviews extends Mage_Core_Model_Abstract {

    const CACHE_TAG  = 'kiyoh_block';

	public function _construct() {
		parent::_construct();
		$this->_init('kiyoh/reviews');
	}

	public function loadbyKiyohId($kiyoh_id) {
		$this->_getResource()->load($this, $kiyoh_id, 'kiyoh_id');
		return $this;
	}

	public function processFeed($feed, $storeid = 0, $type) { 

		$updates = 0; $new = 0; $history = 0;
		$api_id	= Mage::getStoreConfig('kiyoh/general/api_id', $storeid);
		$company = $feed->company->name;

		foreach($feed->review_list->review as $review) {
		
			$kiyoh_id = $review->id;
			$customer_name = $review->customer->name;
			$customer_email = $review->customer->email;
			$customer_place = $review->customer->place;
			$date = $review->customer->date;
			$total_score = $review->total_score;
			
			$recommendation = $review->recommendation;
			$positive = $review->positive;
			$negative = $review->negative;			
			$purchase = $review->purchase;						
			$reaction = $review->reaction;									
			
			if(($recommendation == 'Ja') || ($recommendation == 'Yes')) {
				$recommendation = 1;
			} else {
				$recommendation = 0;			
			}
			
			$questions = array(); 
			foreach($review->questions->question as $question) {
				$questions[] = $question->score; 
			}

			$indatabase = $this->loadbyKiyohId($kiyoh_id);
			
			if($indatabase->getId()) {
				if($type == 'history') {
					$reviews = Mage::getModel('kiyoh/reviews');
					$reviews->setReviewId($indatabase->getReviewId())
							->setShopId($api_id)
							->setCompany($company)
							->setKiyohId($kiyoh_id)
							->setCustomerName($customer_name)
							->setCustomerEmail($customer_email)
							->setCustomerPlace($customer_place)
							->setScore($total_score)
							->setScoreQ2($questions[0])
							->setScoreQ3($questions[1])
							->setScoreQ4($questions[2])
							->setScoreQ5($questions[3])
							->setScoreQ6($questions[4])
							->setScoreQ7($questions[5])
							->setScoreQ8($questions[6])
							->setScoreQ9($questions[7])
							->setScoreQ10($questions[8])																																																															
							->setRecommendation($recommendation)
							->setPositive($positive)
							->setNegative($negative)
							->setPurchase($purchase)
							->setReaction($reaction)
							->setDateCreated($date)
							->save();
					$updates++;
				} else {
					break;
				}
			} else {
				$reviews = Mage::getModel('kiyoh/reviews');
				$reviews->setShopId($api_id)
							->setCompany($company)
							->setKiyohId($kiyoh_id)
							->setCustomerName($customer_name)
							->setCustomerEmail($customer_email)
							->setCustomerPlace($customer_place)
							->setScore($total_score)
							->setScoreQ2($questions[0])
							->setScoreQ3($questions[1])
							->setScoreQ4($questions[2])
							->setScoreQ5($questions[3])
							->setScoreQ6($questions[4])
							->setScoreQ7($questions[5])
							->setScoreQ8($questions[6])
							->setScoreQ9($questions[7])
							->setScoreQ10($questions[8])								
							->setRecommendation($recommendation)
							->setPositive($positive)
							->setNegative($negative)
							->setPurchase($purchase)
							->setReaction($reaction)
							->setDateCreated($date)
							->save();
				$new++;
			}
		}

		$config = new Mage_Core_Model_Config();
		$config->saveConfig('kiyoh/reviews/lastrun', now(), 'default', $storeid);
		$result = array();
		$result['review_updates'] = $updates; 
		$result['review_new'] = $new; 
		$result['company'] = $company;
		return $result; 
	}

}