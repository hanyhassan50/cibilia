<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Product;

use Magento\Catalog\Block\Product\View\Description;
use Magento\Framework\App\ObjectManager;

class Tab extends Description
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'udqa.product.list.toolbar');

        $this->setChild('udqa.list',
            $this->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Product\Question\ListQuestion', 'udqa.product.list')->setTemplate('Unirgy_DropshipVendorAskQuestion::udqa/product/list.phtml')
        );
        $this->setChild('udqa.qa',
            $this->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Product\Question', 'udqa.product.question')
                ->setTemplate('Unirgy_DropshipVendorAskQuestion::udqa/product/question.phtml')
                ->setChild('captcha',
                    $this->getLayout()->createBlock('Magento\Captcha\Block\Captcha', 'captcha')->setFormId('udqa_question_form')->setImgWidth(230)->setImgHeight(50),
                        '', false, 'captcha'
                )
        );
        return $this;
    }
}
