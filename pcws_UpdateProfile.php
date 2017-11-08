<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 23/10/2017
 * Time: 18:51
 */

/**
 * Get user data from mobile app, and update each item if exists.
 *
 * @param $method_name String The method name, not used here
 * @param $data_in Array Contains all user data to update
 *
 * @return mixed Array Returns action result
 */
function UpdateProfile($method_name, $data_in) {

    $dbTable = "user";
    // Write in logs
    $log = "UpdateProfile method called with: ".var_export($data_in, true);
    writeLogs($method_name, $log);

    try {
        if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {
            // Connect to PComm database
            $connectPC = connectDB();
            if (key_exists("faultCode", $connectPC)) {

                // Write in logs
                $log = "Fail to open DB connection: ".var_export($connectPC, true);
                writeLogs($method_name, $log);
                $log = "############################################";
                writeLogs($method_name, $log);

                return $connectPC;
            }
//            TODO Finish

            // Initiate variables
            $responseArray = array();

            $userID = $data_in[0]['user_id'];
            $firstName = $data_in[0]['first_name'];
            $lastName = $data_in[0]['last_name'];
            $userName = $data_in[0]['user_name'];
            $email = $data_in[0]['email_address'];
            $password = $data_in[0]['user_password'];
            $currency = $data_in[0]['currency'];
            $idRole = $data_in[0]['id_role'];

            $first = true;
            $queryUpdateUser = "UPDATE ".$dbTable." SET ";
            if ($firstName != "") {
                $queryUpdateUser = $queryUpdateUser."first_name = '".$firstName."' ";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($lastName != "") {
                $queryUpdateUser = $queryUpdateUser."last_name = '".$lastName."'";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($userName != "") {
                $queryUpdateUser = $queryUpdateUser."user_name = '".$userName."'";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($email != "") {
                $queryUpdateUser = $queryUpdateUser."email_address = '".$email."'";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($password != "") {
                $queryUpdateUser = $queryUpdateUser."password = '".$password."'";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($currency != "") {
                $queryUpdateUser = $queryUpdateUser."currency = '".$currency."'";
                $first = false;
            }
            if (!$first) {
                $queryUpdateUser = $queryUpdateUser.", ";
            }
            if ($idRole != "") {
                $queryUpdateUser = $queryUpdateUser."id_role = ".$idRole."";
            }
            $queryUpdateUser = $queryUpdateUser." WHERE id = ".$userID.";";
//                "(first_name, last_name, user_name, email_address, currency, password, id_role) ".
//                "VALUES ('".
//                $firstName."', '".
//                $lastName."', '".
//                $userName."', '".
//                $email."', '".
//                $currency."', '".
//                $password."', '".
//                $idRole."');";
            $dbResultUpdateUser = mysqli_query($connectPC, $queryUpdateUser);

            if (!$dbResultUpdateUser) {

                $faultCode = "000";
                $faultString = "Unable to update user";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "UpdateProfile failed : ".$faultString.": ".mysqli_error($connectPC);
                writeLogs($method_name, $log);
                $log = "############################################";
                writeLogs($method_name, $log);

                return $responseFault;
            }

            if ($email != "") {

                $userEmail = decrypt($email, "equiermentforencazertyui");
                $subject = "Modification de votre compte PComm !";
                $to = $userEmail;
                $message = "Bonjour " . $userName . ", vous venez de modifier votre compte PComm.\n" .
                    "Veuillez cliquer sur le lien suivant pour confirmer votre nouvel email.";
                $headers = 'From: guilohm.roche@gmail.com' . "\r\n" .
                    'Reply-To: guilohm.roche@gmail.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                $resultMail = mail($to, $subject, $message, $headers);
                if (!$resultMail) {
                    $log = "Unable to send email to " . $userName . ", email: " . $userEmail;
                    writeLogs($method_name, $log);
                } else {
                    $log = "Email sent to " . $userName . ", email: " . $userEmail;
                    writeLogs($method_name, $log);
                }
            }

            $responseArray[0]['faultCode'] = "OK";

            // Write in logs
            $log = "Update user successfully done: ".var_export($responseArray, true);
            writeLogs($method_name, $log);
            $log = "############################################";
            writeLogs($method_name, $log);

            return $responseArray;

        } else {

            $responseFault[0] = array(
                'faultCode' => "NOK",
                'faultString' => "001"
            );

            // Write in logs
            $log = "Unable to access web services, bad credentials.";;
            writeLogs($method_name, $log);
            $log = "############################################";
            writeLogs($method_name, $log);

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
        $log = "UpdateProfile failed : ".$faultString.": ".$e;
        writeLogs($method_name, $log);
        $log = "############################################";
        writeLogs($method_name, $log);

        return $responseFault;
    }
}