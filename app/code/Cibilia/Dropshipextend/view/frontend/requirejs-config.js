/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    shim : {
        "bootstrapSelect" : { "deps" :['jquery'] }
    },
    map: {
        '*': {
            "bootstrapSelect": 	  'StageBit_Design/js/bootstrap-select',
        }
    }
};