<?php namespace Synapse\Sidebar\Model\Config\Source;

/**
 * Class:Categories
 * Synapse\Sidebar\Model\Config\Source
 *
 * @author      Synapse
 * @package     Synapse\Sidebar
 * @copyright   Copyright (c) 2020, vvasiloud. All rights reserved
 */
class Depth implements \Magento\Framework\Option\ArrayInterface {

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('1'),
            ],
            [
                'value' => 2,
                'label' => __('2'),
            ],
            [
                'value' => 3,
                'label' => __('3'),
            ],
            [
                'value' => 4,
                'label' => __('4'),
            ],
            [
                'value' => 5,
                'label' => __('5'),
            ],			
        ];
    }
}