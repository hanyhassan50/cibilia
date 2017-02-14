<?php
namespace Cibilia\Cibilians\Block;
use Unirgy\DropshipMicrosite\Model\RegistrationFactory;
/**
* Baz block
*/
class Refrerform
    extends \Magento\Framework\View\Element\Template
{
    // public function getTitle()
    // {
        // return "Advertisers";
    // }
    protected $_template = 'referral_form.phtml';

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
        $this->pageConfig->getTitle()->set(__('Advertiser Information'));
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
		$result1 = $connection->fetchAll("SELECT email_id FROM cibilian_referrals where referred_by=".$customerId);

        if (!$this->orders) {
            $this->orders = $this->_registrationFactory->create()->getCollection()
            ->addFieldToFilter(
                'email',
                ['in' => $result1]);
        }
        return $this->orders;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
       /* if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            // $this->getOrders()->load();
        }*/
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
    public function getVenodorwork()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendorwork();

        return $arrCollection;
    }
    public function getVendorcat()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendorcat();

        return $arrCollection;
    }
    

    
}
