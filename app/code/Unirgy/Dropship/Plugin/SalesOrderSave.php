<?php
namespace Unirgy\Dropship\Plugin;

use \Unirgy\Dropship\Helper\ProtectedCode;

class SalesOrderSave
{
    /**
     * @var ProtectedCode
     */
    protected $_hlpPr;

    public function __construct(
        ProtectedCode $helperProtectedCode
    )
    {
        $this->_hlpPr = $helperProtectedCode;
    }
    public function afterSave(\Magento\Sales\Model\Order $order, $result)
    {
        if (!$order->getNoDropshipFlag()) {
            $this->_hlpPr->sales_order_save_after($order);
        }
        return $result;
    }
}
