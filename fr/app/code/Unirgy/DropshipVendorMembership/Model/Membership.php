<?php

namespace Unirgy\DropshipVendorMembership\Model;

use Magento\Framework\Model\AbstractModel;

class Membership extends AbstractModel
{
    protected $_eventPrefix = 'udmember_membership';
    protected $_eventObject = 'membership';

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipVendorMembership\Model\ResourceModel\Membership');
    }
}