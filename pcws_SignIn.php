<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 01/10/2017
 * Time: 18:32
 */

// SignIn
function SignIn($method_name, $data_in) {

    $dbTable = "user";
    $function = "signin";
    // Write in logs
    $log = "SignIn method called with: ".var_export($data_in, true);
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

            // Initiate variables
            $responseArray = array();

            $firstName = $data_in[0]['first_name'];
            $lastName = $data_in[0]['last_name'];
            $userName = $data_in[0]['user_name'];
            $email = $data_in[0]['email_address'];
            $password = $data_in[0]['user_password'];
            $currency = $data_in[0]['currency'];
            $idRole = $data_in[0]['id_role'];

            $queryCreateUser = "INSERT INTO ".$dbTable." ".
                "(first_name, last_name, user_name, email_address, currency, password, id_role) ".
                "VALUES ('".
                    $firstName."', '".
                    $lastName."', '".
                    $userName."', '".
                    $email."', '".
                    $currency."', '".
                    $password."', '".
                    $idRole."');";
            $dbresultCreateUser = mysqli_query($connectPC, $queryCreateUser);

            if (!$dbresultCreateUser) {

                $faultCode = "000";
                $faultString = "Unable to create user";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Sign in failed : ".$faultString.": ".mysqli_error($connectPC);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            $userEmail = decrypt($email, "equiermentforencazertyui");
            $subject = "Bienvenue sur PComm !";
            $to      = $userEmail;
            $message = "Bonjour " . $userName . ", vous venez de vous inscrire sur PComm.\n".
                "Veuillez cliquer sur le lien suivant pour confirmer votre inscription.";
            $headers = 'From: guilohm.roche@gmail.com' . "\r\n" .
                'Reply-To: guilohm.roche@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            $resultMail = mail($to, $subject, $message, $headers);
            if (!$resultMail) {
                $log = "Unable to send email to " . $userName . ", email: " . $userEmail;
                writeLogs($function, $log);
            } else {
                $log = "Email sent to " . $userName . ", email: " . $userEmail;
                writeLogs($function, $log);
            }

            $responseArray[0]['faultCode'] = "OK";
            $responseArray[0]['userID'] = mysqli_insert_id($connectPC);

            // Write in logs
            $log = "Sign in successfully done: ".var_export($responseArray, true);
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
        $log = "Sign in failed : ".$faultString.": ".$e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;
    }
}