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
$lInput = ['charity', 'password','questname','desc','address'];
// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lCharityName = get_input('charity');
    $lPassword = get_input('password');
    $lQuestName = get_input('questname');
    $lQuantity = 0;
    $lQuestDescription = get_input('desc');
    $lDropOffLocation = get_input('address');
    
    // connecting to db
    $db = new DBManager();
 
    $lCharityID = $db->get_id_by_charity($lCharityName);
    $lVerify = $db->verify_charity_credentials($lCharityName, $lPassword);
    
    if($lVerify){
        gen_quest($lCharityID, $lQuestName, $lPayment, $lQuantity, $lQuestDescription, $lDropOffLocation);
        $response["success"] = 1;
        $response["message"] = "Insert Successful";
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

    echo json_encode($response);

}
