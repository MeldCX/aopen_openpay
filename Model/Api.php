<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Model;

/**
 * Openpay API model
 *
 */
class Api extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Initialize resource model
     *
     * @return void
    */
    // protected function _construct()
    // {
    //     $this->_init('Aopen\Openpay\Model\ResourceModel\Api');
    // }

    /**
    * @var \Magento\Framework\Message\ManagerInterface
    */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderFactory;


    /**
     * Set the default URL based on the configuration
     */
    public function __construct(       
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\Order $orderFactory

    ) {
        $this->messageManager = $messageManager;
        $this->getOrder = $orderFactory;
        $this->_init('Aopen\Openpay\Model\ResourceModel\Api');
    }


    /**
     * @var string
     * @var int
     *
     * @return string
    */
    protected function formatString($string,$maxLength) {
        return (strlen($string) > ($maxLength-1)) ? substr($string,0,($maxLength-3)).'...' : $string;
    }

    /**
     * @var string
     *
     * @return string
    */
    protected function abbreviateState($state) {
        $states = array('australian capital territory' => 'ACT',
        'new south wales' => 'NSW',
        'northern territory' => 'NT',
        'queensland' => 'QLD',
        'south australia' => 'SA',
        'tasmania' => 'TAS',
        'victoria' => 'VIC',
        'western australia' => 'WA');
        if (strlen($state) > 3) return ($states[strtolower($state)]);
        else return strtoupper($state);
    }

    public function getOpenpayUrl($id, $price, $orderNumber, $firstName, $lastName, $email, $dob, $address1, $address2, $city, $state, $postcode) {
        $price = number_format($price, 2);
        $firstName = $this->formatString($firstName,50);
        $flastName = $this->formatString($firstName,50);
        $email = $this->formatString($email,150);
        $address1 = $this->formatString($address1,100);
        $address2 = $this->formatString($address2,100);
        $city = $this->formatString($city,100);
        $state = $this->abbreviateState($state);
        $postcode = substr($postcode,0,4);
        $dob = date('d M Y',strtotime($dob));
        $result = $this->_getResource()->getOpenpayUrl($id, $price, $orderNumber, $firstName, $lastName, $email, $dob, $address1, $address2, $city, $state, $postcode);
        if (isset($result['error'])) $this->messageManager->addError( $result['error'] );
        else return $result['url'];
    }

    public function onlineOrderReduction($planId,$newPurchasePrice) {
        $newPurchasePrice = number_format($newPurchasePrice, 2);
        $result = $this->_getResource()->onlineOrderReduction($planId,$newPurchasePrice);
        return $result;
    } 

    public function onlineOrderDispatch($orderId) {
        $order = $this->getOrder->load($orderId);
        if ($planId = $order->getPayment()->getLastTransId()) {
            $result = $this->_getResource()->onlineOrderDispatch($planId);
            return $result;
        }
        else {
            $result['error'] = 'There is no Openpay planId for this order.';
            return $result;
        }
    }
}