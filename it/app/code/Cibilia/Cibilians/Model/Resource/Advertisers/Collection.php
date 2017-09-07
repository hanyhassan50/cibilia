<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Cibilians\Model\Resource\Advertisers;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Cibilia\Cibilians\Model\Advertisers', 'Cibilia\Cibilians\Model\Resource\Advertisers');
        //$this->_map['fields']['page_id'] = 'main_table.page_id';
    }
 
    
}
