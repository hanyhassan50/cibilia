<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipPayout
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */
 
namespace Unirgy\DropshipPayout\Model\Payout;

use Magento\Framework\Model\AbstractModel;

class Row extends AbstractModel
{
    protected $_eventPrefix = 'udpayout_payout_row';
    protected $_eventObject = 'payout';

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipPayout\Model\ResourceModel\Payout\Row');
    }
}