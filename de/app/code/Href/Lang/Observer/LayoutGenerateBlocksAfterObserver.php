<?php
/**
 * Copyright © 2015 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Href\Lang\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;

/**
 * Blog observer
 */
class LayoutGenerateBlocksAfterObserver implements ObserverInterface
{
	
	/**
	 * @var \Magento\Framework\View\Page\Config
	 */
	protected $page;

	/**
	 * @var \Magento\Framework\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * DuplicateContent constructor.
	 * @param \Magento\Framework\View\Page\Config $page
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 */
	public function __construct(
		Config $page,
		ObjectManagerInterface $objectManager
	)
	{
		$this->page          = $page;
		$this->objectManager = $objectManager;
	}
	
    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$layout = $observer->getEvent()->getLayout();
		$headBlock = $this->getBlock('head.additional', $layout);
		
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $om->get('Magento\Store\Model\StoreManagerInterface');
		
		$stores = $storeManager->getWebsite()->getStores(); 
		$currentStoreId = $storeManager->getStore()->getId();
		
		 if ($headBlock != false) {
			foreach ($stores as $store) {       
				$lang = $store->getConfig('general/locale/code');
			    $cleanUrl = preg_replace('/\?.*/', '', $store->getCurrentUrl());
				$lang = preg_replace("/[\s_]/", "-", $lang);	

				$this->page->addRemotePageAsset(
					$cleanUrl,
					$contentType = 'canonical',
					$properties = ['attributes' =>['rel' => 'alternate','hreflang' => $lang]
					],
					$name = 'Custom added'
				);
			}
		}
		return $this;
    }
    
	 /**
     * Get block by name
     *
     * @param string $name
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @return \Magento\Framework\View\Element\AbstractBlock|bool
     */
    public function getBlock($name, $layout)
    {
        $blocks = $layout->getAllBlocks();
        return isset($blocks[$name]) ? $blocks[$name] : false;
    }
}
