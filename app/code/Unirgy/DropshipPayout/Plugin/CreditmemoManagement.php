<?php

namespace Unirgy\DropshipPayout\Plugin;

class CreditmemoManagement
{
    /** @var \Unirgy\Dropship\Helper\Data */
    protected $_hlp;
    /** @var \Unirgy\DropshipPayout\Helper\Data */
    protected $_payoutHlp;
    public function __construct(
        \Unirgy\Dropship\Helper\Data $udropshipHelper,
        \Unirgy\DropshipPayout\Helper\Data $payoutHelper
    )
    {
        $this->_hlp = $udropshipHelper;
        $this->_payoutHlp = $payoutHelper;
    }
    public function afterRefund(\Magento\Sales\Api\CreditmemoManagementInterface $subject, $result)
    {
        $this->_payoutHlp->processCreditmemo($result);
        return $result;
    }
}