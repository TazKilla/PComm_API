<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 18/10/2017
 * Time: 17:11
 */

// Check if user name exists
function CheckUserName($method_name, $data_in) {

    $dbTable = "user";
    $function = "checkusername";
    // Write in logs
    $log = "CheckUserName method called with: ".var_export($data_in, true);
    writeLogs($function, $log);

    try {
        if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {

            $connectPC = connectDB();
            if (key_exists("faultCode", $connectPC)) {

                // Write in logs
                $log = "Fail to open DB connection: ".var_export($connectPC, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $connectPC;
            }

//             Initiate variables
            $responseArray = array();
            $userName = $data_in[0]['user_name'];

            $freeUserName = checkFreeValue($connectPC, $dbTable, "user_name", $userName);

            if ($freeUserName) {

                $responseFault[0] = array(
                    'faultCode' => "NOK",
                    'faultString' => "006"
                );

                // Write in logs
                $log = "User name doesn't exists: ".$userName;
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            } else {

                $responseArray[0]['faultCode'] = "OK";

                // Write in logs
                $log = "User name exists: ".$userName;
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseArray;
            }
        } else {

            $responseFault[0] = array(
                'faultCode' => "NOK",
                'faultString' => "001"
            );

            // Write in logs
            $log = "Unable to access web services, bad credentials.";;
            writeLogs($function, $log);
            $log = "############################################";
            writeLogs($function, $log);

            return $responseFault;
        }
    } catch (Exception $e) {

        $faultCode = "000";
        $faultString = "Unknown exception";

        $responseFault[0] = array(
            'faultCode' => $faultCode,
            'faultString' => $faultString
        );

        // Write in logs
        $log = "CheckUserName failed : ".$faultString.": ".$e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;
    }
}