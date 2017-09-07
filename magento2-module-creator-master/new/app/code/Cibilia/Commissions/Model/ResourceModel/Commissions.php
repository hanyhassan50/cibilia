<?php

namespace Cibilia\Commissions\Model\ResourceModel;

/**
 * Commissions Resource Model
 */
class Commissions extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_commissions', 'commissions_id');
    }
}
