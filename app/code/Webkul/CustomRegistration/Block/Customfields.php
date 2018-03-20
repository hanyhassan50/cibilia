<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Block;

use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;

class Customfields extends \Magento\Framework\View\Element\Template
{
    protected $_attributeCollection;

    protected $_eavEntity;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_session;
     /**
      * @var ObjectManagerInterface
      */
    protected $_objectManager;

    protected $_attributeFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection
     * @param \Magento\Eav\Model\Entity $eavEntity
     * @param CustomerFactory $customer
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param Session $session
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
        \Magento\Eav\Model\Entity $eavEntity,
        CustomerFactory $customer,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        Session $session,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_attributeCollection = $attributeCollection;
        $this->_eavEntity = $eavEntity;
        $this->_attributeFactory = $attributeFactory;
        $this->_customer = $customer;
        $this->_session = $session;
        $this->_objectManager = $objectManager;
    }
    /**
     * [attributeCollectionFilter description]
     * @return [type] [description]
     */
    public function attributeCollectionFilter()
    {
        $typeId = $this->_eavEntity->setType('customer')->getTypeId();
        $customField = $this->_objectManager->create(
            'Webkul\CustomRegistration\Model\ResourceModel\Customfields\Collection'
        )->getTable('wk_customfields');
        $collection = $this->_attributeCollection->create()
                ->setEntityTypeFilter($typeId)
                ->addFilter('is_user_defined', 1)
                ->setOrder('sort_order', 'ASC');
                
        $collection->getSelect()
        ->join(
            ["ccp" => $customField],
            "ccp.attribute_id = main_table.attribute_id",
            ["status" => "status"]
        )->where("ccp.status = 1");

        return $collection;
    }
    /**
     * get current customer info.
     * @return object
     */
    public function getCurrentCustomer()
    {
        $customerId = $this->_session->getCustomer()->getId();
        $customerData = $this->_customer->create()->load($customerId);
        return $customerData;
    }
    /**
     *
     * @param  int $id
     * @return array
     */
    public function getUsedInForms($id)
    {
        $attributeModel = $this->_attributeFactory->create();
        return $attributeModel->load($id)->getUsedInForms();
    }
}
