<?php
namespace Cibilia\Commission\Cron;
 
class Cibilian {
 
    protected $_logger;
 
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
    }
    
    /**
     * Method executed when cron runs in server
     */
    public function exdecute() {
        $this->_logger->debug('Running Cron from Cibilian class');
        
        /*$objCommission = $this->_objectManager->create('Cibilia\Commission\Model\Commissions');
        $objCommission->genrateCibilianOrders();*/

        return $this;
    }
}