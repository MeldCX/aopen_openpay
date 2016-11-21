<?php
namespace Aopen\Openpay\Setup;
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_resourceConfig;


    /**
     * Construct
     *
     * @param \Magento\Sales\Model\Order\Config $salesOrderConfig
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     */
    public function __construct(
        \Magento\Config\Model\ResourceModel\Config $resourceConfig
    ) {
        $this->_resourceConfig = $resourceConfig;
    }

	public function install(
		\Magento\Framework\Setup\SchemaSetupInterface $setup,
		\Magento\Framework\Setup\ModuleContextInterface $context
	){


		$installer = $setup;

		// Required tables
		$statusTable = $installer->getTable('sales_order_status');
		$statusStateTable = $installer->getTable('sales_order_status_state');

		$installer->startSetup();

		// Insert statuses
		$installer->getConnection()->insertArray(
		    $statusTable,
		    array(
		        'status',
		        'label'
		    ),
		    array(
		        array('status' => 'pending_approval', 'label' => 'Pending Openpay Approval')
		    )
		);
		 
		// Insert states and mapping of statuses to states
		$installer->getConnection()->insertArray(
		    $statusStateTable,
		    array(
		        'status',
		        'state',
		        'is_default'
		    ),
		    array(
		        array(
		            'status' => 'pending_approval',
		            'state' => 'new',
		            'is_default' => 0
		        )
		    )
		);

		$installer->endSetup();
		$this->_resourceConfig->saveConfig('payment/openpay/order_status','pending_approval', 'default', 0);
	}
}