<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Customer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use Unirgy\Dropship\Model\Source;

class Form extends Template
{
    /**
     * @var Collection
     */
    protected $_shipmentCollection;

    /**
     * @var Source
     */
    protected $_modelSource;

    public function __construct(Context $context, 
        Collection $shipmentCollection, 
        Source $modelSource, 
        array $data = [])
    {
        $this->_shipmentCollection = $shipmentCollection;
        $this->_modelSource = $modelSource;

        parent::__construct($context, $data);
    }

    protected $_shipments;
    public function getShipments()
    {
        if (null === $this->_shipments) {
            $this->_shipments = $this->_shipmentCollection;
            $this->_shipments->addFieldToFilter('customer_id', ObjectManager::getInstance()->get('Magento\Customer\Model\Session')->getCustomerId());
            $this->_shipments->addFieldToSelect([
                'order_id',
                'shipment_increment_id'=>'increment_id',
                'shipment_id'=>'entity_id'
            ]);
            $this->_shipments->join(
                'sales_shipment_grid',
                '`sales_shipment_grid`.entity_id=main_table.entity_id',
                ['order_increment_id']
            );
        }
        return $this->_shipments;
    }
    public function getFormAction()
    {
        return $this->getUrl('udqa/customer/post');
    }
    public function getVendors()
    {
        return $this->_modelSource->getVendors(true);
    }
}
