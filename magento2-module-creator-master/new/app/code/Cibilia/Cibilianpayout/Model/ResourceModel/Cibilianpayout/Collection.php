<?php

/**
 * Cibilianpayout Resource Collection
 */
namespace Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Cibilianpayout\Model\Cibilianpayout', 'Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout');
    }
}
