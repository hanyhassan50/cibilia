<?php

namespace Magento\Catalog\Model\View\Asset {
    /*$hlp = \Magento\Framework\App\ObjectManager::getInstance()->get('\Unirgy\Dropship\Helper\Data');
    if (!$hlp->compareMageVer('2.1.6',null,'=')) {*/
    if (!class_exists('\Magento\Catalog\Model\View\Asset\Image')) {
        class Image {

        }
    }
}