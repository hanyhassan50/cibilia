<?php

namespace Webkul\CustomRegistration\Controller\Editadditionalinfo;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_attributeCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_eavEntity;
    /**
     * @var CustomerRepositoryInterface
    */
    protected $_customerRepository;
    /**
     * @var CustomerInterfaceFactory
     */
    protected $_customerDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;
     /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $_customerMapper;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_attributeCollection = $attributeCollection;
        $this->customerSession = $customerSession;
        $this->_customerRepository = $customerRepository;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_customerMapper = $customerMapper;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_eavEntity = $eavEntity;
        parent::__construct($context);
    }

    /**
     * Rma List.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $paramData = $this->getRequest();
        $customerId = $this->customerSession->getCustomerId();
        $typeId = $this->_eavEntity->setType('customer')->getTypeId();
        $collection = $this->_attributeCollection->create()
            ->setEntityTypeFilter($typeId)
            ->addVisibleFilter()
            ->addFilter('is_user_defined', 1)
            ->setOrder('sort_order', 'ASC');
        $customData = $paramData->getPostValue();
        $savedCustomerData = $this->_customerRepository->getById($customerId);
        $saveData = $this->_customerMapper->toFlatArray($savedCustomerData);
        
        $customer = $this->_customerDataFactory->create();
        $customData = array_merge(
            $this->_customerMapper->toFlatArray($savedCustomerData),
            $customData
        );
        $customData['id'] = $customerId;
		// echo "<pre>";print_r($customData);die;
        $this->_dataObjectHelper->populateWithArray(
            $customer,
            $customData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        $this->_customerRepository->save($customer);

        return $resultRedirect->setPath('*/customfields/');
    }
}
