<?php

namespace StageBit\CustomCode\Observer\Unirgy\DropshipVendorProduct;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogProductSaveBefore
 *
 * @package StageBit\CustomCode\Observer
 */
class CatalogProductSaveBefore implements ObserverInterface
{
    /**
     * @var \Unirgy\Dropship\Model\Session
     */
    protected $_session;

    /**
     * @var \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory
     */
    protected $_udropshipVendorReistration;

    /**
     * CatalogProductSaveBefore constructor.
     *
     * @param \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory $collectionFactory
     * @param \Unirgy\Dropship\Model\Session $authSession
     */
    public function __construct(
        \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory $collectionFactory,
        \Unirgy\Dropship\Model\Session $authSession
    )
    {
        $this->_udropshipVendorReistration = $collectionFactory;
        $this->_session = $authSession;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $_product = $observer->getProduct();
        $vedorEmail = $this->_session->getVendor()->getEmail();
        $vendorStoreId = $this->getVendorStoreId($vedorEmail);

        if ($vendorStoreId === "13") {
            $vndrStorId = array("7");
        }

        if ($vendorStoreId === "14") {
            $vndrStorId = array("5");
        }

        if ($vendorStoreId === "15") {
            $vndrStorId = array("4");
        }

        if ($vendorStoreId === "16") {
            $vndrStorId = array("8");
        }

        if ($vendorStoreId === "17") {
            $vndrStorId = array("6");
        }

        if ($vendorStoreId === "1") {
            $vndrStorId = array("1");
        }

        $_product->setWebsiteIds($vndrStorId);
    }

    public function getVendorStoreId($vendor_email)
    {
        $vendorsEmail = $vendor_email;
        $collection = $this->_udropshipVendorReistration->create()
            ->addFieldToSelect('store_id')
            ->addFieldToFilter('email', $vendorsEmail);
        $firstItem = $collection->getFirstItem();

        if (isset($firstItem)) {
            $storeId = $firstItem->getStoreId();
        }
        return $storeId;
    }

}