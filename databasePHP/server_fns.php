<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//require_once 'misc.php';

    function get_post($aField){
        return filter_input(INPUT_POST, $aField);
    }

    function post_array($aArray){
        $lRet = [];
        for($i = 0; $i < count($aArray); $i++){
            array_push($lRet, get_post($aArray[$i]) );
        }
        
        return $lRet;
    }
    
    function print_array($aArray){
        $lRet = [];
        for($i = 0; $i < count($aArray); $i++){
            echo $aArray[$i] . "<br/>";
        }
        
        return $lRet;
    }
    
    function user_post(){
        return filter_input(INPUT_POST, "user");
    }

    function password_post(){
        return filter_input(INPUT_POST, "userpassword");
    }

    function server_post(){
        //Equiv: ($_SERVER['REQUEST_METHOD'] == "POST")
        return filter_input(INPUT_SERVER, "REQUEST_METHOD", FILTER_SANITIZE_STRING) == "POST";
    }
    
    function get_session_val($aField){
        return $_SESSION[$aField];
    }
    
    function get_user(){  
        return get_session_val('user');
    }
    
    function set_session_val($aField, $aVal){
        $_SESSION[$aField] = $aVal;
    }
    
    function set_user($aUserName){
        $_SESSION['user'] = $aUserName;
    }

    function set_charity($aUserName){
        $_SESSION['user'] = $aUserName;
    }
    
    function crypt_password($aName, $aPassword, $aSalt){
        return crypt($aPassword . $aName . $aSalt,$aName . $aSalt) . crypt($aName . $aPassword,$aName . $aSalt);
    }    
    
    function gen_form($aFormName, $aFormAction, $aText, $aTypes, $aNames, $aValue, $aWidth = 250){
        
        $lColCount = count($aTypes);
        $lForm = "<center>
                    <h3>" . $aFormName . "</h3>
                    <div class=form>
                        <form action=". $aFormAction . " method=post>";
        for( $i = 0; $i<$lColCount; $i++ ){
            //echo "input type=" . $aTypes[$i] . " name=";
            $lForm .= $aText[$i] ."<input type=" . $aTypes[$i] . " name=" . $aNames[$i] . "><br>";
        }
        
        $lForm .= "<input type=submit name=\"" . $aValue . "\" value=\"" . $aValue . "\">";
        
        $lForm .= "</form>
                    </div>
                    </center>
                    <style>
                        .form{
                            border: 1px solid #D3D3D3;
                            text-align: center;
                            width: ". $aWidth ."px;
                        }
                    </style>";
        
        echo $lForm;

    }    
    
// function to get  the address
function get_lat_long($aAddress, $aAPIKey) {
   $array = array();
   $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($aAddress).'&sensor=false&key=' . $aAPIKey);
   //echo $geo;
   
   // We convert the JSON to an array
   $geo = json_decode($geo, true);
   //echo $geo;
   
   // If everything is cool
   if ($geo['status'] == 'OK') {
      $latitude = $geo['results'][0]['geometry']['location']['lat'];
      $longitude = $geo['results'][0]['geometry']['location']['lng'];
      $array = array('lat'=> $latitude ,'long'=>$longitude);
   }

   return $array;
}

function quote($aInput){
    return "'" . $aInput . "'";
}

function gen_data(){
    
    gen_charity("Augusta Food Bank", "9 Summer St, Augusta, ME 04330", "(207) 622-5225", "Augusta Food Bank", "aug_food_bank", "aug_food_bank");
    gen_charity("Franklin County Children's Task Force", "113 Church St, Farmington, ME 04938", "(207) 778-6960", "Franklin County Children's Task Force", "fcctf", "fcctf");
    gen_charity("York County Shelter Programs, Inc.", "147 Shaker Hill Rd, Alfred, ME 04002", "(207) 324-1137" , "York County Shelter Programs, Inc.", "ycspi", "ycspi");
    gen_charity("Hope and Justice Project", "Houlton, ME", "(207) 532-4004", "Hope and Justice Project", "hjp", "hjp");
    gen_charity("Wells-Ogunquit Senior Center", "300 Post Rd, Wells, ME 04090", "(207) 646-7775", "Wells-Ogunquit Senior Center", "wosc", "wosc");
    gen_charity("Camp Capella", "8 Pearl Point Rd, Holden, ME 04429", "(207) 843-5104", "Camp Capella", "camp_capella", "camp_capella");
    gen_charity("Catholic Charities", "1066 Kenduskeag Ave, Bangor, ME 04401", "(207) 941-2855", "Catholic Charities", "cath_char", "cath_char");
    gen_charity("Ronald McDonald House", "654 State St, Bangor, ME 04401", "(207) 942-9003", "Ronald McDonald House", "rmcdh", "rmcdh");
    gen_charity("Healthcare Charities", "1 Cumberland St #300, Bangor, ME 04401", " (207) 973-5055", "Healthcare Charities", "hc_char", "hc_char");
    gen_charity("Bangor Humane Society", "693 Mt Hope Ave, Bangor, ME 04401", "(207) 942-8902", "Bangor Humane Society", "bgrhs", "bgrhs");
    DBManager::getInstance()->upload_image("./bestbhslogo-sm.jpg", "Charity", "ProfileImage", ["CharityLogin"], ["bgrhs"]);
    
    gen_charity("Bangor Homeless Shelter", "263 Main St, Bangor, ME 04401", "(207) 947-0092", "Bangor Homeless Shelter", "bgr_homeless_shelter", "bgr_homeless_shelter");
    gen_charity("Meals On Wheels", "39 Summer St, Rockland, ME 04841", "(207) 594-2740", "Meals On Wheels", "meals_on_wheels", "meals_on_wheels");
    gen_charity("Augusta Children's Shelter", "Augusta, ME", "(207) 555-5555", "Augusta Children's Shelter", "aug_childrens_shelter", "aug_childrens_shelter");      
    gen_charity("Old Town Pet Central", "Old Town, ME", "(207) 555-5555", "Old Town Pet Central", "ot_pet_central", "ot_pet_central" );
    
    gen_user("helpfulguy78", "helpfulguy78", "Helpful Guy", "helpfulguy78@none.com");
    gen_user("volunteering2", "volunteering2", "Voulin Teeringtoo", "volunteering2@none.com");
    gen_user("bangorCustomer45", "bangorCustomer45", "Bangor Customer", "bangorCustomer45@none.com");
    gen_user("Active78Participant", "Active78Participant", "Active Paticipant", "Active78Participant@none.com");
    gen_user("iLoveDonating", "iLoveDonating", "Love Donating", "iLoveDonating@none.com");

    DBManager::getInstance()->upload_image("./img_20160924_011218-sm.jpg", "Accounts", "ProfileImage", ["UserName"], ["helpfulguy78"],[true]);
//    DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], ["volunteering2"],[true]);
//    DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], ["bangorCustomer45"],[true]);
//    DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], ["Active78Participant"],[true]);
//    DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], ["iLoveDonating"],[true]);
//    
    
    gen_quest(1, "Cook Food", 10, 10, "Cook food for people", "9 Summer St, Augusta, ME 04330");
    gen_quest(1, "Bring Food", 10, 10, "Bring food to Ms. McGregor", "Orono, ME");
    
}

function gen_charity($aName, $aAddress, $aPhone, $aDesc, $aLogin, $aPassword){
    
    $lPost = [$aName, $aAddress, $aPhone, $aDesc, $aLogin];
    array_push($lPost, crypt_password($aLogin, $aPassword, DBManager::getInstance()->get_salt() ));
    array_push($lPost, date("Y-m-d"));
    
    $lCoords = get_lat_long( $aAddress, "AIzaSyAvb7YHTZJFhMJstXAOQ4KDPLzzUXemmcQ" );

    array_push($lPost,  $lCoords["lat"]);
    array_push($lPost, $lCoords["long"]);
    array_push($lPost, 0);

    $lFields = ["CharityName", "Address" , "PhoneNumber", "Description", "CharityLogin","PasswordHash","DateAdded","Latitude","Longitude","QuestBank"];
    if(!DBManager::getInstance()->charity_exists($aLogin)){
        //echo "Charity Does Not Exist <br/>";
        
        DBManager::getInstance()->insert_into("Charity",$lFields,$lPost);
        DBManager::getInstance()->upload_image("./default.jpg", "Charity", "ProfileImage", ["CharityLogin"], [$aLogin],[true]);
    } else {
        //echo "Charity Exists <br/>";
    }
    
}

function gen_quest($aCharityID, $aQuestName, $aPayment, $aQuantity, $aQuestDescription, $aDropOffLocation){

    $lPost = [$aCharityID, $aQuestName, $aPayment, $aQuantity, $aQuestDescription, $aDropOffLocation];
    $lCoords = get_lat_long( $aDropOffLocation, "AIzaSyAvb7YHTZJFhMJstXAOQ4KDPLzzUXemmcQ" );
    array_push($lPost,  $lCoords["lat"]);
    array_push($lPost, $lCoords["long"]);
    
    $lFields = ["CharityID", "QuestName", "Payment", "Quantity", "QuestDescription", "DropOffLocation","DropOffLat","DropOffLong"];
    echo DBManager::getInstance()->insert_into("QuestType",$lFields,$lPost);
    

}


function gen_user($aUser, $aPassword, $aName, $aEmail){
    $lPost = [$aUser, $aName, $aEmail];
    array_push($lPost, date("Y-m-d"));
    array_push($lPost, crypt_password($aUser, $aPassword, DBManager::getInstance()->get_salt() ));

    $lFields = ["UserName", "LoginName" , "EmailAddress", "CreateDate", "PasswordHash"];
    
    

    if(!DBManager::getInstance()->user_exists($aUser)){
        //echo "User Does Not Exist <br/>";
        DBManager::getInstance()->insert_into("Accounts",$lFields,$lPost);
        $lUserID = DBManager::getInstance()->get_id_by_username($aUser);
        
        DBManager::getInstance()->insert_into("Volunteers",["UserID", "LastUpdateTime"], [$lUserID, date("Y-m-d")] );
        DBManager::getInstance()->upload_image("./default.jpg", "Accounts", "ProfileImage", ["UserName"], [$aUser],[true]);

        for($i = 0; $i < 5; $i++){
            echo DBManager::getInstance()->insert_into_quotes("DonationRate", ["RowID", "UserID", "CharityID", "Percent"], [$i, $lUserID, DBManager::getInstance()->rand_charity(), 20], [true,true,false,true]);
            echo  "<br/>";
        }        
        
    } else {
        //DBManager::getInstance()->donate()$
//        echo "User Exists <br/>";
        $lUserID = DBManager::getInstance()->get_id_by_username($aUser);
//
//        for($i = 0; $i < 5; $i++){
//            echo DBManager::getInstance()->update_table_quote("DonationRate", ["CharityID", "Percent"], [DBManager::getInstance()->rand_charity(), 20], [false,true], ["RowID", "UserID"], [$i,$lUserID] );
//            echo  "<br/>";
//        }         
        $lQuantity = rand(0,5000);
        //$lQuantity = 0;
        //echo "Quantity: " . $lQuantity . "<br/>";
        //echo $lUserID . " : " . $lQuantity . " : " . DBManager::getInstance()->add_coins($lUserID, $lQuantity) . "<br/>";
        //DBManager::getInstance()->donate($lUserID);
        //echo DBManager::getInstance()->add_steps($lUserID, $lQuantity);
        //////echo "UserID: " . $lUserID;
        //echo "Coins: " . DBManager::getInstance()->get_coins($aUserID) . "<br/>";
        //echo DBManager::getInstance()->day_comp($lUserID) . " : " . DBManager::getInstance()->month_comp($lUserID) . "<br/>";
    }

    
}

