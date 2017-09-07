<?php

namespace Cibilia\CibilianRedemption\Model\ResourceModel;

/**
 * CibilianRedemption Resource Model
 */
class CibilianRedemption extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_cibilianredemption', 'cibilianredemption_id');
    }
}
