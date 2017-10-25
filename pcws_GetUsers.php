<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 28/09/2017
 * Time: 12:16
 */

// Get Users
function GetUsers($method_name, $data_in) {

    $dbtable_pcommdb = "user";

	try {
		if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {
            if ($data_in[0]['role'] == role_webserv) {
                // Connect to PComm database
                $connPC = mysql_connect(server_pcommdb, user_pcommdb, passwd_pcommdb, true);

                if (!$connPC) {

                    $faultCode = "003";
                    $faultString = "Could not connect: " . mysql_error();

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;

                }

                $selectPC = mysql_select_db(dbname_pcommdb, $connPC);

                if (!$selectPC) {

                    $faultCode = "004";
                    $faultString = "Can't use ".dbname_pcommdb." DB: " . mysql_error();

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;

                }

                // Initiate variables
                $responseArray = array();
                $status = "OK";

                $queryGetUsers = "SELECT id, first_name, last_name, user_name, email_address " .
                    "FROM ".$dbtable_pcommdb.";";
                $dbresultGetUsers = mysql_query($queryGetUsers);

                if (!$dbresultGetUsers) {

                    $status = "NOK";
                    $faultCode = "006";
                    $faultString = "Unable to get ".$dbtable_pcommdb."s: " . mysql_error();

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;
                }

                if ($row = mysql_fetch_array($dbresultGetUsers, MYSQL_BOTH)) {

                    $responseArray[0]['faultCode'] = "OK";
                    $i = 1;
                    do {

                        $responseArray[$i]['id'] = $row[0];
                        $responseArray[$i]['first_name'] = $row[1];
                        $responseArray[$i]['last_name'] = $row[2];
                        $responseArray[$i]['user_name'] = $row[3];
                        $responseArray[$i]['email_address'] = $row[4];
                        $i++;

                    } while ($row = mysql_fetch_array($dbresultGetUsers, MYSQL_BOTH));

                } else {

                    $status = "NOK";
                    $faultCode = "005";
                    $faultString = "No data in ".$dbtable_pcommdb." table.";

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;

                }

                return $responseArray;
            } else {
                $responseFault = array(
                    'faultCode' => 002,
                    'faultString' => 'Unable to access method, need higher privileges.');
                return $responseFault;
            }

		} else {

			$responseFault = array(
				'faultCode' => 001,
				'faultString' => 'Unable to access web services, bad credentials.');
			return $responseFault;

		}

	} catch (Exception $e) {

		$faultCode = "000";
		$faultString = "Unknown exception: " . $e;
		
		$responseFault = array(
			'faultCode' => $faultCode,
			'faultString' => $faultString
			);
		return $responseFault;

	}
}

?>