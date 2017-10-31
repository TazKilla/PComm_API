<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 29/09/2017
 * Time: 19:22
 */

/**
 * Check if user name and email are available.
 *
 * @param $method_name String The method name, not used here
 * @param $data_in Array Contains user name and email
 *
 * @return mixed Array Returns action result
 */
function CheckFreeData($method_name, $data_in) {

    $dbTable = "user";
    $function = "checkfreedata";
    // Write in logs
    $log = "CheckFreeData method called with: ".var_export($data_in, true);
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
            $email = $data_in[0]['email_address'];

            $freeUserName = checkFreeValue($connectPC, $dbTable, "user_name", $userName);
            $freeEmail = checkFreeValue($connectPC, $dbTable, "email_address", $email);

            if (!$freeUserName && !$freeEmail) {

                $responseFault[0] = array(
                    'faultCode' => "NOK",
                    'faultString' => "002"
                );

                // Write in logs
                $log = "User name and email already used: ".$userName."/".$email;
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            } else if (!$freeUserName && $freeEmail) {

                $responseFault[0] = array(
                    'faultCode' => "NOK",
                    'faultString' => "003"
                );

                // Write in logs
                $log = "User name already used: ".$userName;
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            } else if ($freeUserName && !$freeEmail) {

                $responseFault[0] = array(
                    'faultCode' => "NOK",
                    'faultString' => "004"
                );

                // Write in logs
                $log = "Email already used: ".$email;
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            } else {

                $responseArray[0]['faultCode'] = "OK";

                // Write in logs
                $log = "User name and email available: ".$userName."/".$email;
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
        $log = "CheckFreeData failed : ".$faultString.": ".$e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;
    }
}