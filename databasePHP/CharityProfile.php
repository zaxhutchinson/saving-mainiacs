<?php
    require_once 'server_fns.php';
    require_once("DBManager.php");
    session_start();
    if (array_key_exists("user", $_SESSION)) {
        //echo "Welcome " . get_user() . " : " . get_session_val("userid");
    } else {
        header('Location: index.php');
        exit;
    }
?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            //echo "Testing" . "<br/>";
            //echo get_session_val("charityid") . "<br/>";
            $lQuery = DBManager::getInstance()->select_table(["Charity"], ["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank", "ProfileImage"], ["CharityID"], [get_session_val('charityid')]);
            echo DBManager::getInstance()->table_from_query(["CharityName", "CharityLogin", "Latitude", "Longitude", "Address", "PhoneNumber", "Description", "QuestBank", "ProfileImage"], $lQuery, '<table border="black">', "ProfileImage");
            
        ?>
    </body>
</html>
