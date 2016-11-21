<?php

namespace Aopen\Openpay\Controller\Order;

class Cancel extends \Magento\Framework\App\Action\Action
{

    /**
     *
     * @var \Aopen\Openpay\Helper\Data
    */
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Aopen\Openpay\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->getHelper = $helper;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $hash = $this->getRequest()->getParam('hash');
        if ($order = $this->getHelper->valid($id,$hash)) {
            $order->setState('processing')->setStatus('processing');
            $ext = '/id/' . $id . '/hash/' . $hash;
            $this->_redirect('openpay/order/failure' . $ext);
        }
    }
}