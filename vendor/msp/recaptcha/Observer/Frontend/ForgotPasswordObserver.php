<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_ReCaptcha
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\ReCaptcha\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use MSP\ReCaptcha\Api\ValidateInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Action\Action;
use MSP\ReCaptcha\Model\Config;

class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var ValidateInterface
     */
    private $validate;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    public function __construct(
        ValidateInterface $validate,
        Config $config,
        ManagerInterface $messageManager,
        UrlInterface $url,
        ActionFlag $actionFlag
    ) {
        $this->validate = $validate;
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->url = $url;
        $this->actionFlag = $actionFlag;
    }

    public function execute(Observer $observer)
    {
        if (!$this->config->getEnabledFrontendForgot()) {
            return;
        }

        $controller = $observer->getControllerAction();

        if (!$this->validate->validate()) {
            $this->messageManager->addErrorMessage($this->config->getErrorDescription());
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

            $url = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $controller->getResponse()->setRedirect($url);
        }
    }
}
