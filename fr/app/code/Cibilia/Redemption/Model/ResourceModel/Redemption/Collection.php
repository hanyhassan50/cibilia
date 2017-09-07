<?php

/**
 * Redemption Resource Collection
 */
namespace Cibilia\Redemption\Model\ResourceModel\Redemption;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Redemption\Model\Redemption', 'Cibilia\Redemption\Model\ResourceModel\Redemption');
    }
}
