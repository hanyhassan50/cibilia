<?php

namespace Cibilia\Cibilianpayout\Model;

/**
 * Cibilianpayout Model
 *
 * @method \Cibilia\Cibilianpayout\Model\Resource\Page _getResource()
 * @method \Cibilia\Cibilianpayout\Model\Resource\Page getResource()
 */
class Cibilianpayout extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout');
    }

}
