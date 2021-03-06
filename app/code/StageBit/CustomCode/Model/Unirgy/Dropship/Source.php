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
 * @package    \Unirgy\Dropship
 * @copyright  Copyright (c) 2015-2016 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace StageBit\CustomCode\Model\Unirgy\Dropship;

use \Unirgy\Dropship\Model\Source as DropshipSource;

class Source extends DropshipSource
{
    const VENDOR_STATUS_ACTIVE    = 'A';
    const VENDOR_STATUS_PENDING    = 'Z';
    const VENDOR_STATUS_INACTIVE  = 'I';
    const VENDOR_STATUS_DISABLED  = 'D';
    const VENDOR_STATUS_REJECTED  = 'R';
    const VENDOR_STATUS_PENDINGMEMBER  = 'P';
    const VENDOR_STATUS_SUSPENDEDMEMBER  = 'S';
    const VENDOR_STATUS_CANCELEDMEMBER  = 'C';
    const VENDOR_STATUS_EXPIREDMEMBER  = 'e';
    const VENDOR_STATUS_REJECTED_BY_VENDOR  = 'X';
    const VENDOR_STATUS_APPROVED_BY_VENDOR  = 'V';
    const VENDOR_STATUS_WAITING_BY_VENDOR  = 'W';


    const ORDER_STATUS_PENDING  = 0;
    const ORDER_STATUS_NOTIFIED = 1;
    const ORDER_STATUS_CANCELED = 2;

    const SHIPMENT_STATUS_PENDING    = 0;
    const SHIPMENT_STATUS_EXPORTED   = 10;
    const SHIPMENT_STATUS_RETURNED   = 11;
    const SHIPMENT_STATUS_ACK        = 9;
    const SHIPMENT_STATUS_BACKORDER  = 5;
    const SHIPMENT_STATUS_ONHOLD     = 4;
    const SHIPMENT_STATUS_READY      = 3;
    const SHIPMENT_STATUS_PENDPICKUP = 8;
    const SHIPMENT_STATUS_PARTIAL    = 2;
    const SHIPMENT_STATUS_SHIPPED    = 1;
    const SHIPMENT_STATUS_CANCELED   = 6;
    const SHIPMENT_STATUS_DELIVERED  = 7;

    const TRACK_STATUS_PENDING   = 'P';
    const TRACK_STATUS_CANCELED  = 'C';
    const TRACK_STATUS_READY     = 'R';
    const TRACK_STATUS_SHIPPED   = 'S';
    const TRACK_STATUS_DELIVERED = 'D';

    const AUTO_SHIPMENT_COMPLETE_NO  = 0;
    const AUTO_SHIPMENT_COMPLETE_ALL = 1;
    const AUTO_SHIPMENT_COMPLETE_ANY = 2;

    const NOTIFYON_TRACK = 1;
    const NOTIFYON_SHIPMENT = 2;

    const HANDLING_SYSTEM = 0;
    const HANDLING_SIMPLE = 1;
    const HANDLING_ADVANCED = 2;

    const CALCULATE_RATES_DEFAULT = 0;
    const CALCULATE_RATES_ROW = 1;
    const CALCULATE_RATES_ITEM = 2;

    protected $_carriers = array();
    protected $_methods = array();
    protected $_vendors = array();
    protected $_taxRegions = array();
    protected $_visiblePreferences = array();

    public function toOptionHash($selector=false)
    {
        $hlp = $this->_hlp;

        $options = array();

        $selectorLabel = '* Please select';

        switch ($this->getPath()) {

            case 'udropship/customer/notify_on_tracking':
            case 'udropship/customer/notify_on_shipment':
            case 'billing_use_shipping':
            case 'yesno':
                $options = array(
                    1 => __('Yes'),
                    0 => __('No'),
                );
                break;

            case 'yesno_useconfig':
                $options = array(
                    -1 => __('Use config'),
                    1 => __('Yes'),
                    0 => __('No'),
                );
                break;

            case 'udropship/customer/notify_on':
                $options = array(
                    0 => __('Disable'),
                    1 => __('When Tracking ID is added'),
                    2 => __('When Vendor Shipment is complete'),
                    #3 => __('When Order is completely shipped'),
                );
                break;

            case 'udropship/customer/poll_tracking':
                // not used
                break;

            case 'udropship/customer/estimate_error_action':
                $options = array(
                    'fail' => __('Fail estimate and show the error'),
                    'skip' => __('Skip failed carrier call and show prices without'),
                );
                break;

            case 'udropship/stock/availability':
            case 'udropship/stock/reassign_availability':
                $options = array();
                $methods = $this->udropshipConfig->getAvailabilityMethod();
                foreach ($methods as $code=>$method) {
                    if (!@$method['active'] || !@$method['label']) {
                        continue;
                    }
                    $options[$code] = (string)@$method['label'];
                }
                break;

            case 'carriers/udropship/free_method':
                $selector = false;
                $options = $this->getMethods(true, true);
                break;

            case 'udropship/vendor/label_carrier_allow_always':
            case 'carriers':
                $options = $this->getCarriers();
                if (in_array($this->getPath(), array('udropship/vendor/label_carrier_allow_always'))) {
                    $newOptions = array();
                    foreach ($options as $cCode=>$cTitle) {
                        if (in_array($cCode, array('fedex','fedexsoap','ups','usps'))) {
                            $newOptions[$cCode] = $cTitle;
                        }
                    }
                    $options = $newOptions;
                }
                break;

            case 'active_vendors':
                $options = $this->getVendors();
                break;

            case 'allvendors':
                $options = $this->getVendors(true);
                break;

            case 'vendors':
            case 'udropship/vendor/local_vendor':
                $options = $this->getVendors(true);
                break;

            case 'udropship/vendor/make_available_to_dropship':
                $selector = false;
                $options = $this->_orderConfig->getStatuses();
                break;

            case 'udropship/vendor/change_order_status_after_po':
                $selector = false;
                $options = $this->_orderConfig->getStatuses();
                $unsetStatuses = array_unique(array_merge(
                    array_keys(
                        $this->_orderConfig->getStateStatuses(\Magento\Sales\Model\Order::STATE_CLOSED)
                    ),
                    array_keys(
                        $this->_orderConfig->getStateStatuses(\Magento\Sales\Model\Order::STATE_COMPLETE)
                    )
                ));
                foreach ($unsetStatuses as $_st) {
                    unset($options[$_st]);
                }
                $options = array('' => __('* Do not change')) + $options;
                break;

            case 'udropship/vendor/visible_preferences':
                $selector = false;
                $options = $this->getVendorVisiblePreferences();
                break;

            case 'udropship/batch/export_on_po_status':
            case 'udropship/vendor/default_shipment_status':
            case 'udropship/vendor/restrict_shipment_status':
            case 'udropship/purchase_order/autoinvoice_shipment_statuses':
            case 'udropship/pocombine/notify_on_status':
            case 'udropship/pocombine/after_notify_status':
            case 'udropship/vendor_rating/ready_status':
            case 'udropship/statement/statement_shipment_status':
            case 'batch_export_orders_export_on_po_status':
            case 'statement_shipment_status':
            case 'shipment_statuses':
            case 'initial_shipment_status':
            case 'vendor_po_grid_status_filter':
                $options = array(
                    self::SHIPMENT_STATUS_PENDING   => __('Pending'),
                    self::SHIPMENT_STATUS_EXPORTED  => __('Exported'),
                    self::SHIPMENT_STATUS_ACK       => __('Acknowledged'),
                    self::SHIPMENT_STATUS_BACKORDER => __('Backorder'),
                    self::SHIPMENT_STATUS_ONHOLD    => __('On Hold'),
                    self::SHIPMENT_STATUS_READY     => __('Ready to Ship'),
                    self::SHIPMENT_STATUS_PENDPICKUP => __('Pending Pickup'),
                    self::SHIPMENT_STATUS_PARTIAL   => __('Label(s) printed'),
                    self::SHIPMENT_STATUS_SHIPPED   => __('Shipped'),
                    self::SHIPMENT_STATUS_DELIVERED => __('Delivered'),
                    self::SHIPMENT_STATUS_CANCELED  => __('Canceled'),
                    self::SHIPMENT_STATUS_RETURNED  => __('Returned'),
                );
                if (in_array($this->getPath(), array('initial_shipment_status','statement_shipment_status','batch_export_orders_export_on_po_status'))) {
                    $options = array('999' => __('* Default (global setting)')) + $options;
                }
                break;

            case 'udropship/vendor/vendor_notification_field':
                $options = $this->getVendorVisiblePreferences();
                array_unshift($options, array('value'=>'', 'label'=>__('* Use Vendor Email')));
                break;

            case 'udropship/vendor/auto_shipment_complete':
                $options = array(
                    self::AUTO_SHIPMENT_COMPLETE_NO => __('No'),
                    self::AUTO_SHIPMENT_COMPLETE_ALL => __('When all items are shipped'),
                    self::AUTO_SHIPMENT_COMPLETE_ANY => __('At least one item shipped'),
                );
                break;

            case 'udropship/vendor/pdf_use_font':
                $options = array(
                    '' => __('* Magento Bundled Fonts'),
                    'TIMES' => __('Times New Roman'),
                    'HELVETICA' => __('Helvetica'),
                    'COURIER' => __('Courier'),
                );
                break;

            case 'udropship/customer/estimate_total_method':
                $options = array(
                    '' => __('Sum of order vendors estimates'),
                    'max' => __('Maximum of order vendors estimates'),
                );
                break;

            case 'udropship/misc/mail_transport':
                $options = array(
                    '' => __('* Automatic'),
                    'sendmail' => __('Sendmail'),
                );
                break;

            case 'vendor_statuses':
                $options = array(
                    self::VENDOR_STATUS_PENDING => __('Pending'),
                    self::VENDOR_STATUS_INACTIVE => __('Inactive'),
                    self::VENDOR_STATUS_ACTIVE   => __('Active'),
                    self::VENDOR_STATUS_DISABLED  => __('Disabled'),
                    self::VENDOR_STATUS_REJECTED_BY_VENDOR => __('Vendor rejected'),
                    self::VENDOR_STATUS_APPROVED_BY_VENDOR  => __('Vendor approved'),
                    self::VENDOR_STATUS_WAITING_BY_VENDOR  => __('Sent email for approval'),

                );
                if ($this->_hlp->isModuleActive('Unirgy_DropshipMicrositePro')) {
                    $options[self::VENDOR_STATUS_REJECTED] = __('Rejected');
                }
                if ($this->_hlp->isModuleActive('Unirgy_DropshipVendorMembership')) {
                    $options[self::VENDOR_STATUS_PENDINGMEMBER] = __('Pending Membership');
                    $options[self::VENDOR_STATUS_SUSPENDEDMEMBER] = __('Suspended Membership');
                    $options[self::VENDOR_STATUS_CANCELEDMEMBER] = __('Canceled Membership');
                    $options[self::VENDOR_STATUS_EXPIREDMEMBER] = __('Expired Membership');
                }
                break;

            case 'new_order_notifications':
                $options = array(
                    '' => __('* No notification'),
                    '1' => __('* Email notification'),
                );
                $config = $this->_hlp->config()->getNotificationMethod();
                foreach ($config as $code=>$node) {
                    if (!@$node['label']) {
                        continue;
                    }
                    $options[$code] = __((string)$node['label']);
                }
                asort($options);
                break;

            case 'udropship/statement/statement_usage':
                $options = array(
                    'payout' => __('Payout'),
                    'invoice' => __('Invoice'),
                );
                break;
            case 'statement_withhold_totals':
                $options = array(
                    '999' => __('* Default (global setting)'),
                    'tax' => __('Tax'),
                    'shipping' => __('Shipping'),
                    'handling' => __('Handling'),
                );
                break;

            case 'udropship/statement/statement_shipping_in_payout':
            case 'udropship/statement/statement_tax_in_payout':
            case 'udropship/statement/statement_discount_in_payout':
            case 'statement_shipping_in_payout':
            case 'statement_tax_in_payout':
            case 'statement_discount_in_payout':
                $options = array(
                    'include' => __('Include'),
                    'exclude_show' => __('Exclude but Show'),
                    'exclude_hide' => __('Exclude and Hide'),
                );
                if (in_array($this->getPath(), array('statement_shipping_in_payout','statement_tax_in_payout','statement_discount_in_payout'))) {
                    $options = array('999' => __('* Default (global setting)')) + $options;
                }
                break;

            case 'udropship/statement/apply_commission_on_discount':
            case 'apply_commission_on_discount':
            case 'udropship/statement/apply_commission_on_tax':
            case 'apply_commission_on_tax':
            case 'udropship/statement/apply_commission_on_shipping':
            case 'apply_commission_on_shipping':
            case 'udropship/statement/shipping_tax_in_shipping':
            case 'shipping_tax_in_shipping':
                $options = array(
                    1 => __('Yes'),
                    0 => __('No'),
                );
                if (in_array($this->getPath(), array('apply_commission_on_tax','apply_commission_on_shipping','apply_commission_on_discount','shipping_tax_in_shipping'))) {
                    $options = array('999' => __('* Default (global setting)')) + $options;
                }
                break;

            case 'stockcheck_method':
                $options = array(
                    '' => __('* Local database'),
                    //'1' => __('Always in stock'),
                );
                $config = $this->udropshipConfig->getStockcheckMethod();
                foreach ($config as $code=>$node) {
                    if (!@$node['label']) {
                        continue;
                    }
                    $options[$code] = __((string)@$node['label']);
                }
                asort($options);
                break;

            case 'tax_regions':
                $options = $this->getTaxRegions();
                break;


            case 'handling_integration':
                $options = array(
                    'bypass' => __('Use system configured handling fee only'),
                    'replace' => __('Use vendor configured handling fee only'),
                    'add' => __('Add vendor handling fee to the system handling fee'),
                );
                break;

            case 'udropship_label/label/poll_tracking':
            case 'poll_tracking':
                $options = array(
                    '-' => __('* Disable tracking API polling'),
                    '' => __('* Use label carrier API if available'),
                );
                $trackConfig = $this->_hlp->config()->getTrackApi();
                foreach ($trackConfig as $code=>$node) {
                    if (@$node['disabled'] || !@$node['label']) {
                        continue;
                    }
                    $options[$code] = (string)$node['label'];
                }
                break;

            case 'udropship_label/label/label_type':
            case 'label_type':
                $options = array(
                    ''=>__('No label printing'),
                    'PDF'=>__('PDF'),
                    'EPL'=>__('EPL'),
//                'ZPL'=>__('ZPL'),
                );
                break;
            case 'udropship/label/label_size':
                $options = array(
                    '4X6'=>__('4X6'),
                );
                break;

            case 'udropship_label/pdf/pdf_label_rotate':
            case 'pdf_label_rotate':
                $options = array(
                    '0'=>'None',
                    '90'=>'90 degrees',
                    '180'=>'180 degrees',
                    '270'=>'270 degrees',
                );
                break;

            case 'udropship_label/endicia/endicia_label_type':
            case 'endicia_label_type':
                $options = array(
                    'Default'=>'Default',
                    'CertifiedMail'=>'CertifiedMail',
                    'DestinationConfirm'=>'DestinationConfirm',
                    //'International'=>'International',
                );
                break;

            case 'udropship_label/endicia/endicia_label_size':
            case 'endicia_label_size':
                $options = array(
                    '4X6'=>'4X6',
                    '4X5'=>'4X5',
                    '4X4.5'=>'4X4.5',
                    'DocTab'=>'DocTab',
                    '6x4'=>'6x4',
                );
                break;

            case 'udropship_label/endicia/endicia_mail_class':
            case 'endicia_mail_class':
                $options = array(
                    'FirstClassMailInternational'=>'First-Class Mail International',
                    'PriorityMailInternational'=>'Priority Mail International',
                    'ExpressMailInternational'=>'Express Mail International',
                    'Express'=>'Express Mail',
                    'First'=>'First-Class Mail',
                    'LibraryMail'=>'Library Mail',
                    'MediaMail'=>'Media Mail',
                    'ParcelPost'=>'Parcel Post',
                    'ParcelSelect'=>'Parcel Select',
                    'Priority'=>'Priority Mail',
                );
                break;

            case 'udropship_label/endicia/endicia_mailpiece_shape':
            case 'endicia_mailpiece_shape':
                $options = array(
                    'Card'=>'Card',
                    'Letter'=>'Letter',
                    'Flat'=>'Flat',
                    'Parcel'=>'Parcel',
                    'FlatRateBox'=>'FlatRateBox',
                    'FlatRateEnvelope'=>'FlatRateEnvelope',
                    'IrregularParcel'=>'IrregularParcel',
                    'LargeFlatRateBox'=>'LargeFlatRateBox',
                    'LargeParcel'=>'LargeParcel',
                    'OversizedParcel'=>'OversizedParcel',
                    'SmallFlatRateBox'=>'SmallFlatRateBox',
                );
                break;

            case 'udropship_label/endicia/endicia_insured_mail':
            case 'endicia_insured_mail':
                $options = array(
                    'OFF' => 'No Insurance',
                    'ON'  => 'USPS Insurance',
                    'UspsOnline' => 'USPS Online Insurance',
                    'Endicia' => 'Endicia Insurance',
                );
                break;

            case 'udropship_label/endicia/endicia_customs_form_type':
            case 'endicia_customs_form_type':
                $options = array(
                    'Form2976' => 'Form 2976 (same as CN22)',
                    'Form2976A' => 'Form 2976A (same as CP72)',
                );
                break;

            case 'weight_units':
                $options = array(
                    'LB'=>'Pounds (lb)',
                    'KG'=>'Kilograms (kg)',
                );
                break;

            case 'udropship_label/label/dimension_units':
            case 'dimension_units':
                $options = array(
                    'IN'=>'Inch',
                    'CM'=>'Centimeter',
                );
                break;

            case 'udropship_label/pdf/pdf_page_size':
            case 'pdf_page_size':
                $options = array(
                    \Zend_Pdf_Page::SIZE_LETTER => 'Letter',
                );
                break;

            case 'udropship_label/ups/ups_pickup':
            case 'ups_pickup':
                $options = array(
                    '' => '* Default',
                    '01' => 'Daily Pickup',
                    '03' => 'Customer Counter',
                    '06' => 'One Time Pickup',
                    '07' => 'On Call Air',
                    '11' => 'Suggested Retail',
                    '19' => 'Letter Center',
                    '20' => 'Air Service Center',
                );
                break;

            case 'udropship_label/ups/ups_container':
            case 'ups_container':
                $options = array(
                    '' => '* Default',
                    '00' => 'Customer Packaging',
                    '01' => 'UPS Letter Envelope',
                    '03' => 'UPS Tube',
                    '21' => 'UPS Express Box',
                    '24' => 'UPS Worldwide 25 kilo',
                    '25' => 'UPS Worldwide 10 kilo',
                );
                break;

            case 'udropship_label/ups/ups_dest_type':
            case 'ups_dest_type':
                $options = array(
                    '' => '* Default',
                    '01' => 'Residential',
                    '02' => 'Commercial',
                );
                break;

            case 'udropship_label/ups/ups_delivery_confirmation':
            case 'ups_delivery_confirmation':
                $options = array(
                    '' => 'No Delivery Confirmation',
                    '1' => 'Delivery Confirmation',
                    '2' => 'Delivery Confirmation Signature Required',
                    '3' => 'Delivery Confirmation Adult Signature Required',
                );
                break;

            case 'udropship_label/ups/ups_shipping_method_combined':
            case 'ups_shipping_method_combined':
                $options = array(
                    'UPS CGI' => array(
                        '1DM'    => __('Next Day Air Early AM'),
                        '1DML'   => __('Next Day Air Early AM Letter'),
                        '1DA'    => __('Next Day Air'),
                        '1DAL'   => __('Next Day Air Letter'),
                        '1DAPI'  => __('Next Day Air Intra (Puerto Rico)'),
                        '1DP'    => __('Next Day Air Saver'),
                        '1DPL'   => __('Next Day Air Saver Letter'),
                        '2DM'    => __('2nd Day Air AM'),
                        '2DML'   => __('2nd Day Air AM Letter'),
                        '2DA'    => __('2nd Day Air'),
                        '2DAL'   => __('2nd Day Air Letter'),
                        '3DS'    => __('3 Day Select'),
                        'GND'    => __('Ground'),
                        'GNDCOM' => __('Ground Commercial'),
                        'GNDRES' => __('Ground Residential'),
                        'STD'    => __('Canada Standard'),
                        'XPR'    => __('Worldwide Express'),
                        'WXS'    => __('Worldwide Express Saver'),
                        'XPRL'   => __('Worldwide Express Letter'),
                        'XDM'    => __('Worldwide Express Plus'),
                        'XDML'   => __('Worldwide Express Plus Letter'),
                        'XPD'    => __('Worldwide Expedited'),
                    ),
                    'UPS XML' => array(
                        '01' => __('UPS Next Day Air'),
                        '02' => __('UPS Second Day Air'),
                        '03' => __('UPS Ground'),
                        '07' => __('UPS Worldwide Express'),
                        '08' => __('UPS Worldwide Expedited'),
                        '11' => __('UPS Standard'),
                        '12' => __('UPS Three-Day Select'),
                        '13' => __('UPS Next Day Air Saver'),
                        '14' => __('UPS Next Day Air Early A.M.'),
                        '54' => __('UPS Worldwide Express Plus'),
                        '59' => __('UPS Second Day Air A.M.'),
                        '65' => __('UPS Saver'),

                        '82' => __('UPS Today Standard'),
                        '83' => __('UPS Today Dedicated Courrier'),
                        '84' => __('UPS Today Intercity'),
                        '85' => __('UPS Today Express'),
                        '86' => __('UPS Today Express Saver'),
                    ),
                );
                break;

            case 'udropship_label/fedex/fedex_payment_type':
            case 'fedex_payment_type':
                $options = array(
                    'SENDER' => __('Sender'),
                    'THIRD_PARTY' => __('Third Party'),
                );
                break;

            case 'udropship_label/fedex/fedex_dropoff_type':
            case 'fedex_dropoff_type':
                $options = array(
                    'REGULAR_PICKUP' => __('Regular Pickup'),
                    'REQUEST_COURIER' => __('Request Courier'),
                    'DROP_BOX' => __('Drop Box'),
                    'BUSINESS_SERVICE_CENTER' => __('Business Service Center'),
                    'STATION' => __('Station'),
                );
                break;

            case 'udropship_label/fedex/fedex_service_type':
            case 'fedex_service_type':
                break;

            case 'udropship_label/fedex/fedex_packaging_type':
            case 'fedex_packaging_type':
                break;

            case 'udropship_label/fedex/fedex_label_stock_type':
            case 'fedex_label_stock_type':
                $options = array(
                    'PAPER_4X6' => __('PDF: Paper 4x6'),
                    'PAPER_4X8' => __('PDF: Paper 4x8'),
                    'PAPER_4X9' => __('PDF: Paper 4x9'),
                    'PAPER_7X4.75' => __('PDF: Paper 7x4.75'),
                    'PAPER_8.5\X11\BOTTOM\HALF\LABEL' => __('PDF: Paper 8.5x11 Bottom Half Label'),
                    'PAPER_8.5\X11\TOP\HALF\LABEL' => __('PDF: Paper 8.5x11 Top Half Label'),

                    'STOCK_4X6' => __('EPL: Stock 4x6'),
                    'STOCK_4X6.75_LEADING_DOC_TAB' => __('EPL: Stock 4x6.75 Leading Doc Tab'),
                    'STOCK_4X6.75_TRAILING_DOC_TAB' => __('EPL: Stock 4x6.75 Trailing Doc Tab'),
                    'STOCK_4X8' => __('EPL: Stock 4x8'),
                    'STOCK_4X9_LEADING_DOC_TAB' => __('EPL: Stock 4x9 Leading Doc Tab'),
                    'STOCK_4X9_TRAILING_DOC_TAB' => __('EPL: Stock 4x9 Trailing Doc Tab'),
                );
                break;

            case 'udropship_label/fedex/fedex_signature_option':
            case 'fedex_signature_option':
                $options = array(
                    'NO_SIGNATURE_REQUIRED' => 'No Signature Required',
                    'SERVICE_DEFAULT' => 'Default Appropriate Signature Option',
                    'DIRECT' => 'Direct',
                    'INDIRECT' => 'Indirect',
                    'ADULT' => 'Adult',
                );
                break;

            case 'udropship_label/fedex/fedex_notify_on':
            case 'fedex_notify_on':
                $options = array(
                    ''  => '* None *',
                    'shipment'  => 'Shipment',
                    'exception' => 'Exception',
                    'delivery'  => 'Delivery',
                );
                break;

            case 'udropship/vendor/reassign_available_shipping':
                $options = array(
                    'all' => __('All'),
                    'order' => __('Limit by order shipping method'),
                );
                break;

            case 'udropship/statement/statement_po_type':
            case 'statement_po_type':
                $options = array(
                    'shipment' => __('Shipment'),
                );
                if ($hlp->isUdpoActive()) {
                    $options['po'] = __('Purchase Order');
                }
                if (in_array($this->getPath(), array('statement_po_type'))) {
                    $options = array('999' => __('* Default (global setting)')) + $options;
                }
                break;

            case 'udropship/statement/statement_subtotal_base':
            case 'statement_subtotal_base':
                $options = array(
                    'price' => __('Price'),
                    'cost'  => __('Cost'),
                );
                if (in_array($this->getPath(), array('statement_subtotal_base'))) {
                    $options = array('999' => __('* Default (global setting)')) + $options;
                }
                break;

            case 'vendor_po_grid_sortby':
                $options = array(
                    'order_increment_id' => __('Order ID'),
                    'order_date' => __('Order Date'),
                    'shipment_date' => __('Available for Shipping Date'),
                    'shipping_method' => __('Delivery Method'),
                    'udropship_status' => __('Shipping Status'),
                );
                break;

            case 'vendor_po_grid_sortdir':
                $options = array(
                    'asc' => __('Ascending'),
                    'desc' => __('Descending'),
                );
                break;

            case 'shipping_extra_charge_type':
                $options = array(
                    'fixed' => __('Fixed'),
                    'shipping_percent' => __('Percent of shipping amount'),
                    'subtotal_percent' => __('Percent of vendor subtotal'),
                );
                break;

            case 'udropship/customer/vendor_enable_disable_action':
                $options = array(
                    'noaction' => __('No action'),
                    'enable_disable' => __('Enable / Disable vendor products'),
                );
                break;

            case 'use_handling_fee':
                $options = array(
                    self::HANDLING_SYSTEM => __('* Default System Rules'),
                    self::HANDLING_SIMPLE => __('Simple Custom Rules'),
                    self::HANDLING_ADVANCED => __('Advanced Custom Rules'),
                );
                break;

            case 'handling_rule':
                $options = array(
                    'price'  => __('Total Price'),
                    'cost'   => __('Total Cost'),
                    'qty'    => __('Qty'),
                    'line'   => __('Line Number'),
                    'weight' => __('Weight'),
                );
                break;

            case 'product_calculate_rates':
                $options = array(
                    self::CALCULATE_RATES_DEFAULT => __('Vendor Package'),
                    self::CALCULATE_RATES_ROW      => __('Row Separate Rate'),
                    self::CALCULATE_RATES_ITEM     => __('Item Separate Rate'),
                );
                break;

            case 'udropship/customer/vendor_delete_action':
                $options = array(
                    'noaction' => __('No action'),
                    'assign_local_enabled' => __('Assign to local vendor and leave vendor products enabled'),
                    'assign_local_disable' => __('Assign to local vendor and disable vendor products'),
                    'delete' => __('Delete vendor products'),
                );
                break;

            case 'allowed_countries':
                /* @var \Magento\Directory\Model\ResourceModel\Country\Collection $countries */
                $countries = $this->_hlp->createObj('\Magento\Directory\Model\ResourceModel\Country\Collection');
                $options = $countries->loadData()->toOptionArray(false);
                array_unshift($options, array('value'=>'*', 'label'=> __('* All Countries')));
                break;

            case 'billing_region_id':
            case 'region_id':
                $selectorLabel = 'Please select region, state or province';
                $options = array(
                );
                break;
            case 'billing_country_id':
            case 'country_id':
                $selectorLabel = 'Please select region, state or province';
                $options = array(
                );
                break;
            case 'carrier_code':
            case 'registration_carriers':
                $options = array();
                break;
            case 'agree_terms_conditions':
                $options = array(
                    '1' => __('Yes Agree')
                );
                break;

            case 'udropship_label/custom/your_role':
            case 'your_role':
                $options = array(
                    '' => 'Please select',
                    'owner'  => 'Owner',
                    'vp_director' => 'VP-Director',
                    'sales'  => 'Sales',
                    'admin'  => 'Admin'
                );
                break;

            case 'udropship_label/custom/company_employee':
            case 'company_employee':
                $options = array(
                    '' => 'Please select',
                    '1'  => '1',
                    '2-5' => '2 - 5',
                    '6-10'  => '6 - 10',
                    '11-20'  => '11 - 20',
                    '21-35'  => '21 - 35',
                    '36-50' => '36 - 50',
                    'more_than_50' => 'More than 50'
                );
                break;

            case 'udropship_label/custom/company_type':
            case 'company_type':
                $options = array(
                    '' => 'Please select',
                    'yes'  => 'Yes',
                    'no' => 'No'
                );
                break;

            case 'udropship_label/custom_step2/product_categories':
            case 'product_categories':
                $category =  $this->_hlp->createObj('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
                $category->addAttributeToFilter('level', 2);
                $category_data = $category->getData();

                $options_array = array();
                $options_array[""] = 'Pick relevant categories:';
                foreach($category_data as $cat_data){
                    $cat = $this->_hlp->createObj('\Magento\Catalog\Model\CategoryFactory')->create()->load($cat_data["entity_id"])->getData();
                    $options_array[$cat["name"]] = $cat["name"];
                }
                $options = $options_array;
                break;

            case 'udropship_label/custom_step2/product_sell_place':
            case 'product_sell_place':
                $options = array(
                    '' => 'Please select',
                    'Independent retailers'  => 'Independent retailers',
                    'Farmers markets / festivals ' => 'Farmers markets / festivals ',
                    'Supermarkets' => 'Supermarkets',
                    'Your own website' => 'Your own website',
                    'Other'=>'Other'
                );
                break;

            case 'udropship_label/custom_step2/best_time_to_call':
            case 'best_time_to_call':
                $options = array(
                    '' => 'Please select',
                    'Morning'  => 'Morning',
                    'Afternoon ' => 'Afternoon',
                    'Evening' => 'Evening'
                );
                break;

            default:
                throw new \Exception(__('Invalid request for source options: '.$this->getPath()));
        }

        if ($selector) {
            $options = array(''=>__($selectorLabel)) + $options;
        }

        return $options;
    }

    public function toOptionArray($selector=false)
    {
        switch ($this->getPath()) {
            case 'udropship/vendor/vendor_notification_field':
            case 'udropship/vendor/visible_preferences':
            case 'allowed_countries':
                return $this->toOptionHash($selector);
        }
        return parent::toOptionArray($selector);
    }

    public function getCarriers()
    {
        if (empty($this->_carriers)) {
            $carriersRaw = $this->_shippingConfig->getAllCarriers();
            $carriers = array();
            foreach ($carriersRaw as $carrierCode=>$carrierModel) {
                $label = $this->_hlp->getScopeConfig('carriers/'.$carrierCode.'/title');
                if (!$label || in_array($carrierCode, array('udropship', 'udsplit'))) {
                    continue;
                }
                $carriers[$carrierCode] = $label;
            }
            $this->_carriers = $carriers;
        }
        return $this->_carriers;
    }

    public function getMethods($codeAsKey=false, $suffixCode=false)
    {
        if (empty($this->_methods)) {
            $methodsCollection = $this->_hlp->getShippingMethods()
                ->setOrder('days_in_transit', 'desc');
            foreach ($methodsCollection as $m) {
                $_k = $codeAsKey ? $m->getShippingCode() : $m->getShippingId();
                $_lbl = $m->getShippingTitle() . ($suffixCode ? " ({$m->getShippingCode()})" : '');
                $this->_methods[$_k] = $_lbl;
            }
        }
        return $this->_methods;
    }

    public function getVendorsColumn($field, $includeInactive=false)
    {
        return $this->_getVendors($includeInactive, $field);
    }
    public function getVendors($includeInactive=false)
    {
        return $this->_getVendors($includeInactive, 'vendor_name');
    }
    protected function _getVendors($includeInactive=false, $field='vendor_name')
    {
        $key = $includeInactive.'-'.$field;
        if (empty($this->_vendors[$key])) {
            $this->_vendors[$key] = array();
            /* @var \Unirgy\Dropship\Model\ResourceModel\Vendor\Collection $vendors */
            $vendors = $this->_hlp->createObj('\Unirgy\Dropship\Model\ResourceModel\Vendor\Collection');
            $vendors
                ->setItemObjectClass('Magento\Framework\DataObject')
                ->addFieldToSelect(array($field))
                ->setOrder('vendor_name', 'asc');
            if (!$includeInactive) {
                $vendors->addStatusFilter('A');
            }
            foreach ($vendors as $v) {
                $this->_vendors[$key][$v->getVendorId()] = $v->getDataUsingMethod($field);
            }
        }
        return $this->_vendors[$key];
    }

    public function getTaxRegions()
    {
        if (!$this->_taxRegions) {
            /* @var \Magento\Directory\Model\ResourceModel\Region\Collection $region */
            $collection = $this->_hlp->createObj('\Magento\Directory\Model\ResourceModel\Region\Collection');
            $collection
                ->addCountryFilter('US')
                ->load();
            $this->_taxRegions = array();
            foreach ($collection as $region) {
                $this->_taxRegions[$region->getRegionId()] = $region->getDefaultName().' ('.$region->getCode().')';
            }
        }
        return $this->_taxRegions;
    }

    public function getVendorVisiblePreferences()
    {
        if (empty($this->_visiblePreferences)) {
            $hlp = $this->_hlp;

            $fieldsets = array();
            foreach ($this->udropshipConfig->getFieldset() as $code=>$node) {
                if (@$node['modules'] && !$hlp->isModulesActive((string)$node['modules'])
                    || @$node['hidden']
                ) {
                    continue;
                }
                $fieldsets[$code] = array(
                    'position' => (int)@$node['position'],
                    'label' => (string)@$node['legend'],
                    'value' => array(),
                );
            }
            foreach ($this->udropshipConfig->getField() as $code=>$node) {
                if (empty($fieldsets[(string)@$node['fieldset']]) || @$node['disabled']) {
                    continue;
                }
                if (@$node['modules'] && !$hlp->isModulesActive((string)$node['modules'])) {
                    continue;
                }
                $field = array(
                    'position' => (int)@$node['position'],
                    'label' => (string)@$node['label'],
                    'value' => $code,
                );
                $fieldsets[(string)@$node['fieldset']]['value'][] = $field;
            }
            uasort($fieldsets, array($hlp, 'usortByPosition'));
            foreach ($fieldsets as $k=>$v) {
                if (empty($v['value'])) {
                    continue;
                }
                uasort($v['value'], array($hlp, 'usortByPosition'));
            }
            $this->_visiblePreferences = $fieldsets;
        }
        return $this->_visiblePreferences;
    }
}
