<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Redemption\Observer;

use Magento\Framework\Event\ObserverInterface;
/**
 * Redemption Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class CheckoutEventCartSaveAfterObserver implements ObserverInterface
{
    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //$quoteId = $observer->getEvent()->getQuote()->getId();
        $objQuote = $observer->getCart()->getQuote();

        $hasRecalculate = false;

        if($objQuote && $objQuote->getId()){
            
            if(!$objQuote->getItemsCount()){
                
                $objQuote->setRedeemAmount(0);

                $hasRecalculate = true;
                
            }

            if($objQuote->getItemsCount()){
                
                if($objQuote->getRedeemAmount() > $objQuote->getSubtotalWithDiscount()){
                    
                    $objQuote->setRedeemAmount($objQuote->getSubtotalWithDiscount());
                    
                    $hasRecalculate = true;
                }
            }
            if($hasRecalculate){

                $objQuote->setTotalsCollectedFlag(false)->collectTotals();

                $objQuote->save();
            }    
        }
    }
}