<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * OfflinePayments Observer
 */
namespace Aopen\Openpay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Aopen\Openpay\Model\Openpay;

class SalesOrderShipmentSaveAfterObserver implements ObserverInterface
{

    /**
    * @var \Magento\Framework\Message\ManagerInterface
    */
    protected $messageManager;

    /**
    * @var \Aopen\Openpay\Model\Api
    */
    protected $openpayModel;

    /**
     *
     * @var \Aopen\Openpay\Helper\Data
    */
    public $helper;

    /**
     * Set the default URL based on the configuration
     */
    public function __construct(       
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Aopen\Openpay\Model\Api $openpayModel,
         \Aopen\Openpay\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->openpayModel = $openpayModel;
        $this->getHelper = $helper;
    }

    /**
     * Sets current instructions for bank transfer account
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getShipment()->getOrder();
        $code = $order->getPayment()->getMethodInstance()->getCode();
        if ($code == 'openpay') {
            $result = $this->openpayModel->onlineOrderDispatch($order->getId());
            if (isset($result['success'])) $this->messageManager->addSuccess('Openpay online order dispatch successful.');
            else $this->messageManager->addError($result['error']);
        }
        return $this;
    }
}
