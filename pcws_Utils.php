<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 28/09/2017
 * Time: 12:16
 */

/**
 * Creates or update log file.
 *
 * @param $function String The function name to identify log file
 * @param $text String The log text to write
 */
function writeLogs($function, $text) {
    $date = date("Y-m-d - ");
    $datetime = date("Y-m-d H:i:s - ");
    $filename = "logs/".$date.$function.".log";

    // Write in logs method call
    file_put_contents($filename, $datetime.$text."\r\n", FILE_APPEND | LOCK_EX);
}

/**
 * Creates and returns mysqli object
 *
 * @return mysqli DB Connection object
 */
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

/**
 * Check if specified value exists in the specified table for a specified attribute.
 *
 * @param $DBLink mysqli The data base connection
 * @param $DBTable String The table to check in
 * @param $attribute String The attribute to check
 * @param $value String The value to look for
 *
 * @return bool True if the value doesn't exists, false if exists
 */
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

/**
 * Decrypt a string in the same way as the mobile app.
 *
 * @param $string String The string to decrypt
 * @param $secret String  The key used to encrypt/decrypt string
 * @return string String The decrypted string
 */
function decrypt($string, $secret) {
    $string = base64_decode($string);

    $td = mcrypt_module_open('rijndael-128', '', 'ecb', '');

    mcrypt_generic_init($td, $secret, '');
    $decrypted = mdecrypt_generic($td, $string);

    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return utf8_encode(trim($decrypted));
}
