<?php

namespace Cibilia\Redemption\Model\ResourceModel;

/**
 * Redemption Resource Model
 */
class Redemption extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_redemption', 'redemption_id');
    }
}
