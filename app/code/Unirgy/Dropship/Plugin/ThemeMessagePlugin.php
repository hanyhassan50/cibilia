<?php

namespace {
    include_once __DIR__ . '/ThemeMessagePluginInc.php';
}

namespace Unirgy\Dropship\Plugin {

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\MessageInterface;

    class ThemeMessagePlugin extends \Magento\Theme\Controller\Result\MessagePlugin
    {
        public function afterRenderResult(
            ResultInterface $subject,
            ResultInterface $result
        ) {
            if (!$subject instanceof Redirect) {
                return parent::afterRenderResult($subject, $result);
            } else {
                return $result;
            }
        }
    }
}