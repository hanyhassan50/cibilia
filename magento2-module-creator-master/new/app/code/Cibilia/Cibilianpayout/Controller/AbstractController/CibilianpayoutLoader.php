<?php

namespace Cibilia\Cibilianpayout\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CibilianpayoutLoader implements CibilianpayoutLoaderInterface
{
    /**
     * @var \Cibilia\Cibilianpayout\Model\CibilianpayoutFactory
     */
    protected $cibilianpayoutFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\Cibilianpayout\Model\CibilianpayoutFactory $cibilianpayoutFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\Cibilianpayout\Model\CibilianpayoutFactory $cibilianpayoutFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->cibilianpayoutFactory = $cibilianpayoutFactory;
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

        $cibilianpayout = $this->cibilianpayoutFactory->create()->load($id);
        $this->registry->register('current_cibilianpayout', $cibilianpayout);
        return true;
    }
}
