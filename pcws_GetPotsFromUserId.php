<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 14/10/2017
 * Time: 10:49
 */

/**
 * Get user ID from mobile app and send back pots data.
 *
 * @param $method_name String The method name, not used here
 * @param $data_in Array Contains user ID
 *
 * @return mixed Array Returns get pots result, and pot list if OK
 */

function GetPotsFromUserId($method_name, $data_in) {

    $function = "getpotsfromuserid";
    // Write in logs
    $log = "GetPotsFromUserId method called with: " . var_export($data_in, true);
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
            $userId = $data_in[0]["user_id"];

            /* ######################################################## */
            /* ######## A: Logic to get all user participation ######## */
            /* ######################################################## */

            $queryGetPots = "SELECT id_common_pot, creator, amount " .
                "FROM participate " .
                "WHERE id = " . $userId . ";";
            $dbResultGetPots = mysqli_query($connectPC, $queryGetPots);

            if (!$dbResultGetPots) {

                $faultCode = "000";
                $faultString = "Unable to get pots: " . mysqli_error($connectPC);

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Get pots failed : " . var_export($responseFault, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            if ($row = mysqli_fetch_array($dbResultGetPots, MYSQLI_BOTH)) {

                $responseArray[0]['faultCode'] = "OK";
                $i = 1;
                do {

                    /* ######################################################## */
                    /* ######### B: Logic to get all user pot details ######### */
                    /* ######################################################## */

                    $queryGetPotName = "SELECT label " .
                        "FROM common_pot " .
                        "WHERE id = " . $row[0] . ";";

                    $dbResultGetPotName = mysqli_query($connectPC, $queryGetPotName);

                    if (!$dbResultGetPotName) {
                        $faultCode = "000";
                        $faultString = "Unable to get pot name: " . mysqli_error($connectPC);

                        $responseFault[0] = array(
                            'faultCode' => $faultCode,
                            'faultString' => $faultString
                        );

                        // Write in logs
                        $log = "Get pot name failed : " . var_export($responseFault, true);
                        writeLogs($function, $log);
                        $log = "############################################";
                        writeLogs($function, $log);

                        return $responseFault;
                    }

                    if ($subRow = mysqli_fetch_array($dbResultGetPotName, MYSQLI_BOTH)) {
                        $responseArray[$i]["pot_name"] = $subRow[0];
                    } else {

                        $faultCode = "NOK";
                        $faultString = "005";

                        $responseFault[0] = array(
                         'faultCode' => $faultCode,
                         'faultString' => $faultString
                        );

                        // Write in logs
                        $log = "Pot name not found with pot Id " . $row[0] . ".";
                        writeLogs($function, $log);
                        $log = "############################################";
                        writeLogs($function, $log);

                        return $responseFault;
                    }

                    $responseArray[$i]["pot_id"] = $row[0];
                    $responseArray[$i]["creator"] = $row[1];
                    $responseArray[$i]["amount"] = $row[2];

                    $log = "Pot data fetched: ".var_export($responseArray[$i], true);
                    writeLogs($function, $log);

                    $i++;

//                    $queryGetTBFElements = "SELECT id_tbf_element " .
//                        "FROM own " .
//                        "WHERE id = " . $row[0] . ";";
//
//                    $dbResultGetTBFElements = mysqli_query($connectPC, $queryGetTBFElements);
//
//                    if (!$dbResultGetTBFElements) {
//                        $faultCode = "000";
//                        $faultString = "Unable to get element to fund: " . mysqli_error($connectPC);
//
//                        $responseFault[0] = array(
//                            'faultCode' => $faultCode,
//                            'faultString' => $faultString
//                        );
//
//                        // Write in logs
//                        $log = "Get element to fund failed : " . var_export($responseFault, true);
//                        writeLogs($function, $log);
//                        $log = "############################################";
//                        writeLogs($function, $log);
//
//                        return $responseFault;
//                    }
//
//                    if ($subRow = mysqli_fetch_array($dbResultGetTBFElements, MYSQLI_BOTH)) {
////                        TODO: had logic for TBFElements
//                    } else {
//
//                        $faultCode = "NOK";
//                        $faultString = "005";
//
//                        $responseFault[0] = array(
//                            'faultCode' => $faultCode,
//                            'faultString' => $faultString
//                        );
//
//                        // Write in logs
//                        $log = "Pot with Id " . $row[0] . " have no element to fund.";
//                        writeLogs($function, $log);
//                        $log = "############################################";
//                        writeLogs($function, $log);
//
//                        return $responseFault;
//                    }

                } while ($row = mysqli_fetch_array($dbResultGetPots, MYSQLI_BOTH));

            } else {

                $faultCode = "NOK";
                $faultString = "007";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Get pots failed : No pots found with this user id: " . $userId . ".";
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            }

            // Write in logs
            $log = "Get pots from user id successfully done: ".var_export($responseArray, true);
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
        $log = "Get pots failed : " . $faultString . ": " . $e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;

    }
}