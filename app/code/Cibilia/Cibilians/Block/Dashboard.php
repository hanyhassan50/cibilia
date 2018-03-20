<?php

namespace Cibilia\Cibilians\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Dashboard
 *
 * @package Cibilia\Cibilians\Block
 */
class  Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cibilia\Summary\Block\Referred
     */
    protected $_referedProducts;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var Advertisers
     */
    protected $_advertisers;

    /**
     * @var \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory
     */
    protected $_redemptionCollection;

    /**
     * @var \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory
     */
    protected $_commissionsCollection;

    /**
     * @var \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory
     */
    protected $_summaryCollection;

    /**
     * @var \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory
     */
    protected $_vendorRegistrationCollection;

    /**
     * @var array
     */
    protected $_categoryIds = [];

    /**
     * @var \StageBit\CustomCode\Helper\Data
     */
    protected $_stagebitHelper;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Cibilia\Summary\Block\Referred $referredProducts
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param Advertisers $advertisers
     * @param \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory
     * @param \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory $commissionsCollectionFactory
     * @param \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $summaryCollectionFactory
     * @param \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory $unergyRegistrationFactory
     * @param \StageBit\CustomCode\Helper\Data $stagebitHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Cibilia\Summary\Block\Referred $referredProducts,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Cibilia\Cibilians\Block\Advertisers $advertisers,
        \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory,
        \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory $commissionsCollectionFactory,
        \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $summaryCollectionFactory,
        \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\CollectionFactory $unergyRegistrationFactory,
        \StageBit\CustomCode\Helper\Data $stagebitHelper,
        array $data = []
    )
    {
        $this->_customerSession = $session;
        $this->_referedProducts = $referredProducts;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_advertisers = $advertisers;
        $this->_redemptionCollection = $redemptionCollectionFactory;
        $this->_commissionsCollection = $commissionsCollectionFactory;
        $this->_summaryCollection = $summaryCollectionFactory;
        $this->_vendorRegistrationCollection = $unergyRegistrationFactory;
        $this->_stagebitHelper = $stagebitHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getGraph()
    {
        $customerId = $this->_customerSession->getCustomer()->getId();
        $commisionLocked = $commisionReceived = [];

        $lastMonthDay = date('Y-m-01 00:00:00', strtotime('last month'));
        $lastMonth = date('m', strtotime($lastMonthDay));
        $lastYear = date('Y', strtotime($lastMonthDay));
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $lastYear);

        for ($i = 0; $i < $totalDays; $i++) {
            $lockedAmount = $receiveAmount = 0;
            $day = $i+1;
            $fromDate = date("$lastYear-$lastMonth-$day 00:00:00");
            $toDate = date("$lastYear-$lastMonth-$day 23:59:59");

            /**
             * Get Cibilian locked amount
             */
            $commisionLocked = $this->_commissionsCollection->create()
                ->addFieldToFilter('cibilian_id', $customerId )
                ->addFieldToFilter('status', 3)
                ->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
            foreach ($commisionLocked as $locked) {
                $lockedAmount = $lockedAmount + $locked->getTotalCommission();
            }

            /**
             * Get Cibilian receive amount
             */
            $commisionReceived = $this->_redemptionCollection->create()
                ->addFieldToFilter('cibilian_id', $customerId )
                ->addFieldToFilter('status', 3)
                ->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate));
            foreach ($commisionReceived as $received) {
                $receiveAmount = $receiveAmount + $received->getAmount();
            }

            $graph['locked'][] = [
                'year' => $lastYear,
                'month' => $lastMonth,
                'day' => $day,
                'data' => $lockedAmount
            ];

            $graph['received'][] = [
                'year' => $lastYear,
                'month' => $lastMonth,
                'day' => $day,
                'data' => $receiveAmount
            ];
        }

        return $graph;
    }

    /**
     * @return array
     */
    public function getTotalLockedAndReceivedAmount(){
        $lockedAmount = 0;
        $customerId = $this->_customerSession->getCustomer()->getId();

        $commisionLocked = $this->_commissionsCollection->create()
            ->addFieldToFilter('cibilian_id', $customerId )
            ->addFieldToFilter('status', 3);
        foreach ($commisionLocked as $locked) {
            $lockedAmount = $lockedAmount + $locked->getTotalCommission();
        }

        $receivedAmount = $this->_summaryCollection->create()
            ->addFieldToSelect('paid_amount')
            ->addFieldToFilter('cibilian_id', $customerId );

        $totalAmounts = [
            'locked'    => $lockedAmount,
            'received'  => $receivedAmount->getFirstItem()->getPaidAmount()
        ];

        return $totalAmounts;
    }

    /**
     * @return mixed|null
     */
    public function getTotalCibilianReferelProduct()
    {
        $product_data = $this->_referedProducts->_getProductCollection();
        if ($product_data instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            return null;
        } else {
            return $product_data['collection'];
        }

    }

    /**
     * @param $category
     * @param null $status
     * @return int
     */
    public function getCibilianReferelProduct($category, $status = null)
    {
        $categoryIds = array_unique($this->_stagebitHelper->_getAllCategoryIds($category));
        $categoryIds = implode(',', $categoryIds);

        $collection = $this->getTotalCibilianReferelProduct();
        if (!$collection) {
            return 0;
        }
        if($status) {
            $collection->addAttributeToFilter('status', $status);
        }

        $collection->getSelect()->joinLeft(
            array("ccp" => 'catalog_category_product'),
            "e.entity_id = ccp.product_id",
            array("category_id" => "ccp.category_id")
        )->where('ccp.category_id IN (' . $categoryIds . ')');

        return $collection->getSize();
    }

    /**
     * @param bool $isActive
     * @param bool $sortBy
     * @param bool $pageSize
     * @return mixed
     */
    public function getCategoryCollection($isActive = true, $sortBy = 'entity_id', $pageSize = false)
    {
        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addfieldtofilter('level', '2');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getFoodmakers($number = false)
    {
        $customerId = $this->_customerSession->getCustomer()->getId();
        $vendorCollection = $this->_vendorRegistrationCollection->create()
            ->addFieldToFilter('referred_by',$customerId);
        if($number == true){
            return $vendorCollection->getSize();
        }
        $vendorCollection->getSelect()->order('reg_id','desc');
        $vendorCollection->getSelect()->limit(6);

        return $vendorCollection;
    }

    /**
     * @param $vendor
     * @return array
     */
    public function getVendorStatus($vendor)
    {
        return $this->_advertisers->_prepareGridData($vendor);
    }

    /**
     * @return array
     */
    public function getVendorCredit()
    {
        return $this->_advertisers->_prepareVendorCommission();
    }

}