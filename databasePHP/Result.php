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

            echo DBManager::getInstance()->construct_dropdown("fResultForm",
                    "fResultList",
                    array("mammo_id"),
                    array("mammo_id"),
                    "SELECT distinct mammo_id FROM Result;");

            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, "fResultList");
                $lKeyArray = explode(",",$lSelectedKey);
                //$lResult = MammoDB::getInstance()->get_mammogram_partitionfunction_data($lKeyArray[0]);
                $lResult = DBManager::getInstance()->query("SELECT * from Result WHERE mammo_id = " . $lKeyArray[0] . ";");
                $lColumnNames = array("mammo_id","region","top_x","top_y","amin","amax","group_type");
                
                echo DBManager::getInstance()->table_from_query($lColumnNames,$lResult,'<table border="black">');
                    
                mysqli_free_result($lResult);
            }
        ?>
            
    </body>
</html>