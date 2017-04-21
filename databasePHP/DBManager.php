<?php
include 'tablefunctions.php';
include 'misc.php';

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
    
    public function start_transaction(){
        $this->query("START TRANSACTION;");
    }
    
    public function commit_transaction(){
        $this->query("COMMIT;");
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
        $lRet = "SELECT " . $aSelectField 
                . " FROM " . $aTable 
                . " WHERE " . $aSearchField . " = '" . $lSearchVal . "';";
        
        //echo $lRet . "<br/>";
        
        $result = $this->query($lRet);
        
        
        
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
    
    public function insert_into($aTableName, $aFields, $aValues){
        $lRet = "INSERT INTO " . $aTableName . " (" . $this->array_to_string_noquote($aFields) . ") VALUES (" . $this->array_to_string($aValues) . ");"; 
        $this->query($lRet);
        return $lRet;
    }

    public function insert_into_quotes($aTableName, $aFields, $aValues, $aQuotes){
        $lRet = "INSERT INTO " . $aTableName . " (" . $this->array_to_string_noquote($aFields) . ") VALUES (" . $this->array_to_string_quotes($aValues, $aQuotes) . ");"; 
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
    
    public function update_table_quote($aTableName, $aFields, $aValues, $aValueQuotes, $aWhereFields, $aWhereValues, $aWhereQuotes){
        $lRet = "UPDATE " . $aTableName . " SET " . $this->zip_set_arrays_quote($aFields, $aValues, $aValueQuotes) . " WHERE " . $this->zip_and_array_quote($aWhereFields,$aWhereValues, $aWhereQuotes) . ";";
        $this->query($lRet);
        return $lRet;    
    }    

    /**
     * Deletes record(s) from a table
     * @param type $aTableName Table to remove the record from
     * @param type $aWhereFields 
     * @param type $aWhereValues
     * @param type $aWhereQuotes
     * @return string
     */
    public function delete_table_quote($aTableName, $aWhereFields, $aWhereValues, $aWhereQuotes){
        $lRet = "DELETE FROM " . $aTableName . " WHERE " . $this->zip_and_array_quote($aWhereFields,$aWhereValues, $aWhereQuotes) . ";";
        $this->query($lRet);
        //echo $lRet;
        return $lRet;    
    }    
    
    public function select_table($aTableName, $aFields, $aWhereFields = "", $aWhereValues = ""){
        if($aWhereFields == ""){
            $lRet = "SELECT " . $this->array_to_string_noquote($aFields) . " FROM " . $this->array_to_string_noquote($aTableName) . ";";
        } else {
            $lRet = "SELECT " . $this->array_to_string_noquote($aFields) . " FROM " . $this->array_to_string_noquote($aTableName) . " WHERE " . $this->zip_and_array_noquote($aWhereFields,$aWhereValues) . ";";
        }
        //echo $lRet . "<br/>";
        return $this->query($lRet);
    }
    
    public function zip_and_array($aFields, $aValues){
        $lColCount = count($aFields);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            $lRet .= $this->real_escape_string($aFields[$i]) . "='" . $this->real_escape_string($aValues[$i]) . "' AND ";
        }        

        return rtrim($lRet,'AND '); 
    }
    
    public function zip_and_array_noquote($aFields, $aValues){
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

    public function zip_set_arrays_quote($aFields, $aValues, $aQuotes){
        $lColCount = count($aFields);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            if($aQuotes[$i]){
                $lRet .= $this->real_escape_string($aFields[$i]) . "='" . $this->real_escape_string($aValues[$i]) . "',";
            } else {
                $lRet .= $this->real_escape_string($aFields[$i]) . "=" . $this->real_escape_string($aValues[$i]) . ",";
            }
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

    public function array_to_string_quotes($aDataArray, $aQuotes){
        $lColCount = count($aDataArray);
        $lRet = "";
        
        for( $i = 0; $i<$lColCount; $i++ ){
            if($aQuotes[$i]){
                $lRet .= "'" . $this->real_escape_string($aDataArray[$i]) . "',";
            } else {
                $lRet .= $this->real_escape_string($aDataArray[$i]) . ",";
            }
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
        
    //////////////////////////////////////////////////////////////////
    //More specific Functions...
    
    //=================================================
    // -Charity specific functions
    
    public function count_charities(){
        $lRet = "SELECT COUNT(*) FROM Charity";
        $lQuery = $this->query($lRet);

        if ($lQuery->num_rows > 0){
            $lRow = $lQuery->fetch_row();
            return $lRow[0];
        } else {
            return null;
        }
    }
    
    public function rand_charity(){
        //echo $this->count_charities();
        return rand(1,$this->count_charities());
    }
    
    public function verify_charity_credentials ($aName, $aPassword){
        
        $lName = $this->real_escape_string($aName);
        $lPassword = crypt_password($aName, $aPassword,$this->salt);

        $lResult = $this->query("SELECT 1 FROM Charity WHERE CharityLogin = '" . $lName . "' AND PasswordHash = '" . $lPassword . "'");
        $lRet = $lResult->data_seek(0);
        mysqli_free_result($lResult);
        return $lRet;
    }  
    
    public function charity_exists($aCharityName){
        return $this->value_exists("Charity", "CharityLogin", $aCharityName);
    } 
    
    public function get_id_by_charity($aCharityName){
        return $this->get_db_val("CharityID","Charity", "CharityLogin", $aCharityName);
    }

    public function get_id_by_quest($aQuestID){
        return $this->get_db_val("CharityID","QuestType", "QuestID", $aQuestID);
    }
    
    public function get_charity_coins($aCharityID){
        return $this->get_db_val("QuestBank","Charity", "CharityID", $aCharityID);
    }
    
    public function get_quest_coins($aQuestID){
        return $this->get_db_val("Payment","QuestType", "QuestID", $aQuestID);
    }

    public function get_quest_quantity($aQuestID){
        return $this->get_db_val("Quantity","QuestType", "QuestID", $aQuestID);
    }
    
    /**
     * This function adds (or subtracts) coins to the user's account.  If the
     * quantity is negative the number of coins are not removed from the total
     * coins. 
     * @param type $aCharityID  The user to give coins to
     * @param type $aQuantity The number of coins to give to the user.
     * @return type Total coins remaining in the user's account.
     */
    public function add_charity_coins($aCharityID, $aQuantity){
        $lCoins = $this->get_charity_coins($aCharityID);
        $lQuantity = $aQuantity;

        if( ($aQuantity < 0) && ($lCoins < abs($aQuantity)) ){
            return false;
        }
        
        $lTotal = $lCoins+$lQuantity;        //Total coins in the charity account now.

        $this->update_table("Charity", ["CharityID", "QuestBank"], [$aCharityID, $lTotal], ["UserID"], [$aCharityID] );
        
        return $lTotal;
    }
    
    /**
     * Attempts to increment a quest.  
     * @param type $aCharityID The charity associated with the quest.
     * @param type $aQuestID The quest to increment.
     * @return boolean True if successful, false otherwise.
     */
    public function increment_quest($aQuestID, $aDecCharity){
        
        $lQuestCost = $this->get_quest_coins($aQuestID);
        $lCharityID = $this->get_id_by_quest($aQuestID);
        $lCharityCoins = $this->get_charity_coins($lCharityID);
        
        if($lCharityCoins >= $lQuestCost){
            $this->start_transaction();
            if($aDecCharity){
                $this->add_charity_coins($lCharityID,-$lQuestCost);
            }
            $this->update_table_quote("QuestType", ["Quantity"], ["Quantity+1"], [false], ["QuestID"], [$aQuestID] );
            $this->commit_transaction();
            return true;
        }
        
        return false;
    }
    
/**
     * Attempts to increment a quest.  
     * @param type $aCharityID The charity associated with the quest.
     * @param type $aQuestID The quest to increment.
     * @return boolean True if successful, false otherwise.
     */
    public function decrement_quest($aQuestID, $aIncCharity){
        
        $lQuestQuantity = $this->get_quest_quantity($aQuestID);
        $lQuestCost = $this->get_quest_coins($aQuestID);
        $lCharityID = $this->get_id_by_quest($aQuestID);

        //echo "Quest Quantity: " . $lQuestQuantity . "<br/>";
        //echo "Quest Cost: " . $lQuestCost . "<br/>";
        
        if($lQuestQuantity > 0){
            //$this->start_transaction();
            if($aIncCharity){
                $this->add_charity_coins($lCharityID,$lQuestCost);
            }
            $this->update_table_quote("QuestType", ["Quantity"], ["Quantity-1"], [false], ["QuestID"], [$aQuestID] );
            //$this->commit_transaction();
            return true;
        } else {
            return false;
        }
    }

    public function user_accept_quest($aUserID, $aQuestID){
        //Quest is placed in the user's questlog
        //Quest is decremented

        $lQuestFields = ["CharityID", "Payment"];
        $lResult = $this->select_table(["QuestType"], $lQuestFields, ["QuestID"], [$aQuestID] );        
        
        if ($lResult->num_rows > 0){
            $lRow = mysqli_fetch_array($lResult);

            if($this->decrement_quest($aQuestID, false)){
                
                $lPost = [$aQuestID, $aUserID, $lRow[0], $lRow[1], date("Y-m-d\TH:i:sP")];        
                $lFields = ["QuestID", "UserID", "CharityID", "RewardAmount", "AcceptDate"];
                $this->insert_into("ActiveQuests",$lFields,$lPost);
                return true;
            }
            
        }
        
        return false;
            
    }
    
    public function user_leave_quest($aActiveQuestID){
        //Increments the quest number without taking from the charity bank
        //Deletes the quest from the
        
        $lQuestFields = ["QuestID"];
        $lResult = $this->select_table(["ActiveQuests"], $lQuestFields, ["ActiveID"], [$aActiveQuestID] );          
        
        if ($lResult->num_rows > 0){
            $lRow = mysqli_fetch_array($lResult);
            $this->increment_quest($lRow[0], false);
            $this->delete_table_quote("ActiveQuests",["ActiveID"], [$aActiveQuestID],[false]);
            return true;
        }
        
        return false;
        
    }
    
    
    public function quest_completed($aActiveQuestID){
        //User marks the quest as completed
        $this->update_table_quote("ActiveQuests", ["Completed", "CompletedDate"], [1,date("Y-m-d\TH:i:sP")], [true,true], ["ActiveID"], [$aActiveQuestID], [true]);
        
    }
    
    public function quest_completion_rejected($aActiveQuestID,$aRejectedComment){
        //Charity rejects the completion of the quest
        //Charity leaves a memo on why it was rejected.
        $this->update_table_quote("ActiveQuests", ["Completed", "Rejected", "RejectedComment","CompletedDate","RejectedDate"], [0,1,$aRejectedComment,"NULL",date("Y-m-d\TH:i:sP")], [true,true,true,false,true], ["ActiveID"], [$aActiveQuestID], [true]);
    }    
    
    public function quest_reward($aActiveQuestID){
        //Charity rewards the user for the competion of the quest
        
        $lSet = $this->get_db_val("Rewarded","ActiveQuests", "ActiveID", $aActiveQuestID);
        $lID = $this->get_db_val("ActiveID","ActiveQuests", "ActiveID", $aActiveQuestID);
        
        //Not the best security, but still prevents a reward from being applied multiple times.
        if(!$lSet && $lID){
            $lReward = $this->get_db_val("RewardAmount","ActiveQuests", "ActiveID", $aActiveQuestID);
            $lUserID = $this->get_db_val("UserID","ActiveQuests", "ActiveID", $aActiveQuestID);
            $this->update_table_quote("ActiveQuests", ["Rewarded", "RewardedDate"], [1,date("Y-m-d\TH:i:sP")], [true,true], ["ActiveID"], [$aActiveQuestID], [true]);
            $this->add_coins($lUserID, $lReward);
            return true;
        }
        return false;
    }
    
    public function modify_quest(){
        //Charity modifies the quest.  This can only be done if there
        //are no active quests out.
    }
    
    //=================================================
    // -User specific functions
    
    
    public function update_donation_rate($aUserID, $aIndex, $aCharityID, $aPercent){
        return $this->update_table_quote("DonationRate", ["CharityID", "Percent"], [$aCharityID, $aPercent], [false,true], ["RowID", "UserID"], [$aIndex,$aUserID] );
    }
    
    public function get_user_coins($aUserID){
        return $this->get_db_val("Coins","Volunteers", "UserID", $aUserID);
    }
    
    public function get_user_total_coins($aUserID){
        return $this->get_db_val("TotalCoins","Volunteers", "UserID", $aUserID);
    }
    
    public function get_user_update($aUserID){
        return $this->get_db_val("LastUpdateTime","Volunteers", "UserID", $aUserID);
    }
    
    public function day_comp($aUserID){
        $lParse = date_parse($this->get_user_update($aUserID));
        return strtotime(date("Y")."-".date("m")."-".date("d"))-strtotime($lParse["year"]."-".$lParse["month"]."-".$lParse["day"]);
    }
    
    public function month_comp($aUserID){
        $lParse = date_parse($this->get_user_update($aUserID));
        return strtotime(date("Y")."-".date("m"))- strtotime($lParse["year"]."-".$lParse["month"]);
    }
    
    public function add_steps($aUserID, $aSteps){
        $lUpdateDay = "DaySteps +" . $aSteps;
        $lUpdateMonth = "MonthSteps +" . $aSteps;
        if($this->day_comp($aUserID) > 0){
            $lUpdateDay = $aSteps;
        }
        
        if($this->month_comp($aUserID)){
            $lUpdateMonth = $aSteps;
        }
        
        return $this->update_table_quote("Volunteers", ["DaySteps", "MonthSteps", "TotalSteps", "LastUpdateTime"], [$lUpdateDay, $lUpdateMonth, "TotalSteps +" . $aSteps, date("Y-m-d\TH:i:sP")], [false, false, false, true], ["UserID"], [$aUserID], [false] );
    }
    
    
    
    
    /**
     * This function adds (or subtracts) coins to the user's account.  If the
     * quantity is negative the number of coins are not removed from the total
     * coins. 
     * @param type $aUserID  The user to give coins to
     * @param type $aQuantity The number of coins to give to the user.
     * @return type Total coins remaining in the user's account.
     */
    public function add_coins($aUserID, $aQuantity){
        $lCoins = $this->get_user_coins($aUserID);
        $lQuantity = $aQuantity;

        if( ($aQuantity < 0) && ($lCoins < abs($aQuantity)) ){
            $lQuantity = -$lCoins;
        }
        $lTotal = $lCoins+$lQuantity;        //Total coins in the user account now.

        $this->update_table("Volunteers", ["UserID", "Coins"], [$aUserID, $lTotal], ["UserID"], [$aUserID] );
        
        if( $aQuantity > 0){
            $lTotalCoins = $this->get_user_total_coins($aUserID) + $aQuantity;
            $this->update_table("Volunteers", ["UserID", "TotalCoins"], [$aUserID, $lTotalCoins], ["UserID"], [$aUserID] );
        }
        
        return $lTotal;
    }
    
    public function donate($aUserID){
        $lPercent = 0;
        $aTotalNumber = $this->get_user_coins($aUserID);
        
        if($aTotalNumber == 0){
            return 0;
        }
        
        for($i = 0; $i < 5; $i++){
            $lQuery = $this->select_table(["DonationRate"], ["Percent","CharityID"], ["RowID", "UserID"], [$i, $aUserID]);
            $lPercent = 0;
            $lCharity = -1;
            
            if ($lQuery->num_rows > 0){
                $lRow = $lQuery->fetch_row();
                $lPercent = $lRow[0]/100.0;
                $lCharity = $lRow[1];
            }
            
            $lDonate = $lPercent*$aTotalNumber;
            $this->insert_into("PointDonations", ["CharityID", "UserID", "Quantity", "Date"], [$lCharity, $aUserID, $lDonate, date("Y-m-d\TH:i:sP")]);
            $this->add_coins($aUserID,-$lDonate);
            
        }
        
        return $aTotalNumber;
    }
    
    public function verify_user_credentials ($aName, $aPassword){
        
        $lName = $this->real_escape_string($aName);
        $lPassword = crypt_password($aName, $aPassword,$this->salt);
        //echo $lPassword . "<br/>";
        $lResult = $this->query("SELECT 1 FROM " . $this->credentials_table . " WHERE UserName = '" . $lName . "' AND PasswordHash = '" . $lPassword . "'");
        //mysqli_free_result($lResult);
        $lRet = $lResult->data_seek(0);
        return $lRet;
    }
    
    
    
    public function create_user ($aName, $aPassword){
        $lFields = ["UserName", "PasswordHash", "CreateDate"];
        $lValues = [$aName, crypt_password($aName, $aPassword,$this->salt), date("Y-m-d")];
        return $this->insert_into($this->credentials_table, $lFields, $lValues );
    }
    
    public function user_exists($aName){
        return $this->value_exists("Accounts", "UserName", $aName);
    }
           

    public function get_id_by_username($aUserName){
        return $this->get_db_val("UserID","Accounts", "UserName", $aUserName);
    }
    
    
    
}
