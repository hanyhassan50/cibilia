<?php
namespace Cibilia\Idproofs\Block;
/**
* Baz block
*/
class Vendortype extends \Magento\Framework\View\Element\Template
{
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Select Your Vendor Type'));
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }
    public function getVendortype()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendortype();

        return $arrCollection;
    }

    
}
