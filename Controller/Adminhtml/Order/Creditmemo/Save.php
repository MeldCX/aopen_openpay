<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Controller\Adminhtml\Order\Creditmemo;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class Save extends \Magento\Sales\Controller\Adminhtml\Order\Creditmemo\Save
{

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader
     */
    protected $creditmemoLoader;

    /**
     * @var CreditmemoSender
     */
    protected $creditmemoSender;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Sales\Model\Order\Payment\Transaction
     */
    protected $transaction;

    /**
     * @var \Aopen\Openpay\Model\Api
     */
    protected $openpayModel;

    /**
     * @param Action\Context $context
     * @param \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader
     * @param CreditmemoSender $creditmemoSender
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Controller\Adminhtml\Order\CreditmemoLoader $creditmemoLoader,
        CreditmemoSender $creditmemoSender,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Model\Order\Payment\Transaction $transaction,
        \Aopen\Openpay\Model\Api $openpayModel
    ) {
        $this->creditmemoLoader = $creditmemoLoader;
        $this->creditmemoSender = $creditmemoSender;
        $this->resultForwardFactory = $resultForwardFactory;
         \Magento\Backend\App\Action::__construct($context);
        $this->getTransaction = $transaction;
        $this->openpayModel = $openpayModel;
    }


    /**
     * Save creditmemo
     * We can save only new creditmemo. Existing creditmemos are not editable
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Backend\Model\View\Result\Forward
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPost('creditmemo');
        if (!empty($data['comment_text'])) {
            $this->_getSession()->setCommentText($data['comment_text']);
        }
        try {
            $this->creditmemoLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->creditmemoLoader->setCreditmemoId($this->getRequest()->getParam('creditmemo_id'));
            $this->creditmemoLoader->setCreditmemo($this->getRequest()->getParam('creditmemo'));
            $this->creditmemoLoader->setInvoiceId($this->getRequest()->getParam('invoice_id'));
            $creditmemo = $this->creditmemoLoader->load();
            if ($creditmemo) {
                if (!$creditmemo->isValidGrandTotal()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The credit memo\'s total must be positive.')
                    );
                }
                if (!empty($data['comment_text'])) {
                    $creditmemo->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );
                    $creditmemo->setCustomerNote($data['comment_text']);
                    $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                }
                if (isset($data['do_offline'])) {
                    //do not allow online refund for Refund to Store Credit
                    if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('Cannot create online refund for Refund to Store Credit.')
                        );
                    }
                }
                $order = $creditmemo->getOrder();

                $paymentMethod = $order->getPayment()->getMethodInstance()->getCode();
                $transaction = $this->getTransaction->getCollection()
                    ->addFieldToSelect('txn_id')
                    ->addFieldToFilter('order_id', $order->getId())
                    ->addFieldToFilter('txn_type', \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE)
                    ->getFirstItem();
                if ($paymentMethod == 'openpay' && $transaction->getTxnId() && !$data['do_offline']) {
                    $return = $this->openpayModel->onlineOrderReduction($transaction->getTxnId(),$data['adjustment_negative'],$creditmemo->getGrandTotal());
                    if (isset($return['success'])) {
                        $creditmemoManagement = $this->_objectManager->create(
                            'Magento\Sales\Api\CreditmemoManagementInterface'
                        );
                        $creditmemoManagement->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));

                        if (!empty($data['send_email'])) {
                            $this->creditmemoSender->send($creditmemo);
                        }

                        // $payment = $order->getPayment();
                        // $payment->setTransactionId($transaction->getTxnId());
                        // $transaction = $payment->addTransaction(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND, null, false, 'Openpay refund successful.');
                        // $transaction->setParentTxnId($result['planId']);
                        // $transaction->setIsClosed(0);
                        // $transaction->save();

                        $this->messageManager->addSuccess(__('Openpay refund successful'));
                    }
                    else {
                        $this->messageManager->addError($return['error']);
                    }
                    $this->_getSession()->getCommentText(true);
                    $resultRedirect->setPath('sales/order/view', ['order_id' => $creditmemo->getOrderId()]);
                    return $resultRedirect;
                }
                else {
                    $creditmemoManagement = $this->_objectManager->create(
                        'Magento\Sales\Api\CreditmemoManagementInterface'
                    );
                    $creditmemoManagement->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));

                    if (!empty($data['send_email'])) {
                        $this->creditmemoSender->send($creditmemo);
                    }
                    $this->messageManager->addSuccess(__('You created the credit memo.'));
                    $this->_getSession()->getCommentText(true);
                    $resultRedirect->setPath('sales/order/view', ['order_id' => $creditmemo->getOrderId()]);
                    return $resultRedirect;
               }
            } else {
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_getSession()->setFormData($data);
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->messageManager->addError(__('We can\'t save the credit memo right now.'));
        }
        $resultRedirect->setPath('sales/*/new', ['_current' => true]);
        return $resultRedirect;
    }
}
