<?php
namespace Cibilia\Idproofs\Block\Adminhtml\Commission\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

class Name extends AbstractRenderer {

	public function render(DataObject $row) {
		$name = $row->getFirstname()."&nbsp;&nbsp;".$row->getLastname();
		return $name;
	}
}