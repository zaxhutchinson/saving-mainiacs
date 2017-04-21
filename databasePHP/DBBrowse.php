<?php
require_once("MammoDB.php");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DBBrowse
 *
 * @author brian
 */
class DBBrowse {
    //put your code here
        /**
     * This function SHOULD take a query and extracts the column names from it.
     * For some reason it does not work as intended.
     * @param type $aQuery The query we are extracting
     * @return type Array of column names.
     */
    public function get_query_colnames($aQuery){

        $lColCount = $aQuery->columnCount();
        $lColumns = array();
        
        for($i = 0; $i < $lColCount; $i++) {
            $lColumns[] = $aQuery->getColumnMeta($i)["name"];
        }
        return array($lColCount,$lColCount);
        //return $lColumns;
    }
    
    public function get_user_id_by_name($aName) {

        $lName = $this->real_escape_string($aName);
        $result = $this->query("SELECT id FROM " . $this->credentials_table . " WHERE name = '"
                . $lName . "'");
        
        if ($result->num_rows > 0){
            $row = $result->fetch_row();
            return $row[0];
        } else {
            return null;
        }
    }
    
    
}
