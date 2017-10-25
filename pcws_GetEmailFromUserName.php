<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 17/10/2017
 * Time: 10:15
 */

// Get email from user name
function GetEmailFromUserName($method_name, $data_in) {

    $function = "getemailfromusername";
    // Write in logs
    $log = "GetEmailFromUserName method called with: " . var_export($data_in, true);
    writeLogs($function, $log);

    try {
        if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {

            // Connect to PComm database
            $connectPC = connectDB();
            if (key_exists("faultCode", $connectPC)) {

                // Write in logs
                $log = "Fail to open DB connection: " . var_export($connectPC, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $connectPC;
            }

            // Initiate variables
            $responseArray = array();
            $userName = $data_in[0]["user_name"];

            $queryGetEmail = "SELECT email_address " .
                "FROM user " .
                "WHERE user_name = '" . $userName . "';";
            $dbResultGetEmail = mysqli_query($connectPC, $queryGetEmail);

            if (!$dbResultGetEmail) {

                $faultCode = "000";
                $faultString = "Unable to get email: " . mysqli_error($connectPC);

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Get email failed : " . var_export($responseFault, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            if ($row = mysqli_fetch_array($dbResultGetEmail, MYSQLI_BOTH)) {

                $responseArray[0]['faultCode'] = "OK";

                $decryptedEmail = decrypt($row[0], "equiermentforencazertyui");
                $responseArray[1]["email_address"] = $decryptedEmail;
                $responseArray[1]["user_name"] = $userName;

            } else {

                $faultCode = "NOK";
                $faultString = "005";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Get email failed : No email found with this user name: " . $userName . ".";
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            }

            // Write in logs
            $log = "Get email from user name successfully done: ".var_export($responseArray, true);
            writeLogs($function, $log);
            $log = "############################################";
            writeLogs($function, $log);

            return $responseArray;

        } else {

            $responseFault[0] = array(
                'faultCode' => "NOK",
                'faultString' => "001"
            );

            // Write in logs
            $log = "Unable to access web services, bad credentials";
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
        $log = "Get email failed : " . $faultString . ": " . $e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;

    }
}