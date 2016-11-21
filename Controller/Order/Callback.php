<?php

namespace Aopen\Openpay\Controller\Order;

class Callback extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var \Aopen\Openpay\Helper\Data
    */
    protected $helper;

    /**
     *
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
    */
    protected $sendEmail;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Aopen\Openpay\Helper\Data $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $sendEmail
    ) {
        parent::__construct($context);
        $this->getHelper = $helper;
        $this->sendEmail = $sendEmail;
    }

    protected function saveTransaction($order,$planId)
    {
        $order->setState('processing')->setStatus('processing');
        $payment = $order->getPayment();
        $payment->setTransactionId($planId);
        $transaction = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE, null, false, 'Approved by openpay');
        $transaction->setParentTxnId($planId);
        $transaction->setAdditionalInformation('planId',$planId);
        $transaction->setIsClosed(0);
        $transaction->save();
        $order->save();
        $this->sendEmail->send($order,true);
    }

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $hash = $this->getRequest()->getParam('hash');
        $status = $this->getRequest()->getParam('status');
        $planId = $this->getRequest()->getParam('planid');
        if ($order = $this->getHelper->valid($id,$hash)) {
            if ($status == 'SUCCESS' & is_numeric($planId)) {
                $this->saveTransaction($order,$planId);
                $ext = '/id/' . $id . '/hash/' . $hash;
                $this->_redirect('openpay/order/success' . $ext);
            }
            else {
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Openpay Payment Cancelled', true)->save();
                $ext = '/id/' . $id . '/hash/' . $hash;
                $this->_redirect('openpay/order/failure' . $ext);
            }
        }

    }
}