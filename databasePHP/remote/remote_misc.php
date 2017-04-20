<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function build_json_response($aQuery, $aFields){
    
    if ($aQuery->num_rows > 0){

        $lNumbCols = count($aFields);
        $lResponse["results"] = array();
        $lResponse["success"] = 1;
        
        while ($lRow = mysqli_fetch_array($aQuery)) {

            $lData = array();
            for($i = 0; $i < $lNumbCols; $i++){
                $lData[$aFields[$i]] = $lRow[$i];

            }
            
            array_push($lResponse["results"], $lData);
            
        }

        // echoing JSON response
        echo json_encode($lResponse);
    } else {
        // failed to insert row
        $lResponse["success"] = 0;
        $lResponse["message"] = "Data not found.";
 
        // echoing JSON response
        echo json_encode($lResponse);
    }
    
}