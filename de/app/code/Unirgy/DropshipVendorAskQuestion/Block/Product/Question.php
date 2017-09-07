<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Product;

use Magento\Catalog\Block\Product\View\Description;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Unirgy\Dropship\Helper\Data as HelperData;
use Unirgy\Dropship\Model\Source;

class Question extends Description
{
    /**
     * @var Source
     */
    protected $_modelSource;

    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Context $context, 
        Registry $registry, 
        Source $modelSource, 
        HelperData $helperData, 
        array $data = [])
    {
        $this->_modelSource = $modelSource;
        $this->_helperData = $helperData;

        parent::__construct($context, $registry, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        ObjectManager::getInstance()->get('Magento\Customer\Model\Session')->setData('udqa_question_form_show_captcha',1);
        return $this;
    }
    public function getFormAction()
    {
        return $this->getUrl('udqa/customer/post');
    }
    public function getVendors()
    {
        $product = $this->getProduct();
        $simpleProducts = [];
        if ($product->getTypeId()=='configurable') {
            $simpleProducts = $product->getTypeInstance(true)->getUsedProducts($product);
        }
        array_unshift($simpleProducts, $product);
        $vendors = $this->_modelSource->getVendors(true);
        $vIds = [];
        $isUdm = $this->_helperData->isUdmultiActive();
        foreach ($simpleProducts as $p) {
            if ($isUdm) {
                $_vIds = $p->getMultiVendorData();
                $_vIds = is_array($_vIds) ? array_keys($_vIds) : [];
                $vIds = array_merge($vIds, $_vIds);
            } else {
                $vIds[] = $p->getUdropshipVendor();
            }
        }
        $vIds = array_filter($vIds);
        return array_intersect_key($vendors, array_flip($vIds));
    }
    public function addToParentGroup($groupName)
    {
        if ($this->getParentBlock()) {
            $this->getParentBlock()->addToChildGroup($groupName, $this);
        }
        return $this;
    }
}