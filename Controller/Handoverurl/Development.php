<?php

namespace Aopen\Openpay\Controller\Handoverurl;

class Development extends \Magento\Framework\App\Action\Action
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        echo '<h1 style="text-align: center;">Open Pay Development Interface</h1>';
        echo '<blockquote>';
        foreach ($this->getRequest()->getParams() as $key => $value) {
            echo $key .': '. $value . '<br/>';
        }
        echo '<div style="height: 50px;"></div>';
        echo '<div style="width: 33.3333%; float: left">';
        echo '<a href="javascript:window.top.location.href=\'' . $this->getRequest()->getParam('JamCallbackURL') . '\?status=SUCCESS&planid=' . $this->getRequest()->getParam('JamPlanID') . '\';">Success</a>';
        echo '</div>';
        echo '<div style="width: 33.3333%; float: left">';
        echo '<a href="javascript:window.top.location.href=\'' . $this->getRequest()->getParam('JamFailURL') . '\'">Fail</a>';
        echo '</div>';
        echo '<div style="width: 33.3333%; float: left">';
        echo '<a href="javascript:window.top.location.href=\'' . $this->getRequest()->getParam('JamCancelURL') . '\'">Cancel</a>';
        echo '</div>';
        echo '</blockquote>';
    }
}