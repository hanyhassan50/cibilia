<?php

namespace Unirgy\DropshipVendorAskQuestion\Model;

use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\Dropship\Model\Source\AbstractSource;

class Source extends AbstractSource
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(
        HelperData $helperData,
        array $data = []
    )
    {
        $this->_helperData = $helperData;

        parent::__construct($data);
    }

    const UDQA_STATUS_DECLINED = -1;
    const UDQA_STATUS_PENDING  = 0;
    const UDQA_STATUS_APPROVED = 1;

    const UDQA_VISIBILITY_PRIVATE = 0;
    const UDQA_VISIBILITY_PUBLIC  = 1;

    public function toOptionHash($selector=false)
    {
        $hlp = $this->_helperData;

        $options = [];

        switch ($this->getPath()) {

            case 'udqa/general/default_question_status':
            case 'udqa/general/default_answer_status':
            case 'statuses':
                $options = [
                    self::UDQA_STATUS_PENDING  => __('Pending'),
                    self::UDQA_STATUS_APPROVED => __('Approved'),
                    self::UDQA_STATUS_DECLINED => __('Declined'),
                ];
                break;

            case 'visibility':
                $options = [
                    self::UDQA_VISIBILITY_PRIVATE => __('Private'),
                    self::UDQA_VISIBILITY_PUBLIC  => __('Public'),
                ];
                break;

            default:
                throw new \Exception(__('Invalid request for source options: '.$this->getPath()));
        }

        if ($selector) {
            $options = [''=>__('* Please select')] + $options;
        }

        return $options;
    }
}