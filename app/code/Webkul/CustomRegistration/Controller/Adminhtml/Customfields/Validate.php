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

class Validate extends \Magento\Catalog\Controller\Adminhtml\Product\Attribute
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;
     /**
      * @var string
      */
    protected $_customerEntityTypeId;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Cache\FrontendInterface $attributeLabelCache
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Cache\FrontendInterface $attributeLabelCache,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context, $attributeLabelCache, $coreRegistry, $resultPageFactory);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
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
        $this->_customerEntityTypeId = $this->_objectManager->create(
            'Magento\Eav\Model\Entity'
        )->setType(
            \Magento\Customer\Model\Customer::ENTITY
        )->getTypeId();

        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        $attributeCode = $this->getRequest()->getParam('attribute_code');
        $frontendLabel = $this->getRequest()->getParam('frontend_label');
        
        $attributeId = $this->getRequest()->getParam('attribute_id');

        $attribute = $this->_objectManager->create(
            'Magento\Customer\Model\Attribute'
        )->loadByCode(
            $this->_customerEntityTypeId,
            $attributeCode
        );

        if ($attribute->getId() && !$attributeId) {
            if (strlen($this->getRequest()->getParam('attribute_code'))) {
                $response->setMessage(
                    __('An attribute with this code already exists.')
                );
            } else {
                $response->setMessage(
                    __('An attribute with the same code (%1) already exists.', $attributeCode)
                );
            }
            $response->setError(true);
        }
        if ($this->getRequest()->has('dependable_attribute_code')) {
            $attributeCode = $this->getRequest()->getParam('dependable_attribute_code');
            $attribute = $this->_objectManager->create(
                'Magento\Customer\Model\Attribute'
            )->loadByCode(
                $this->_customerEntityTypeId,
                $attributeCode
            );

            if ($attribute->getId() && !$attributeId) {
                if (strlen($this->getRequest()->getParam('dependable_attribute_code'))) {
                    $response->setMessage(
                        __('An dependable attribute with this code already exists.')
                    );
                } else {
                    $response->setMessage(
                        __('An dependable attribute with the same code (%1) already exists.', $attributeCode)
                    );
                }
                $response->setError(true);
            }
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_CustomRegistration::index');
    }
}
