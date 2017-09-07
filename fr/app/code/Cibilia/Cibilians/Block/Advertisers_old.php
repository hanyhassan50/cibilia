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
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->_registrationFactory->create()->getCollection();
			// ->addFieldToSelect(
                // '*');
            // )->addFieldToFilter(
                // 'customer_id',
                // $customerId
            // )->addFieldToFilter(
                // 'status',
                // ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            // )->setOrder(
                // 'created_at',
                // 'desc'
            // );
        }
		// echo "<pre>";
		// print_r($this->orders->getData()s);die;
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
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
