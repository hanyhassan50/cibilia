<?php
namespace Cibilia\Commission\Block\Adminhtml\Commission\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Locked extends AbstractRenderer {

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
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$lockedCommission = 0.00;
		$arrLockedCommission = $objectManager->create('Cibilia\Commission\Model\Commissions')->getCollection()->addFieldToFilter('cibilian_id',$this->_getValue($row))->addFieldToFilter('status',3);
        /*echo "<pre>";
        print_r($arrLockedCommission->getData());
		echo "</pre>";*/
        if(count($arrLockedCommission) > 0){
        	foreach ($arrLockedCommission as $locked) {
        		$lockedCommission = $lockedCommission + $locked->getTotalCommission();
        	}	
        }

        return $objectManager->create('Magento\Framework\Pricing\Helper\Data')->currency($lockedCommission,true,false);
	}
}
