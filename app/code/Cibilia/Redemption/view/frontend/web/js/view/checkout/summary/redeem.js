/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Cibilia_Redemption/checkout/summary/redeem'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals()) {
                    //price = parseFloat(this.totals().redeem);
                    price = parseFloat(totals.getSegment('redeem').value);
                }
                return price;

            }
        });
    }
);