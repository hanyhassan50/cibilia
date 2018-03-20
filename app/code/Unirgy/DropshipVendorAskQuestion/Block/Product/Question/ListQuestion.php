<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Product\Question;

use Magento\Catalog\Block\Product\View\Description;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;

class ListQuestion extends Description
{
    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Context $context, 
        Registry $registry, 
        LayoutFactory $viewLayoutFactory, 
        HelperData $helperData, 
        array $data = [])
    {
        $this->_viewLayoutFactory = $viewLayoutFactory;
        $this->_helperData = $helperData;

        parent::__construct($context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($toolbar = $this->getLayout()->getBlock('udqa.product.list.toolbar')) {
            $toolbar->setCollection($this->getQuestionsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }
    protected $_questionsCollection;
    public function getQuestionsCollection()
    {
        if (is_null($this->_questionsCollection)) {
            $this->_questionsCollection = $this->_helperData->getProductQuestionsCollection();
        }
        return $this->_questionsCollection;
    }
    public function getProductUrl($question)
    {
        return $this->getUrl('catalog/product/view', ['id'=>$question->getProductId()]);
    }
}