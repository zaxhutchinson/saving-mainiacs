<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <link href="CreateUser.css" type="text/css" rel="stylesheet" media="all" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        

        
        
        <?php
            require_once("DBManager.php");
            require_once("server_fns.php");
            require_once("googleHelper.php");
            /** other variables */
            


            /** Check that the page was requested from itself via the POST method. */
            if (server_post()) {

                
                /** Check whether the boolean values show that the input data was validated successfully.
                * If the data was validated successfully, add it as a new entry in the "wishers" database.
                * After adding the new entry, close the connection and redirect the application to editWishList.php.
                */
                //if (!$lUserIsEmpty && $lUserNameIsUnique && !$lPasswordIsEmpty && !$lPasswordComfIsEmpty && $lPasswordIsValid) {
                    $lPost = post_array(["charity_name", "address", "phone_number", "description", "login_id"]);

                    array_push($lPost, crypt_password(get_post("login_id"), get_post("password"), DBManager::getInstance()->get_salt() ));
                    array_push($lPost, date("Y-m-d"));
                    

                    $lCoords = get_lat_long( get_post("address"), "AIzaSyAvb7YHTZJFhMJstXAOQ4KDPLzzUXemmcQ" );
                    
                    
                    array_push($lPost,  $lCoords["lat"]);
                    array_push($lPost, $lCoords["long"]);
                    array_push($lPost, 0);

                    //print_array($lPost);
                    
                    $lFields = ["CharityName", "Address" , "PhoneNumber", "Description", "CharityLogin","PasswordHash","DateAdded","Latitude","Longitude","QuestBank"];
                    DBManager::getInstance()->insert_into("Charity",$lFields,$lPost);
                    DBManager::getInstance()->upload_image("./default.jpg", "Charity", "ProfileImage", ["CharityLogin"], [get_post("login_id")],[true]);
                    session_start();
                    $_SESSION['charity'] = get_post("user");
                    header('Location: index.php' );
                    exit;
                //}

            }
        ?>
      
        
        <?php
            
            echo gen_form("Register Charity", 
                    "CreateCharity.php", 
                    ["Charity Name: ", "Address: ", "Phone Number: ", "Description: ", "Login ID", "Password: ", "Confirm Password: "], 
                    ["text", "text", "text", "text", "text", "password", "password"], 
                    ["charity_name", "address", "phone_number", "description", "login_id", "password", "passwordconf"], 
                    "Register" );
            
        ?>
        
      
      
     </body>
</html>
