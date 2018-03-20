/**
 * Copyright � 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            "productGallery":     'Unirgy_DropshipVendorProduct/js/product-gallery',
            "baseImage":          'Unirgy_DropshipVendorProduct/js/catalog/base-image-uploader',
            "treeSuggest":        "mage/backend/tree-suggest",
            "actionLink":         "mage/backend/action-link",
            "jstree":             "jquery/jstree/jquery.jstree",
            "newVideoDialog":     "Unirgy_DropshipVendorProduct/js/new-video-dialog",
            "openVideoModal":     "Unirgy_DropshipVendorProduct/js/video-modal",
        }
    },
    "bundles": {
        "Unirgy_DropshipVendorProduct/js/backend_theme": [
            "globalNavigation",
            "globalSearch",
            "modalPopup",
            "useDefault",
            "loadingPopup",
            "backendCollapsable"
        ]
    },
    "deps": [
        "Unirgy_DropshipVendorProduct/js/backend_theme"
    ]
};