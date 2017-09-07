<?php

namespace Unirgy\DropshipVendorAskQuestion\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Unirgy\Dropship\Block\Adminhtml\Vendor\Edit\Tabs;

class UdropshipAdminhtmlVendorTabsAfter extends AbstractObserver implements ObserverInterface
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
        if (!$block instanceof Tabs
            || !$this->_request->getParam('id', 0)
        ) {
            return;
        }

        if ($block instanceof Tabs) {
            if ($this->_authorization->isAllowed('Unirgy_DropshipVendorAskQuestion::udqa_all')) {
                $block->addTab('udqa', [
                    'label'     => __('Customer Questions'),
                    'class'     => 'ajax',
                    'url'       => $block->getUrl('udqa/index/vendorQuestions', ['_current' => true]),
                    'after'     => 'products_section'
                ]);
            }
        }
    }
}
