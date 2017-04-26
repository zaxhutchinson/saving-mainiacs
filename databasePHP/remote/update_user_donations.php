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
$lInput = ['user', 'password','c1','c2','c3','c4','c5','p1','p2','p3','p4','p5'];
// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lUserName = get_input('user');
    $lPassword = get_input('password');
    
    $lPercents = [get_input('p1'), get_input('p2'), get_input('p3'), get_input('p4') ,get_input('p5')];
    $lCharityIDs = [get_input('c1'), get_input('c2'), get_input('c3'), get_input('c4') ,get_input('c5')];

    // connecting to db
    $db = new DBManager();
 
    $lUserID = $db->get_id_by_username($lUserName);
    $lVerify = $db->verify_user_credentials($lUserName, $lPassword);
    //$lFields = ["UserName", "LoginName", "EmailAddress", "DaySteps", "MonthSteps", "TotalSteps", "LastLatitude", "LastLongitude", "Coins", "TotalCoins"];

    //$lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    
    if($lVerify){
        
        $db->update_donations($lUserID, $lCharityIDs, $lPercents);
        
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
