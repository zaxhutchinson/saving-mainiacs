<?php

require_once "../DBManager.php";
require_once "../server_fns.php";
require_once "remote_misc.php";

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    
    if($lVerify){
        $response["success"] = 1;
        $response["message"] = "Session Established";
        echo json_encode($response);
        //session_id($lUserName);
        //session_start();
        set_session_val("userid",$lUserName);
    } else {
        $response["success"] = 0;
        $response["message"] = "Unauthorized Request";
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Bad Request";
 
    // echoing JSON response
    echo json_encode($response);
    //echo $_POST['user'];
}

//echo session_id();
//echo get_session_val("userid");