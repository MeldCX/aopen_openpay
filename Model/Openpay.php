<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Model;

/**
 * Openpay payment method model
 *
 * @method \Magento\Quote\Api\Data\PaymentMethodExtensionInterface getExtensionAttributes()
 */
class Openpay extends \Magento\Payment\Model\Method\AbstractMethod
{
    const PAYMENT_METHOD_OPENPAY_CODE = 'openpay';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_METHOD_OPENPAY_CODE;

    /**
     * Openpay payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'Aopen\Openpay\Block\Form\Openpay';

    /**
     * Info instructions block path
     *
     * @var string
     */
    protected $_infoBlockType = 'Magento\Payment\Block\Info\Instructions';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = false;


    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canOrder                   = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canCapture                 = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canRefund                  = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canUseForMultishipping     = false;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isInitializeNeeded         = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canFetchTransactionInfo    = true;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_canReviewPayment           = true;

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }
}
