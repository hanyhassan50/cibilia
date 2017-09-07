<?php

namespace Unirgy\DropshipVendorAskQuestion\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CoreBlockAbstractToHtmlBefore extends AbstractObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;
    protected $_authorization;

    public function __construct(
        RequestInterface $request,
        \Magento\Framework\AuthorizationInterface $authorization
    )
    {
        $this->_request = $request;
        $this->_authorization = $authorization;
    }

    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        if (!$block instanceof \Magento\Customer\Block\Adminhtml\Edit\Tabs
            || !$this->_request->getParam('id', 0)
        ) {
            return;
        }

        if ($block instanceof Tabs) {
            if ($this->_authorization->isAllowed('Unirgy_DropshipVendorAskQuestion::udqa_all')) {
                $block->addTab('udqa', [
                    'label'     => __('Vendor Questions'),
                    'class'     => 'ajax',
                    'url'       => $block->getUrl('udqa/index/customerQuestions', ['_current' => true]),
                    'after'     => 'reviews'
                ]);
            }
        }
    }
}
