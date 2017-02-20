<?php
namespace Aopen\Openpay\Cron;
 
class Cancelpending {
 
    protected $_logger;

    protected $_orderCollectionFactory;


    protected $cancelOrder;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Api\Data\OrderInterface $cancelOrder
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_logger = $logger;
        $this->cancelOrder = $cancelOrder;
    }
    
    /**
     * Method executed when cron runs in server
     */
    public function execute() {

    if ($cancelPendingOrders = $this->scopeConfig->getValue('payment/openpay/cancel_pending', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
        $frequency = $this->scopeConfig->getValue('payment/openpay/cancel_pending_frequency', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $orders = $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('state', 'new')
            ->addFieldToFilter('status', 'pending_approval');
            foreach($orders as $order) {
                $orderAge = (-1*(strtotime($order->getUpdatedAt()) - time()));
                if ($orderAge > $frequency) {
                    $this->cancelOrder->load($order->getId())->setState('canceled')->setStatus('canceled')->save();
                }
            }
        }
        return $this;
    }
}