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
            
            echo DBManager::getInstance()->construct_dropdown("fImageResultForm",
                    "fImageResultList",
                    array("mammo_id","image_type","step_size"),
                    array("mammo_id","image_type","step_size"),
                    "SELECT mammo_id, image_type, step_size FROM ImageResult;");
            
            if(isset($_POST['formSubmit']) ){
                $lSelectedKey=filter_input(INPUT_POST, 'fImageResultList');
                $lKeyArray = explode(",",$lSelectedKey);
                $lResult = DBManager::getInstance()->get_mammogram_image_result_by_mammo_id($lKeyArray[0],$lKeyArray[1],$lKeyArray[2]);

                $lColumnNames = array("mammo_id","image_type","step_size","image");
                
                echo DBManager::getInstance()->table_from_query($lColumnNames,$lResult,'<table border="black">',"image");
                    
                mysqli_free_result($lResult);

            }
        ?>
            
    </body>
</html>