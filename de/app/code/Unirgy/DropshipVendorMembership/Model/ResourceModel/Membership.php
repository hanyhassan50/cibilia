<?php

namespace Unirgy\DropshipVendorMembership\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Membership extends AbstractDb
{
    protected $_eventPrefix = 'udmember_membership_resource';

    protected function _construct()
    {
        $this->_init('udmember_membership', 'membership_id');
    }

}
