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
if ( isset($_GET['user'] ) && isset($_GET['password'] ) ) {
 
    $lUserName = $_GET['user']; //get_post("name");
    $lPassword = $_GET['password']; //isset(get_post("password"));

    
    // connecting to db
    $db = new DBManager();
 
    $lUserID = $db->get_id_by_username($lUserName);
    $lVerify = $db->verify_user_credentials($lUserName, $lPassword);
    $lFields = ["CharityID", "UserID", "Quantity", "Date"];

    $lResult = $db->select_table(["PointDonations"], $lFields, ["UserID"], [$lUserID]);  
    
    if($lVerify){
        build_json_response($lResult,$lFields);
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
