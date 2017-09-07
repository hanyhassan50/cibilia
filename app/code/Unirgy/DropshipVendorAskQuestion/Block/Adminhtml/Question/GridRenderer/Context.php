<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\GridRenderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Text;
use Magento\Framework\DataObject;

class Context extends Text
{
    public function render(DataObject $row)
    {
        $html = '';
        if ($row->getShipmentId()) {
            $html .= sprintf('<h5>SHIPMENT:</h5> <a onclick="this.target=\'blank\'" href="%sshipment_id/%s/">#%s</a> for order <a onclick="this.target=\'blank\'" href="%sorder_id/%s/">#%s</a>', $this->getUrl('sales/shipment/view'), $row->getShipmentId(), $row->getShipmentIncrementId(), $this->getUrl('sales/order/view'), $row->getOrderId(), $row->getOrderIncrementId());
        }
        if ($row->getProductId()) {
            $html .= sprintf('<h5>PRODUCT:</h5> SKU: %s<br /><a onclick="this.target=\'blank\'" href="%sid/%s/">%s</a>', $row->getProductSku(), $this->getUrl('catalog/product/edit'), $row->getProductId(), $row->getProductName());
        }
        return $html;
    }
}