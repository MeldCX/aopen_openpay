<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Block\Form;

/**
 * Block for Cash On Delivery payment method form
 */
class Openpay extends \Aopen\Openpay\Block\Form\AbstractInstruction
{
    /**
     * Cash on delivery template
     *
     * @var string
     */
    protected $_template = 'form/openpay.phtml';
}
