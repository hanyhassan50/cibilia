<?php

/**
 * Commissions Resource Collection
 */
namespace Cibilia\Commissions\Model\ResourceModel\Commissions;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Commissions\Model\Commissions', 'Cibilia\Commissions\Model\ResourceModel\Commissions');
    }
}
