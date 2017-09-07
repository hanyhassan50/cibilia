<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Vendor;

use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\App;
use Magento\Framework\Model\Locale;
use Magento\Framework\Model\Resource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\Dropship\Helper\Data as HelperData;

class Questions extends Template
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var HelperData
     */
    protected $_hlp;

    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var Url
     */
    protected $_backendUrl;

    public function __construct(Context $context, 
        Registry $frameworkRegistry, 
        HelperData $helperData,
        QuestionFactory $modelQuestionFactory,
        Url $modelUrl, 
        array $data = [])
    {
        $this->_coreRegistry = $frameworkRegistry;
        $this->_hlp = $helperData;
        $this->_questionFactory = $modelQuestionFactory;
        $this->_backendUrl = $modelUrl;

        parent::__construct($context, $data);
    }

    protected $_collection;
    protected $_oldStoreId;
    protected $_unregUrlStore;

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        if (!$this->_coreRegistry->registry('url_store')) {
            $this->_unregUrlStore = true;
            $this->_coreRegistry->register('url_store', $this->_storeManager->getStore());
        }
        $this->_oldStoreId = $this->_storeManager->getStore()->getId();
        $this->_storeManager->setCurrentStore(0);

        if ($toolbar = $this->getLayout()->getBlock('udqa.grid.toolbar')) {
            $toolbar->setCollection($this->getQuestionsCollection());
        }

        return $this;
    }

    protected function _getUrlModelClass()
    {
        return 'core/url';
    }
    public function getUrl($route = '', $params = [])
    {
        if (!isset($params['_store']) && $this->_oldStoreId) {
            $params['_store'] = $this->_oldStoreId;
        }
        return parent::getUrl($route, $params);
    }

    protected function _afterToHtml($html)
    {
        if ($this->_unregUrlStore) {
            $this->_unregUrlStore = false;
            $this->_coreRegistry->unregister('url_store');
        }
        $this->_storeManager->setCurrentStore($this->_oldStoreId);
        return parent::_afterToHtml($html);
    }

    protected function _applyRequestFilters($collection)
    {
        /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate */
        $localeDate = $this->_hlp->getObj('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $datetimeFormatInt = \Magento\Framework\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT;
        $dateFormat = $localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $r = $this->_request;
        $param = $r->getParam('filter_product_sku');
        if (!is_null($param) && $param!=='') {
            $collection->addFieldToFilter('product_sku', ['like'=>$param.'%']);
        }
        $param = $r->getParam('filter_product_name');
        if (!is_null($param) && $param!=='') {
            $collection->addFieldToFilter('product_name', ['like'=>'%'.$param.'%']);
        }
        $param = $r->getParam('filter_customer_name');
        if (!is_null($param) && $param!=='') {
            $collection->addFieldToFilter('main_table.customer_name', ['like'=>'%'.$param.'%']);
        }
        $param = $r->getParam('filter_replied');
        if (!is_null($param) && $param!=='') {
            if ($param) {
                $collection->addFieldToFilter('LENGTH(answer_text)', ['gt' => 0]);
            } else {
                $collection->addFieldToFilter(['answer_text','LENGTH(answer_text)'], [['null' => 1], ['eq' => 0]]);
            }
        }
        $param = $r->getParam('filter_question');
        if (!is_null($param) && $param!=='') {
            $collection->addFieldToFilter('question_text', ['like'=>'%'.$param.'%']);
        }
        $param = $r->getParam('filter_answer');
        if (!is_null($param) && $param!=='') {
            $collection->addFieldToFilter('answer_text', ['like'=>'%'.$param.'%']);
        }
        if (($v = $r->getParam('filter_order_id_from'))) {
            $collection->addFieldToFilter('order_increment_id', ['gteq'=>$v]);
        }
        if (($v = $r->getParam('filter_order_id_to'))) {
            $collection->addFieldToFilter('order_increment_id', ['lteq'=>$v]);
        }

        if (($v = $r->getParam('filter_question_date_from'))) {
            $collection->addFieldToFilter('question_date', ['gteq'=>$this->_hlp->dateLocaleToInternal($v, null, true)]);
        }
        if (($v = $r->getParam('filter_question_date_to'))) {
            $_filterDate = $this->_hlp->dateLocaleToInternal($v, $dateFormat, true);
            $_filterDate = $localeDate->date($_filterDate, null, false);
            $_filterDate->add(new \DateInterval('P1D'));
            $_filterDate->sub(new \DateInterval('PT1S'));
            $_filterDate = datefmt_format_object($_filterDate, $datetimeFormatInt);
            $collection->addFieldToFilter('question_date', ['lteq'=>$_filterDate]);

        }
        $collection->addVendorFilter($this->getVendor()->getId());
        $collection->addApprovedQuestionsFilter();
        return $this;
    }

    public function getVendor()
    {
        return ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getVendor();
    }

    public function getQuestionsCollection()
    {
        if (!$this->_collection) {
            $v = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getVendor();
            if (!$v || !$v->getId()) {
                return [];
            }
            $r = $this->_request;
            $res = $this->_hlp->rHlp();
            $collection = $this->_questionFactory->create()->getCollection();
            $collection->joinShipments()->joinProducts()->joinVendors();
            $collection->getSelect()->columns(['is_replied' => new \Zend_Db_Expr('if(LENGTH(answer_text)>0,1,0)')]);

            $this->_applyRequestFilters($collection);

            $this->_collection = $collection;
        }
        return $this->_collection;
    }

    public function getShipmentUrl($question)
    {
        return $this->_urlBuilder->getUrl('udropship/vendor/', ['_query'=>'filter_order_id_from='.$question->getOrderIncrementId().'&filter_order_id_to='.$question->getOrderIncrementId()]);
    }
    public function getProductUrl($question)
    {
        if ($this->_hlp->isModuleActive('Unirgy_DropshipVendorProduct')) {
            return $this->_urlBuilder->getUrl('udprod/vendor/products', ['_query'=>'filter_sku='.$question->getProductSku()]);
        } elseif ($this->_hlp->isModuleActive('umicrosite')
            && ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getVendor()->getShowProductsMenuItem()
        ) {
            $params = [];
            $hlp = $this->_backendUrl;
            if ($hlp->useSecretKey()) {
                $params[Url::SECRET_KEY_PARAM_NAME] = $hlp->getSecretKey();
            }
            $params['id'] = $question->getProductId();
            return $this->_backendUrl->getUrl('catalog/product/edit', $params);
        } else {
            return $this->_urlBuilder->getUrl('udropship/vendor/product', ['_query'=>'filter_sku='.$question->getProductSku()]);
        }
    }
}