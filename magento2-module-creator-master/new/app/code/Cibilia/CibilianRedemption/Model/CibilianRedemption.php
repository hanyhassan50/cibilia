<?php

namespace Cibilia\CibilianRedemption\Model;

/**
 * CibilianRedemption Model
 *
 * @method \Cibilia\CibilianRedemption\Model\Resource\Page _getResource()
 * @method \Cibilia\CibilianRedemption\Model\Resource\Page getResource()
 */
class CibilianRedemption extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption');
    }

}
