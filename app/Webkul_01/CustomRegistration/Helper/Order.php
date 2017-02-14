<?php

namespace Webkul\CustomRegistration\Helper;

use Magento\Framework\UrlInterface;

/**
 * Custom Registration Orders helper.
 */
class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * url builder.
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    protected $_attributeCollection;

    protected $_eavEntity;

    /**
     * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Magento\Framework\App\Helper\Context              $context
     * @param Magento\Framework\ObjectManagerInterface          $objectManager
     * @param Magento\Customer\Model\Session                    $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\DateTime       $date
     * @param \Magento\Framework\Stdlib\DateTime                $dateTime
     * @param Magento\Store\Model\StoreManagerInterface         $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->_attributeCollection = $attributeCollection;
        $this->_eavEntity = $eavEntity;
        $this->_objectManager = $objectManager;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }
    /**
     * get All custom attribute collection.
     * @return collection
     */
    public function attributeCollectionFilter()
    {
        $typeId = $this->_eavEntity->setType('customer')->getTypeId();
        $custom_field = $this->_objectManager->create('Webkul\CustomRegistration\Model\ResourceModel\Customfields\Collection')->getTable('wk_customfields');
        $collection = $this->_attributeCollection->create()
                ->setEntityTypeFilter($typeId)
                ->addFilter('is_user_defined', 1)
                ->setOrder('sort_order', 'ASC');
        $collection->getSelect()
        ->join(array("ccp" => "wk_customfields"), "ccp.attribute_id = main_table.attribute_id", array("status" => "status"))->where("ccp.status = 1");
        return $collection;
    }
    /**
     * check for custom attribute should be display in order view
     * @param  int  $attrId
     * @return boolean
     */
    public function isShowInOrder($attrId)
    {
        $isShow = 0;
        $collection = $this->_objectManager->create('Webkul\CustomRegistration\Model\Customfields')
                ->getCollection()
                ->addFieldToFilter('attribute_id', ['eq'=>$attrId])
                ->addFieldToFilter('show_in_order', ['eq'=>'1']);
        if (count($collection)) {
            $isShow = 1;
        }
        return $isShow;
    }
    /**
     * check for custom attribute should be display in order email
     * @param  int  $attrId
     * @return boolean
     */
    public function isShowInEmail($attrId)
    {
        $isShow = 0;
        $collection = $this->_objectManager->create('Webkul\CustomRegistration\Model\Customfields')
                ->getCollection()
                ->addFieldToFilter('attribute_id', ['eq'=>$attrId])
                ->addFieldToFilter('show_in_email', ['eq'=>'1']);
        if (count($collection)) {
            $isShow = 1;
        }
        return $isShow;
    }
    /**
     * get current customer data.
     * @param  int $customerId
     */
    public function getCurrentCustomer($customerId)
    {
        $customerData = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        return $customerData;
    }
    /**
     * Retrieve order model.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }
}
