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
            
            echo DBManager::getInstance()->construct_dropdown("fMaxGaussForm",
                    "fMaxGaussList",
                    array("mammo_id"),
                    array("mammo_id"),
                    "SELECT DISTINCT mammo_id FROM MaxGaussian;");
            
            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, 'fMaxGaussList');
                $lKeyArray = explode(",",$lSelectedKey);
                $lResult = DBManager::getInstance()->get_mammogram_maxgauss_data($lKeyArray[0]);

                $lColumnNames = array("mammo_id","primary_id","secondary_id","view","id","octave","voice");
                
                echo DBManager::getInstance()->table_from_query($lColumnNames,$lResult,'<table border="black">');
                    
                mysqli_free_result($lResult);

            }
        ?>
            
    </body>
</html>