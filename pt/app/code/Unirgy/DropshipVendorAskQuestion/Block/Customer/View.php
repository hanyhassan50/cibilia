<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Customer;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class View extends Template
{
    /**
     * @var Registry
     */
    protected $_frameworkRegistry;

    public function __construct(Context $context, 
        Registry $frameworkRegistry, 
        array $data = [])
    {
        $this->_frameworkRegistry = $frameworkRegistry;

        parent::__construct($context, $data);
    }

    public function getQuestion()
    {
        return $this->_frameworkRegistry->registry('udqa_question');
    }
    public function getNewUrl()
    {
        return $this->getUrl('udqa/customer/new');
    }
}
