<?php

namespace StageBit\CustomCode\Observer\Header;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Toplinks
 * @package StageBit\CustomCode\Observer\Header
 */
class Toplinks implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Block\Account\AuthorizationLink
     */
    protected $_authorization;

    /**
     * Toplinks constructor.
     * @param \Magento\Customer\Block\Account\AuthorizationLink $authorizationLink
     */
    public function __construct(
        \Magento\Customer\Block\Account\AuthorizationLink $authorizationLink
    )
    {
        $this->_authorization   =   $authorizationLink;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $name = $observer->getElementName();
        if($name == 'my-account-link') {
            $transport = $observer->getTransport();
            $output = $transport->getData('output');

            $svg = '<svg version="1.1" id="bi-globe" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
                        <circle fill="none" cx="50" cy="50" r="27.5"/>
                        <ellipse fill="none" cx="50" cy="50" rx="15.2" ry="27.2"/>
                        <line fill="none" x1="50" y1="22.5" x2="50" y2="77.5"/>
                        <ellipse fill="none" cx="50" cy="50" rx="27.2" ry="15.2"/>
                        <line fill="none" x1="22.5" y1="50" x2="77.5" y2="50"/>
                    </svg>';
            $output = str_replace('My Account', $svg.__('My Account'), $output);
            $output = str_replace('Il mio account', $svg.__('Il mio account'), $output);
            $output = str_replace('Minha Conta', $svg.__('Minha Conta'), $output);
            $output = str_replace('<li>', '<li class="ai-icon">', $output);

            $transport->setData('output', $output);
        }

        if ($name == 'authorization-link') {
            if ($this->_authorization->isLoggedIn()) {
                $transport = $observer->getTransport();
                $output = $transport->getData('output');
                $svg = '<svg version="1.1" id="si-unlock" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                            <polygon points="31.709,42.761 31.709,74.581 49.854,74.581 50.146,74.581 68.291,74.581 68.291,42.761 "/>
                            <path d="M31.709,42.761c0,0,0.742-17.342,18.291-17.342c5.484,0,9.327,1.694,12.018,4.022"/>
                            <circle cx="50" cy="62.722" r="4"/>
                        </svg>';
                $output = str_replace('Sign Out', $svg.__('Sign Out'), $output);
                $output = str_replace('Esci', $svg.__('Esci'), $output);
                $output = str_replace('Sair', $svg.__('Sair'), $output);
                $output = str_replace('authorization-link', 'authorization-link ai-icon', $output);

                $transport->setData('output', $output);
            }

            if (!$this->_authorization->isLoggedIn()) {
                $transport = $observer->getTransport();
                $output = $transport->getData('output');
                $svg = '<svg version="1.1" id="si-lock" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" style="enable-background:new 0 0 100 100;" xml:space="preserve">
                            <polygon points="31.709,42.761 31.709,74.581 49.854,74.581 50.146,74.581 68.291,74.581 68.291,42.761 "/>
                            <path d="M31.709,42.761c0,0,0.742-17.342,18.291-17.342s18.291,17.342,18.291,17.342"/>
                            <circle cx="50" cy="62.722" r="4"/>
                        </svg>';
                $output = str_replace('Login', $svg.__('Login'), $output);
                $output = str_replace('Entra', $svg.__('Entra'), $output);
                $output = str_replace('authorization-link', 'authorization-link ai-icon', $output);

                $transport->setData('output', $output);
            }
        }
    }
}