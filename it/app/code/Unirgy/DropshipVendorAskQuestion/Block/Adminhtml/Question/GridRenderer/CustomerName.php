<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\GridRenderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\Text;
use Magento\Framework\DataObject;

class CustomerName extends Text
{
    public function render(DataObject $row)
    {
        $oldFormat = $this->getColumn()->getFormat();
        if (!$row->getCustomerId()) {
            $this->getColumn()->setFormat(null);
        }
        $html = parent::render($row);
        $this->getColumn()->setFormat($oldFormat);
        return $html;
    }
}