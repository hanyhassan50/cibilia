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

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;

use Magento\Captcha\Helper\Data as CaptchaHelperData;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

abstract class AbstractCustomer extends Action
{
    /**
     * @var HelperData
     */
    protected $_qaHlp;

    /**
     * @var DropshipHelperData
     */
    protected $_hlp;

    /**
     * @var CaptchaHelperData
     */
    protected $_captchaHelper;

    public function __construct(Context $context, 
        HelperData $helperData, 
        DropshipHelperData $dropshipHelperData, 
        CaptchaHelperData $captchaHelperData)
    {
        $this->_qaHlp = $helperData;
        $this->_hlp = $dropshipHelperData;
        $this->_captchaHelper = $captchaHelperData;

        parent::__construct($context);
    }

    protected function _saveFormData($data=null, $id=null)
    {
        $this->_qaHlp->saveFormData($data, $id);
    }

    protected function _fetchFormData($id=null)
    {
        return $this->_qaHlp->fetchFormData($id);
    }

    public function checkCaptcha()
    {
        if (!$this->_hlp->isModuleActive('Magento_Captcha')) return $this;
        ObjectManager::getInstance()->get('Magento\Customer\Model\Session')->setData('udqa_question_form_show_captcha',1);
        $formId = 'udqa_question_form';
        $captchaModel = $this->_captchaHelper->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            if (!$captchaModel->isCorrect($this->_getCaptchaString($this->getRequest(), $formId))) {
                throw new \Exception(__('Incorrect CAPTCHA.'));
            }
        }
        return $this;
    }

    protected function _getCaptchaString($request, $formId)
    {
        $captchaParams = $request->getPost(CaptchaHelperData::INPUT_NAME_FIELD_VALUE);
        return $captchaParams[$formId];
    }

}