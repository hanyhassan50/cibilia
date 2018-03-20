/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
 define('js/theme', [
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $('body').on('change','#dependable_attribute_code',function(){
      var customAttrInput = $('body').find('#customfields_attribute_code');
        if(customAttrInput.val()== $(this).val()){
           $(this).val('');
           alert('Dependable Field Attribute Code can Not be same.')
        }
    });

});
