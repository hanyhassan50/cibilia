<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Cibilians\Block;

use Magento\Framework\UrlInterface;

/**
 * Class Link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    
	/**
     * @var UrlInterface
     */
    protected $urlBuilder;
	
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,    
		UrlInterface $urlBuilder,		
        array $data = []
    ) {		
		$this->urlBuilder = $urlBuilder;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
		echo "Hello";
		die;
        return $this->urlBuilder->getUrl('testing-url');
    }
	
	 /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {        
        return parent::_toHtml();
    }
}
