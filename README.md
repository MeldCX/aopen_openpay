# Openpay Payment Gateway for Magento 2+
-------
``This plugin is released under the MIT license.``

**If you have any questions or issues, feel free to file an [issue](https://github.com/MeldCX/aopen_openpay/issues)**

The Openpay payment gateway is a payment method for Magento2+ stores that allows the customer to purchase items through a series of 

ments.

**Note:** This is a beta release and we are still working on plugin improvements.

## Table of Contents

[Features](#features) <br />

[Prerequisites][1]

[Installation][2]
* [Installing Manually][2.1]
* [Installing with Composer][2.2]
* [Enabling Openpay Payment Gateway][2.3]

[Configuration][3]
* [Configuration Parameters][3.1]
* [Uninstallation][3.2]

[Usage][4]

[Troubleshooting][5]


##Features
The Openpay payment gateway is a payment method for Magento2+ stores that allows the customer to purchase items through a series of installments.

* Test mode
* Capture payments
* Refund payments

##Prerequisites

A valid Openpay Auth Token. 

Contact [www.openpay.com.au](http://www.openpay.com.au) for info on creating an account and obtaining an Auth Token

## Installation

There are two ways in which you can install the plugin:

* [Manual installation][2.1] by downloading the repository copying and pasting folders into the magento installation
* [Composer Intallation][2.2]

See the sections below to find out about steps for each of the procedures.

### Installing Manually

To install the plugin manually:

* Download the extension
* Unzip the file
* Create a folder {Magento root}/app/code/Aopen/Openpay
* Copy the content from the unzip folder

### Installing with Composer 

```
composer config repositories.aopen_openpay vcs https://github.com/MeldCX/aopen_openpay.git
composer require aopen/openpay
```

### Enabling Openpay Payment Gateway

```
php -f bin/magento module:enable --clear-static-content Oliverbode_Storelocator
php -f bin/magento setup:upgrade
```
  
## Configuration

Independently of the installation method, the configuration looks the same:

1. Go to the Magento administration page [http://your-magento-url/admin].
2. Go to **System** > **Configuration** window. 
3. From the **Configuration** menu on the left, select  **Payment Methods**
4. In the list of available payment methods select **Openpay Payment Method**,  expand the configuration form, and specify the [configuration parameters][3.1].
5. Click save.

### Configuration Parameters

The tables below present the descriptions of the configuration form parameters.

#### Main parameters

The main parameters for plugin configuration are as follows:

### General Tab

| Parameter | Values | Description | 
|:---------:|:------:|:-----------:|
|Title|String|The title of the payment method that is to be displayed.|
|Enabled|Yes/No|Enable the payment method|
|Auth Token|Authorization key provided by Openpay.|Authentication token required to connect to Openpay API|
|Test Mode|Yes/No|Test mode payment gateway|
|Payment from applicable countries|Specific Countries/All Allowed Countries|Limit where the payment gateway should be displayed|
|Payment from specific countries|List of countries|Applies when above option is switched to specific countries|
|Minimum Order Total|integer|Supplied by Openpay. Minimum amount of order where payment gateway is available|
|Debug|Yes/No|If switched on will create a log file of all API calls at var/log/openpay.log|


### Uninstallation
Remove all installed files. Configuration data is stored in the table: core_config_data and can be removed with the command: DELETE FROM core_config_data WHERE path LIKE '%openpay%'; The module also adds a custom status. This can be removed using the following commands. DELETE FROM sales_order_status WHERE label = 'Pending Openpay Approval'; DELETE FROM sales_order_status_state WHERE status = 'pending_approval';
To remove the module from the core resource table issue the following command: DELETE FROM core_resource WHERE code = 'openpay_setup';


## Usage
Customer Orders
Install and configure the module. Payment method will appear in checkout if order meets all the criteria. After placing order on checkout, customers will be redirected to openpay to complete their installment plan. Orders remain in a pending approval state until the openpay process is complete. On success orders will be set as Processing. Failed and Cancelled orders will be set to Cancelled. When the merchant ships the item and presses "ship" a request is made to Openpay.

Refunds
Online refunds can be made only after an order has been invoiced. This is done by the merchant going into Sales > Invoices and selecting "Credit Memo" and then selecting "Refund"

## Troubleshooting

A log file is created that records all the requests to the Openpay API and is located in [/path/to/magento/installation/var/log/openpay.log]

<!--LINKS-->

<!--topic urls:-->
[1]: https://github.com/MeldCX/aopen_openpay#prerequisites
[2]: https://github.com/MeldCX/aopen_openpay#installation
[2.1]: https://github.com/MeldCX/aopen_openpay#installing-manually
[2.2]: https://github.com/MeldCX/aopen_openpay#installing-with-composer
[2.3]: https://github.com/MeldCX/aopen_openpay#enabling-openpay-payment-gateway
[3]: https://github.com/MeldCX/aopen_openpay#configuration
[3.1]: https://github.com/MeldCX/aopen_openpay#configuration-parameters
[3.2]: https://github.com/MeldCX/aopen_openpay#uninstallation
[4]: https://github.com/MeldCX/aopen_openpay#usage
[5]: https://github.com/MeldCX/aopen_openpay#troubleshooting

<!--external links:-->

[ext1]: https://github.com/MeldCX/aopen_openpay
[ext2]: https://github.com/MeldCX/aopen_openpay#configuration-parameters
