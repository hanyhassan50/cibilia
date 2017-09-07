<?php

namespace Cibilia\CibilianSummary\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class CibilianSummaryLoader implements CibilianSummaryLoaderInterface
{
    /**
     * @var \Cibilia\CibilianSummary\Model\CibilianSummaryFactory
     */
    protected $cibiliansummaryFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\CibilianSummary\Model\CibilianSummaryFactory $cibiliansummaryFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\CibilianSummary\Model\CibilianSummaryFactory $cibiliansummaryFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->cibiliansummaryFactory = $cibiliansummaryFactory;
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

        $cibiliansummary = $this->cibiliansummaryFactory->create()->load($id);
        $this->registry->register('current_cibiliansummary', $cibiliansummary);
        return true;
    }
}
