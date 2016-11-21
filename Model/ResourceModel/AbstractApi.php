<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Model\ResourceModel;

abstract class AbstractApi extends \Aopen\Openpay\Model\Zend\Rest\Client
{

    /**
    * @var \Magento\Framework\Message\ManagerInterface
    */
    public $messageManager;

    /**
     *
     * @var \Aopen\Openpay\Helper\Data
    */
    public $helper;

    /**
     *
     * @var string
    */
    protected $basePath;


    /**
     * \Aopen\Openpay\Model\Zend\Rest\Client
     *
     * @var array
    */
    protected function _getHeaders() {
        return array(
                    \Zend_Http_Client::CONTENT_TYPE => 'application/xml; charset=utf-8'
                );
    }
    
    /**
     * \Aopen\Openpay\Model\Zend\Rest\Client
     *
     * @var array
    */
    protected function _getConfig() {
        return array(
                    'timeout' => 120
                );
    }

    /**
     * Set the default URL based on the configuration
     */
    public function __construct(       
        \Magento\Framework\Message\ManagerInterface $messageManager,
         \Aopen\Openpay\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->getHelper = $helper;
        if ($uri = \Zend_Uri_Http::fromString($this->getHelper->getApiUrl())) {
            parent::__construct($uri);
        } 
        else {
            $this->messageManager->addError( __('Could not retrieve Openpay API URL') );
        }
    }
    /**
     * @return _basepath
     */
    public function getBasePath() {
        $this->basePath = $this->getHelper->getApiPath();
        return $this->basePath;
    }
        
    /**
     * @param array $paths
     * @return string
     */
    public function generatePath($paths) {
        if(is_array($paths)) {
            $path = '';
            foreach($paths as $extra) {
                $path .= '/' . $extra;
            }
        }
        return $path;
    }
    
    /**
     * @param $params
     * @param string $type
     */
    protected function _logApiRequest($path, $data = null, $type = 'GET') {
        $this->getHelper->log("\n******* Openpay API - $type *******");
        $this->getHelper->log(' > URI: ' . $this->getHelper->getApiUrl() . $path);
        if ($data) {
            $this->getHelper->log(' > Data: ' . "\n" .  $data);
        }
    }
    
    /**
     * @param Zend_Http_Response $response
     * @param null|string $message
     */
    protected function _logApiResponse($response, $message = null) {
        $this->getHelper->log(' > Response: ' . $response->getStatus());
        if($message) {
            $this->getHelper->log(' > Message: ' . $message);
        } else {
            $this->getHelper->log(' > Message: ' . $response->getMessage());
        }
        if ($response->getBody()) {
            $this->getHelper->log(' > Content: ' . "\n" .  $response->getBody());
        }
        $this->getHelper->log('*************************************');
    }
    
    /**
     * @param string $type
     * @param array $params
     * @param SimpleXMLElement $payload
     *
     * @return Zend_Http_Response
     * @throws Mage_Core_Exception
     */
    public function makeRestCall($type, $paths = array(), $data = null) {
        //reset URI
        $this->getUri()->setPath('');
        $this->getUri()->setQuery(array());
        $path = $this->getBasePath() . $this->generatePath($paths);
        $this->_logApiRequest($path, $data, $type);
        switch($type) {
            default:
            case \Zend_Http_Client::GET:
                /** @var Zend_Http_Response $response */
                $response = $this->restGet(
                    $path,
                    $data
                );
                break;
            case \Zend_Http_Client::DELETE:
                /** @var Zend_Http_Response $response */
                $response = $this->restDelete(
                    $path,
                    $data
                );
                break;
            case \Zend_Http_Client::POST:
                /** @var Zend_Http_Response $response */
                $response = $this->restPost(
                    $path,
                    $data
                );
                break;
            case \Zend_Http_Client::PUT:
                /** @var Zend_Http_Response $response */
                $response = $this->restPut(
                    $path,
                    $data
                );
                break;
        }
        if (!$response->isSuccessful()) {
            $this->messageManager->addError( __('Connection to Openpay API failed' . ': (Response: ' . $response->getStatus() . ', Message: ' . $response->getMessage() .')') );
            $result = $response->getBody();
            return $result;
        }
        $this->_logApiResponse($response);
        $result = $response->getBody();
        return $result;
    }
}
