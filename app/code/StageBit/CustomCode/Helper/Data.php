<?php

namespace StageBit\CustomCode\Helper;

use Magento\Framework\App\Helper\Context;
use  Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $allowedExtensions = ['jpg', 'jpeg', 'svg', 'png'];

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_cibilian;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlinterface;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Message\Manager
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Unirgy\Dropship\Helper\Data
     */
    protected $_unergyHelper;

    /**
     * @var array
     */
    protected $_categoryIds = [];

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Message\Manager $manager
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\UrlInterface $urlInterFace
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Unirgy\Dropship\Helper\Data $unergyHelperData
     */
    public function __construct(
        Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Message\Manager $manager,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\UrlInterface $urlInterFace,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Unirgy\Dropship\Helper\Data $unergyHelperData
    )
    {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_session     =   $session;
        $this->_cibilian    =   $customer;
        $this->_urlinterface  = $urlInterFace;
        $this->_messageManager  =   $manager;
        $this->_timezone = $timezone;
        $this->_unergyHelper = $unergyHelperData;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function getCibilianInfo(){
        $cibilianID =   $this->_session->getCustomer()->getId();
        $cibilia   =   $this->_cibilian->load($cibilianID);

        return $cibilia;
    }

    /**
     * @return string
     */
    public function getMediaUrl(){

        $media_dir = $this->_urlinterface->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

        return $media_dir;
    }

    /**
     * @param $fileId
     * @return string
     */
    public function _uploadVendorImage($fileId, $vendorId = null)
    {
        try {

            $vendorDir = 'registration'.DIRECTORY_SEPARATOR.$vendorId.'/';
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowedExtensions($this->allowedExtensions);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $destinationPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($vendorDir);
            if (!$uploader->save($destinationPath)) {
                throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                );
            }
            $uploader->save($destinationPath);
            $uploadedFileName   =  $uploader->getUploadedFileName();

            $filename   =   'registration/'.$vendorId.'/'.$uploadedFileName;

        } catch (\Exception $e) { }

        return false;
    }

    /**
     * @param $category
     * @return array
     */
    public function _getAllCategoryIds($category)
    {
        $this->_categoryIds = [];
        $this->_categoryIds[] = $category->getId();

        if ($category->hasChildren()) {
            $childIds = $this->getCategoryIds($category, $this->_categoryIds);
        }
        return array_merge($this->_categoryIds, $childIds);
    }

    /**
     * @param $category
     * @param $childIds
     * @return array
     */
    public function getCategoryIds($category, $childIds)
    {
        foreach ($category->getChildrenCategories() as $category) {
            $this->_categoryIds[] = $category->getId();
            if ($category->hasChildren()) {
                $this->getCategoryIds($category, $this->_categoryIds);
            }
        }
        return array_merge($childIds);
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     * @throws \Exception
     */
    public function getVendorShipmentCollection()
    {
        $datetimeFormatInt = \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT;
        $dateFormat = $this->_timezone->getDateFormat(\IntlDateFormatter::SHORT);
        $vendorId = $this->_unergyHelper->session()->getVendorId();
        $vendor = $this->_unergyHelper->getVendor($vendorId);
        /* @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $collection */
        $collection = $this->_unergyHelper->createObj('\Magento\Sales\Model\Order\Shipment')->getCollection();
        $orderTableQted = $collection->getResource()->getConnection()->quoteIdentifier('sales_order');

        $collection->join('sales_order', "$orderTableQted.entity_id=main_table.order_id", array(
            'order_increment_id' => 'increment_id',
            'order_created_at' => 'created_at',
            'total_amount' => 'grand_total',
            'shipping_method',
        ));

        $collection->addAttributeToFilter('main_table.udropship_vendor', $vendorId);


        $r = $this->_request;
        if (($v = $r->getParam('filter_order_id_from'))) {
            $collection->addAttributeToFilter('sales_order.increment_id', array('gteq' => $v));
        }
        if (($v = $r->getParam('filter_order_id_to'))) {
            $collection->addAttributeToFilter('sales_order.increment_id', array('lteq' => $v));
        }

        if (($v = $r->getParam('filter_order_date_from'))) {
            $_filterDate = $this->_unergyHelper->dateLocaleToInternal($v, $dateFormat, true);
            $_filterDate = $this->_timezone->date($_filterDate, null, false);
            $_filterDate = datefmt_format_object($_filterDate, $datetimeFormatInt);
            $collection->addAttributeToFilter('sales_order.created_at', ['gteq' => $_filterDate]);
        }
        if (($v = $r->getParam('filter_order_date_to'))) {
            $_filterDate = $this->_unergyHelper->dateLocaleToInternal($v, $dateFormat, true);
            $_filterDate = $this->_timezone->date($_filterDate, null, false);
            $_filterDate->add(new \DateInterval('P1D'));
            $_filterDate->sub(new \DateInterval('PT1S'));
            $_filterDate = datefmt_format_object($_filterDate, $datetimeFormatInt);
            $collection->addAttributeToFilter('sales_order.created_at', ['lteq' => $_filterDate]);

        }

        if (($v = $r->getParam('filter_shipment_date_from'))) {
            $_filterDate = $this->_unergyHelper->dateLocaleToInternal($v, $dateFormat, true);
            $_filterDate = $this->_timezone->date($_filterDate, null, false);
            $_filterDate = datefmt_format_object($_filterDate, $datetimeFormatInt);
            $collection->addAttributeToFilter('main_table.created_at', ['gteq' => $_filterDate]);
        }
        if (($v = $r->getParam('filter_shipment_date_to'))) {
            $_filterDate = $this->_unergyHelper->dateLocaleToInternal($v, $dateFormat, true);
            $_filterDate = $this->_timezone->date($_filterDate, null, false);
            $_filterDate->add(new \DateInterval('P1D'));
            $_filterDate->sub(new \DateInterval('PT1S'));
            $_filterDate = datefmt_format_object($_filterDate, $datetimeFormatInt);
            $collection->addAttributeToFilter('main_table.created_at', ['lteq' => $_filterDate]);
        }

        if (!$r->getParam('apply_filter') && $vendor->getData('vendor_po_grid_status_filter')) {
            $filterStatuses = $vendor->getData('vendor_po_grid_status_filter');
            $filterStatuses = array_combine($filterStatuses, array_fill(0, count($filterStatuses), 1));
            $r->setParam('filter_status', $filterStatuses);
        }

        if (($v = $r->getParam('filter_method'))) {
            $collection->addAttributeToFilter('main_table.udropship_method', array('in' => array_keys($v)));
        }
        if (($v = $r->getParam('filter_status'))) {
            $collection->addAttributeToFilter('main_table.udropship_status', array('in' => array_keys($v)));
        }

        if (!$r->getParam('sort_by') && $vendor->getData('vendor_po_grid_sortby')) {
            $r->setParam('sort_by', $vendor->getData('vendor_po_grid_sortby'));
            $r->setParam('sort_dir', $vendor->getData('vendor_po_grid_sortdir'));
        }

        if (($v = $r->getParam('sort_by'))) {
            $map = array('order_date' => 'order_created_at', 'shipment_date' => 'created_at');
            if (isset($map[$v])) {
                $v = $map[$v];
            }
            $collection->setOrder($v, $r->getParam('sort_dir'));
        }

        return $collection;
    }
}