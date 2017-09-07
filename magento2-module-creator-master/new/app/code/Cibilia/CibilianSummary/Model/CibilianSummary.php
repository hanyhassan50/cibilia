<?php

namespace Cibilia\CibilianSummary\Model;

/**
 * CibilianSummary Model
 *
 * @method \Cibilia\CibilianSummary\Model\Resource\Page _getResource()
 * @method \Cibilia\CibilianSummary\Model\Resource\Page getResource()
 */
class CibilianSummary extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary');
    }

}
