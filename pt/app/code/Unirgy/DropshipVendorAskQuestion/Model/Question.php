<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipVendorAskQuestion
 * @copyright  Copyright (c) 2011-2012 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipVendorAskQuestion\Model;

use Magento\Backend\Model\UrlFactory as ModelUrlFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\UrlFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\Source as ModelSource;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;

class Question extends AbstractModel
{
    /**
     * @var HelperData
     */
    protected $_qaHlp;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Unirgy\Dropship\Model\Source
     */
    protected $_src;

    /**
     * @var UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var ModelUrlFactory
     */
    protected $_backendUrlFactory;

    protected $_hlp;

    public function __construct(
        Context $context,
        Registry $registry,
        \Unirgy\Dropship\Helper\Data $udropshipHelper,
        HelperData $helperData, 
        ScopeConfigInterface $configScopeConfigInterface,
        \Unirgy\Dropship\Model\Source $modelSource,
        UrlFactory $modelUrlFactory, 
        ModelUrlFactory $backendModelUrlFactory, 
        AbstractResource $resource = null, 
        AbstractDb $resourceCollection = null, 
        array $data = [])
    {
        $this->_hlp = $udropshipHelper;
        $this->_qaHlp = $helperData;
        $this->_scopeConfig = $configScopeConfigInterface;
        $this->_src = $modelSource;
        $this->_urlFactory = $modelUrlFactory;
        $this->_backendUrlFactory = $backendModelUrlFactory;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected $_eventPrefix = 'udqa_question';
    protected $_eventObject = 'question';

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipVendorAskQuestion\Model\ResourceModel\Question');
    }

    public function afterCommitCallback()
    {
        $this->load($this->getId());
        $this->_qaHlp->notifyAdminCustomer($this);
        $this->_qaHlp->notifyAdminVendor($this);
        $this->_qaHlp->notifyCustomer($this);
        $this->_qaHlp->notifyVendor($this);
        return parent::afterCommitCallback();
    }

    public function beforeSave()
    {
        if ($this->getAnswerText() && $this->isEmptyAnswerDate()) {
            $this->setAnswerDate($this->_hlp->now());
        }
        if ($this->isChangeQuestionStatusToDefault()) {
            $this->setIsAdminQuestionNotified(0);
            $this->setQuestionStatus($this->_scopeConfig->getValue('udqa/general/default_question_status', ScopeInterface::SCOPE_STORE));
        }
        if ($this->isChangeAnswerStatusToDefault()) {
            $this->setIsAdminAnswerNotified(0);
            $this->setAnswerStatus($this->_scopeConfig->getValue('udqa/general/default_answer_status', ScopeInterface::SCOPE_STORE));
        }
        return parent::beforeSave();
    }

    public function isChangeQuestionStatusToDefault()
    {
        return !$this->hasQuestionStatus()
            || $this->getQuestionStatus()!=ModelSource::UDQA_STATUS_DECLINED
                && !$this->getIsAdminChanges()
                && $this->dataHasChangedFor('question_text')
                && !$this->getIsSkipAutoQuestionStatus();
    }

    public function isChangeAnswerStatusToDefault()
    {
        return !$this->hasAnswerStatus()
            || $this->getAnswerStatus()!=ModelSource::UDQA_STATUS_DECLINED
                && !$this->getIsAdminChanges()
                && $this->dataHasChangedFor('answer_text')
                && !$this->getIsSkipAutoAnswerStatus();
    }

    public function isEmptyAnswerDate()
    {
        return $this->_qaHlp->isEmptyDate($this->getAnswerDate());
    }

    public function validate()
    {
        $errors = [];
        $qaHlp = $this->_qaHlp;
        if (!(new NotEmpty())->isValid($this->getQuestionText())) {
            $errors[] = __('Please enter the question text.');
        }
        if (!(new NotEmpty())->isValid($this->getCustomerName())) {
            $errors[] = __('Please enter your name.');
        }
        if (!(new EmailAddress())->isValid($this->getCustomerEmail())) {
            $errors[] = __('Invalid email address "%1".', htmlspecialchars($this->getCustomerEmail()));
        }
        if ($this->getShipmentId()) {
            $shipment = $this->_hlp->createObj('Magento\Sales\Model\Order\Shipment')->load($this->getShipmentId());
            if (!$shipment->getId()) {
                $errors[] = __('Shipment not found.');
            } else {
                if ($shipment->getCustomerId()!=$this->getCustomerId()) {
                    $errors[] = __('Shipment not found.');
                } else {
                    $this->setVendorId($shipment->getUdropshipVendor());
                }
            }
        }
        return !empty($errors) ? $errors : true;
    }

    public function validateVendor($vId)
    {
        if ($vId instanceof DataObject) {
            $vId = $vId->getVendorId();
        }
        return $vId==$this->getVendorId();
    }
    public function validateCustomer($cId)
    {
        if ($cId instanceof DataObject) {
            $cId = $cId->getCustomerId();
        }
        return $cId==$this->getCustomerId();
    }


    public function getVendorName()
    {
        $vendors = $this->_src->getVendors(true);
        return @$vendors[$this->getVendorId()];
    }

    public function getVendorEmail()
    {
        $vendors = $this->_src->getVendorsColumn('email', true);
        return @$vendors[$this->getVendorId()];
    }

    public function canCustomerViewAnswer()
    {
        return $this->getAnswerText()
            && $this->getAnswerStatus()==ModelSource::UDQA_STATUS_APPROVED;
    }

    public function canVendorViewQuestion()
    {
        return $this->getQuestionStatus()==ModelSource::UDQA_STATUS_APPROVED;
    }

    public function canShowCustomerInfo()
    {
        return $this->_qaHlp->canShowCustomerInfo($this);
    }

    public function canShowVendorInfo()
    {
        return $this->_qaHlp->canShowCustomerInfo($this);
    }

    public function getVendorUrl()
    {
        return $this->_urlFactory->create()->getUrl('udqa/vendor/questionEdit', ['id'=>$this->getId()]);
    }

    public function getAdminUrl()
    {
        return $this->_backendUrlFactory->create()->getUrl('adminhtml/udqaadmin_index/edit', ['id'=>$this->getId()]);
    }

}