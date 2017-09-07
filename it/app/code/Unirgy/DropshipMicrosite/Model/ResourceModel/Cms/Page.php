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
 * @package    Unirgy_DropshipMicrosite
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipMicrosite\Model\ResourceModel\Cms;

use Magento\Cms\Model\ResourceModel\Page as ResourceModelPage;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipMicrosite\Helper\Data as HelperData;

class Page extends ResourceModelPage
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Context $context, 
        StoreManagerInterface $storeManager, 
        DateTime $dateTime, 
        HelperData $helperData)
    {
        $this->_helperData = $helperData;

        parent::__construct($context, $storeManager, $dateTime);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $vendor = $this->_helperData->getCurrentVendor();
        if ($vendor) {
            $select->where("udropship_vendor=? || identifier='no-route'", $vendor->getId());
        } else {
            $select->where('udropship_vendor is null');
        }
        return $select;
    }
}