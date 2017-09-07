<?php

/**
 * Webkul CustomRegistration Options Block
 *
 * @category    Webkul
 * @package     Webkul_CustomRegistration
 * @author      Webkul Software Private Limited
 *
 */
namespace Webkul\CustomRegistration\Block\Adminhtml\Attribute\Edit;

use Magento\Store\Model\ResourceModel\Store\Collection;

class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    /**
     * @var string
     */
    protected $_template = 'Webkul_CustomRegistration::customfields/options.phtml';

    /**
     * Retrieve attribute object from registry
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @codeCoverageIgnore
     */
    protected function getAttributeObject()
    {
        $parentAttribute = $this->_registry->registry('entity_attribute');
        if ($parentAttribute->getFrontendInput() == 'dependable') {
            return $this->_registry->registry('dependfields');
        } else {
            return $this->_registry->registry('entity_attribute');
        }
    }
}
