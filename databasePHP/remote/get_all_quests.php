<?php

require_once "../DBManager.php";
require_once "../server_fns.php";
require_once "remote_misc.php";

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// array for JSON response
$response = array();

// check for required fields
if ( true  ) {
    //$lCharityID = $_GET['charityid'];
    // connecting to db
    $db = new DBManager();
 
    $lFields = ["QuestID", "CharityID", "QuestName", "Payment", "Quantity", "QuestDescription", "DropOffLocation", "DropOffLat", "DropOffLong"];


    $lResult = $db->select_table(["QuestType"], $lFields);

    build_json_response($lResult,$lFields);

    
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
    echo json_encode($response);
    //echo $_POST['user'];
}
