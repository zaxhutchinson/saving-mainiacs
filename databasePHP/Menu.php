<?php
    require_once("server_fns.php");
    session_start();
    if (array_key_exists("user", $_SESSION)) {
        echo "Welcome " . get_user() . " : " . get_session_val("userid");
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
        <br><a href="CreateCharity.php">View Create Charity</a>
        <br><a href="MammogramKey.php">View Mammogram Keys</a>
        <br><a href="MammogramUser.php">View Users</a>
        <br><a href="Region.php">View Regions</a>
        <br><a href="Result.php">View Result</a>
        <br><a href="ResultNoScaling.php">View No Scaling</a>
        <br><a href="ResultStatistic.php">View Result Statistic</a>
        <br><a href="ResultQValue.php">View Result QValue</a>
        <br><a href="ViewImageResults.php">View Image Result</a>
        <br><a href="ChartData.php">View Chart Data</a>
        <br><a href="PartitionFunction.php">View Partition Function</a>
        <br><a href="PFData.php">View Partition Function Data</a>
        <br><a href="MaxGaussian.php">View Max Gaussian</a>
    </body>
</html>
