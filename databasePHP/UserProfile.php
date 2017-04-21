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
            $lQuery = DBManager::getInstance()->select_table(["Accounts", "Volunteers"], ["UserName", "LoginName", "EmailAddress", "ProfileImage", "DaySteps", "MonthSteps", "TotalSteps", "Coins", "TotalCoins"], ["Accounts.UserID", "Accounts.UserID"], ["Volunteers.UserID", get_session_val('userid')]);
            echo DBManager::getInstance()->table_from_query(["UserName", "LoginName", "EmailAddress", "ProfileImage", "DaySteps", "MonthSteps", "TotalSteps", "Coins", "TotalCoins"], $lQuery, '<table border="black">', "ProfileImage");
            
            echo "<br/>List of Charities (For selecting the user's charities)<br/>";
            echo DBManager::getInstance()->construct_dropdown("fSelectCharity1",
                    "fSelectCharity1",
                    ["CharityID"],
                    ["CharityName"],
                    "SELECT CharityID,CharityName FROM Charity;");
            echo "<br/>";
            
            
        ?>

    </body>
</html>
