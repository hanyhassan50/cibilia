<?php

namespace StageBit\Foodmaker\Block;

use Magento\Framework\View\Element\Template;
use Magento\CatalogInventory\Model\Stock;

/**
 * Class Dashboard
 * @package StageBit\Foodmaker\Block
 */
class Dashboard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cibilia\Cibilians\Block\Dashboard
     */
    protected $_cibiliaDashboard;

    /**
     * @var \Unirgy\Dropship\Block\Vendor\Product\Grid
     */
    protected $_vendorProducts;

    /**
     * @var \StageBit\CustomCode\Helper\Data
     */
    protected $_stagebitHelper;

    /**
     * @var \Unirgy\Dropship\Helper\Data
     */
    protected $_unerdyHelper;

    /**
     * @var \Magento\Framework\Stdlib\ArrayUtils
     */
    protected $_arrayUtils;

    /**
     * @var \Unirgy\Dropship\Model\Session
     */
    protected $_vendorSession;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cibilia\Cibilians\Block\Dashboard $cibiliaDashboard
     * @param \Unirgy\Dropship\Block\Vendor\Product\Grid $vendorProductGrid
     * @param \StageBit\CustomCode\Helper\Data $stagebitHelper
     * @param \Unirgy\Dropship\Helper\Data $unergyHelperData
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Unirgy\Dropship\Model\Session $vendorSession
     * @param \Magento\Sales\Model\Order $orderModel
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cibilia\Cibilians\Block\Dashboard $cibiliaDashboard,
        \Unirgy\Dropship\Block\Vendor\Product\Grid $vendorProductGrid,
        \StageBit\CustomCode\Helper\Data $stagebitHelper,
        \Unirgy\Dropship\Helper\Data $unergyHelperData,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Unirgy\Dropship\Model\Session $vendorSession,
        \Magento\Sales\Model\Order $orderModel,
        array $data = []
    )
    {
        $this->_customerSession = $customerSession;
        $this->_cibiliaDashboard = $cibiliaDashboard;
        $this->_vendorProducts = $vendorProductGrid;
        $this->_stagebitHelper = $stagebitHelper;
        $this->_unerdyHelper = $unergyHelperData;
        $this->_arrayUtils = $arrayUtils;
        $this->_vendorSession = $vendorSession;
        $this->_order = $orderModel;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getFoodmakerGraph(){
        $lastMonthDay = date('Y-m-01 00:00:00', strtotime('last month'));
        $lastMonth = date('m', strtotime($lastMonthDay));
        $lastYear = date('Y', strtotime($lastMonthDay));
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $lastMonth, $lastYear);

        for ($i = 0; $i < $totalDays; $i++) {
            $amount = 0;
            $day = $i + 1;
            $fromDate = date("$lastYear-$lastMonth-$day 00:00:00");
            $toDate = date("$lastYear-$lastMonth-$day 23:59:59");

            $vendorOrderCollection  = $this->getVendorOrders();
            $vendorOrderCollection->addFieldToFilter('sales_order.created_at', array('from'=>$fromDate, 'to'=>$toDate));

            foreach ($vendorOrderCollection as $value){
                $orderId = ($value->getOrderId()) ? $value->getOrderId() : '0';
                $amount = $amount + $value->getTotalAmount();
            }

            $graphAmount[] = [
                'order' => $orderId,
                'year'  => $lastYear,
                'month' => $lastMonth,
                'day'   => $day,
                'data'  => $amount
            ];
        }

        return $graphAmount;
    }

    public function getShippingOrderMapData(){
        $vendorOrders = $this->getVendorOrders()->getData();

        if($vendorOrders) {
            foreach($vendorOrders as $order) {
                $orderId = $order['order_id'];
                $amount = $order['total_amount'];
                $orderObj = $this->_order->load($orderId);
                $shipCountry = $orderObj->getShippingAddress()->getCountryId();

                $mapData[] = [
                  'country' =>  $shipCountry,
                  'total'   =>  $amount
                ];
            }
        }

        return $mapData;
    }

    /**
     * @return int
     */
    public function getLastMonthTotalOrderAmount(){
        $amountArray = $this->getFoodmakerGraph();
        $total = 0;
        foreach($amountArray as $amount){
            $total = $total + $amount['data'];
        }
        return $total;
    }

    /**
     * @return mixed
     */
    public function getTotalVendorProducts(){
        return $this->_vendorProducts->getProductCollection();
    }

    /**
     * @param $category
     * @param null $status
     * @return int
     */
    public function getVendorProducts($category, $status = null){
        $categoryIds = array_unique($this->_stagebitHelper->_getAllCategoryIds($category));
        $categoryIds = implode(',', $categoryIds);

        $collection = $this->_getVendorProductCollection();

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
     * @return array
     */
    protected function _getVendorProductCollection(){
        $v = $this->_unerdyHelper->session()->getVendor();
        if (!$v || !$v->getId()) {
            return array();
        }

        $res = $this->_unerdyHelper->rHlp();
        $stockTable = $res->getTableName('cataloginventory_stock_item');
        $collection = $this->_unerdyHelper->createObj('\Unirgy\Dropship\Model\ResourceModel\ProductCollection')
            ->setFlag('udskip_price_index',1)
            ->setFlag('has_stock_status_filter', 1)
            ->addAttributeToFilter('type_id', array('in'=>array('simple','downloadable','virtual')))
            ->addAttributeToSelect(array('sku', 'name', 'visibility'/*, 'cost'*/))
        ;
        $conn = $collection->getConnection();
        $collection->addAttributeToFilter('entity_id', array('in'=>$v->getAssociatedProductIds()));
        $collection->getSelect()->join(
            array('cisi' => $stockTable),
            $conn->quoteInto('cisi.product_id=e.entity_id AND cisi.stock_id=?', Stock::DEFAULT_STOCK_ID),
            array('_stock_status'=>$this->_getStockField('status'), '_is_stock_qty'=>$this->_getStockField('is_qty'))
        );
        if ($this->_unerdyHelper->isUdmultiAvailable()) {
            $collection->getSelect()->join(
                array('uvp' => $res->getTableName('udropship_vendor_product')),
                $conn->quoteInto('uvp.product_id=e.entity_id AND uvp.vendor_id=?', $v->getId()),
                array('_stock_qty'=>$this->_getStockField('qty'), 'vendor_sku'=>'uvp.vendor_sku', 'vendor_cost'=>'uvp.vendor_cost')
            );
            //$collection->getSelect()->columns(array('_stock_qty'=>'IFNULL(uvp.stock_qty,cisi.qty'));
        } else {
            if (($vsAttrCode = $this->_scopeConfig->getValue('udropship/vendor/vendor_sku_attribute')) && $this->_unerdyHelper->checkProductAttribute($vsAttrCode)) {
                $collection->addAttributeToSelect(array($vsAttrCode));
            }
            $collection->getSelect()->columns(array('_stock_qty'=>$this->_getStockField('qty')));
        }

        $this->_applyRequestFilters($collection);

        return $collection;
    }

    /**
     * @param $collection
     * @return $this
     */
    protected function _applyRequestFilters($collection){
        $r = $this->_request;
        $param = $r->getParam('filter_sku');
        if (!is_null($param) && $param!=='') {
            $collection->addAttributeToFilter('sku', array('like'=>'%'.$param.'%'));
        }
        $param = $r->getParam('filter_vendor_sku');
        if (!is_null($param) && $param!=='') {
            $vsAttrCode = $this->_scopeConfig->getValue('udropship/vendor/vendor_sku_attribute');
            if ($this->_unerdyHelper->isUdmultiAvailable()) {
                $collection->getSelect()->where('uvp.vendor_sku like ?', $param.'%');
            } elseif ($vsAttrCode && $this->_unerdyHelper->checkProductAttribute($vsAttrCode)) {
                $collection->addAttributeToFilter($vsAttrCode, array('like'=>$param.'%'));
            }
        }
        $param = $r->getParam('filter_name');
        if (!is_null($param) && $param!=='') {
            $collection->addAttributeToFilter('name', array('like'=>$param.'%'));
        }
        $param = $r->getParam('filter_stock_status');
        if (!is_null($param) && $param!=='') {
            $collection->getSelect()->where($this->_getStockField('status').'=?', $param);
        }
        $param = $r->getParam('filter_stock_qty_from');
        if (!is_null($param) && $param!=='') {
            $collection->getSelect()->where($this->_getStockField('qty').'>=?', $param);
        }
        $param = $r->getParam('filter_stock_qty_to');
        if (!is_null($param) && $param!=='') {
            $collection->getSelect()->where($this->_getStockField('qty').'<=?', $param);
        }
        return $this;
    }

    /**
     * @param $type
     * @return string|\Zend_Db_Expr
     */
    protected function _getStockField($type){
        $v = $this->_unerdyHelper->session()->getVendor();
        if (!$v || !$v->getId()) {
            $isLocalVendor = 0;
        } else {
            $isLocalVendor = intval($v->getId()==$this->_scopeConfig->getValue('udropship/vendor/local_vendor'));
        }
        if ($this->_unerdyHelper->isUdmultiAvailable()) {
            switch ($type) {
                case 'is_qty':
                    return new \Zend_Db_Expr('1');
                case 'qty':
                    return new \Zend_Db_Expr('IF(uvp.vendor_product_id is null, cisi.qty, uvp.stock_qty)');
                case 'status':
                    return new \Zend_Db_Expr("IF(uvp.vendor_product_id is null, cisi.is_in_stock, null)");
            }
        } else {
            $isManageStock = $this->_unerdyHelper->getScopeFlag(\Magento\CatalogInventory\Model\Configuration::XML_PATH_MANAGE_STOCK);
            switch ($type) {
                case 'is_qty':
                    return new \Zend_Db_Expr(sprintf('IF (cisi.use_config_manage_stock && 0=%d || !cisi.use_config_manage_stock && 0=cisi.manage_stock, null, 1)', $isManageStock));
                case 'qty':
                    return 'cisi.qty';
                case 'status':
                    return new \Zend_Db_Expr(sprintf('IF (cisi.use_config_manage_stock && 0=%d || !cisi.use_config_manage_stock && 0=cisi.manage_stock, null, cisi.is_in_stock)', $isManageStock));
            }
        }
    }

    /**
     * @return mixed
     */
    public function getCategoryCollection()
    {
        return $this->_cibiliaDashboard->getCategoryCollection();
    }

    /**
     * @return array
     */
    public function getVendorOrders($limit = null){
        $collection = $this->_stagebitHelper->getVendorShipmentCollection();

        if($limit) {
            $collection->getSelect()->order('entity_id', 'desc');
            $collection->getSelect()->limit($limit);
        }

        return $collection;
    }

    /**
     * @param $method
     * @return \Magento\Framework\Phrase|string|void
     */
    public function getVendorShipmentMethod($method)
    {
        $vendor = $this->_vendorSession->getVendor();
        return $vendor->getShippingMethodName($method);
    }

    /**
     * @param $method
     * @return string
     */
    public function getVendorShippingStatus($method)
    {
        return $this->_unerdyHelper->getShipmentStatusName($method);
    }
}