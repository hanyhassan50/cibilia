<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;

class ListCustomer extends Template
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = [])
    {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->getBlock('udqa_list.toolbar')) {
            $toolbar->setCollection($this->getQuestionsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }
    protected $_questionsCollection;
    public function getQuestionsCollection()
    {
        if (is_null($this->_questionsCollection)) {
            $this->_questionsCollection = $this->_helperData->getCustomerQuestionsCollection();
        }
        return $this->_questionsCollection;
    }
    public function getNewUrl()
    {
        return $this->getUrl('udqa/customer/new');
    }
    public function getViewUrl($question)
    {
        return $this->getUrl('udqa/customer/view', ['question_id'=>$question->getId()]);
    }
    public function getProductUrl($question)
    {
        return $this->getUrl('catalog/product/view', ['id'=>$question->getProductId()]);
    }
    public function getShipmentUrl($question)
    {
        return $this->getUrl('sales/order/shipment', ['order_id'=>$question->getOrderId()]);
    }

}