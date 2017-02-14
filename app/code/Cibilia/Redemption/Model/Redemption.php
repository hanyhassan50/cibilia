<?php

namespace Cibilia\Redemption\Model;

/**
 * Redemption Model
 *
 * @method \Cibilia\Redemption\Model\Resource\Page _getResource()
 * @method \Cibilia\Redemption\Model\Resource\Page getResource()
 */
class Redemption extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Redemption\Model\ResourceModel\Redemption');
    }

}
