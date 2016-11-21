<?php

namespace Aopen\Openpay\Controller\Log;

class Index extends \Magento\Framework\App\Action\Action
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
        $html = '';
        if (file_exists(BP . $this->getHelper->logfile)) {
            $html .= '<pre>';
            $html .= htmlentities(file_get_contents(BP . $this->getHelper->logfile));
            $html .= '</pre>';
        }
        else $html .= '<blockquote>logfile: ' . BP . $this->getHelper->logfile . ' does not exist</blockquote>';
        die($html);
    }
}