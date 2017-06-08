<?php

define ('HMAC_SHA256', 'sha256');
define ('SECRET_KEY', 'cd5456e040ea4fd1947a0ec086760795109ede06f10645f79a4fb7f9e5aeabd76d78bdeb02724a3f8ebf4868711ef8a4c541dcd6a90d4e969125f0fe9e2785ff7cc8c229d5244eff8ea7f692fcdc1182e63156f472124ab6808835d8acf139a85263f61a802049e2a0f539f958c1993f7beae6804b3345dc997e1d902bc99216');

function sign ($params) {
  return signData(buildDataToSign($params), SECRET_KEY);
}

function signData($data, $secretKey) {
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as &$field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return commaSeparate($dataToSign);
}

function commaSeparate ($dataToSign) {
    return implode(",",$dataToSign);
}

?>
