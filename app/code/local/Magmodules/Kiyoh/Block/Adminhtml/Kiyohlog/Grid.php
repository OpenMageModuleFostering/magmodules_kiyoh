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
 
class Magmodules_Kiyoh_Block_Adminhtml_Kiyohlog_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('kiyohlogGrid');
		$this->setDefaultSort('date');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getModel('kiyoh/log')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
  
		$this->addColumn('company', array(
			'header'	=> Mage::helper('kiyoh')->__('Shop'),
			'index'		=> 'company',
			'width'		=> '120px',
		));

		$this->addColumn('type', array(
			'header'	=> Mage::helper('kiyoh')->__('Type'),
			'align'		=> 'left',
			'index'		=> 'type',
			'width'		=> '120',
			'type'		=> 'options',
			'options'=> array(
				'reviews'		=> Mage::helper('kiyoh')->__('Reviews'),
				'invitation'	=> Mage::helper('kiyoh')->__('Invitation Call'),
			),
		));

		if(Mage::app()->getRequest()->getParam('showapiurl')) {
			$this->addColumn('api_url', array(
				'header'	=> Mage::helper('kiyoh')->__('Api URL'),
				'align'		=> 'left',
				'index'		=> 'api_url',
				'filter'	=> false,
				'sortable'	=> false,
			));
		}

		$this->addColumn('qty', array(
			'header'	=> Mage::helper('kiyoh')->__('Description'),
			'align'		=> 'left',
			'index'		=> 'qty',
			'renderer'	=> 'kiyoh/adminhtml_widget_grid_log',
			'filter'	=> false,
			'sortable'	=> false,
		));

		$this->addColumn('cron', array(
			'header'	=> Mage::helper('kiyoh')->__('Cron'),
			'align'		=> 'left',
			'index'		=> 'cron',
			'width'		=> '120',
			'type'		=> 'options',
			'options'	=> array(
					''				=> Mage::helper('kiyoh')->__('Manual'),
					'stats'			=> Mage::helper('kiyoh')->__('Stats Cron'),
					'reviews'		=> Mage::helper('kiyoh')->__('Reviews Cron'),
					'orderupdate'	=> Mage::helper('kiyoh')->__('Invitation'),
			),
		));

		$this->addColumn('time', array(
			'header'	=> Mage::helper('kiyoh')->__('Time'),
			'align'		=> 'left',
			'index'		=> 'time',
			'width'		=> '60',
			'renderer'	=> 'kiyoh/adminhtml_widget_grid_seconds',
		));

		$this->addColumn('date', array(
			'header'	=> Mage::helper('kiyoh')->__('Date'),
			'align'		=> 'left',
			'type'		=> 'datetime',
			'index'		=> 'date',
			'width'		=> '140',
		));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('logids');

		$this->getMassactionBlock()->addItem('hide', array(
			'label'	=> Mage::helper('kiyoh')->__('Delete'),
			'url'	=> $this->getUrl('*/*/massDelete'),
		));
		return $this;
	}

	public function getRowUrl($row)
	{
		return;
	}

}