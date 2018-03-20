<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipPayout
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipPayout\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Tax\Helper\Data as TaxHelperData;
use Unirgy\DropshipPayout\Helper\Data as DropshipPayoutHelperData;
use Unirgy\DropshipPayout\Helper\ProtectedCode;
use Unirgy\Dropship\Helper\Data as HelperData;
use Unirgy\Dropship\Model\Source as DropshipSource;
use Unirgy\Dropship\Model\Vendor\StatementFactory;
use Unirgy\Dropship\Model\Vendor\Statement\AbstractStatement;
use Unirgy\Dropship\Model\Vendor\Statement\StatementInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\Collection as ItemCollection;
use \Magento\Framework\Data\Collection as DataCollection;

class Payout extends AbstractStatement implements StatementInterface
{
    /**
     * @var StatementFactory
     */
    protected $_statementFactory;

    /**
     * @var DropshipPayoutHelperData
     */
    protected $_payoutHlp;

    /**
     * @var ProtectedCode
     */
    protected $_payoutHlpPr;

    public function __construct(
        HelperData $helperData,
        TaxHelperData $taxHelperData,
        DropshipSource $source,
        Context $context, 
        Registry $registry, 
        StatementFactory $vendorStatementFactory, 
        DropshipPayoutHelperData $dropshipPayoutHelperData,
        ProtectedCode $helperProtectedCode,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null, 
        array $data = [])
    {
        $this->_statementFactory = $vendorStatementFactory;
        $this->_payoutHlp = $dropshipPayoutHelperData;
        $this->_payoutHlpPr = $helperProtectedCode;

        parent::__construct($helperData, $taxHelperData, $source, $context, $registry, $resource, $resourceCollection, $data);
    }

    protected $_eventPrefix = 'udpayout_payout';
    protected $_eventObject = 'payout';
    
    const TYPE_AUTO      = 'auto';
    const TYPE_MANUAL    = 'manual';
    const TYPE_SCHEDULED = 'scheduled';
    const TYPE_STATEMENT = 'statement';
    
    const STATUS_PENDING    = 'pending';
    const STATUS_SCHEDULED  = 'scheduled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_HOLD       = 'hold';
    const STATUS_PAYPAL_IPN = 'paypal_ipn';
    const STATUS_PAID       = 'paid';
    const STATUS_REVERSED   = 'reversed';
    const STATUS_ERROR      = 'error';
    const STATUS_CANCELED   = 'canceled';

    protected function _construct()
    {
        $this->_init('Unirgy\DropshipPayout\Model\ResourceModel\Payout');
    }
    
    public function getAdjustmentPrefix()
    {
        return $this->_hlp->getAdjustmentPrefix('payout');
    }
    
    public function isMyAdjustment($adjustment)
    {
        return 0 === strpos($adjustment->getAdjustmentId(), $this->getAdjustmentPrefix())
            || 0 === strpos($adjustment->getAdjustmentId(), $this->_hlp->getAdjustmentPrefix('statement:payout'));
    }
    
    protected $_statement;
    public function getStatement()
    {
        if (is_null($this->_statement)) {
            $this->_statement = $this->_statementFactory->create()->load($this->getStatementId(), 'statement_id');
        }
        return $this->_statement;
    }
    public function setStatement($statement)
    {
        $this->_statement = $statement;
        return $this;
    }

    public function calculateOrder($order)
    {
        if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
            return $this->_calculateOrderTierCom($order);
        } else {
            return $this->_calculateOrder($order);
        }
    }

    protected function _calculateOrderTierCom($order)
    {
        /** @var \Magento\Tax\Helper\Data $taxHelper */
        $taxHelper = $this->_hlp->createObj('\Magento\Tax\Helper\Data');
        $taxInSubtotal = $taxHelper->displaySalesBothPrices() || $taxHelper->displaySalesPriceInclTax();
        if (is_null($order['com_percent'])) {
            $order['com_percent'] = $this->getVendor()->getCommissionPercent();
        }
        $order['com_percent'] *= 1;
        if (is_null($order['po_com_percent'])) {
            $order['po_com_percent'] = $this->getVendor()->getCommissionPercent();
        }
        $order['po_com_percent'] *= 1;

        if (isset($order['amounts']['tax']) && in_array($this->getVendor()->getStatementTaxInPayout(),
                ['', 'include'])
        ) {
            if ($taxInSubtotal) {
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $order['amounts']['subtotal'] += $order['amounts']['tax'];
                    $order['amounts']['subtotal'] += $order['amounts']['hidden_tax'];
                    $order['amounts']['com_amount'] = $order['amounts']['subtotal'] * $order['com_percent'] / 100;
                } else {
                    $order['amounts']['com_amount'] = $order['amounts']['subtotal'] * $order['com_percent'] / 100;
                    $order['amounts']['subtotal'] += $order['amounts']['tax'];
                    $order['amounts']['subtotal'] += $order['amounts']['hidden_tax'];
                }
            } else {
                $order['amounts']['com_amount'] = $order['amounts']['subtotal'] * $order['com_percent'] / 100;
                $order['amounts']['total_payout'] += $order['amounts']['tax'];
                $order['amounts']['total_payout'] += $order['amounts']['hidden_tax'];
                $order['amounts']['total_payment'] += $order['amounts']['tax'];
                $order['amounts']['total_payment'] += $order['amounts']['hidden_tax'];
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $taxCom = round($order['amounts']['tax'] * $order['com_percent'] / 100, 2);
                    $order['amounts']['com_amount'] += $taxCom;
                    //$order['amounts']['total_payout'] -= $taxCom;
                }
            }
        } else {
            $order['amounts']['com_amount'] = $order['amounts']['subtotal'] * $order['com_percent'] / 100;
        }

        $order['amounts']['com_amount'] = round($order['amounts']['com_amount'], 2);

        $order['amounts']['total_payout'] = $order['amounts']['subtotal'] - $order['amounts']['com_amount'] - $order['amounts']['trans_fee'] + $order['amounts']['adj_amount'];
        $order['amounts']['total_payment'] = $order['amounts']['subtotal'] + $order['amounts']['adj_amount'];

        if (isset($order['amounts']['discount']) && in_array($this->getVendor()->getStatementDiscountInPayout(),
                ['', 'include'])
        ) {
            if ($this->getVendor()->getApplyCommissionOnDiscount()) {
                $discountCom = round($order['amounts']['discount'] * $order['com_percent'] / 100, 2);
                $order['amounts']['com_amount'] -= $discountCom;
                $order['amounts']['total_payout'] += $discountCom;
            }
            $order['amounts']['total_payout'] -= $order['amounts']['discount'];
        }
        $order['amounts']['total_payment'] -= $order['amounts']['discount'];

        if (isset($order['amounts']['shipping']) && in_array($this->getVendor()->getStatementShippingInPayout(),
                ['', 'include'])
        ) {
            if ($this->getVendor()->getApplyCommissionOnShipping()) {
                $shipCom = round($order['amounts']['shipping'] * $order['po_com_percent'] / 100, 2);
                $order['amounts']['com_amount'] += $shipCom;
                $order['amounts']['total_payout'] -= $shipCom;
            }
            $order['amounts']['total_payout'] += $order['amounts']['shipping'];
        }
        $order['amounts']['total_payment'] += $order['amounts']['shipping'];
        $order['amounts']['total_invoice'] = $order['amounts']['com_amount'] + $order['amounts']['trans_fee'] + $order['amounts']['adj_amount'];

        return $order;
    }

    public function addPo($po)
    {
        if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
            return $this->_addPoTierCom($po);
        } else {
            return $this->_addPo($po);
        }
    }

    protected function _addPoTierCom($po)
    {
        $hlp = $this->_hlp;

        $this->initTotals();

        $hlp->collectPoAdjustments([$po]);
        $hlp->addVendorSkus($po);

        $onlySubtotal = false;
        foreach ($po->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy(true)) continue;
            $order = $this->initPoItem($item, $onlySubtotal);
            $onlySubtotal = true;

            $this->_eventManager->dispatch('udropship_vendor_payout_item_row', [
                'payout' => $this,
                'po' => $po,
                'po_item' => $item,
                'order' => &$order
            ]);

            $order = $this->calculateOrder($order);
            $this->_totals_amount = $this->accumulateOrder($order, $this->_totals_amount);

            $poId = $po->getId() ? $po->getId() : spl_object_hash($po);
            $this->_orders[$poId . '-' . $item->getId()] = $order;
        }

        return $this;
    }

    public function initPoItem($poItem, $onlySubtotal)
    {
        $po = $poItem->getPo() ? $poItem->getPo() : $poItem->getShipment();
        $orderItem = $poItem->getOrderItem();
        $hlp = $this->_hlp;
        $order = [
            'po_id' => $po->getId(),
            'date' => $hlp->getPoOrderCreatedAt($po),
            'id' => $hlp->getPoOrderIncrementId($po),
            'po_com_percent' => $po->getCommissionPercent(),
            'com_percent' => $poItem->getCommissionPercent(),
            'trans_fee' => $poItem->getTransactionFee(),
            'adjustments' => $onlySubtotal ? [] : $po->getAdjustments(),
            'order_id' => $po->getOrderId(),
            'order_created_at' => $hlp->getPoOrderCreatedAt($po),
            'order_increment_id' => $hlp->getPoOrderIncrementId($po),
            'po_increment_id' => $po->getIncrementId(),
            'po_created_at' => $po->getCreatedAt(),
            'po_statement_date' => $po->getStatementDate(),
            'po_type' => $po instanceof \Unirgy\DropshipPo\Model\Po ? 'po' : 'shipment',
            'sku' => $poItem->getSku(),
            'simple_sku' => $poItem->getOrderItem()->getProductOptionByCode('simple_sku'),
            'vendor_sku' => $poItem->getVendorSku(),
            'vendor_simple_sku' => $poItem->getVendorSimpleSku(),
            'product' => $poItem->getName(),
            'po_item_id' => $poItem->getId()
        ];
        if ($this->getVendor()->getStatementSubtotalBase() == 'cost') {
            if (abs($poItem->getBaseCost()) > 0.001) {
                $subtotal = $poItem->getBaseCost() * $poItem->getQty();
            } else {
                $subtotal = $orderItem->getBaseCost() * $poItem->getQty();
            }
        } else {
            $subtotal = $orderItem->getBasePrice() * $poItem->getQty();
        }

        $qtyOrdered = $orderItem->getQtyOrdered();
        $_rowDivider = $poItem->getQty() / ($qtyOrdered > 0 ? $qtyOrdered : 1);
        $iHiddenTax = $orderItem->getBaseHiddenTaxAmount() * ($_rowDivider > 0 ? $_rowDivider : 1);
        $iTax = $orderItem->getBaseTaxAmount() * ($_rowDivider > 0 ? $_rowDivider : 1);
        $iDiscount = $orderItem->getBaseDiscountAmount() * ($_rowDivider > 0 ? $_rowDivider : 1);

        if ($po->getOrder()->getData('udpo_amount_fields') && $poItem->getPo()
            || $po->getOrder()->getData('ud_amount_fields') && $poItem->getShipment()
        ) {
            $iHiddenTax = $poItem->getBaseHiddenTaxAmount();
            $iTax = $poItem->getBaseTaxAmount();
            $iDiscount = $poItem->getBaseDiscountAmount();
            $subtotal = $poItem->getBaseRowTotal();
        }

        $shippingAmount = $po->getBaseShippingAmount();
        if ($this->getVendor()->getIsShippingTaxInShipping()) {
            $shippingAmount += $po->getBaseShippingTax();
        } else {
            if ($onlySubtotal) {
                $iTax += $po->getBaseShippingTax();
            }
        }
        $iTax = $this->_deltaRound($iTax, $po->getId());
        $amountRow = [
            'subtotal' => $subtotal,
            'shipping' => $onlySubtotal ? 0 : $shippingAmount,
            'tax' => $iTax,
            'hidden_tax' => $iHiddenTax,
            'discount' => $iDiscount,
            'handling' => $onlySubtotal ? 0 : $po->getBaseHandlingFee(),
            'trans_fee' => $onlySubtotal ? 0 : $po->getTransactionFee(),
            'adj_amount' => $onlySubtotal ? 0 : $po->getAdjustmentAmount(),
        ];
        foreach ($amountRow as &$_ar) {
            $_ar = is_null($_ar) ? 0 : $_ar;
        }
        unset($_ar);
        $order['amounts'] = array_merge($this->_getEmptyTotals(), $amountRow);
        return $order;
    }

    public function initRefundItem($refundItem, $onlySubtotal)
    {
        $pOptions = $refundItem->getProductOptions();
        if (!is_array($pOptions)) {
            $pOptions = $this->_hlp->unserialize($pOptions);
        }
        $hlp = $this->_hlp;
        $order = [
            'po_id' => $refundItem->getPoId(),
            'date' => $refundItem->getPoCreatedAt(),
            'id' => $refundItem->getPoIncrementId(),
            'com_percent' => $refundItem->getCommissionPercent(),
            'po_com_percent' => $refundItem->getPoCommissionPercent(),
            'trans_fee' => $refundItem->getTransactionFee(),
            'order_id' => $refundItem->getOrderId(),
            'order_created_at' => $refundItem->getOrderCreatedAt(),
            'order_increment_id' => $refundItem->getOrderIncrementId(),
            'refund_created_at' => $refundItem->getRefundCreatedAt(),
            'refund_increment_id' => $refundItem->getRefundIncrementId(),
            'po_increment_id' => $refundItem->getPoIncrementId(),
            'po_created_at' => $refundItem->getPoCreatedAt(),
            'po_type' => $refundItem->getPoType(),
            'sku' => $refundItem->getSku(),
            'simple_sku' => @$pOptions['simple_sku'],
            'vendor_sku' => $refundItem->getVendorSku(),
            'vendor_simple_sku' => $refundItem->getVendorSimpleSku(),
            'product' => $refundItem->getName(),
            'po_item_id' => $refundItem->getPoItemId(),
            'refund_item_id' => $refundItem->getRefundItemId(),
            'refund_id' => $refundItem->getRefundId()
        ];
        $refundQty = min($refundItem->getQty(), $refundItem->getRefundQty());
        $iHiddenTax = $refundItem->getBaseHiddenTaxAmount() / max(1, $refundItem->getQtyOrdered());
        $iHiddenTax = $iHiddenTax * $refundQty;
        $iTax = $refundItem->getBaseTaxAmount() / max(1, $refundItem->getQtyOrdered());
        $iTax = $iTax * $refundQty;
        $iDiscount = $refundItem->getBaseDiscountAmount() / max(1, $refundItem->getQtyOrdered());
        $iDiscount = $iDiscount * $refundQty;
        if ($this->getVendor()->getStatementSubtotalBase() == 'cost') {
            $subtotal = $refundItem->getBaseCost() * $refundQty;
        } else {
            $subtotal = $refundItem->getBasePrice() * $refundQty;
        }
        $amountRow = [
            'subtotal' => $subtotal,
            'shipping' => $onlySubtotal ? 0 : min($refundItem->getBaseShippingAmount(),
                                                  $refundItem->getRefundShippingAmount()),
            'tax' => $iTax,
            'hidden_tax' => $iHiddenTax,
            'discount' => $iDiscount,
            'trans_fee' => $onlySubtotal ? 0 : $refundItem->getPoTransactionFee(),
        ];
        foreach ($amountRow as &$_ar) {
            $_ar = is_null($_ar) ? 0 : $_ar;
        }
        unset($_ar);
        $order['amounts'] = array_merge($this->_getEmptyTotals(), $amountRow);
        return $order;
    }

    public function calculateRefund($order)
    {
        if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
            return $this->_calculateRefundTierCom($order);
        } else {
            return $this->_calculateRefund($order);
        }
    }

    /**
     * @param $refund
     * @return mixed
     */
    protected function _calculateRefundTierCom($refund)
    {
        $taxInSubtotal = $this->_taxHelperData->displaySalesBothPrices() || $this->_taxHelperData->displaySalesPriceInclTax();
        if (is_null($refund['com_percent'])) {
            $refund['com_percent'] = $this->getVendor()->getCommissionPercent();
        }
        $refund['com_percent'] *= 1;
        if (is_null($refund['po_com_percent'])) {
            $refund['po_com_percent'] = $this->getVendor()->getCommissionPercent();
        }
        $refund['po_com_percent'] *= 1;

        if (isset($refund['amounts']['tax']) && in_array($this->getVendor()->getStatementTaxInPayout(),
                                                         ['', 'include'])
        ) {
            if ($taxInSubtotal) {
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $refund['amounts']['subtotal'] += $refund['amounts']['tax'];
                    $refund['amounts']['subtotal'] += $refund['amounts']['hidden_tax'];
                    $refund['amounts']['com_amount'] = $refund['amounts']['subtotal'] * $refund['com_percent'] / 100;
                } else {
                    $refund['amounts']['com_amount'] = $refund['amounts']['subtotal'] * $refund['com_percent'] / 100;
                    $refund['amounts']['subtotal'] += $refund['amounts']['tax'];
                    $refund['amounts']['subtotal'] += $refund['amounts']['hidden_tax'];
                }
            } else {
                $refund['amounts']['com_amount'] = $refund['amounts']['subtotal'] * $refund['com_percent'] / 100;
                $refund['amounts']['total_refund'] += $refund['amounts']['tax'];
                $refund['amounts']['total_refund'] += $refund['amounts']['hidden_tax'];
                $refund['amounts']['refund_payment'] += $refund['amounts']['tax'];
                $refund['amounts']['refund_payment'] += $refund['amounts']['hidden_tax'];
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $taxCom = round($refund['amounts']['tax'] * $refund['com_percent'] / 100, 2);
                    $refund['amounts']['com_amount'] += $taxCom;
                    //$refund['amounts']['total_refund'] -= $taxCom;
                }
            }
        } else {
            $refund['amounts']['com_amount'] = $refund['amounts']['subtotal']*$refund['com_percent']/100;
        }

        $refund['amounts']['com_amount'] = round($refund['amounts']['com_amount'], 2);

        $refund['amounts']['total_refund'] = $refund['amounts']['subtotal'] - $refund['amounts']['com_amount'] - $refund['amounts']['trans_fee'] + $refund['amounts']['adj_amount'];
        $refund['amounts']['refund_payment'] = $refund['amounts']['subtotal'] + $refund['amounts']['adj_amount'];

        if (isset($refund['amounts']['discount']) && in_array($this->getVendor()->getStatementDiscountInPayout(),
                                                              ['', 'include'])
        ) {
            if ($this->getVendor()->getApplyCommissionOnDiscount()) {
                $discountCom = round($refund['amounts']['discount'] * $refund['com_percent'] / 100, 2);
                $refund['amounts']['com_amount'] -= $discountCom;
                $refund['amounts']['total_refund'] += $discountCom;
            }
            $refund['amounts']['total_refund'] -= $refund['amounts']['discount'];
        }
        $refund['amounts']['refund_payment'] -= $refund['amounts']['discount'];
        if (isset($refund['amounts']['shipping']) && in_array($this->getVendor()->getStatementShippingInPayout(),
                                                              ['', 'include'])
        ) {
            if ($this->getVendor()->getApplyCommissionOnShipping()) {
                $shipCom = round($refund['amounts']['shipping'] * $refund['po_com_percent'] / 100, 2);
                $refund['amounts']['com_amount'] += $shipCom;
                $refund['amounts']['total_refund'] -= $shipCom;
            }
            $refund['amounts']['total_refund'] += $refund['amounts']['shipping'];
        }
        $refund['amounts']['refund_payment'] += $refund['amounts']['shipping'];
        $refund['amounts']['refund_invoice'] = $refund['amounts']['com_amount'];

        return $refund;
    }

    protected function _calculateRefund($refund)
    {
        $taxInSubtotal = $this->_taxHelperData->displaySalesBothPrices() || $this->_taxHelperData->displaySalesPriceInclTax();
        if (is_null($refund['com_percent'])) {
            $refund['com_percent'] = $this->getVendor()->getCommissionPercent();
        }
        $refund['com_percent'] *= 1;

        if (isset($refund['amounts']['tax']) && in_array($this->getVendor()->getStatementTaxInPayout(), array('', 'include'))) {
            if ($taxInSubtotal) {
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $refund['amounts']['subtotal'] += $refund['amounts']['tax'];
                    $refund['amounts']['subtotal'] += $refund['amounts']['hidden_tax'];
                    $refund['amounts']['com_amount'] = $refund['amounts']['subtotal']*$refund['com_percent']/100;
                } else {
                    $refund['amounts']['com_amount'] = $refund['amounts']['subtotal']*$refund['com_percent']/100;
                    $refund['amounts']['subtotal'] += $refund['amounts']['tax'];
                    $refund['amounts']['subtotal'] += $refund['amounts']['hidden_tax'];
                }
            } else {
                $refund['amounts']['com_amount'] = $refund['amounts']['subtotal']*$refund['com_percent']/100;
                $refund['amounts']['total_refund']  += $refund['amounts']['tax'];
                $refund['amounts']['total_refund']  += $refund['amounts']['hidden_tax'];
                $refund['amounts']['refund_payment'] += $refund['amounts']['tax'];
                $refund['amounts']['refund_payment'] += $refund['amounts']['hidden_tax'];
                if ($this->getVendor()->getApplyCommissionOnTax()) {
                    $taxCom = round($refund['amounts']['tax']*$refund['com_percent']/100, 2);
                    $refund['amounts']['com_amount'] += $taxCom;
                    //$refund['amounts']['total_refund'] -= $taxCom;
                }
            }
        } else {
            $refund['amounts']['com_amount'] = $refund['amounts']['subtotal']*$refund['com_percent']/100;
        }

        $refund['amounts']['com_amount'] = round($refund['amounts']['com_amount'], 2);

        $refund['amounts']['total_refund'] = $refund['amounts']['subtotal']-$refund['amounts']['com_amount']-$refund['amounts']['trans_fee']+$refund['amounts']['adj_amount'];
        $refund['amounts']['refund_payment'] = $refund['amounts']['subtotal']+$refund['amounts']['adj_amount'];

        if (isset($refund['amounts']['discount']) && in_array($this->getVendor()->getStatementDiscountInPayout(), array('', 'include'))) {
            if ($this->getVendor()->getApplyCommissionOnDiscount()) {
                $discountCom = round($refund['amounts']['discount']*$refund['com_percent']/100, 2);
                $refund['amounts']['com_amount'] -= $discountCom;
                $refund['amounts']['total_refund'] += $discountCom;
            }
            $refund['amounts']['total_refund'] -= $refund['amounts']['discount'];
        }
        $refund['amounts']['refund_payment'] -= $refund['amounts']['discount'];
        if (isset($refund['amounts']['shipping']) && in_array($this->getVendor()->getStatementShippingInPayout(), array('', 'include'))) {
            if ($this->getVendor()->getApplyCommissionOnShipping()) {
                $shipCom = round($refund['amounts']['shipping']*$refund['com_percent']/100, 2);
                $refund['amounts']['com_amount'] += $shipCom;
                $refund['amounts']['total_refund'] -= $shipCom;
            }
            $refund['amounts']['total_refund'] += $refund['amounts']['shipping'];
        }
        $refund['amounts']['refund_payment'] += $refund['amounts']['shipping'];
        $refund['amounts']['refund_invoice'] = $refund['amounts']['com_amount'];

        return $refund;
    }

    public function accumulateRefund($refund, $totals_amount)
    {
        $totals_amount['total_refund'] += $refund['amounts']['total_refund'];
        $totals_amount['refund_payment'] += $refund['amounts']['refund_payment'];
        $totals_amount['refund_invoice'] += $refund['amounts']['refund_invoice'];
        return $totals_amount;
    }

    /**
     * @return ItemCollection
     */
    protected function _getRefundCollectionTierCom()
    {
        $poType = $this->getVendor()->getStatementPoType();
        $excludeStatus = [\Unirgy\Dropship\Model\Source::SHIPMENT_STATUS_CANCELED];
        if ($poType == 'po') {
            $excludeStatus = [\Unirgy\DropshipPo\Model\Source::UDPO_STATUS_CANCELED];
        }
        $fields = ['base_price', 'base_tax_amount', 'base_discount_amount', 'qty_ordered'];
        //if ($baseCost) $fields[] = 'base_cost';
        $res = $this->_hlp->rHlp();
        $refunds = $this->_hlp->createObj('\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Item\Collection');
        $refunds->addFieldToSelect(['refund_item_id' => 'entity_id', 'refund_qty' => 'qty']);
        $refunds->getSelect()
            ->join(
                ['r' => $res->getTableName('sales_creditmemo')],
                'r.entity_id=main_table.parent_id',
                [
                    'refund_increment_id' => 'increment_id',
                    'refund_created_at' => 'created_at',
                    'refund_id' => 'entity_id',
                    'refund_shipping_amount' => 'base_shipping_amount'
                ]
            )
            ->join(
                ['o' => $res->getTableName('sales_order')],
                'o.entity_id=r.order_id',
                []
            )
            ->join(
                ['tg' => $poType == 'po' ? $res->getTableName('udropship_po_grid') : $res->getTableName('sales_shipment_grid')],
                'tg.order_id=o.entity_id',
                [
                    'order_increment_id',
                    'po_increment_id' => 'increment_id',
                    'order_id',
                    'po_id' => 'entity_id',
                    'order_created_at',
                    'po_created_at' => 'created_at'
                ]
            )
            ->join(
                ['t' => $poType == 'po' ? $res->getTableName('udropship_po') : $res->getTableName('sales_shipment')],
                't.entity_id=tg.entity_id',
                ['base_shipping_amount', 'po_commission_percent' => 'commission_percent', 'po_transaction_fee' => 'transaction_fee']
            )
            ->join(['i' => $res->getTableName('sales_order_item')], 'i.item_id=main_table.order_item_id', $fields)
            ->join(['pi' => $poType == 'po' ? $res->getTableName('udropship_po_item') : $res->getTableName('sales_shipment_item')],
                   'i.item_id=pi.order_item_id and t.entity_id=pi.parent_id',
                   ['po_item_id' => 'entity_id', 'qty', 'commission_percent', 'base_cost', 'transaction_fee'])
            ->columns(['po_type' => new \Zend_Db_Expr("'$poType'")])
            ->where("t.udropship_status not in (?)", $excludeStatus)
            ->where("t.udropship_vendor=?", $this->getVendorId())
            ->where("r.order_id in (?)", $this->getOrderIds())
            ->order('main_table.entity_id asc')
            ->group('main_table.entity_id');

        return $refunds;
    }

    /**
     * @var
     */
    protected $_refundCollection;

    protected function _getRefundCollection()
    {
        $poType = $this->getVendor()->getStatementPoType();
        $excludeStatus = [\Unirgy\Dropship\Model\Source::SHIPMENT_STATUS_CANCELED];
        if ($poType == 'po') {
            $excludeStatus = [\Unirgy\DropshipPo\Model\Source::UDPO_STATUS_CANCELED];
        }
        $res = $this->_hlp->rHlp();
        $refunds = $this->_hlp->createObj('\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection');
        $refunds->addFieldToSelect(array('refund_increment_id'=>'increment_id','refund_id'=>'entity_id','refund_shipping_amount'=>'base_shipping_amount'));
        $refunds->getSelect()
        ->join(
            array('o'=>$res->getTableName('sales_order')),
            'o.entity_id=main_table.order_id',
            array()
        )
        ->join(
            array('tg'=>$poType == 'po' ? $res->getTableName('udropship_po_grid') : $res->getTableName('sales_shipment_grid')),
            'tg.order_id=o.entity_id',
            array('order_increment_id','po_increment_id'=>'increment_id','order_id','po_id'=>'entity_id','order_created_at','po_created_at'=>'created_at')
        )
        ->join(
            array('t'=>$poType == 'po' ? $res->getTableName('udropship_po') : $res->getTableName('sales_shipment')),
            't.entity_id=tg.entity_id',
            array('commission_percent','base_shipping_amount')
        )
        ->columns(array('po_type'=>new \Zend_Db_Expr("'$poType'")))
        ->where("t.udropship_status not in (?)", $excludeStatus)
        ->where("t.udropship_vendor=?", $this->getVendorId())
        ->where("r.order_id in (?)", $this->getOrderIds())
        ->order('main_table.entity_id asc');

        return $refunds;
    }
    public function processRefunds($pos, $subtotalBase)
    {
        $poItemsToLoad = array();
        $subtotalKey = $subtotalBase == 'cost' ? 'total_cost' : 'base_total_value';
        foreach ($pos as $po) {
            $id = $po->getPoId().'-'.$po->getRefundId();
            foreach (array($subtotalKey, 'base_tax_amount', 'base_discount_amount') as $k) {
                $poItemsToLoad[$id][$k] = true;
            }
        }
        if ($poItemsToLoad) {
            if ($pos instanceof DataCollection) {
                $samplePo = $pos->getFirstItem();
            } else {
                reset($pos);
                $samplePo = current($pos);
            }
            $refundIds = $poIds = array();
            foreach ($poItemsToLoad as $id=>$_dummy) {
                list($poId, $refundId) = explode('-', $id);
                $poIds[] = $poId;
                $refundIds[] = $refundId;
            }
            $poType = $samplePo->getPoType();

            if ($poType == 'po') {
                $poItems = $this->_hlp->createObj('\Unirgy\DropshipPo\Model\Po\Item')->getCollection();
            } else {
                $poItems = $this->_hlp->createObj('\Magento\Sales\Model\Order\Shipment\Item')->getCollection();
            }
            $fields = array('base_price', 'base_tax_amount', 'base_discount_amount', 'qty_ordered');
            $rFields = array('refund_qty'=>'qty');
            $fields[] = 'base_cost';
            $poItems->getSelect()
                ->join(array('i'=>$poItems->getTable('sales_order_item')), 'i.item_id=main_table.order_item_id', $fields)
                ->join(array('o'=>$poItems->getTable('sales_order')), 'i.order_id=o.entity_id', array())
                ->join(array('r'=>$poItems->getTable('sales_creditmemo')), 'r.order_id=o.entity_id', array('refund_id'=>'entity_id'))
                ->join(array('ri'=>$poItems->getTable('sales_creditmemo_item')), 'i.item_id=ri.order_item_id and r.entity_id=ri.parent_id', $rFields)
                ->where('main_table.order_item_id<>0 and main_table.parent_id in (?)', array_keys($poIds))
                ->where('r.entity_id in (?)', array_keys($refundIds))
                ->where('concat(main_table.parent_id,"-",r.entity_id) in (?)', array_keys($poItemsToLoad))
            ;

            $itemTotals = array();
            foreach ($poItems as $item) {
                $id = $item->getId();
                if (empty($itemTotals[$id])) {
                    $itemTotals[$id] = array($subtotalKey=>0, 'base_tax_amount'=>0, 'base_discount_amount'=>0);
                }
                $refundQty = min($item->getQty(),$item->getRefundQty());
                $itemTotals[$id][$subtotalKey] += $subtotalBase == 'cost' ? $item->getBaseCost()*$refundQty : $item->getBasePrice()*$refundQty;
                $iTax = $item->getBaseTaxAmount()/max(1,$item->getQtyOrdered());
                $iTax = $iTax*$refundQty;
                $iDiscount = $item->getBaseDiscountAmount()/max(1,$item->getQtyOrdered());
                $iDiscount = $iDiscount*$refundQty;
                $itemTotals[$id]['base_tax_amount'] += $iTax;
                $itemTotals[$id]['base_discount_amount'] += $iDiscount;
            }
            foreach ($itemTotals as $id=>$total) {
                foreach ($total as $k=>$v) {
                    if (!empty($poItemsToLoad[$id][$k])) {
                        $pos->getItemById($id)->setData($k, $v);
                    }
                }
            }
        }
    }

    /**
     * @param bool $reload
     * @return ItemCollection
     */
    public function getRefundCollection($reload = false)
    {
        if (is_null($this->_refundCollection) || $reload) {
            if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
                $this->_refundCollection = $this->_getRefundCollectionTierCom();
            } else {
                $this->_refundCollection = $this->_getRefundCollection();
                $this->processRefunds($this->_refundCollection, $this->getVendor()->getStatementSubtotalBase());
            }
        }
        return $this->_refundCollection;
    }

    public function getOrderIds()
    {
        $orders = $this->getOrders();
        $oIds = [];
        foreach ($orders as $order) {
            $oIds[] = $order['order_id'];
        }
        return $oIds;
    }

    protected $_roundingDeltas = [];

    protected function _deltaRound($price, $id)
    {
        if ($price) {
            $delta = isset($this->_roundingDeltas[$id]) ? $this->_roundingDeltas[$id] : 0;
            $price += $delta;
            $this->_roundingDeltas[$id] = $price - round($price, 2);
            $price = round($price, 2);
        }
        return $price;
    }

    protected function _compactTotals()
    {
        parent::_compactTotals();
        if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
            $ordersCnt = [];
            foreach ($this->getOrders() as $order) {
                $ordersCnt[$order['po_id']] = 1;
            }
            $this->setTotalOrders(array_sum($ordersCnt));
        }
        return $this;
    }

    protected function _addPo($po)
    {
        $hlp = $this->_hlp;
        $ptHlp = $this->_payoutHlp;
        $vendor = $this->getVendor();

        $this->initTotals();

        $hlp->collectPoAdjustments([$po]);
        
        $sId = $po->getId();
        $order = $this->initOrder($po);
    
        $this->_eventManager->dispatch('udropship_vendor_payout_row', [
            'payout'   => $this,
            'po' => $po,
            'order'    => &$order
        ]);
        
        $order = $this->calculateOrder($order);
        $this->_totals_amount = $this->accumulateOrder($order, $this->_totals_amount);

        $poId = $po->getId() ? $po->getId() : spl_object_hash($po);
        $this->_orders[$poId] = $order;

        return $this;
    }
    
    public function finishPayout()
    {
        $this->finishStatement();
        $this->_calculateRefundDue();
        return $this;
    }

    protected function _calculateRefundDue()
    {
        $this->initTotals();
        $this->_totals_amount['total_reversed'] = $this->getTotalReversed();
        $this->_totals_amount['refund_due']  = $this->_totals_amount['total_refund'] - $this->_totals_amount['total_reversed'];
        $this->setTotalReversed($this->_totals_amount['total_reversed']);
        $this->setTotalRefund($this->_totals_amount['total_refund']);
        $this->setRefundDue($this->_totals_amount['refund_due']);
        return $this;
    }

    protected function _getEmptyTotals($format=false)
    {
        return $this->_payoutHlp->getEmptyPayoutTotals($format);
    }
    
    protected function _getEmptyCalcTotals($format=false)
    {
        return $this->_payoutHlp->getEmptyPayoutCalcTotals($format);
    }
    
    public function getAdjustmentClass()
    {
        if (is_null($this->_adjustmentClass)) {
            $this->_adjustmentClass = '\Unirgy\DropshipPayout\Model\Payout\Adjustment';
        }
        return $this->_adjustmentClass;
    }
    
    public function pay()
    {
        $this->_payoutHlpPr->payoutPay($this);
        return $this;
    }
    
    public function afterPay()
    {
        $this->markDueAmountsPaid($this);
        $this->addMessage(__('Successfully paid'), self::STATUS_PAID)->setIsJustPaid(true);
        $this->initTotals();
        foreach ($this->_orders as &$order) {
            $order['paid'] = true;
        }
        unset($order);
        if ($this->getPayoutType() == self::TYPE_STATEMENT
            && ($statement = $this->_statementFactory->create()->load($this->getStatementId(), 'statement_id'))
            && $statement->getId()
        ) {
            $statement->completePayout($this);
        }
        return $this;
    }

    public function refreshRefunds()
    {
        $this->_resetRefunds();
        $this->_resetRefundTotals();
        $this->setTotalRefund(0);
        if ($this->_hlp->isModuleActive('Unirgy_DropshipTierCommission')) {
            $this->_refreshRefundsTierCom();
        } else {
            $this->_refreshRefunds();
        }
        $this->finishPayout();
        return $this;
    }

    protected function _refreshRefundsTierCom()
    {
        $refunds = $this->getRefundCollection();

        $totals_amount = $this->_totals_amount;
        $processedRefundIds = [];
        foreach ($refunds as $id => $refund) {
            if ($refund->getOrderItem()->isDummy(true)) continue;
            $refundRow = $this->initRefundItem($refund, in_array($refund->getRefundId(), $processedRefundIds));
            $processedRefundIds[] = $refund->getRefundId();

            $this->_eventManager->dispatch('udropship_vendor_statement_refund_item_row', [
                'statement' => $this,
                'refund' => $refund,
                'refund_row' => &$refundRow
            ]);

            $refundRow = $this->calculateRefund($refundRow);
            $totals_amount = $this->accumulateRefund($refundRow, $totals_amount);

            $this->_refunds[$id] = $refundRow;
        }
        $this->_totals_amount = $totals_amount;
    }

    protected function _refreshRefunds()
    {
        $refunds = $this->getRefundCollection();

        $totals_amount = $this->_totals_amount;
        foreach ($refunds as $id=>$refund) {

            $refundRow = $this->initRefund($refund);

            $this->_eventManager->dispatch('udropship_vendor_statement_refund_row', array(
                'statement'=>$this,
                'refund'=>$refund,
                'refund_row'=>&$refundRow
            ));

            $refundRow = $this->calculateRefund($refundRow);

            $this->_refunds[$id] = $refundRow;
        }
        $this->_totals_amount = $totals_amount;
    }

    public function reverse()
    {
        $this->_payoutHlpPr->payoutReverse($this);
        return $this;
    }

    public function afterReverse()
    {
        $this->markRefundAmountsReverse($this);
        $this->addMessage(__('Successfully Reversed'), self::STATUS_REVERSED)->setIsJustReversed(true);
        $this->initTotals();
        foreach ($this->_refunds as &$refund) {
            $refund['reversed'] = true;
        }
        unset($refund);
        return $this;
    }

    public function markRefundAmountsReverse($obj)
    {
        $this->setTotalReversed($this->getTotalReversed()+$obj->getRefundDue());
        $this->finishPayout();
        return $this;
    }
    
    public function addMessage($message, $status=null)
    {
        $ei = sprintf("%s\n[%s] %s",
            $this->getErrorInfo(),
            $this->_hlp->now(),
            $message
        );
        $this->setErrorInfo(ltrim($ei));
        if (!empty($status)
            && $this->getPayoutStatus() != self::STATUS_PAID
            && ($this->getPayoutStatus() != self::STATUS_PAYPAL_IPN || $status == self::STATUS_PAID)
        ) {
            $this->setPayoutStatus($status);
        }
        return $this;
    }
    
    public function setPayoutStatus($status)
    {
        if ($status==self::STATUS_HOLD) {
            $this->setData('before_hold_status', $this->getPayoutStatus());
        }
        return $this->setData('payout_status', $status);
    }
    
    public function cancel()
    {
        $this->setCleanPayoutFlag(true)->setPayoutStatus(self::STATUS_CANCELED)->save();
        return $this;
    }

    public function getMethodInstance()
    {
        $pmNode = $this->_hlp->config()->getPayoutMethod($this->getPayoutMethod());
        if (!$pmNode) {
            return false;
        }
        $methodClass = $pmNode['model'];
        if (!class_exists($methodClass)) {
            return false;
        }
        return $this->_hlp->createObj($methodClass);
    }

    public function isReversible()
    {
        if (!$this->getPayoutMethod()) throw new \Exception(__("Empty payout method"));
        $pmNode = $this->_hlp->config()->getPayoutMethod($this->getPayoutMethod());
        if (!$pmNode) throw new \Exception(__("Unknown payout method: '%1'", $this->getPayoutMethod()));
        $methodClass = $pmNode['model'];
        if (!class_exists($methodClass)) throw new \Exception(__("Can't find payout method class"));
        $method = $this->_hlp->createObj($methodClass);
        return $method->isReversible();
    }
}
