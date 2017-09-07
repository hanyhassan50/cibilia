<?php

namespace Cibilia\CibilianCommission\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CibilianCommissionLoader implements CibilianCommissionLoaderInterface
{
    /**
     * @var \Cibilia\CibilianCommission\Model\CibilianCommissionFactory
     */
    protected $cibiliancommissionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\CibilianCommission\Model\CibilianCommissionFactory $cibiliancommissionFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\CibilianCommission\Model\CibilianCommissionFactory $cibiliancommissionFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->cibiliancommissionFactory = $cibiliancommissionFactory;
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

        $cibiliancommission = $this->cibiliancommissionFactory->create()->load($id);
        $this->registry->register('current_cibiliancommission', $cibiliancommission);
        return true;
    }
}
