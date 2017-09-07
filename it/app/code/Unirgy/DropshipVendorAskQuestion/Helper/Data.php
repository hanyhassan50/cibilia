<?php

namespace Unirgy\DropshipVendorAskQuestion\Helper;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\DropshipVendorAskQuestion\Model\Source;

class Data extends AbstractHelper
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HelperData
     */
    protected $_helperData;

    protected $_hlp;

    public function __construct(
        Context $context, 
        QuestionFactory $modelQuestionFactory, 
        Registry $frameworkRegistry, 
        Config $modelConfig, 
        StoreManagerInterface $modelStoreManagerInterface, 
        \Unirgy\Dropship\Helper\Data $udropshipHelper
    )
    {
        $this->_questionFactory = $modelQuestionFactory;
        $this->_coreRegistry = $frameworkRegistry;
        $this->_eavConfig = $modelConfig;
        $this->_storeManager = $modelStoreManagerInterface;
        $this->_hlp = $udropshipHelper;

        parent::__construct($context);
    }

    public function isEmptyDate($date)
    {
        return empty($date) || $date=='0000-00-00' || $date=='0000-00-00 00:00:00';
    }
    public function saveFormData($data=null, $id=null)
    {
        $formData = ObjectManager::getInstance()->get('Unirgy\DropshipVendorAskQuestion\Model\Session')->getFormData();
        if (!is_array($formData)) {
            $formData = [];
        }
        $data = !is_null($data) ? $data : $this->_request->getPost();
        $id = !is_null($id) ? $id : $this->_request->getParam('question_id');
        $formData[$id] = $data;
        ObjectManager::getInstance()->get('Unirgy\DropshipVendorAskQuestion\Model\Session')->setFormData($formData);
    }

    public function fetchFormData($id=null)
    {
        $formData = ObjectManager::getInstance()->get('Unirgy\DropshipVendorAskQuestion\Model\Session')->getFormData();
        if (!is_array($formData)) {
            $formData = [];
        }
        $id = !is_null($id) ? $id : $this->_request->getParam('question_id');
        $result = false;
        if (isset($formData[$id]) && is_array($formData[$id])) {
            $result = $formData[$id];
            unset($formData[$id]);
            if (empty($formData)) {
                ObjectManager::getInstance()->get('Unirgy\DropshipVendorAskQuestion\Model\Session')->getFormData(true);
            } else {
                ObjectManager::getInstance()->get('Unirgy\DropshipVendorAskQuestion\Model\Session')->setFormData($formData);
            }
        }
        return $result;
    }
    public function getCustomerQuestionsCollection()
    {
        return $this->_questionFactory->create()->getCollection()
            ->joinShipments()
            ->joinProducts()
            ->addCustomerFilter(ObjectManager::getInstance()->get('Magento\Customer\Model\Session')->getCustomerId())
            ->setDateOrder();
    }
    public function getProductQuestionsCollection()
    {
        $questions = $this->_questionFactory->create()->getCollection();
        if (!$this->_coreRegistry->registry('current_product')) {
            $questions->setEmptyFilter();
        } else {
            $questions
                ->joinProducts()
                ->addPublicProductFilter($this->_coreRegistry->registry('current_product')->getId())
                ->setDateOrder();
        }
        return $questions;
    }

    public function addProductAttributeToSelect($select, $attrCode, $entity_id)
    {
        $alias = $attrCode;
        if (is_array($attrCode)) {
            reset($attrCode);
            $alias = key($attrCode);
            $attrCode = current($attrCode);
        }
        $attribute = $this->_eavConfig->getAttribute(Product::ENTITY, $attrCode);
        if (!$attribute || !$attribute->getAttributeId()) {
            $select->columns([$alias=>new \Zend_Db_Expr("''")]);
            return $this;
        }
        $attributeId    = $attribute->getAttributeId();
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $select->getAdapter();
        $store = $this->_storeManager->getStore()->getId();

        $rowIdField = $this->_hlp->rowIdField();

        if ($attribute->isScopeGlobal()) {
            $_alias = 'ta_' . $attrCode;
            $select->joinLeft(
                [$_alias => $attributeTable],
                "{$_alias}.{$rowIdField} = {$entity_id} AND {$_alias}.attribute_id = {$attributeId}"
                    . " AND {$_alias}.store_id = 0",
                []
            );
            $expression = new \Zend_Db_Expr("{$_alias}.value");
        } else {
            $dAlias = 'tad_' . $attrCode;
            $sAlias = 'tas_' . $attrCode;

            $select->joinLeft(
                [$dAlias => $attributeTable],
                "{$dAlias}.{$rowIdField} = {$entity_id} AND {$dAlias}.attribute_id = {$attributeId}"
                    . " AND {$dAlias}.store_id = 0",
                []
            );
            $select->joinLeft(
                [$sAlias => $attributeTable],
                "{$sAlias}.{$rowIdField} = {$entity_id} AND {$sAlias}.attribute_id = {$attributeId}"
                    . " AND {$sAlias}.store_id = {$store}",
                []
            );
            $expression = $this->getCheckSql($this->getIfNullSql("{$sAlias}.value_id", -1) . ' > 0',
                "{$sAlias}.value", "{$dAlias}.value");
        }

        $select->columns([$alias=>$expression]);

        return $this;
    }

    public function getCaseSql($valueName, $casesResults, $defaultValue = null)
    {
        $expression = 'CASE ' . $valueName;
        foreach ($casesResults as $case => $result) {
            $expression .= ' WHEN ' . $case . ' THEN ' . $result;
        }
        if ($defaultValue !== null) {
            $expression .= ' ELSE ' . $defaultValue;
        }
        $expression .= ' END';

        return new \Zend_Db_Expr($expression);
    }

    public function getCheckSql($expression, $true, $false)
    {
        if ($expression instanceof \Zend_Db_Expr || $expression instanceof Select) {
            $expression = sprintf("IF((%s), %s, %s)", $expression, $true, $false);
        } else {
            $expression = sprintf("IF(%s, %s, %s)", $expression, $true, $false);
        }

        return new \Zend_Db_Expr($expression);
    }

    public function getIfNullSql($expression, $value = 0)
    {
        if ($expression instanceof \Zend_Db_Expr || $expression instanceof Select) {
            $expression = sprintf("IFNULL((%s), %s)", $expression, $value);
        } else {
            $expression = sprintf("IFNULL(%s, %s)", $expression, $value);
        }

        return new \Zend_Db_Expr($expression);
    }

    public function getStore($question)
    {
        return $this->_storeManager->getDefaultStoreView();
    }
    public function isNotifyAdminVendor($question)
    {
        $store = $this->getStore($question);
        return !$question->getIsAdminQuestionNotified()
            && $this->scopeConfig->isSetFlag('udqa/general/send_admin_notifications', ScopeInterface::SCOPE_STORE, $store);
    }
    public function notifyAdminVendor($question)
    {
        $this->helperProtected()->notifyAdminVendor($question);
        return $this;
    }

    /**
     * @return \Unirgy\DropshipVendorAskQuestion\Helper\ProtectedCode
     */
    public function helperProtected()
    {
        return $this->_hlp->getObj('\Unirgy\DropshipVendorAskQuestion\Helper\ProtectedCode');
    }

    public function isNotifyAdminCustomer($question)
    {
        $store = $this->getStore($question);
        return !$question->getIsAdminAnswerNotified()
            && $question->getAnswerText()
            && $this->scopeConfig->isSetFlag('udqa/general/send_admin_notifications', ScopeInterface::SCOPE_STORE, $store);
    }
    public function notifyAdminCustomer($question)
    {
        $this->helperProtected()->notifyAdminCustomer($question);
        return $this;
    }

    public function isNotifyCustomer($question)
    {
        $store = $this->getStore($question);
        return !$question->getIsCustomerNotified()
            && $question->getCustomerEmail()
            && $question->canCustomerViewAnswer()
            && $this->scopeConfig->isSetFlag('udqa/general/send_customer_notifications', ScopeInterface::SCOPE_STORE, $store)
            || $question->getForcedCustomerNotificationFlag()
                && $question->getCustomerEmail();
    }
    public function notifyCustomer($question)
    {
        $this->helperProtected()->notifyCustomer($question);
        return $this;
    }

    public function isNotifyVendor($question)
    {
        $store = $this->getStore($question);
        return !$question->getIsVendorNotified()
            && $question->getVendorEmail()
            && $question->getQuestionStatus()==Source::UDQA_STATUS_APPROVED
            && $this->scopeConfig->isSetFlag('udqa/general/send_vendor_notifications', ScopeInterface::SCOPE_STORE, $store)
            || $question->getForcedVendorNotificationFlag()
                && $question->getVendorEmail();
    }
    public function notifyVendor($question)
    {
        $this->helperProtected()->notifyVendor($question);
        return $this;
    }

}