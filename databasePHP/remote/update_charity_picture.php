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
$lInput = ['charity', 'password','picture'];
// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lUserName = get_input('charity');
    $lPassword = get_input('password');
    $lPicture = base64_decode(get_input('picture'));
    
    // connecting to db
    $db = new DBManager();
    $lCharityID = $db->get_id_by_charity($aCharityName);
    $lVerify = $db->verify_user_credentials($lUserName, $lPassword);
    //$lFields = ["UserName", "LoginName", "EmailAddress", "DaySteps", "MonthSteps", "TotalSteps", "LastLatitude", "LastLongitude", "Coins", "TotalCoins"];

    //$lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    
    if($lVerify){
        $db->add_coins($lUserID, $lAddCoins);
        $db->add_steps($lUserID, $lSteps);
        $db->upload_image_data($lPicture, "Charity", "ProfileImage", ["CharityID"], [$lCharityID],[true]);
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
