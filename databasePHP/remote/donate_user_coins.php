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
if ( true ) {
 
    //$lUserName = $_GET['user']; //get_post("name");
    //$lPassword = $_GET['password']; //isset(get_post("password"));

    
    // connecting to db
    $db = new DBManager();
 
    //$lUserID = $db->get_id_by_username($lUserName);
    $lVerify = true; //$db->verify_user_credentials($lUserName, $lPassword);
    //$lFields = ["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank"];

    //$lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    //$lResult = $db->select_table(["Charity"], ["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank"]);

    $lResult = $db->query("SELECT UserID FROM Accounts;");
    
    //$lResult = $db->query("SELECT UserName,LoginName,PointDonations.UserID,sum(Quantity) AS SUMQuantity FROM Accounts,PointDonations WHERE PointDonations.UserID=Accounts.UserID GROUP BY UserID ORDER BY SUMQuantity DESC;");
    $lFields = ["UserID"];
    
    if($lVerify){
        $lQueryRes = build_query_array($lResult,$lFields);
        $lCount = count($lQueryRes);
        
        //echo count($lQueryRes) . " : " . count($lQueryRes[0]) . " : " . count($lQueryRes[0][0]) . "<br/>";
        
        for($i = 0; $i < $lCount; $i++){
            //echo $i . " : " . $lQueryRes[$i]["UserID"] . "<br/>";
            $db->donate($lQueryRes[$i]["UserID"]);
        }
        
        $response["success"] = 1;
        $response["message"] = "Coins Donated";
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
