<?php

namespace StageBit\Foodmaker\Controller\Dashboard;

/**
 * Class Index
 * @package Cibilia\Cibilians\Controller\Dashboard
 */
class Index extends \Unirgy\Dropship\Controller\Vendor\AbstractVendor
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $this->_renderPage(null, 'foodmaker');
    }
}