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
namespace Webkul\CustomRegistration\Plugin\Controller;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\Result\RedirectFactory;

class CreatePost extends \Magento\Customer\Controller\Account\CreatePost
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var RedirectFactory
     */
    protected $_redirect;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_attributeCollection;

    /**
     * Agreement constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Artera\Privacy\Model\Agreement                  $agreement
     * @param \Artera\Privacy\Model\Page                       $page
     * @param array                                            $data
     * @param \Magento\Eav\Model\Entity                        $eavEntity
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RedirectFactory $redirect
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_eavEntity = $eavEntity;
        $this->_redirect = $redirect;
        $this->_attributeCollection = $attributeCollection;
    }

    public function aroundExecute(
        $subject,
        $proceed,
        $data = "null",
        $requestInfo = false
    ) {
        $resultRedirect = $subject->resultRedirectFactory->create();

        $refererUrl = explode('?', $subject->_redirect->getRefererUrl())[0];

        $typeId = $this->_eavEntity->setType('customer')->getTypeId();

        $collection = $this->_attributeCollection->create()
            ->setEntityTypeFilter($typeId)
            ->addFilter('is_user_defined', 1)
            ->setOrder('sort_order', 'ASC');

        $error = [];

        $customData = $this->_request->getParams();

        foreach ($collection as $attribute) {
            foreach ($customData as $attributeCode => $attributeValue) {
                if ($attributeCode==$attribute->getAttributeCode()) {
                    $required = explode(' ',$attribute->getFrontendClass());

                    if (in_array('required', $required)) {
                        if (empty($attributeValue)) {
                            $error[] = $attribute->getAttributeCode();
                        }
                    }
                }
            }
        }
        if (count($error)) {
            $subject->messageManager->addError(
                __(
                    'Please Fill all the Required Fields.'
                )
            );
            $resultRedirect = $this->_redirect->create();
            $resultRedirect->setPath('customer/account/create');
            return $resultRedirect;
        }


        if ($this->getConfigData('enable_registration')) {
            $params = $this->_request->getParams();
            if (!isset($params['account_create_privacy_condition']) ||
                $params['account_create_privacy_condition'] == 0
            ) {
                $subject->messageManager->addError(__('Check Terms and Conditions & Privacy & Cookie Policy.'));
                $resultRedirect = $this->_redirect->create();
                $resultRedirect->setPath('*/*/create', ['_secure' => true]);
                return $resultRedirect;
            }
        }
        return $proceed();
    }

    /**
     * Retrieve information from carrier configuration.
     *
     * @param string $field
     *
     * @return void|false|string
     */
    public function getConfigData($field)
    {
        $path = 'customer_termandcondition/parameter/'.$field;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore()->getId()
        );
    }
}
