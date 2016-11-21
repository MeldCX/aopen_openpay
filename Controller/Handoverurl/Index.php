<?php

namespace Aopen\Openpay\Controller\Handoverurl;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Aopen\Openpay\Model\Openpay
     */
    protected $openpayApi;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Aopen\Openpay\Model\Api $openpayApi,
        \Magento\Checkout\Model\Session $session
    ) {
        parent::__construct($context);
        $this->getOpenpayApi = $openpayApi;
        $this->getSession = $session;
    }

    protected function getLastOrder()
    {
        return $this->getSession->getLastRealOrder();
    }    

    public function execute()
    {
        if ($this->getLastOrder()) {
            $response = array();
            $lastOrder = $this->getLastOrder()->getData();
            $address = $this->getLastOrder()->getBillingAddress()->getData();
            $street = explode("\n",$address['street']);
            $street[1] = (isset($street[1])) ? $street[1] : '';
            $this->getSession->clearStorage();
            $url = $this->getOpenpayApi->getOpenpayUrl($lastOrder['entity_id'], $lastOrder['grand_total'], $lastOrder['increment_id'], $lastOrder['customer_firstname'], $lastOrder['customer_lastname'], $lastOrder['customer_email'], $lastOrder['customer_dob'], $street[0], $street[1], $address['city'], $address['region'], $address['postcode']);
            if (substr($url,0,4) == 'http') return $this->resultRedirectFactory->create()->setUrl($url);
            else return $this->resultRedirectFactory->create()->setPath('checkout/onepage/failure');
        }
    }
}