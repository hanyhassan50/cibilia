<?php

namespace Cibilia\Commission\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CommissionLoader implements CommissionLoaderInterface
{
    /**
     * @var \Cibilia\Commission\Model\CommissionFactory
     */
    protected $commissionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\Commission\Model\CommissionFactory $commissionFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\Commission\Model\CommissionFactory $commissionFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->commissionFactory = $commissionFactory;
        $this->registry = $registry;
        $this->url = $url;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return bool
     */
    public function load(RequestInterface $request, ResponseInterface $response)
    {
        $id = (int)$request->getParam('id');
        if (!$id) {
            $request->initForward();
            $request->setActionName('noroute');
            $request->setDispatched(false);
            return false;
        }

        $commission = $this->commissionFactory->create()->load($id);
        $this->registry->register('current_commission', $commission);
        return true;
    }
}
