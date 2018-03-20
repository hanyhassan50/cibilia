<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipVendorAskQuestion
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipVendorAskQuestion\Model\ResourceModel\Question;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\Source;

class Collection extends AbstractCollection
{
    /**
     * @var HelperData
     */
    protected $_qaHlp;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger, 
        FetchStrategyInterface $fetchStrategy, 
        ManagerInterface $eventManager, 
        HelperData $helperData, 
        AdapterInterface $connection = null, 
        AbstractDb $resource = null)
    {
        $this->_qaHlp = $helperData;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected $_eventPrefix = 'udqa_question_collection';
    protected $_eventObject = 'question_collection';

    protected $_map = ['fields' => [
        'shipment_name' => 'shipment_grid.shipping_name',
        'order_increment_id'=>'shipment_grid.order_increment_id',
        'order_id'=>'shipment_grid.order_id',
        'shipment_increment_id'=>'shipment_grid.increment_id',
        'shipment_id'=>'shipment_grid.entity_id',
        'product_id' => 'product.entity_id',
        'product_sku' => 'product.sku'
    ]];

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipVendorAskQuestion\Model\Question', 'Unirgy\DropshipVendorAskQuestion\Model\ResourceModel\Question');
        $this->_map['fields']['answer_text_length'] = new \Zend_Db_Expr('LENGTH(answer_text)');
    }

    public function setDateOrder($dir='DESC')
    {
        $this->setOrder('answer_date', $dir);
        $this->setOrder('question_date', $dir);
        return $this;
    }
    public function addCustomerFilter($customer)
    {
        $filter = $customer;
        is_object($customer) && ($filter = $customer->getId());
        $this->addFieldToFilter('main_table.customer_id', $filter);
        return $this;
    }
    public function addVendorFilter($vendor)
    {
        $filter = $vendor;
        is_object($vendor) && ($filter = $vendor->getId());
        $this->addFieldToFilter('main_table.vendor_id', $filter);
        return $this;
    }
    public function addApprovedQuestionsFilter()
    {
        $this->getSelect()->where('main_table.question_status=1');
        return $this;
    }
    public function addApprovedAnswersFilter()
    {
        $this->getSelect()->where('main_table.answer_status=1');
        return $this;
    }

    public function addPublicProductFilter($pId)
    {
        if ($pId instanceof DataObject) {
            $pId = $pId->getProductId() ? $pId->getProductId() : $pId->getEntityId();
        }
        if (!is_array($pId)) {
            $pId = [$pId];
        }
        $this->addFieldToFilter('main_table.product_id',['in'=>$pId]);
        $this->addFieldToFilter('main_table.visibility',Source::UDQA_VISIBILITY_PUBLIC);
        $this->addApprovedQuestionsFilter();
        $this->addApprovedAnswersFilter();
        return $this;
    }

    public function addPendingStatusFilter()
    {
        $this->getSelect()->where('main_table.question_status=0 OR main_table.answer_status=0');
        return $this;
    }
    public function addContextFilter($value)
    {
        $this->joinProducts();
        $this->joinShipments();
        $filter = ['like'=>$value.'%'];
        $columns = [
            'shipment_grid.increment_id',
            'shipment_grid.order_increment_id',
            'product.sku',
            'product.entity_id'
        ];
        $filters = [$filter,$filter,$filter,$filter];
        foreach ($this->getSelect()->getPart(Select::COLUMNS) as $_selCol) {
            if (@$_selCol[2]=='product_name') {
                $columns[] = ''.$_selCol[1];
                $filters[] = $filter;
                break;
            }
        }
        $this->addFieldToFilter($columns, $filters);
        return $this;
    }
    public function joinShipments()
    {
        if ($this->getFlag('joinShipments')) return $this;
        $this->getSelect()
            ->joinLeft(
                ['shipment_grid' => $this->getTable('sales_shipment_grid')],
                'main_table.shipment_id = shipment_grid.entity_id',
                [
                    'shipment_name' => 'shipment_grid.shipping_name',
                    'order_increment_id'=>'shipment_grid.order_increment_id',
                    'order_id'=>'shipment_grid.order_id',
                    'shipment_increment_id'=>'shipment_grid.increment_id',
                    'shipment_id'=>'shipment_grid.entity_id'
                ]
            )
        ;
        $this->setFlag('joinShipments', 1);
        return $this;
    }
    public function joinProducts()
    {
        if ($this->getFlag('joinProducts')) return $this;
        $this->getSelect()
            ->joinLeft(
                ['product' => $this->getTable('catalog_product_entity')],
                'main_table.product_id = product.entity_id',
                [
                    'product_id' => 'product.entity_id',
                    'product_sku' => 'product.sku'
                ]
            )
        ;
        $this->addProductAttributeToSelect(['product_name'=>'name']);
        foreach ($this->getSelect()->getPart(Select::COLUMNS) as $_selCol) {
            if (@$_selCol[2]=='product_name') {
                $this->_map['fields']['product_name'] = $_selCol[1];
                break;
            }
        }
        $this->setFlag('joinProducts', 1);
        return $this;
    }
    public function joinVendors()
    {
        if ($this->getFlag('joinVendors')) return $this;
        $this->getSelect()
            ->joinLeft(
            ['vendor' => $this->getTable('udropship_vendor')],
            'main_table.vendor_id = vendor.vendor_id',
            [
                'vendor_name'  => 'vendor.vendor_name',
                'vendor_email' => 'vendor.email',
                'vendor_id'    => 'vendor.vendor_id'
            ]
        )
        ;
        $this->setFlag('joinVendors', 1);
        return $this;
    }
    public function addProductAttributeToSelect($attrCode)
    {
        $this->_qaHlp->addProductAttributeToSelect($this->getSelect(), $attrCode, 'main_table.product_id');
        return $this;
    }
    public  function setEmptyFilter()
    {
        $this->getSelect()->where('false');
        return $this;
    }
}