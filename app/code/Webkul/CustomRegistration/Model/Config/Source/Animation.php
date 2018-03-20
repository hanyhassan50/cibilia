<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Model\Config\Source;

/**
 * Generic source
 */
class Animation
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $code = '';
    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value'=>'popup', 'label'=>'Popup'],
            ['value'=>'slide', 'label'=>'Slide'],
        ];
        
        return $options;
    }
}
