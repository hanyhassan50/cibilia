<?php

namespace Cibilia\Commission\Model;

/**
 * Commission Model
 *
 * @method \Cibilia\Commission\Model\Resource\Page _getResource()
 * @method \Cibilia\Commission\Model\Resource\Page getResource()
 */
class Commission extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Commission\Model\ResourceModel\Commission');
    }

}
