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
$lInput = ['charityname','address','phone','description','charitylogin','password','email'];
// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lName = get_input('charityname');
    $lAddress = get_input('address');
    $lPhone = get_input('phone');
    $lDesc = get_input('description');
    $lLogin = get_input('charitylogin');
    $lPassword = get_input('password');
    $lEmail = get_input('email');
    
    // connecting to db
    $db = new DBManager();
 
    if(gen_charity($lName, $lAddress, $lPhone, $lDesc, $lLogin, $lPassword, $lEmail)){
        $response["success"] = 1;
        $response["message"] = "Charity Created";
        echo json_encode($response);
        
    } else {
        $response["success"] = 0;
        $response["message"] = "Charity account already exists.";
        echo json_encode($response);
    }
    
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    echo json_encode($response);

}
