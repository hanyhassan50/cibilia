<?php

/**
 * Summary Resource Collection
 */
namespace Cibilia\Summary\Model\ResourceModel\Summary;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Summary\Model\Summary', 'Cibilia\Summary\Model\ResourceModel\Summary');
    }
}
