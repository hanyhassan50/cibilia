<?php
namespace Cibilia\Commission\Block\Adminhtml\Commission\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Cibilian extends AbstractRenderer {

	private $_storeManager;

	/**
	 * @param \Magento\Backend\Block\Context $context 
	 * @param StoreManagerInterface $storeManager 
	 * @param array|array $data 
	 */
	public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storeManager, array $data = []) {
		$this->_storeManager = $storeManager;
		parent::__construct($context, $data);
		$this->_authorization = $context->getAuthorization();
	}

	public function render(DataObject $row) {
		$strCibilianName = "";

		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $objCibilian = $objectManager->create('Magento\Customer\Model\Customer')->load($this->_getValue($row));
        if($objCibilian && $objCibilian->getId()){
			$strCibilianName = $objCibilian->getName();        	
        }

        return $strCibilianName;
	}
}
