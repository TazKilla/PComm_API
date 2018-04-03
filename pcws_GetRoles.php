<?php

// Get Users
function GetRoles($method_name, $data_in) {

    $dbtable_pcommdb = "role";

	try {
		if ($data_in[0]['user'] == user_webserv && $data_in[0]['password'] == passwd_webserv) {
            if ($data_in[0]['role'] == role_webserv) {
                // Connect to PComm database
                $connPC = mysqli_connect(server_pcommdb, user_pcommdb, passwd_pcommdb, dbname_pcommdb);

                if (!$connPC) {

                    $faultCode = "003";
                    $faultString = "Could not connect: " . mysqli_error($connPC);

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;

                }

                // Initiate variables
                $responseArray = array();
                $status = "OK";

                $queryGetRoles = "SELECT id, label, description " .
                    "FROM ".$dbtable_pcommdb.";";
                $dbresultGetRoles = mysqli_query($connPC, $queryGetRoles);

                if (!$dbresultGetRoles) {

                    $status = "NOK";
                    $faultCode = "006";
                    $faultString = "Unable to get ".$dbtable_pcommdb."s: " . mysqli_error($connPC);

                    $responseFault = array(
                        'faultCode' => $faultCode,
                        'faultString' => $faultString
                    );
                    return $responseFault;
                }

                if ($row = mysqli_fetch_array($dbresultGetRoles, MYSQL_BOTH)) {

                    $i = 0;
                    do {

                        $responseArray[$i][0] = $row[0];
                        $responseArray[$i][1] = $row[1];
                        $responseArray[$i][2] = $row[2];
                        $i++;

                    } while ($row = mysqli_fetch_array($dbresultGetRoles, MYSQL_BOTH));

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