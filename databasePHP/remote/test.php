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
if ( false ) {
 
    $lHeader = get_input("h");
    echo $lHeader . "<br/>";
    echo gzdecode($lHeader);
    //echo (urlendecode($lHeader)) . "<br/>";
    
    //$lUserName = $_GET['user']; //get_post("name");
    //$lPassword = $_GET['password']; //isset(get_post("password"));

    
    // connecting to db
    $db = new DBManager();
 
    //$lUserID = $db->get_id_by_username($lUserName);
    $lVerify = true; //$db->verify_user_credentials($lUserName, $lPassword);
    //$lFields = ["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank"];

    //$lResult = $db->select_table(["Accounts", "Volunteers"], $lFields, ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", $lUserID]);  
    //$lResult = $db->select_table(["Charity"], ["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank"]);
    $lResult = $db->query("SET @rank=0;");
    $lResult = $db->query("SELECT @rank:=@rank+1 AS Rank, UserName,LoginName,UserID, SUMQuantity FROM (SELECT UserName,LoginName,PointDonations.UserID as UserID,sum(Quantity) AS SUMQuantity FROM Accounts,PointDonations WHERE PointDonations.UserID=Accounts.UserID GROUP BY UserID ORDER BY SUMQuantity DESC) AS Temp;");
    
    //$lResult = $db->query("SELECT UserName,LoginName,PointDonations.UserID,sum(Quantity) AS SUMQuantity FROM Accounts,PointDonations WHERE PointDonations.UserID=Accounts.UserID GROUP BY UserID ORDER BY SUMQuantity DESC;");
    $lFields = ["Rank", "UserName", "LoginName", "UserID","SumQuantity"];
    
    if($lVerify){
        $lEncoded = openssl_encrypt("testing", "AES-256-OFB", "testing");
        $lDecoded = openssl_decrypt($lEncoded, "AES-256-OFB", "testing");
        $lZipObj = new ZipArchive();
        $lZipObj->setPassword("testing");
        //$lZipObj->addFromString($localname, $contents)
        $lZipped = gzencode(build_json_response_string($lResult,$lFields));
        
        $lBase64 = urlencode($lZipped);
        
        echo $lEncoded . "<br/>";
        echo $lDecoded . "<br/>";
        
        
        //$lReg = base64_decode($lBase64);
        //echo $lZipped . "<br/>";
        //echo $lBase64 . "<br/>";
        //echo $lReg . "<br/>";
        //echo gzdecode($lZipped) . "<br/>";
        
    } else {
        $response["success"] = 0;
        $response["message"] = "Unauthorized Request";
        echo base64_encode(gzcompress(json_encode($response)));
        
    }
    
} else {
    // required field is missing
    //$response["success"] = 0;
    //$response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
    //echo gzcompress(json_encode($response));
    //echo $_POST['user'];
    
    //print_r( [12,45,66,77,46] );
    //print_r( normalize([12,45,66,77,46]) );
    //print_r( normalize_int([12,45,66,77,46]) );
    
    echo base64_encode(file_get_contents("test.jpg"));
    
}
