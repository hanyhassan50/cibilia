<?php
namespace Cibilia\Cibilians\Block;
use Unirgy\DropshipMicrosite\Model\RegistrationFactory;
/**
* Baz block
*/
class Advertisers
    extends \Magento\Framework\View\Element\Template
{
    // public function getTitle()
    // {
        // return "Advertisers";
    // }
    protected $_template = 'advertisers.phtml';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_registrationFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        RegistrationFactory $modelRegistrationFactory,
        \Magento\Customer\Model\Session $customerSession,
        // \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->_registrationFactory = $modelRegistrationFactory;
        $this->_customerSession = $customerSession;
        // $this->_orderConfig = $orderConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders'));
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
	 public function getRecommedationUrl($vendor)
    {
        return $this->getUrl('*/*/recommend', ['vendor_id' => $vendor->getData("reg_id")]);
    }
	
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }

		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		$connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
		//$result1 = $connection->fetchAll("SELECT email_id FROM cibilian_referrals where referred_by=".$customerId);
        if (!$this->orders) {
            $this->orders = $this->_registrationFactory->create()->getCollection()
            ->addFieldToFilter('referred_by',$customerId);
        }
        return $this->orders;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            // $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
     /**
     * @return string
     */
    public function getStatus($email)
    {
        $status = 'Z';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($email,'email');
        if($objVendor && $objVendor->getId()){
            $status = $objVendor->getStatus();
        }
        return $status;
    }
    public function getStorelink($email)
    {
        $storelink = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($email,'email');
        if($objVendor && $objVendor->getId()){
            $storelink = $objVendor->getStatus();
        }
        return $storelink;
    }
    public function _prepareGridData($_vendors)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $arrGridData = array();
        $arrStatus  = $objectManager->get('\Unirgy\Dropship\Model\Source')->setPath('vendor_statuses')->toOptionHash();
        $manageStore = 0;

        $arrVendorCommission = $this->_prepareVendorCommission();
        foreach ($_vendors as $_vendor){
            $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($_vendor->getEmail(),'email');
            if($objVendor && $objVendor->getId()){

                if($objVendor->getStatus() == 'R'){
                    $arrGridData[$_vendor->getId()]['status'] = $arrStatus[$objVendor->getStatus()].'<a href="#" class="reject-tooltip" title="'.$objVendor->getRejectReason().'">&nbsp;</a>';
                }else{
                    if(!$objVendor->getIsInfoReviewed() && $objVendor->getStatus() == 'V'){
                        $arrGridData[$_vendor->getId()]['status'] = __('Sent email for approval');
                    }else{
                        $arrGridData[$_vendor->getId()]['status'] = $arrStatus[$objVendor->getStatus()];
                    }
                }
                if($objVendor->getStatus() == 'V' && $objVendor->getVendorType() == '1' && $objVendor->getIsInfoReviewed() == '1'){
                    $arrGridData[$_vendor->getId()]['link'] = '<a class="manage-vendor" href='.$this->getUrl('idproofs/index/vendorlogin',array('id' => $objVendor->getId())).' Title="'.__('Manage Vendor').'" target="_blank">'.__('Manage Vendor').'</a>';
                    if(!$manageStore){
                        $manageStore = 1;
                    }

                }else{
                    $arrGridData[$_vendor->getId()]['link'] = '';
                }
            }else{
                $arrGridData[$_vendor->getId()]['status'] = __('Pending');
                $arrGridData[$_vendor->getId()]['link'] = '';
            }
        }
        $arrGridData['manage'] = $manageStore;
        return $arrGridData;
    }
    public function _prepareVendorCommission(){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $arrCommissionCollection = $objectManager->create('Cibilia\Commission\Model\Commissions')->getCollection()->addFieldToFilter('cibilian_id',$this->_customerSession->getCustomerId());
        //$arrCommissionCollection = $this->_commissionCollectionFactory->create();
        //$arrCommissionCollection->addFieldToFilter('cibilian_id',$this->customerSession->getCustomer()->getId());

        

        
        $arrRefrredProducts = array();
        foreach ($arrCommissionCollection as $commission) {
            $arrEarned = json_decode($commission->getData('cibilia_commision_rate'),true);
            foreach ($arrEarned as $key => $rates) {
                $objProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($rates['pid']);
                //if($objProduct && $objProduct->getId() && $objProduct->getCreatedBy() == 2){
                if($objProduct && $objProduct->getId() ) {
                    $objVendor = $objectManager->create('Unirgy\Dropship\Model\Vendor')->load($objProduct->getUdropshipVendor());
                    //if($objVendor && $objVendor->getId() && $objVendor->getVendorType() == 1){
                    if($objVendor && $objVendor->getId()){
                        if($commission->getStatus() == 3){
                            if(array_key_exists($objVendor->getId(), $arrRefrredProducts)){
                                $earned = $arrRefrredProducts[$objVendor->getId()]['lock'];
                                $arrRefrredProducts[$objVendor->getId()]['lock'] = $earned + $rates['earned'];
                            }else{
                                $arrRefrredProducts[$objVendor->getId()]['lock'] = $rates['earned'];
                            }
                        }elseif($commission->getStatus() == 2){
                            if(array_key_exists($objVendor->getId(), $arrRefrredProducts)){
                                $earned = $arrRefrredProducts[$objVendor->getId()]['earn'];
                                $arrRefrredProducts[$objVendor->getId()]['earn'] = $earned + $rates['earned'];
                            }else{
                                $arrRefrredProducts[$objVendor->getId()]['earn'] = $rates['earned'];
                            }
                        }
                    }
                }
            }
                
            
        }
        return $arrRefrredProducts;

    }
    
}
