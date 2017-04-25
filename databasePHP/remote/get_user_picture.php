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
$lInput = ['userid'];

// check for required fields
if ( isset_input_list($lInput) ) {
 
    $lUserID = get_input('userid');
    
    // connecting to db
    $db = new DBManager();
    $lData = $db->get_db_blob("ProfileImage", "Accounts", "UserID", $lUserID);
    
    if(!is_null($lData)){
        $response["data"] = $lData;
        $response["type"] = "data:image/jpeg;base64";
        $response["success"] = 1;
        $response["message"] = "Image Data Retrieved";
        echo json_encode($response);
        //session_id($lUserName);
        //session_start();
        set_session_val("userid",$lUserName);
    } else {
        $response["success"] = 0;
        $response["message"] = "Bad Request";
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
