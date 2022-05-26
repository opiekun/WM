<?php

namespace Photoslurp\Pswidget\Model\Config\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'visible', 'label' => __('All Visible')],
            ['value' => 'visible_parent', 'label' => __('Parent Visible')],
            ['value' => 'visible_child', 'label' => __('Child Visible')],
        ];
    }
}
