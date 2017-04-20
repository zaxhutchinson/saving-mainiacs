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
if ( isset($_GET['user'] ) && isset($_GET['password'] ) && isset($_GET['coins'] ) && isset($_GET['steps'] ) && isset($_GET['lat'] ) && isset($_GET['long'] ) ) {
 
    $lUserName = $_GET['user']; //get_post("name");
    $lPassword = $_GET['password']; //isset(get_post("password"));
    $lAddCoins = $_GET['coins'];
    $lLat = $_GET['lat'];
    $lLong = $_GET['long'];
    $lSteps = $_GET['steps'];
    
    // connecting to db
    $db = new DBManager();
 
    $lUserID = $db->get_id_by_username($lUserName);
    $lVerify = $db->verify_user_credentials($lUserName, $lPassword);
    //$lFields = ["UserName", "LoginName", "EmailAddress", "DaySteps", "MonthSteps", "TotalSteps", "LastLatitude", "LastLongitude", "Coins", "TotalCoins"];

    //$lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    
    if($lVerify){
        $db->add_coins($lUserID, $lAddCoins);
        $db->add_steps($lUserID, $lSteps);
        $db->update_table("Volunteers", ["LastLatitude", "LastLongitude" ], [$lLat, $lLong], ["UserID"], [$lUserID]);
        $response["success"] = 1;
        $response["message"] = "Update Successful";
        echo json_encode($response);
        
    } else {
        $response["success"] = 0;
        $response["message"] = "Unauthorized Request";
        echo json_encode($response);
    }
    
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
    echo json_encode($response);
    //echo $_POST['user'];
}
