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
 * @package    MSP_TwoFactorAuth
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\TwoFactorAuth\Observer;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Registry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\TwoFactorAuth\Api\TfaInterface;

class ViewBlockAbstractToHtmlBefore implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var TfaInterface
     */
    private $tfa;

    public function __construct(
        Session $session,
        TfaInterface $tfa,
        Registry $registry
    ) {
        $this->tfa = $tfa;
        $this->session = $session;
        $this->registry = $registry;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!count($this->tfa->getAllEnabledProviders())) {
            return;
        }

        /** @var $block \Magento\User\Block\User\Edit\Tabs */
        $block = $observer->getBlock();

        $nameInLayout = $block->getNameInLayout();
        if ($nameInLayout == 'adminhtml.user.edit.tabs') {
            $tfaForm = $block->getLayout()->createBlock(
                'MSP\TwoFactorAuth\Block\User\Edit\Tab\Tfa',
                'msp.user.tfa.edit'
            )->toHtml();

            $block->addTabAfter(
                'msp_twofactorauth',
                [
                    'label' => __('2FA'),
                    'title' => __('2FA'),
                    'content' => $tfaForm,
                    'active' => true
                ],
                'roles_section'
            );
        }
    }
}
