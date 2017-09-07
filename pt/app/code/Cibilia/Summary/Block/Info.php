<?php

namespace Cibilia\Summary\Block;

/**
 * Summary content block
 */
class Info extends \Magento\Framework\View\Element\Template
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
        $this->pageConfig->getTitle()->set(__('Review Your Information'));
    }
     public function getVenodorwork()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendorwork();

        return $arrCollection;
    }
    public function getVendorcat()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendorcat();

        return $arrCollection;
    }
    public function getVendortype()
    {
        $arrCollection = array();
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $arrCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->getVendortype();

        return $arrCollection;
    }

}
