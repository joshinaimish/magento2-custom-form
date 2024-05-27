<?php

namespace Nxtech\Customform\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use \Magento\Framework\Option\ArrayInterface;

class Options implements ArrayInterface
{

    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('BCA')],
            ['value' => 2, 'label' => __('BBA')],
            ['value' => 3, 'label' => __('MCA')],
            ['value' => 4, 'label' => __('MBA')]
        ];
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string|bool
     */
    public function getOptionText($value)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}