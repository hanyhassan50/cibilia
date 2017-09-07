<?php

namespace Cibilia\CibilianCommission\Model\ResourceModel;

/**
 * CibilianCommission Resource Model
 */
class CibilianCommission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_cibiliancommission', 'cibiliancommission_id');
    }
}
