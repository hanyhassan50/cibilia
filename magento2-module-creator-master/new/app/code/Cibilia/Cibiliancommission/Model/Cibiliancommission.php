<?php

namespace Cibilia\CibilianCommission\Model;

/**
 * CibilianCommission Model
 *
 * @method \Cibilia\CibilianCommission\Model\Resource\Page _getResource()
 * @method \Cibilia\CibilianCommission\Model\Resource\Page getResource()
 */
class CibilianCommission extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission');
    }

}
