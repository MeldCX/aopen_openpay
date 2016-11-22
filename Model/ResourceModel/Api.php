<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aopen\Openpay\Model\ResourceModel;

class Api extends \Aopen\Openpay\Model\ResourceModel\AbstractApi
{

    protected function getPlanId($price) {
        $path = array('NewOnlineOrder');
        $data = $this->createXmlPayload($path[0], $price);
        $result = $this->makeRestCall(\Zend_Http_Client::POST, $path, $data);
        return $this->result($result);
    }

    protected function result($result) {
        $return = array();
        $xml = new \SimpleXMLElement($result);
        $array = get_object_vars($xml);
        if (!$array['status']) {
            $return['success'] = $array['reason'];
            $return['planId'] = $array['PlanID'];
        } 
        else {
            $return['error'] = $array['reason'];
        }
        return $return;     
    }

    protected function createXmlPayload($path, $price=null, $planId=null, $newPurchasePrice=null,$reducePriceBy='0.00',$fullRefund='true') {
        $xml = '<' . $path . '>' . "\n";
        $xml .= "\t" . '<JamAuthToken>';
        $xml .= $this->getHelper->getJamAuthToken();
        $xml .= '</JamAuthToken>' . "\n";
        $xml .= "\t" . '<AuthToken>';
        $xml .= $this->getHelper->getAuthToken();
        $xml .= '</AuthToken>' . "\n";
        if ($price) {
            $xml .= "\t" . '<PurchasePrice>';
            $xml .= $price;
            $xml .= '</PurchasePrice>' . "\n";
        }
        if ($planId) {
            $xml .= "\t" . '<PlanID>';
            $xml .= $planId;
            $xml .= '</PlanID>' . "\n";
        }
        if ($path == 'OnlineOrderReduction') {
            $xml .= "\t" . '<NewPurchasePrice>';
            $xml .= $newPurchasePrice;
            $xml .= '</NewPurchasePrice>' . "\n";
            $xml .= "\t" . '<ReducePriceBy>';
            $xml .= $reducePriceBy;
            $xml .= '</ReducePriceBy>' . "\n";
            $xml .= "\t" . '<FullRefund>';
            $xml .= $fullRefund;
            $xml .= '</FullRefund>' . "\n";
        }
        $xml .= '</' . $path . '>' . "\n";
        return $xml;
    }

    public function getOpenpayUrl($id, $price, $orderNumber, $firstName, $lastName, $email, $dob, $address1, $address2, $city, $state, $postcode) {
        $result = $this->getPlanId($price);
        if (isset($result['planId'])) {
            $hash = md5($id . $this->getHelper->getJamAuthToken() . $orderNumber);
            $ext = '/id/' . $id . '/hash/' .  $hash;
            $url = $this->getHelper->getHandoverUrl();
            $url .= '?JamCallbackURL=' . urlencode($this->getHelper->getJamCallbackUrl()) . $ext;
            $url .= '&JamCancelURL=' . urlencode($this->getHelper->getJamCancelUrl()) . $ext;
            $url .= '&JamFailURL=' . urlencode($this->getHelper->getJamFailUrl()) . $ext;
            $url .= '&JamAuthToken=' . urlencode($this->getHelper->getJamAuthToken());
            $url .= ($price) ? '&JamPlanID=' . urlencode($result['planId']) : '';
            $url .= ($orderNumber)? '&JamRetailerOrderNo=' . urlencode($orderNumber) : '';
            $url .= ($price) ? '&JamPrice=' . urlencode($price) : '';
            $url .= ($firstName) ? '&JamFirstName=' . urlencode($firstName) : '';
            $url .= ($lastName) ? '&JamFamilyName=' . urlencode($lastName) : '';
            $url .= ($email) ? '&JamEmail=' . urlencode($email) : '';
            $url .= ($dob) ? '&JamDateOfBirth=' . urlencode($dob) : '';
            $url .= ($address1) ? '&JamAddress1=' . urlencode($address1) : '';
            $url .= ($address2) ? '&JamAddress2=' . urlencode($address2) : '';
            $url .= ($city) ? '&JamSuburb=' . urlencode($city) : '';
            $url .= ($state) ? '&JamState=' . urlencode($state) : '';
            $url .= ($postcode) ? '&JamPostcode=' . urlencode($postcode) : '';
            $result['url'] = $url;
        }
        return $result;
    }

    public function onlineOrderReduction($planId,$newPurchasePrice,$adjustmentNegative) {
        $path = array('OnlineOrderReduction');
        if ($adjustmentNegative) {
            $reducePriceBy = $adjustmentNegative;
            $fullRefund = 'false';
        }
        else {
            $reducePriceBy = '0.00';
            $fullRefund = 'true';
        }
        $data = $this->createXmlPayload($path[0],'',$planId,$newPurchasePrice,$reducePriceBy,$fullRefund);
        $result = $this->makeRestCall(\Zend_Http_Client::POST, $path, $data);
        return $this->result($result);
    }
    public function onlineOrderDispatch($planId) {
        $path = array('OnlineOrderDispatchPlan');
        $data = $this->createXmlPayload($path[0],'',$planId);
        $result = $this->makeRestCall(\Zend_Http_Client::POST, $path, $data);
        return $this->result($result);
    }
}
