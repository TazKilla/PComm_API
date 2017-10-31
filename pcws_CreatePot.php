<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 02/10/2017
 * Time: 18:56
 */

/**
 * Get new pot data from mobile app, and create it on data base.
 *
 * @param $method_name String The method name, not used here
 * @param $data_in Array Contains new pot data
 *
 * @return mixed Array Returns pot creation result
 */
function CreatePot($method_name, $data_in) {

    $function = "createpot";
    // Write in logs
    $log = "CreatePot method called with: ".var_export($data_in, true);
    writeLogs($function, $log);

    try {
        if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {
            // Connect to PComm database
            $connectPC = connectDB();
            if (key_exists("faultCode", $connectPC)) {

                // Write in logs
                $log = "Fail to open DB connection: ".var_export($connectPC, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $connectPC;
            }

            // Write in logs
            $log = "CreatePot get data";
            writeLogs($function, $log);

            // Initiate variables
            $responseArray = array();

            $userId = $data_in[0]["user_id"];
            $userAmount = $data_in[0]["user_amount"];
            $potName = $data_in[0]["pot_name"];
            $partList = $data_in[0]["part_list"];
            $elemList = $data_in[0]["elem_list"];

            // Write in logs
            $log = "CreatePot set up query";
            writeLogs($function, $log);

            $queryCreatePot = "INSERT INTO common_pot ".
                "(label) ".
                "VALUES ('".
                $potName."');";

            // Write in logs
            $log = "CreatePot query: ".$queryCreatePot;
            writeLogs($function, $log);

            $dbresultCreatePot = mysqli_query($connectPC, $queryCreatePot);

            if (!$dbresultCreatePot) {

                $faultCode = "000";
                $faultString = "Unable to create pot";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Create pot failed : ".$faultString.": ".mysqli_error($connectPC);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            $potId = mysqli_insert_id($connectPC);

            $notification = array();

            for ($i = 0; $i < 2; $i++) {
                if ($i == 0) {
                    $queryUserToPot = "INSERT INTO participate ".
                        "(creator, amount, id, id_common_pot) ".
                        "VALUES (".
                        "1, ".
                        $userAmount.", ".
                        $userId.", ".
                        $potId.");";

                    // Write in logs
                    $log = "CreatePot ownUser: ".$queryUserToPot;
                    writeLogs($function, $log);

                    $dbresultUserToPot = mysqli_query($connectPC, $queryUserToPot);
                } else {
                    $counter = 0;
                    foreach ($partList as $partName => $amount) {
                        $queryGetSecondaryUser = "SELECT id, email_address FROM user " .
                            "WHERE user_name = '".$partName."';";

                        // Write in logs
                        $log = "CreatePot getUserId: ".$queryGetSecondaryUser;
                        writeLogs($function, $log);

                        $dbResultGetSecondaryUser = mysqli_query($connectPC, $queryGetSecondaryUser);

                        if (!$dbResultGetSecondaryUser) {
                            $faultCode = "000";
                            $faultString = "Unable to get user id";

                            $responseFault[0] = array(
                                'faultCode' => $faultCode,
                                'faultString' => $faultString
                            );

                            // Write in logs
                            $log = "Get user id failed : ".$faultString.": ".mysqli_error($connectPC);
                            writeLogs($function, $log);
                            $log = "############################################";
                            writeLogs($function, $log);

                            return $responseFault;
                        }

                        if ($row = mysqli_fetch_array($dbResultGetSecondaryUser, MYSQLI_BOTH)) {

                            $queryUserToPot = "INSERT INTO participate ".
                                "(creator, amount, id, id_common_pot) ".
                                "VALUES (".
                                "0, ".
                                $amount.", ".
                                $row[0].", ".
                                $potId.");";

                            // Write in logs
                            $log = "CreatePot User: ".$queryUserToPot;
                            writeLogs($function, $log);

                            $dbresultUserToPot = mysqli_query($connectPC, $queryUserToPot);

                            $notification[$counter]["name"] = $partName;
                            $notification[$counter]["address"] = decrypt($row[1], "equiermentforencazertyui");

                        } else {

                            $faultCode = "NOK";
                            $faultString = "005";

                            $responseFault[0] = array(
                                'faultCode' => $faultCode,
                                'faultString' => $faultString
                            );

                            // Write in logs
                            $log = "User not found with user name ".$partName.".";
                            writeLogs($function, $log);
                            $log = "############################################";
                            writeLogs($function, $log);

                            return $responseFault;
                        }
                    }
                }
            }

            foreach ($elemList as $name => $value) {
                $queryCreateTBFElem = "INSERT INTO tbf_element ".
                    "(label, value) ".
                    "VALUES ('".
                    $name."', ".
                    $value.");";

                // Write in logs
                $log = "CreatePot tbfElem: ".$queryCreateTBFElem;
                writeLogs($function, $log);

                $dbresultCreateTBFElem = mysqli_query($connectPC, $queryCreateTBFElem);

                $tBFElemId = mysqli_insert_id($connectPC);

                $queryTBFEToCommPot = "INSERT INTO own ".
                    "(id, id_tbf_element) ".
                    "VALUES (".
                    $potId.", ".
                    $tBFElemId.");";

                // Write in logs
                $log = "CreatePot tbfElemToCommPot: ".$queryTBFEToCommPot;
                writeLogs($function, $log);

                $dbresultCreateTBFElem = mysqli_query($connectPC, $queryTBFEToCommPot);
            }

            $subject = "Vous participez à un nouveau pot commun !";
            foreach ($notification as $userData) {
                $to      = $userData["address"];
                $message = "Bonjour " . $userData["name"] . ", vous avez été inscrit au pot commun " . $potName . ".\n".
                    "Consultez l'application pour voir tous les détails.";
                $headers = 'From: guilohm.roche@gmail.com' . "\r\n" .
                    'Reply-To: guilohm.roche@gmail.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                $resultMail = mail($to, $subject, $message, $headers);
                if (!$resultMail) {
                    $log = "Unable to send email to " . $userData["name"] . ", email: " . $userData["address"];
                    writeLogs($function, $log);
                } else {
                    $log = "Email sent to " . $userData["name"] . ", email: " . $userData["address"];
                    writeLogs($function, $log);
                }
            }

            $responseArray[0]['faultCode'] = "OK";

            // Write in logs
            $log = "Create pot successfully done: ".var_export($responseArray, true);
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
        $log = "Create pot failed : ".$faultString.": ".$e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;
    }
}