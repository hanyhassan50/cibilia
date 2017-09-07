<?php

namespace Unirgy\DropshipVendorMembership\Model\ResourceModel\Membership;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_eventPrefix = 'udmember_membership_collection';
    protected $_eventObject = 'membership_collection';

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipVendorMembership\Model\Membership', 'Unirgy\DropshipVendorMembership\Model\ResourceModel\Membership');
    }

    public function toOptionHash($valueField='membership_id', $labelField='membership_title')
    {
        return $this->_toOptionHash($valueField, $labelField);
    }
}