<?php

/**
 * CibilianSummary Resource Collection
 */
namespace Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianSummary\Model\CibilianSummary', 'Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary');
    }
}
