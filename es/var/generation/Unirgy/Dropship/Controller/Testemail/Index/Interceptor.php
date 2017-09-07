<?php
namespace Unirgy\Dropship\Controller\Testemail\Index;

/**
 * Interceptor class for @see \Unirgy\Dropship\Controller\Testemail\Index
 */
class Interceptor extends \Unirgy\Dropship\Controller\Testemail\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Catalog\Model\ProductFactory $product, \Unirgy\DropshipVendorProduct\Helper\Data $vendorProductHelper, \Unirgy\Dropship\Helper\Data $udropshipHelper, \Unirgy\Dropship\Helper\Error $errorHelper, \Unirgy\Dropship\Model\Vendor\NotifyLowstock $vendorNotifylowstock)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $product, $vendorProductHelper, $udropshipHelper, $errorHelper, $vendorNotifylowstock);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepareForNotification($prods)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepareForNotification');
        if (!$pluginInfo) {
            return parent::prepareForNotification($prods);
        } else {
            return $this->___callPlugins('prepareForNotification', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        if (!$pluginInfo) {
            return parent::getActionFlag();
        } else {
            return $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        if (!$pluginInfo) {
            return parent::getResponse();
        } else {
            return $this->___callPlugins('getResponse', func_get_args(), $pluginInfo);
        }
    }
}
