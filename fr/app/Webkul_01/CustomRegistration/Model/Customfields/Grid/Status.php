<?php

namespace Webkul\CustomRegistration\Model\Customfields\Grid;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Webkul\CustomRegistration\Model\Customfields
     */
    protected $fields;
    /**
     * Constructor.
     */
    public function __construct(\Webkul\CustomRegistration\Model\Customfields $fields)
    {
        $this->fields = $fields;
    }
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->fields->getStatusOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                    'label' => $value,
                    'value' => $key,
                ];
        }

        return $options;
    }
}
