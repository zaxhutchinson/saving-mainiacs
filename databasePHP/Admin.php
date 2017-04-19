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
        // put your code here
        ?>
    </body>
</html>
