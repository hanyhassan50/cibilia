<?php

namespace Unirgy\DropshipVendorMembership\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Unirgy\Dropship\Helper\Data as HelperData;

class Data extends AbstractHelper
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Context $context, 
        HelperData $helperData)
    {
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    public function sortBySortOrder($a, $b)
    {
        if (@$a['sort_order']<@$b['sort_order']) {
            return -1;
        } elseif (@$a['sort_order']>@$b['sort_order']) {
            return 1;
        }
        return 0;
    }
    public function getExpressActionUrl($action)
    {
        return $this->_urlBuilder->getUrl('udmember/express/'.$action);
    }
}
