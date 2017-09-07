<?php

namespace Cibilia\Cibilianpayout\Model\ResourceModel;

/**
 * Cibilianpayout Resource Model
 */
class Cibilianpayout extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_cibilianpayout', 'cibilianpayout_id');
    }
}
