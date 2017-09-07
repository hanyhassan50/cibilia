<?php

/**
 * CibilianRedemption Resource Collection
 */
namespace Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianRedemption\Model\CibilianRedemption', 'Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption');
    }
}
