<?php

namespace Cibilia\Redemption\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class RedemptionLoader implements RedemptionLoaderInterface
{
    /**
     * @var \Cibilia\Redemption\Model\RedemptionFactory
     */
    protected $redemptionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\Redemption\Model\RedemptionFactory $redemptionFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\Redemption\Model\RedemptionFactory $redemptionFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->redemptionFactory = $redemptionFactory;
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

        $redemption = $this->redemptionFactory->create()->load($id);
        $this->registry->register('current_redemption', $redemption);
        return true;
    }
}
