<?php

/**
 * EgoPay Sci Class
 */
class EgoPaySci {
    //User payment url

    const EGOPAY_PAYMENT_URL = "https//www.egopay.com/api/pay";

    //EgoPay Store ID
    protected $store_id;
    //EgoPay Store password
    protected $store_password;
    // Set these urls if you don't want use the ones you have in the website
    //User gets redirected after success payment
    protected $success_url;
    //User gets redirected then he goes back without paying
    protected $fail_url;

    /**
     * Constructor
     * @param mixed $aParams - parametersthat initiate API object
     * The available parameters are:
     * Required:
     *   store_id - id of the store
     *   store_password - unique generated password for the store
     * Optional:
     *   success_url - success callback url
     *   fail_url - failed callback url
     */
    function __construct($aParams) {
        //Required parameters
        $aRequired = array('store_id', 'store_password');
        $aOptional = array('success_url', 'fail_url');

        foreach ($aRequired as $required)
            if (!array_key_exists($required, $aParams) || !$aParams[$required])
                throw new EgoPayException("This param is required - '$required'");

        $aBoth = array_merge($aRequired, $aOptional);
        foreach ($aBoth as $key)
            if (array_key_exists($key, $aParams) && $aParams[$key])
                $this->{$key} = $aParams[$key];
    }

    /**
     * Creates confirmation url
     * @param type $aData - data that will be sent
     * @return string - confirmation url
     */
    public function getConfirmationUrl($aData) {
        $sHash = $this->createHash($aData);
        return self::EGOPAY_PAYMENT_URL . '/?hash=' . urlencode($sHash);
    }

    public function sendRequest($aData) {
        $sUrl = $this->getConfirmationUrl($aData);
        header('Location: ' . $sUrl);
    }

    /**
     * Creates encoded data hash
     * @param type $aData
     * @return type
     */
    public function createHash($aData) {
        $aRequired = array('amount', 'currency');

        foreach ($aRequired as $required)
            if (!array_key_exists($required, $aData) || !$aData[$required])
                throw new EgoPayException("This param is required - '$required'");

        if ($this->success_url)
            $aData['success_url'] = $this->success_url;
        if ($this->fail_url)
            $aData['fail_url'] = $this->fail_url;

        $sData = serialize($aData);
        $sResult = $this->store_id . $this->encode($sData);
        return $sResult;
    }

    /**
     * Required for encoding
     * @param type $string
     * @return type
     */
    protected function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * Encodes given value
     * @param type $value
     * @return type
     */
    protected function encode($data) {
        if (!$data) {
            return false;
        }
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->store_password, $data, MCRYPT_MODE_ECB, $iv);
        return trim($this->safe_b64encode($crypttext));
    }

}

class EgoPaySciCallback {

    //EgoPay Store ID
    protected $store_id;
    //EgoPay Store password
    protected $store_password;

    //Payment details url

    const EGOPAY_REQUEST_URL = "https://www.egopay.com/api/request";

    /**
     * Constructor
     * @param mixed $aParams - parameters that initiate API object
     * The available parameters are:
     * Required:
     *   store_id - id of the store
     *   store_password - unique generated password for the store
     */
    function __construct($aParams) {
        //Required parameters
        $aRequired = array('store_id', 'store_password');

        foreach ($aRequired as $required)
            if (!array_key_exists($required, $aParams) || !$aParams[$required])
                throw new EgoPayException("This param is required - '$required'");

        foreach ($aRequired as $key)
            if (array_key_exists($key, $aParams) && $aParams[$key])
                $this->{$key} = $aParams[$key];
    }

    /**
     * Sends response to the EgoPay server with data that was sent from EgoPay
     * server
     * @param type $aParams
     * @return string response
     */
    public function getResponse($aParams) {
        if (!isset($aParams['product_id']))
            throw new EgoPayException("This param is required - 'product_id'");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, self::EGOPAY_REQUEST_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "product_id=" . urlencode($aParams['product_id'])
                . "&security_password=" . urlencode($this->store_password)
                . "&store_id=" . urlencode($this->store_id)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        curl_close($ch);

        if ($response == 'INVALID')
            throw new EgoPayException('Invalid Request To EgoPay');

        $aResponse = array();
        parse_str($response, $aResponse);
        return $aResponse;
    }

}

/**
 * EgoPay Api Exception class
 */
class EgoPayException extends Exception {

}
