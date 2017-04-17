<?php
include 'tablefunctions.php';

/**
 * Description of MammoDB
 *
 * @author brian
 */
class DBManager extends mysqli{
    
    // single instance of self shared among all instances
    private static $instance = null;
    
    //Database login information
    private $user = "mucoftware";
    private $pass = "!muCoftware1";
    private $dbName = "mucoftware";
    private $dbHost = "localhost";
    private $salt = "1234567";
    private $credentials_table = "Accounts";

    public function get_salt(){
        return $this->salt;
    }
    
    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone() {trigger_error('Clone is not allowed.', E_USER_ERROR);}
    public function __wakeup() {trigger_error('Deserializing is not allowed.', E_USER_ERROR);    }

    // private constructor
    public function __construct() {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
        parent::set_charset('utf-8'); 
    }

    public function get_id_by_username($aUserName){
        return $this->get_db_val("UserID","Accounts", "UserName", $aUserName);
    }
    
    public function get_id_by_charity($aCharityName){
        return $this->get_db_val("CharityID","Charity", "CharityLogin", $aCharityName);
    }
    
    /**
     * Quick search of a particular value in the database
     * @param type $aSelectField Field we are returning
     * @param type $aTable Table we are searching
     * @param type $aSearchField Field we are looking through
     * @param type $aSearchVal Value we are searching for
     * @return type The value of Selected Field.
     */
    public function get_db_val($aSelectField, $aTable, $aSearchField, $aSearchVal) {
        $lSearchVal = $this->real_escape_string($aSearchVal);
        $result = $this->query("SELECT " . $aSelectField 
                . " FROM " . $aTable 
                . " WHERE " . $aSearchField . " = '" . $lSearchVal . "'");
        
        if ($result->num_rows > 0){
            $row = $result->fetch_row();
            return $row[0];
        } else {
            return null;
        }
    }    

    public function value_exists($aTable, $aSearchField, $aSearchVal) {
        $lSearchVal = $this->real_escape_string($aSearchVal);
        $lQuery = "SELECT 1"
                . " FROM " . $aTable 
                . " WHERE " . $aSearchField . " = '" . $lSearchVal . "'";
        $lResult = $this->query($lQuery);
        return ($lResult->data_seek(0) ==  1);
    } 
    
    public function create_user ($aName, $aPassword){
        $lFields = ["UserName", "PasswordHash", "CreateDate"];
        $lValues = [$aName, crypt_password($aName, $aPassword,$this->salt), date("Y-m-d")];
        return $this->insert_into($this->credentials_table, $lFields, $lValues );
    }
    
    public function user_exists($aName){
        return $this->value_exists("Accounts", "UserName", $aName);
    }
    
    public function insert_into($aTableName, $aFields, $aValues){
        $lRet = "INSERT INTO " . $aTableName . " (" . $this->array_to_string_noquote($aFields) . ") VALUES (" . $this->array_to_string($aValues) . ");"; 
        $this->query($lRet);
        return $lRet;
    }
    
    public function upload_image($aPath, $aTableName, $aField, $aWhereFields, $aWhereValues, $aQuote){
        $lImage=addslashes(file_get_contents($aPath));
        $lRet = "UPDATE " . $aTableName . " SET " . $aField . " = '" . $lImage . "' WHERE " . $this->zip_and_array_quote($aWhereFields,$aWhereValues,$aQuote) . ";";
        $lQuery = $this->query($lRet);
        mysqli_free_result($lQuery);
        return $lRet;
    }    
    
    public function update_table($aTableName, $aFields, $aValues, $aWhereFields, $aWhereValues){
        $lRet = "UPDATE " . $aTableName . " SET " . $this->zip_set_arrays($aFields, $aValues) . " WHERE " . $this->zip_and_array($aWhereFields,$aWhereValues) . ";";
        $this->query($lRet);
        return $lRet;    
    }
    
    public function select_table($aTableName, $aFields, $aWhereFields, $aWhereValues){
        $lRet = "SELECT " . $this->array_to_string_noquote($aFields) . " FROM " . $this->array_to_string_noquote($aTableName) . " WHERE " . $this->zip_and_array($aWhereFields,$aWhereValues) . ";";
        //echo $lRet . "<br/>";
        return $this->query($lRet);
    }
    
    public function zip_and_array($aFields, $aValues){
        $lColCount = count($aFields);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            $lRet .= $this->real_escape_string($aFields[$i]) . "=" . $this->real_escape_string($aValues[$i]) . " AND ";
        }        

        return rtrim($lRet,'AND '); 
    }
    
    public function zip_and_array_quote($aFields, $aValues, $aQuote){
        $lColCount = count($aFields);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            if($aQuote[i]){
                $lRet .= $this->real_escape_string($aFields[$i]) . "=" . $this->real_escape_string($aValues[$i]) . " AND ";
            } else {
                $lRet .= $this->real_escape_string($aFields[$i]) . "='" . $this->real_escape_string($aValues[$i]) . "' AND ";
            }
            
        }        

        return rtrim($lRet,'AND '); 
    }    
    
    public function zip_set_arrays($aFields, $aValues){
        $lColCount = count($aFields);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            $lRet .= $this->real_escape_string($aFields[$i]) . "='" . $this->real_escape_string($aValues[$i]) . "',";
        }        

        return rtrim($lRet,',');        
    }
    
    public function array_escape_string($aDataArray){
        $lColCount = count($aDataArray);
        $lRet = [];
        for( $i = 0; $i<$lColCount; $i++ ){
            array_push($lRet, $this->real_escape_string($aDataArray[$i]));
        }        
        
        return $lRet;
    }
    
    public function array_to_string_noquote($aDataArray){
        $lColCount = count($aDataArray);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            $lRet .= $this->real_escape_string($aDataArray[$i]) . ",";
        }        

        return rtrim($lRet,',');
    }    
    
    public function array_to_string($aDataArray){
        $lColCount = count($aDataArray);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            $lRet .= "'" . $this->real_escape_string($aDataArray[$i]) . "',";
        }        

        return rtrim($lRet,',');
    }
    
    public function verify_user_credentials ($aName, $aPassword){
        
        $lName = $this->real_escape_string($aName);
        $lPassword = crypt_password($aName, $aPassword,$this->salt);
        echo $lPassword . "<br/>";
        $lResult = $this->query("SELECT 1 FROM " . $this->credentials_table . " WHERE UserName = '" . $lName . "' AND PasswordHash = '" . $lPassword . "'");
        //mysqli_free_result($lResult);
        $lRet = $lResult->data_seek(0);
        return $lRet;
    }
    

    
    public function verify_charity_credentials ($aName, $aPassword){
        
        $lName = $this->real_escape_string($aName);
        $lPassword = crypt_password($aName, $aPassword,$this->salt);

        $lResult = $this->query("SELECT 1 FROM Charity WHERE CharityLogin = '" . $lName . "' AND PasswordHash = '" . $lPassword . "'");
        $lRet = $lResult->data_seek(0);
        mysqli_free_result($lResult);
        return $lRet;
    }  
    
    public function query_to_array($aQuery, $aIndex){
        $lRet = array();
        while ($lRow = mysqli_fetch_array($aQuery)):
            $lRet[] = $lRow[$aIndex];
        endwhile;
        mysqli_free_result($aQuery);
        return $lRet;
    }
   

    
    function table_from_querystring($aColumns, $aQueryString, $aTableTag, $aImageColumn = ""){
        $lQuery = $this->query($aQueryString);
        $lRet = $aTableTag;
            $lRet .= $this->table_header_from_array($aColumns);
            $lRet .= $this->table_body_from_query($aColumns,$lQuery,$aImageColumn);
        $lRet .= "</table>";
        mysqli_free_result($lQuery);
        return $lRet;
    }    
    
    function table_from_query($aColumns, $aQuery, $aTableTag, $aImageColumn = ""){
        $lRet = $aTableTag;
            $lRet .= $this->table_header_from_array($aColumns);
            $lRet .= $this->table_body_from_query($aColumns,$aQuery,$aImageColumn);
        $lRet .= "</table>";
        return $lRet;
    }
    

    
    /**
     * Constructs a pivot table from the query
     * @param type $aXValueArray 
     * @param type $aPivotCol
     * @param type $aValueCol
     * @param type $aQuery
     * @param type $aTableTag
     * @return string
     */
    function pivot_table_from_query($aXValueArray, $aPivotCol, $aValueCol, $aQuery, $aTableTag, $aImage = 0){
        $lRet = $aTableTag;
        $lColCount = count($aXValueArray);
        $lCount = 1;
        $lHeader = $aXValueArray;
        array_unshift($lHeader, $aPivotCol);
        $lRet .= $this->table_header_from_array($lHeader);
        
        while ($lRow = mysqli_fetch_array($aQuery)):
            if($lCount % $lColCount == 1){
                $lRet .= "<tr><td>" . htmlentities($lRow[$aPivotCol]) . "</td>";
            }
            
            if($aImage){
                $lRet .= '<td><img src="data:image/png;base64,'.base64_encode($lRow[$aValueCol] ).'"/></td>';
            } else {
                $lRet .= "<td>" . htmlentities($lRow[$aValueCol]) . "</td>";
            }
            
            if($lCount % $lColCount == 0){$lRet .= "</tr>";};$lCount ++;
            
        endwhile;
        
        $lRet .= "</table>";
        mysqli_free_result($aQuery);
        return $lRet;        
    }
    
    /**
     * Builds a dropdown menu from 
     * @param type $aFormName Name of the form
     * @param type $aDropDownName Name of the dropdown menu
     * @param type $aOptions Array of options fields
     * @param type $aColumns Array of column fields
     * @param type $aQueryString The string query used to pull the data from.
     * @return string
     */
    function construct_dropdown($aFormName,$aDropDownName, $aOptions, $aColumns, $aQueryString){
        $lResults = $this->query($aQueryString);
        $lRet = "<form id=\"" . $aFormName ."\" method=\"POST\">";
        $lRet .= "<select name=\"" . $aDropDownName ."\" onchange=\"<?php echo \$_SERVER[\"PHP_SELF\"];?>\" >";
        
        while ($lRow = mysqli_fetch_array($lResults)){
            
            $lRet .= "<option value='";
            for($i = 0; $i < count($aOptions); $i++){
                $lRet .= htmlentities($lRow[$aOptions[$i]]) . ",";
            }
            $lRet .= "'>";

            for($i = 0; $i < count($aColumns); $i++){
                $lRet .= "<column>" . htmlentities($lRow[$aColumns[$i]]) . "</column> ";
            }            

            $lRet .= "</option>";          
        }
        
        $lRet .= '</select><input type="submit" name="formSubmit" value="Submit" ></form>';
        mysqli_free_result($lResults);
        return $lRet;
    }

    /**
     * Builds table header information from an array
     * @param type $aArray The array we are turning into a header
     * @return string
     */    
    function table_header_from_array($aArray){
        $lRet = "<tr>";
        $lColCount = count($aArray);
        
        for($i = 0; $i < $lColCount; $i++){
            $lRet .= "<th>" . htmlentities($aArray[$i]) . "</th>";
        }
        $lRet .= "</tr>";
        
        return $lRet;
    }

    
    
    /**
     * Builds a table body from a database query.
     * @param type $aColumns List of columns to use
     * @param type $aQuery Query to pull data from
     * @return string
     */
    function table_body_from_query($aColumns, $aQuery, $aImageColumn){
        $lColCount = count($aColumns);
        $lRet = "";
        
        while ($lRow = mysqli_fetch_array($aQuery)):
            $lRet .= "<tr>";
                for($i = 0; $i < $lColCount; $i++){
                    if($aImageColumn == $aColumns[$i]){
                        $lRet .= '<td><img src="data:image/jpeg;base64,'.base64_encode( $lRow[$aColumns[$i]] ).'"/></td>';
                    } else {
                        $lRet .= "<td>" . htmlentities($lRow[$aColumns[$i]]) . "</td>";
                    }
                    
                }
            $lRet .= "</tr>";     
        endwhile;
        mysqli_free_result($aQuery);
        
        return $lRet;
    }
        
}
