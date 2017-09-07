<?php

namespace Cibilia\Commissions\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CommissionsLoader implements CommissionsLoaderInterface
{
    /**
     * @var \Cibilia\Commissions\Model\CommissionsFactory
     */
    protected $commissionsFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\Commissions\Model\CommissionsFactory $commissionsFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\Commissions\Model\CommissionsFactory $commissionsFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->commissionsFactory = $commissionsFactory;
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

        $commissions = $this->commissionsFactory->create()->load($id);
        $this->registry->register('current_commissions', $commissions);
        return true;
    }
}
