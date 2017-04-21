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
            
            echo DBManager::getInstance()->construct_dropdown("fChartDataForm",
                    "fChartDataList",
                    array("mammo_id","region","property"),
                    array("mammo_id","region","property"),
                    "SELECT distinct mammo_id, region, property FROM ChartData;");


            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, "fChartDataList");
                $lKeyArray = explode(",",$lSelectedKey);
                
                $lResult = DBManager::getInstance()->get_mammogram_chartdata_data($lKeyArray[0],$lKeyArray[1],$lKeyArray[2]);
                $lXValues = DBManager::getInstance()->get_mammogram_chartdata_xvalues($lKeyArray[0],$lKeyArray[1],$lKeyArray[2]);
                $lXValueArray = DBManager::getInstance()->query_to_array($lXValues,"x_value");

                echo DBManager::getInstance()->pivot_table_from_query($lXValueArray, "qvalue", "y_value", $lResult, '<table border="black">');

                mysqli_free_result($lResult);

                
            }
        ?>
            
    </body>
</html>