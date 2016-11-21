<?php

namespace Aopen\Openpay\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const JAM_API_URL = 'https://retailer.myopenpay.com.au/';
    const JAM_DEMO_PATH = 'ServiceTraining/JAMServiceImpl.svc';
    const JAM_LIVE_PATH = 'ServiceLive/JAMServiceImpl.svc';
    const DEMO_HAND_OVER_URL = 'https://retailer.myopenpay.com.au/WebSalesTraining/';
    const LIVE_HAND_OVER_URL = 'https://retailer.myopenpay.com.au/WebSalesLive/';
//const DEV_HAND_OVER_URL = 'http://magento2.com/openpay/handoverurl/development';
    const JAM_CALLBACK_URL = 'openpay/order/callback';
    const JAM_CANCEL_URL = 'openpay/order/cancel';
    const JAM_FAIL_URL = 'openpay/order/fail';

    /**
     * @var $logfile
     */
    public $logfile = '/var/log/openpay.log';

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $orderFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
    */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $orderFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->getOrder = $orderFactory;
    }

    /**
     * @param $path
     * @return string
     */
    protected function getConfigData($path) {
        $prefix = 'payment/openpay/';
        return $this->scopeConfig->getValue($prefix . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        return self::JAM_API_URL;
    }

    /**
     * @return string
     */
    public function getApiPath() {
        if ($this->getConfigData('test')) return self::JAM_DEMO_PATH;
        else  return self::JAM_LIVE_PATH;
    }

    /**
     * @return boolean
     */
    public function getOpenpayActive() {
        return $this->getConfigData('active');
    }

    /**
     * @return bullean
     */
    public function getOpenpayDebug() {
        return $this->getConfigData('debug');
    }

    /**
     * @return string
     */
    public function getJamAuthToken() {
        if (!$jamAuthToken = $this->getConfigData('jam_auth_token')) {
            Mage::throwException('Openpay Jam Auth Token not set.');
        }
        return $jamAuthToken;
    }

    /**
     * @return string
     */
    public function getAuthToken() {
        return $this->getJamAuthToken();
    }

    /**
     * @return string
     */
    public function getHandOverUrl() {
//return self::DEV_HAND_OVER_URL;
        if ($this->getConfigData('test')) return self::DEMO_HAND_OVER_URL;
        else  return self::LIVE_HAND_OVER_URL;
    }

    /**
     * @return string
     */
    public function getJamCallbackUrl() {
        return $this->_storeManager->getStore()->getBaseUrl() . self::JAM_CALLBACK_URL;
    }

    /**
     * @return string
     */
    public function getJamCancelUrl() {
        return $this->_storeManager->getStore()->getBaseUrl() . self::JAM_CANCEL_URL;
    }

    /**
     * @return string
     */
    public function getJamFailUrl() {
        return $this->_storeManager->getStore()->getBaseUrl() . self::JAM_FAIL_URL;
    }

    /**
     * @param $message
     * @return Mage_Log
     */
    public function log($message)
    {
    	if ($this->getOpenpayDebug()) {
			$writer = new \Zend\Log\Writer\Stream(BP . $this->logfile);
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($message);
		}
        return false;
    }

    /**
     * @param (int) $id
     * @param (string) $hash
     * @return $object
     */
    public function valid($id, $hash) {
        $order = $this->getOrder->load($id);
        if ($hash == md5($id . $this->getJamAuthToken() . $order->getIncrementId())) return $order;
        return false;
    }


}