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
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Magento\Backend\App\Action;
use Magento\Customer\Model\AttributeMetadataDataProviderFactory;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var string
     */
    protected $_entityTypeId;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var AttributeMetadataDataProvider
     */
    private $_attributeMetaData;

    protected $_attributeFactory;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        AttributeMetadataDataProviderFactory $attributeMetaData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_attributeMetaData = $attributeMetaData;
        $this->_attributeFactory = $attributeFactory;
        parent::__construct($context);
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_CustomRegistration::customregistration');
    }

    /**
     * Init actions.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_CustomRegistration::index')
            ->addBreadcrumb(__('Customregistration'), __('Customregistration'))
            ->addBreadcrumb(__('Manage Custom Fields'), __('Manage Custom Fields'));

        return $resultPage;
    }

    /**
     * Dispatch request.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $this->_entityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Customer\Model\Customer::ENTITY
        )->getTypeId();

        return parent::dispatch($request);
    }

    /**
     * Edit Custom fields page.
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');

        $modelCustomfield = $this->_objectManager->create('Webkul\CustomRegistration\Model\Customfields');
        $attributeModel = $this->_attributeFactory->create();
        $childAttributeModel = $this->_objectManager->create(
            'Magento\Customer\Model\Attribute'
        )->setEntityTypeId(
            $this->_entityTypeId
        );
        /** @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $model = $this->_objectManager->create(
            'Magento\Customer\Model\Attribute'
        )->setEntityTypeId(
            $this->_entityTypeId
        );
        if ($id) {
            $modelCustomfield->load($id);
            $attributeId = $this->_attributeMetaData
                        ->create()
                        ->getAttribute('customer', $modelCustomfield->getAttributeCode())
                        ->getAttributeId();

            $model->load($attributeId);
            $model->setIsVisible($modelCustomfield->getStatus());
            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/customfields/index');
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/customfields/index');
            }

            $requiredCheck = $model->getFrontendClass();
            $require = explode(' ', $requiredCheck);
            $model->setFrontendClass($require[0]);
            if (in_array('required', $require)) {
                $model->setIsRequired(1);
            }
            /*
             * if dependable attribute presents.
             */
            if (in_array('dependable_field_'.$model->getAttributeCode(), $require)) {
                $childAtrributeId = (int) $attributeId + 1;
                $childAttributeModel->load($childAtrributeId);
                $requiredCheck = $childAttributeModel->getFrontendClass();
                $require = explode(' ', $requiredCheck);
                if (in_array('required', $require)) {
                    $childAttributeModel->setIsRequired(1);
                }
                $childAttributeModel->setFrontendClass($require[0]);
                $model->setFrontendInput('dependable');
                $model->setDependAttributeId($childAtrributeId);
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->_coreRegistry->register('entity_attribute', $model);
        $this->_coreRegistry->register('customfields', $model);
        $this->_coreRegistry->register('dependfields', $childAttributeModel);

        $item = $id ? __('Edit Product Attribute') : __('New Customer Attribute');

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Customer Attribute'));
        return $resultPage;
    }
}
