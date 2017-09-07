<?php

namespace Webkul\CustomRegistration\Block\Adminhtml\Attribute\Edit\Tab;

abstract class AbstractOptions extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Preparing layout, adding buttons
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        //$this->addChild('labels', 'Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Labels');
        $this->addChild('options', 'Webkul\CustomRegistration\Block\Adminhtml\Attribute\Edit\Options');
        return parent::_prepareLayout();
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml();
    }
}
