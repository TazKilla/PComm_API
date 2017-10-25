<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 28/09/2017
 * Time: 12:16
 */

function writeLogs($function, $text) {
    $date = date("Y-m-d - ");
    $datetime = date("Y-m-d H:i:s - ");
    $filename = "logs/".$date.$function.".log";

    // Write in logs method call
    file_put_contents($filename, $datetime.$text."\r\n", FILE_APPEND | LOCK_EX);
}

function connectDB() {
    // Connect to PComm database
    $connPC = mysqli_connect(server_pcommdb, user_pcommdb, passwd_pcommdb, dbname_pcommdb);

    if (!$connPC) {

        $faultCode = "000";
        $faultString = "Could not connect: " . mysqli_error($connPC);

        $responseFault[0] = array(
            'faultCode' => $faultCode,
            'faultString' => $faultString
        );
        return $responseFault;
    }

    return $connPC;
}

function checkFreeValue($DBLink, $DBTable, $attribute, $value) {

    $queryCheckFreeValue = "SELECT id " .
        "FROM ".$DBTable." " .
        "WHERE ".$attribute." = '".$value."';";
    $dbresultCheckFreeValue = mysqli_query($DBLink, $queryCheckFreeValue);

    if (!$dbresultCheckFreeValue) {

        $faultCode = "000";
        $faultString = "Unable to get ".$DBTable."s: " . mysqli_error($DBLink);

        $responseFault[0] = array(
            'faultCode' => $faultCode,
            'faultString' => $faultString
        );
        return $responseFault;
    }

    if ($row = mysqli_fetch_array($dbresultCheckFreeValue, MYSQLI_BOTH)) {
        return false;
    } else {
        return true;
    }
}

function decrypt($string, $secret) {
    $string = base64_decode($string);
//    $code = hex2bin($string);

    $td = mcrypt_module_open('rijndael-128', '', 'ecb', '');

    mcrypt_generic_init($td, $secret, '');
    $decrypted = mdecrypt_generic($td, $string);

    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return utf8_encode(trim($decrypted));
}
//
//function hex2bin($hexdata) {
//    $bindata = '';
//
//    for ($i = 0; $i < strlen($hexdata); $i += 2) {
//        $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
//    }
//
//    return $bindata;
//}
