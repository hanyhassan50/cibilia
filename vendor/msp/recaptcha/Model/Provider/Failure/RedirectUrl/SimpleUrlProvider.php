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

namespace MSP\ReCaptcha\Model\Provider\Failure\RedirectUrl;

use Magento\Framework\UrlInterface;
use MSP\ReCaptcha\Model\Provider\Failure\RedirectUrlProviderInterface;

class SimpleUrlProvider implements RedirectUrlProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var string
     */
    private $urlPath;

    /**
     * @var array
     */
    private $urlParams;

    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $url,
        $urlPath,
        $urlParams = null
    ) {
        $this->_request = $request;
        $this->urlPath = $urlPath;
        $this->urlParams = $urlParams;
        $this->url = $url;
    }

    /**
     * Get redirection URL
     * @return string
     */
    public function execute()
    {
        if($this->_request->getParam('home')) {
            return $this->url->getUrl('home');
        }

        return $this->url->getUrl($this->urlPath, $this->urlParams);
    }
}
