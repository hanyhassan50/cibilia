<?php
/**
 * Copyright © 2015 Cibilia. All rights reserved.
 */
namespace Cibilia\Idproofs\Model\ResourceModel;

/**
 * Idproof resource
 */
class Idproof extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('idproofs_idproof', 'id');
    }

  
}
