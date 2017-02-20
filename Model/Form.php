<?php

namespace Aopen\Openpay\Model;

class Form implements \Magento\Framework\Option\ArrayInterface
{
   public function toOptionArray()
    {
        return array(
            array('value'=>3600, 'label'=>__('After one hour')),
            array('value'=>7200, 'label'=>__('After two hours')),
            array('value'=>14400, 'label'=>__('After four hours')),            
            array('value'=>21600, 'label'=>__('After six hours')),                        
            array('value'=>43200, 'label'=>__('After twelve hours')),                        
            array('value'=>86400, 'label'=>__('After one day')),                        
        );
    }
}