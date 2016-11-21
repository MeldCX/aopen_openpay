<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Block;

class Failure extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var \Aopen\Openpay\Helper\Data
    */
    protected $helper;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Aopen\Openpay\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->getHelper = $helper;
    }

    /**
     * @return mixed
     */
    public function getRealOrderId()
    {
        $id = $this->getRequest()->getParam('id');
        $hash = $this->getRequest()->getParam('hash');
        $order = $this->getHelper->valid($id, $hash);
        return $order->getIncrementId();
    }

    /**
     *  Payment custom error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return 'Your transaction with openpay failed!';
    }

    /**
     * Continue shopping URL
     *
     * @return string
     */
    public function getContinueShoppingUrl()
    {
        return $this->getUrl('checkout/cart');
    }
}
