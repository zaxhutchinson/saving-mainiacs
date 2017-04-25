<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <link href="CreateUser.css" rel="stylesheet" media="all" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <div id ="banner">
    	<h1><strong><span style="color: #fff;">Bangor </span></strong><span style="color: #939598;">Community</span></h1>
    </div>
    <div id="contain">
    <body>
		<main>
			<div id="bod">
			<div id="create">
			<?php
				require_once("DBManager.php");
				require_once("server_fns.php");
				/** other variables */
				$lUserNameIsUnique = true;
				$lPasswordIsValid = true;				
				$lUserIsEmpty = false;					
				$lPasswordIsEmpty = false;				
				$lPasswordComfIsEmpty = false;


				/** Check that the page was requested from itself via the POST method. */
				if (server_post()) {

				
				
					/** Check whether the user has filled in their name in the text field "user" */    
					if ($_POST["user"]=="") {
						$lUserIsEmpty = true;
					}

					if ($_POST["password"]=="") {
						$lPasswordIsEmpty = true;
					}
					if ($_POST["passwordconf"]=="") {
						$lPasswordComfIsEmpty = true;
					}
					if ($_POST["password"]!=$_POST["passwordconf"]) {
						$lPasswordIsValid = false;
					} 


					if (DBManager::getInstance()->user_exists($_POST["user"])){
						$lUserNameIsUnique = false;
					} else{
						$lUserNameIsUnique = true;
					}
				

					/** Check whether the boolean values show that the input data was validated successfully.
					* If the data was validated successfully, add it as a new entry in the "wishers" database.
					* After adding the new entry, close the connection and redirect the application to editWishList.php.
					*/
					if (!$lUserIsEmpty && $lUserNameIsUnique && !$lPasswordIsEmpty && !$lPasswordComfIsEmpty && $lPasswordIsValid) {

						gen_user(get_post("user"), get_post("password"), get_post("name"), get_post("email"));
					
						session_start();
						$_SESSION['user'] = get_post("user");
						$_SESSION['userid'] = DBManager::getInstance()->get_id_by_username(get_session_val("user"));

						header('Location: index.php' );
						exit;
					}

				}
			?>
			<center>
			<div id="form">
				<form action="CreateUser.php" method="post">
					<h2>CREATE A PROFILE</h2>
					<input type="text" name="username" placeholder="Username...."><br>
					<input type="text" name="name" placeholder="Name...."><br>
					<input type="text" name="email" placeholder="Email...."><br>
					<input type="password" name="userpassword" placeholder="Password...."><br>
					<input type="password" name="userpassword" placeholder="Confirm Password...."><br>
					<input type="submit" name="login" value="Create"><br>

					<?php
			
						// echo gen_form("Register Account", 
			//                     "CreateUser.php", 
			//                     ["User Name: ", "Name: ", "Email: ", "Password: ", "Confirm Password: "], 
			//                     ["text", "text", "text", "password", "password"], 
			//                     ["user", "name", "email", "password", "passwordconf"], 
			//                     "Register" );
			
						if ($lUserIsEmpty) {
							echo ("<br/>");
							echo ("Enter a valid user name.");
						}                
						if (!$lUserNameIsUnique) {
							echo ("<br/>");
							echo ("This account already exists.");
						}
						if ($lPasswordIsEmpty) {
							echo ("<br/>");
							echo ("Password must not be blank.");
						}
						if ($lPasswordComfIsEmpty) {
							echo ("<br/>");
							echo ("Confirm password must not be blank.");  
						}                
						if (!$lPasswordComfIsEmpty && !$lPasswordIsValid) {
							echo ("<br/>");
							echo  ("The passwords do not match."); 
						} 
					?>        
				</form>
				</div>
			</center>
			</div>
		  </div>
		</main>
    </body>
    </div>
    <footer>
	<p style="height: 26px; padding-bottom: 0px; margin-bottom: 10px;">
        <strong>Bangor Support: 1.877.226.4671</strong> <span class="eh"><a href="mailto:bangorsupport@bangor.com" onclick="PopEmail(this.href); return false;">bangorsupport@bangor.com</a></span>
        <a href="https://www.facebook.com/bangorsavingsbank" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px;"><img src="fb-icons-foot.png" width="25px" height="25px"></a>
        <a href="https://twitter.com/bangorsavings" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="tw-icons-foot.png" width="31px" height="25px"></a>
        <a href="https://www.youtube.com/channel/UC6hwOwRZiNJGfBnmLgMxSAA" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="yt-logo-foot.png" height="25px" width="60px"></a>
        <a href="https://www.linkedin.com/company/bangor-savings-bank" onclick="Departure(this.href, 'general'); return false;" style="height: 25px; position: relative; top: 4px; left: 12px;"><img src="li-icons-foot.png" height="25px" width="28px"></a>        
      </p>
      <br>
	<div id="footer">
		<ul>
			<li>
				<a href="http://www.bangor.com/About-Us/Career-Opportunities.aspx" target="_self">Careers</a>
			</li>
			<li>
				<a href="http://www.bangor.com/About-Us/Contact-Us.aspx" target="_self">Contact Us</a>
			</li>
		</ul>
		<br>
		<p><a href="http://www.bangor.com/Utility/Copyright.aspx">Copyright © 2017 Bangor Savings Bank</a> <span>Member FDIC</span> <span>Equal Housing Lender</span></p>
	</div>
</footer>

</html>
