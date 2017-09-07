<?php
namespace Cibilia\Redemption\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
/**
 * Customer login observer
 */
class Test implements ObserverInterface
{
 
    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
 
    /**
     * Constructor
     *
     * @param  \Magento\Framework\Message\ManagerInterface $messageManager Message Manager
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->messageManager = $messageManager;
    }
 
    /**
     * Display a custom message when customer log in
     *
     * @param  \Magento\Framework\Event\Observer $observer Observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event    = $observer->getEvent();
        $customer = $event->getCustomer();
        
        $this->messageManager->addNotice(__('Welcome back %1!', $customer->getName()));
    }
 
}