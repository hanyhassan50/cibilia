/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    shim : {
        "bootstrapMin" : { "deps" :['jquery'] }
    },
    map: {
        '*': {
            "bootstrapMin": 	  'StageBit_Design/js/bootstrap.min',
            "bootstrapMultiselect": 'StageBit_Design/js/bootstrap-multiselect',
            "jqueryFlot": 'StageBit_Design/js/jquery.flot',
            "jqueryFlotTooltipMin":   'StageBit_Design/js/jquery.flot.tooltip.min',
            "jqueryFlotSpline": 'StageBit_Design/js/jquery.flot.spline',
            "jqueryFlotResize": 'StageBit_Design/js/jquery.flot.resize',
            "jqueryFlotPie":   'StageBit_Design/js/jquery.flot.pie',
            "jqueryFlotSymbol": 'StageBit_Design/js/jquery.flot.symbol',
            "jqueryFlotTime":   'StageBit_Design/js/jquery.flot.time',
            "jqueryJvectormap": 'StageBit_Design/js/jquery-jvectormap-2.0.2.min',
            "jvectormapWorldMill": 'StageBit_Design/js/jquery-jvectormap-world-mill-en',
        }
    }
};