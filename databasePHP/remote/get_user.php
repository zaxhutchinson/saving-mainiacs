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
$lInput = ['user', 'password'];

// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lUserName = get_input('user');
    $lPassword = get_input('password');

    
    // connecting to db
    $db = new DBManager();
 
    $lUserID = $db->get_id_by_username($lUserName);
    $lVerify = $db->verify_user_credentials($lUserName, $lPassword);
    $lFields = ["Accounts.UserID","UserName", "LoginName", "EmailAddress", "DaySteps", "MonthSteps", "TotalSteps", "LastLatitude", "LastLongitude", "Coins", "TotalCoins"];
    $lFieldsNames = ["UserID","UserName", "LoginName", "EmailAddress", "DaySteps", "MonthSteps", "TotalSteps", "LastLatitude", "LastLongitude", "Coins", "TotalCoins"];
    
    $lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    
    //$lResult = $db->query("SET @rank=0;");
    //$lResult = $db->query("SELECT * (FROM SELECT @rank:=@rank+1 AS Rank,VIEW_PROFILES_SUM.* FROM VIEW_PROFILES_SUM) AS Temp WHERE Temp.UserID =" . $lUserID . ";");
        
    
    if($lVerify){
        build_json_response($lResult,$lFieldsNames);
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
