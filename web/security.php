<?php

define ('HMAC_SHA256', 'sha256');
define ('SECRET_KEY', '7b2d78d922874575a8774f414eb3cddb37f7b7724f8b477aa053316ea0becb796e6a231ac49a41d5a39a9d1ccbda5ecc7bc4e3c95fc04d0e9bde6a0df6ed103b46de3baa227845e3bb7bb55f3cb91282ac46c21b08d041049924c522ce665e27801eb3b95af5452198d4753938eda991498d55a867c647e185599d709a092870');

function sign ($params) {
  return signData(buildDataToSign($params), SECRET_KEY);
}

function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return commaSeparate($dataToSign);
}

function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

?>
