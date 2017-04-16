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

            echo DBManager::getInstance()->construct_dropdown("fResultStatisticForm",
                    "fResultStatisticList",
                    array("mammo_id","property_type"),
                    array("mammo_id","property_type"),
                    "SELECT distinct mammo_id,property_type FROM ResultStatistic;");

            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, "fResultStatisticList");
                $lKeyArray = explode(",",$lSelectedKey);
                $lResult = DBManager::getInstance()->query("SELECT * FROM ResultStatistic WHERE mammo_id = " . $lKeyArray[0] . " AND property_type = '" . $lKeyArray[1] ."';");

                $lColumnNames = array("mammo_id","region","property_type","mean","weighted_mean","stdev","weighted_stdev");
                
                echo DBManager::getInstance()->table_from_query($lColumnNames,$lResult,'<table border="black">');
                    
                mysqli_free_result($lResult);
            }
        ?>
            
    </body>
</html>