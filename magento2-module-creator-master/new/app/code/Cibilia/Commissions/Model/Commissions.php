<?php

namespace Cibilia\Commissions\Model;

/**
 * Commissions Model
 *
 * @method \Cibilia\Commissions\Model\Resource\Page _getResource()
 * @method \Cibilia\Commissions\Model\Resource\Page getResource()
 */
class Commissions extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Commissions\Model\ResourceModel\Commissions');
    }

}
