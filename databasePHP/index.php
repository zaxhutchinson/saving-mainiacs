<!DOCTYPE html>

<?php
    require_once 'server_fns.php';
    require_once("DBManager.php");
    gen_data();
    $logonSuccess = false;
    $logonCharitySuccess = false;
    // verify user's credentials
    if (server_post() ){    
        $logonSuccess = (DBManager::getInstance()->verify_user_credentials(user_post(), password_post()));
        if ($logonSuccess == true && isset($_POST['login'])) {
            session_start();
            set_user(get_post("user"));
            set_session_val("userid", DBManager::getInstance()->get_id_by_username(get_session_val("user")));
            header('Location: UserProfile.php');
            exit;
        }
        
        $logonCharitySuccess = (DBManager::getInstance()->verify_charity_credentials(get_post("charity_login"), get_post("charity_password")));
        if ($logonCharitySuccess == true) {
            session_start();
            set_user(get_post("charity_login"));
            set_session_val('charityid', DBManager::getInstance()->get_id_by_charity(get_post("charity_login")));
            header('Location: CharityProfile.php');
            exit;
        }
        
    }
?>


<html>
    <head>
        <link href="style.css" type="text/css" rel="stylesheet" media="all" />
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        <br>Register User <a href="CreateUser.php">Create now</a>
        <br>Register Charity <a href="CreateCharity.php">Create now</a>
    </body>
    
    <center>
        <h3>Community Matters Login</h3>
        <div class="form">
            <form action="index.php" method="post">
                User Name: <input type="text" name="user"><br>
                Password : <input type="password" name="userpassword"><br>
                <input type="submit" name="login" value="login"><br>

                <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
                        if (!$logonSuccess) {
                            echo "Invalid name and/or password";
                        }
                    }
                ?>            
            </form>
        </div>
    </center>
    <style>
        .form{
            border: 1px solid #D3D3D3;
            text-align: center;
            width: 300px;
        }
    </style>    

    <?php




    
        echo gen_form("Charity Login", 
                "index.php", 
                ["Charity Login: ", "Charity Password: "], 
                ["text", "password"], 
                ["charity_login", "charity_password"], 
                "Charity Login" );
        

            if ($_SERVER["REQUEST_METHOD"] == "POST" && get_post("Charity Login") == "Charity Login") { 
                if (!$logonCharitySuccess) {
                    echo "Invalid charity name and/or password";
                }
            }
        
            //echo get_post("CharityLogin");
            
    ?>
    
    
</html>



