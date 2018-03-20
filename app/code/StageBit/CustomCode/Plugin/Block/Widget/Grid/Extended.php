<?php

namespace StageBit\CustomCode\Plugin\Block\Widget\Grid;

/**
 * Class Extended
 *
 * @package StageBit\CustomCode\Plugin\Block\Widget\Grid
 */
class Extended {

    /**
     * @var \Unirgy\Dropship\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $_vendorColleaction;

    /**
     * Extended constructor.
     *
     * @param \Unirgy\Dropship\Model\ResourceModel\Vendor\CollectionFactory $vendorColleaction
     */
    public function __construct(
        \Unirgy\Dropship\Model\ResourceModel\Vendor\CollectionFactory $vendorColleaction
    )
    {
        $this->_vendorColleaction = $vendorColleaction;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Extended $subject
     * @param $collection
     * @return array
     */
    public function beforeSetCollection(
        \Magento\Backend\Block\Widget\Grid\Extended $subject,
        $collection
    ){
        if($collection instanceof \Unirgy\DropshipMicrosite\Model\ResourceModel\Registration\Collection) {
            $vendorCollection   =  $this->_vendorColleaction->create()
                ->addFieldToSelect('email');

            $collection->addFieldToFilter('main_table.email', ['nin' => $vendorCollection->getSelect()]);
        }

        return [$collection];
    }
}