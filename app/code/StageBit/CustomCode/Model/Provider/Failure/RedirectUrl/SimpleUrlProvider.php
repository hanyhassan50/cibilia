<?php

namespace StageBit\CustomCode\Model\Provider\Failure\RedirectUrl;

use Magento\Framework\UrlInterface;
use Magento\TestFramework\Event\Magento;
use MSP\ReCaptcha\Model\Provider\Failure\RedirectUrlProviderInterface;

class SimpleUrlProvider implements RedirectUrlProviderInterface
{
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

    protected $_request;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        UrlInterface $url,
        $urlPath,
        $urlParams = null
    ) {
        $this->urlPath = $urlPath;
        $this->urlParams = $urlParams;
        $this->url = $url;
        $this->_request = $request;
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
