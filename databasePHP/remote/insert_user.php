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
$lInput = ['user', 'password','fullname','email'];
// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lUser = get_input('user');
    $lPassword = get_input('password');
    $lName = get_input('fullname');
    $lEmail = get_input('email');
    
    // connecting to db
    $db = new DBManager();
 
    if(gen_user($lUser, $lPassword, $lName, $lEmail)){
        $response["success"] = 1;
        $response["message"] = "User Created";
        echo json_encode($response);
        
    } else {
        $response["success"] = 0;
        $response["message"] = "User account already exists.";
        echo json_encode($response);
    }
    
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    echo json_encode($response);

}
