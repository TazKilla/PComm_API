<?php
/**
 * Created by PhpStorm.
 * User: Talkamynn
 * Date: 28/09/2017
 * Time: 12:16
 */

/*
 * PHP PComm XMLRPC Server entry points
 */

require_once ('pcws_Config.php');
require_once ('pcws_Utils.php');
require_once ('pcws_GetUsers.php');
require_once ('pcws_GetRoles.php');
require_once ('pcws_LogIn.php');
require_once ('pcws_SignIn.php');
require_once ('pcws_CheckFreeData.php');
require_once ('pcws_CreatePot.php');
require_once ('pcws_GetPotsFromUserId.php');
require_once ('pcws_GetEmailFromUserName.php');
require_once ('pcws_CheckUserName.php');
require_once ('pcws_GetPotDetails.php');

// Read an XMLRPC request through the input stream
$request_xml = file_get_contents("php://input");

// Create PComm XMLRPC server
$xmlrpc_server = xmlrpc_server_create();

// Register PComm XMLRPC server
xmlrpc_server_register_method($xmlrpc_server, "GetUsers", "GetUsers");
xmlrpc_server_register_method($xmlrpc_server, "GetRoles", "GetRoles");
xmlrpc_server_register_method($xmlrpc_server, "LogIn", "LogIn");
xmlrpc_server_register_method($xmlrpc_server, "SignIn", "SignIn");
xmlrpc_server_register_method($xmlrpc_server, "CheckFreeData", "CheckFreeData");
xmlrpc_server_register_method($xmlrpc_server, "CreatePot", "CreatePot");
xmlrpc_server_register_method($xmlrpc_server, "GetPotsFromUserId", "GetPotsFromUserId");
xmlrpc_server_register_method($xmlrpc_server, "GetEmailFromUserName", "GetEmailFromUserName");
xmlrpc_server_register_method($xmlrpc_server, "CheckUserName", "CheckUserName");
xmlrpc_server_register_method($xmlrpc_server, "GetPotDetails", "GetPotDetails");

// Start the server listener
header('Content-Type: text/xml');
print xmlrpc_server_call_method($xmlrpc_server, $request_xml, array());

?>
