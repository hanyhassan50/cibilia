<?php

namespace Cibilia\Summary\Model\ResourceModel;

/**
 * Summary Resource Model
 */
class Summary extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cibilia_summary', 'summary_id');
    }
}
