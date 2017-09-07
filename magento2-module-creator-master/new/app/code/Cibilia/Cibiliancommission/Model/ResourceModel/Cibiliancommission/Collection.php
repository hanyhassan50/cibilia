<?php

/**
 * CibilianCommission Resource Collection
 */
namespace Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianCommission\Model\CibilianCommission', 'Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission');
    }
}
