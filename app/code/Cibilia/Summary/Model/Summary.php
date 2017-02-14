<?php

namespace Cibilia\Summary\Model;

/**
 * Summary Model
 *
 * @method \Cibilia\Summary\Model\Resource\Page _getResource()
 * @method \Cibilia\Summary\Model\Resource\Page getResource()
 */
class Summary extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Summary\Model\ResourceModel\Summary');
    }

}
