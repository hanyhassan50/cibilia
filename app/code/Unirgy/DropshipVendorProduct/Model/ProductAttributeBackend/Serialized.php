<?php

namespace Unirgy\DropshipVendorProduct\Model\ProductAttributeBackend;

class Serialized extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    protected $_hlp;

    public function __construct(
        \Unirgy\Dropship\Helper\Data $udropshipHelper
    )
    {
        $this->_hlp = $udropshipHelper;
    }
    public function beforeSave($object)
    {
        $this->_serialize($object);

        return $this;
    }

    public function afterSave($object)
    {
        parent::afterSave($object);
        $this->_unserialize($object);
        return $this;
    }

    public function afterLoad($object)
    {
        parent::afterLoad($object);
        $this->_unserialize($object);
        return $this;
    }

    protected function _serialize(\Magento\Framework\DataObject $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->hasData($attrCode)) {
            $object->setData($attrCode, $this->_hlp->serialize($object->getData($attrCode)));
        }
    }
    protected function _unserialize(\Magento\Framework\DataObject $object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($object->getData($attrCode)) {
            try {
                $unserialized = $this->_hlp->unserialize($object->getData($attrCode));
                $object->setData($attrCode, $unserialized);
            } catch (\Exception $e) {
                $object->unsetData($attrCode);
            }
        }

        return $this;
    }
}