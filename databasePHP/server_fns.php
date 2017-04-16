<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    
    function get_user(){  
        return $_SESSION["user"];
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
