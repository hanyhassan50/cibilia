<?php

namespace Cibilia\Summary\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;

class SummaryLoader implements SummaryLoaderInterface
{
    /**
     * @var \Cibilia\Summary\Model\SummaryFactory
     */
    protected $summaryFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @param \Cibilia\Summary\Model\SummaryFactory $summaryFactory
     * @param OrderViewAuthorizationInterface $orderAuthorization
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Cibilia\Summary\Model\SummaryFactory $summaryFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->summaryFactory = $summaryFactory;
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

        $summary = $this->summaryFactory->create()->load($id);
        $this->registry->register('current_summary', $summary);
        return true;
    }
}
