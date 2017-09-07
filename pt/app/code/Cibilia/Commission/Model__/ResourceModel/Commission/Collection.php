<?php

/**
 * Commission Resource Collection
 */
namespace Cibilia\Commission\Model\ResourceModel\Commission;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Commission\Model\Commission', 'Cibilia\Commission\Model\ResourceModel\Commission');
    }
}
