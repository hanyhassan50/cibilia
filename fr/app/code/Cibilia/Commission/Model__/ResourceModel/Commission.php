<?php

namespace Cibilia\Commission\Model\ResourceModel;

/**
 * Commission Resource Model
 */
class Commission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_commission', 'commission_id');
    }
}
