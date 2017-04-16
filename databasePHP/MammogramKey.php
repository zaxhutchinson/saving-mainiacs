<?php
    session_start();
    if (array_key_exists("user", $_SESSION)) {

    } else {
        header('Location: index.php');
        exit;
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <link href="wishlist.css" type="text/css" rel="stylesheet" media="all" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <br><a href="Menu.php">Main Menu</a>
        <!--
        <iframe src="http://abnet.ddns.net:8787/p/3848/" style="border: none; width: 440px; height: 500px"></iframe>
        -->

        <?php
            require_once("MammoDB.php");

            $lColumnNames = array("mammo_id","primary_id","secondary_id","view");
            echo DBManager::getInstance()->table_from_querystring($lColumnNames,"SELECT mammo_id,primary_id,secondary_id,view FROM MammogramKey;",'<table border="black">');
  
        ?>
            
    </body>
</html>