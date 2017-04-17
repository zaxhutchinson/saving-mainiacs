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
            /** other variables */
            $lUserNameIsUnique = true;
            $lPasswordIsValid = true;				
            $lUserIsEmpty = false;					
            $lPasswordIsEmpty = false;				
            $lPasswordComfIsEmpty = false;


            /** Check that the page was requested from itself via the POST method. */
            if (server_post()) {

                
                
                /** Check whether the user has filled in their name in the text field "user" */    
                if ($_POST["user"]=="") {
                    $lUserIsEmpty = true;
                }

                if ($_POST["password"]=="") {
                    $lPasswordIsEmpty = true;
                }
                if ($_POST["passwordconf"]=="") {
                    $lPasswordComfIsEmpty = true;
                }
                if ($_POST["password"]!=$_POST["passwordconf"]) {
                    $lPasswordIsValid = false;
                } 


                if (DBManager::getInstance()->user_exists($_POST["user"])){
                    $lUserNameIsUnique = false;
                } else{
                    $lUserNameIsUnique = true;
                }
                

                /** Check whether the boolean values show that the input data was validated successfully.
                * If the data was validated successfully, add it as a new entry in the "wishers" database.
                * After adding the new entry, close the connection and redirect the application to editWishList.php.
                */
                if (!$lUserIsEmpty && $lUserNameIsUnique && !$lPasswordIsEmpty && !$lPasswordComfIsEmpty && $lPasswordIsValid) {

                    $lPost = post_array(["user", "name", "email"]);
                    array_push($lPost, date("Y-m-d"));
                    array_push($lPost, crypt_password(get_post("user"), get_post("password"), DBManager::getInstance()->get_salt() ));
                    
                    $lFields = ["UserName", "LoginName" , "EmailAddress", "CreateDate", "PasswordHash"];
                    DBManager::getInstance()->insert_into("Accounts",$lFields,$lPost);
                    DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], [get_post("user")],[true]);
                    session_start();
                    $_SESSION['user'] = get_post("user");
                    $_SESSION['userid'] = DBManager::getInstance()->get_id_by_username(get_session_val("user"));
                    DBManager::getInstance()->insert_into("Volunteers",["UserID", "LastUpdateTime"],$_SESSION['userid'], date("Y-m-d"));
                    
                    header('Location: index.php' );
                    exit;
                }

            }
        ?>
      
        
        <?php
            
            echo gen_form("Register Account", 
                    "CreateUser.php", 
                    ["User Name: ", "Name: ", "Email: ", "Password: ", "Confirm Password: "], 
                    ["text", "text", "text", "password", "password"], 
                    ["user", "name", "email", "password", "passwordconf"], 
                    "Register" );
            
            if ($lUserIsEmpty) {
                echo ("<br/>");
                echo ("Enter a valid user name.");
            }                
            if (!$lUserNameIsUnique) {
                echo ("<br/>");
                echo ("This account already exists.");
            }
            if ($lPasswordIsEmpty) {
                echo ("<br/>");
                echo ("Password must not be blank.");
            }
            if ($lPasswordComfIsEmpty) {
                echo ("<br/>");
                echo ("Confirm password must not be blank.");  
            }                
            if (!$lPasswordComfIsEmpty && !$lPasswordIsValid) {
                echo ("<br/>");
                echo  ("The passwords do not match."); 
            } 
        ?>
        
<!--    <center>
        <h3>Register Account</h3>
        <div class="form">
            <form action="CreateUser.php" method="post">
                User Name: <input type="text" name="user"><br>
                Password : <input type="password" name="password"><br>
                Confirm Password : <input type="password" name="passwordconf"><br>
                <input type="submit" value="Register"/>

                <?php

                    if ($lUserIsEmpty) {
                        echo ("<br/>");
                        echo ("Enter a valid user name.");
                    }                
                    if (!$lUserNameIsUnique) {
                        echo ("<br/>");
                        echo ("This account already exists.");
                    }
                    if ($lPasswordIsEmpty) {
                        echo ("<br/>");
                        echo ("Password must not be blank.");
                    }
                    if ($lPasswordComfIsEmpty) {
                        echo ("<br/>");
                        echo ("Confirm password must not be blank.");  
                    }                
                    if (!$lPasswordComfIsEmpty && !$lPasswordIsValid) {
                        echo ("<br/>");
                        echo  ("The passwords do not match."); 
                    }   
                ?>
                
            </form>
        </div>
    </center>
    <style>
        .form{
            border: 1px solid #D3D3D3;
            text-align: center;
            width: 300px;
        }
    </style>         -->
      
      
     </body>
</html>
