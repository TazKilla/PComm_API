<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 28/09/2017
 * Time: 12:16
 */

// LogIn
function LogIn($method_name, $data_in) {

    $dbTable = "user";
    $function = "login";
    // Write in logs
    $log = "LogIn method called with: ".var_export($data_in, true);
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

            $userName = $data_in[0]['user_name'];
            $userPassword = $data_in[0]['user_password'];

            $queryCheckLogin = "SELECT id, first_name, last_name, user_name, email_address, password " .
                "FROM ".$dbTable." ".
                "WHERE user_name = '".$userName."' ".
                "AND password = '".$userPassword."';";
            $dbResultCheckLogin = mysqli_query($connectPC, $queryCheckLogin);

//            If mysqli_query failed
            if (!$dbResultCheckLogin) {

                $faultCode = "000";
                $faultString = "Unable to get ".$dbTable."s: " . mysqli_error(connectDB());

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Login failed : ".var_export($responseFault, true);
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            if ($row = mysqli_fetch_array($dbResultCheckLogin, MYSQLI_BOTH)) {

                $responseArray[0]['faultCode'] = "OK";
                $i = 1;
                do {

                    $responseArray[$i]['id'] = $row[0];
                    $responseArray[$i]['first_name'] = $row[1];
                    $responseArray[$i]['last_name'] = $row[2];
                    $responseArray[$i]['user_name'] = $row[3];
                    $responseArray[$i]['email_address'] = $row[4];
                    $responseArray[$i]['password'] = $row[5];
                    $i++;

                } while ($row = mysqli_fetch_array($dbResultCheckLogin, MYSQLI_BOTH));

            } else {

                $faultCode = "NOK";
                $faultString = "005";

                $responseFault[0] = array(
                    'faultCode' => $faultCode,
                    'faultString' => $faultString
                );

                // Write in logs
                $log = "Login failed : No user found with those credentials.";
                writeLogs($function, $log);
                $log = "############################################";
                writeLogs($function, $log);

                return $responseFault;
            }

            // Write in logs
            $log = "Login successfully done: ".var_export($responseArray, true);
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
        $log = "Login failed : ".$faultString.": ".$e;
        writeLogs($function, $log);
        $log = "############################################";
        writeLogs($function, $log);

        return $responseFault;
	}
}