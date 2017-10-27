<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 22/10/2017
 * Time: 15:50
 */

/** Get pot details
 *
 * From Pot ID, return participant list and to be fund element list
 *
 * @param
 */
function GetPotDetails($method_name, $data_in) {

    $function = "getpotdetails";
    // Write in logs
    $log = "GetPotDetails method called with: " . var_export($data_in, true);
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
            $potId = $data_in[0]["pot_id"];

            /* ######################################################## */
            /* ####### A: Logic to get all element from the pot ####### */
            /* ######################################################## */

            $queryGetPotDetails = "SELECT id_tbf_element " .
                "FROM own " .
                "WHERE id = " . $potId . ";";
            $dbResultGetPotDetails = mysqli_query($connectPC, $queryGetPotDetails);

            if (!$dbResultGetPotDetails) {

                $faultCode = "000";
                $faultString = "Unable to get pot details: " . mysqli_error($connectPC);

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

            if ($row = mysqli_fetch_array($dbResultGetPotDetails, MYSQLI_BOTH)) {

                $responseArray[0]['faultCode'] = "OK";
                $i = 1;
                do {

                    /* ######################################################## */
                    /* ####### B: Logic to get all element pot details ######## */
                    /* ######################################################## */

                    $queryGetPotName = "SELECT label, value " .
                        "FROM tbf_element " .
                        "WHERE id = " . $row[0] . ";";

                    $dbResultGetPotElements = mysqli_query($connectPC, $queryGetPotName);

                    if (!$dbResultGetPotElements) {
                        $faultCode = "000";
                        $faultString = "Unable to get pot details: " . mysqli_error($connectPC);

                        $responseFault[0] = array(
                            'faultCode' => $faultCode,
                            'faultString' => $faultString
                        );

                        // Write in logs
                        $log = "Get pot details failed : " . var_export($responseFault, true);
                        writeLogs($function, $log);
                        $log = "############################################";
                        writeLogs($function, $log);

                        return $responseFault;
                    }

                    if ($subRow = mysqli_fetch_array($dbResultGetPotElements, MYSQLI_BOTH)) {

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

                    $responseArray[$i]["element_name"] = $subRow[0];
                    $responseArray[$i]["element_value"] = $subRow[1];

                    $log = "Pot details fetched: ".var_export($responseArray[$i], true);
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
////                        TODO: add logic for TBFElements
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

                } while ($row = mysqli_fetch_array($dbResultGetPotDetails, MYSQLI_BOTH));

            } else {

                $faultCode = "NOK";
                $faultString = "007";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Get pot details failed : No details found with this pot id: " . $potId . ".";
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;

            }

            // Write in logs
            $log = "Get pot details id successfully done: ".var_export($responseArray, true);
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
        $log = "Get pot details failed : " . $faultString . ": " . $e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;

    }
}