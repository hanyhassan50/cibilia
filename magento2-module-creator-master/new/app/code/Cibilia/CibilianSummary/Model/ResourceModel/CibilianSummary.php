<?php

namespace Cibilia\CibilianSummary\Model\ResourceModel;

/**
 * CibilianSummary Resource Model
 */
class CibilianSummary extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_cibiliansummary', 'cibiliansummary_id');
    }
}
