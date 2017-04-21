<?php
    session_start();
    if (array_key_exists("user", $_SESSION)) {
        //echo "Hello " . $_SESSION['user'];
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
            
            echo DBManager::getInstance()->construct_dropdown("fPartitionFunctionForm",
                    "fPartitionFunctionList",
                    array("mammo_id","region","eqn_type"),
                    array("mammo_id","region","eqn_type"),
                    "SELECT distinct mammo_id,region,eqn_type FROM PFData;");

            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, "fPartitionFunctionList");
                $lKeyArray = explode(",",$lSelectedKey);
                $lResult = DBManager::getInstance()->get_mammogram_pfdata_by_mammo_id($lKeyArray[0],$lKeyArray[1],$lKeyArray[2]);
                $lPFIDs = DBManager::getInstance()->get_mammogram_pfdata_ids($lKeyArray[0],$lKeyArray[1],$lKeyArray[2]);
                $lXValueArray = DBManager::getInstance()->query_to_array($lPFIDs,"id");
                //$lColumnNames = array("mammo_id","region","qvalue","eqn_type","id","octave","voice","value");
                
                echo DBManager::getInstance()->pivot_table_from_query($lXValueArray, "qvalue", "value", $lResult, '<table border="black">');
                //echo MammoDB::getInstance()->table_from_query($lColumnNames,$lResult,'<table border="black">');
                
                mysqli_free_result($lResult);
            }
        ?>
            
    </body>
</html>