<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MammoTableMaker
 *
 * @author brian
 */
class MammoTableMaker {

    private function create_relation_credentials(){
        $lSchema =  "id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT," .
                    "name       TEXT," .    
                    "password   TEXT," .
                    "PRIMARY KEY(name)";
                
        $this->create_relation($this->credentials_table, $lSchema); 
    }  
    
    private function create_relation_mammogram_key(){
        $lSchema =  "mammo_id       NOT NULL AUTO_INCREMENT PRIMARY KEY," .
                    "primary_id     TEXT PRIMARY KEY," .
                    "secondary_id   TEXT PRIMARY KEY," .
                    "view           TEXT PRIMARY KEY";
        
        $this->create_relation("MammogramKey", $lSchema);
    }
    
    private function create_relation_mammogram(){
        $lSchema =  "mammo_id       INTEGER," .
                    "age            INTEGER," .
                    "date           DATETIME," .
                    "density        INTEGER," .
                    "image_width    INTEGER," .
                    "image_height   INTEGER," .
                    "resolution     INTEGER," .
                    "image          BLOB," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("Mammogram", $lSchema);
    }
    
    private function create_relation_image_result(){
        $lSchema =  "mammo_id       INTEGER," .
                    "type           TEXT PRIMARY KEY," .
                    "step_size      INTEGER," .
                    "image          BLOB," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("ImageReslut", $lSchema); 
    }    
    
    private function create_relation_pathology_report(){
        $lSchema =  "mammo_id       INTEGER," .
                    "lesion_id      INTEGER PRIMARY KEY," .
                    "assessment     INTEGER," .
                    "subtlety       INTEGER," .
                    "pathology      TEXT," .
                    "tumor_outline  BLOB," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("PathologyReport", $lSchema); 
    }        
    
    private function create_relation_result(){
        $lSchema =  "mammo_id       INTEGER," .    
                    "region         INTEGER PRIMARY KEY," .
                    "top_x          INTEGER," .
                    "top_y          INTEGER," .
                    "amin           FLOAT," .
                    "amax           FLOAT," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("Result", $lSchema); 
    }      
    
    private function create_relation_no_scaling(){
        
        $lSchema =  "region         INTEGER," .
                    "reason         TEXT PRIMARY KEY,";
                    "FOREIGN KEY (region) REFERENCES Result(region) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("NoScaling", $lSchema); 
    }       
    
    private function create_relation_result_statistic(){
        
        $lSchema =  "mammo_id       INTEGER," .            
                    "type           TEXT PRIMARY KEY," .
                    "mean           DOUBLE," .
                    "weighted_mean  DOUBLE," .
                    "stdev          DOUBLE," .
                    "weighted_stdev DOUBLE," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("ResultStatistic", $lSchema); 
    }     

    private function create_relation_result_result_qvalue(){
        
        $lSchema =  "mammo_id       INTEGER," .                 
                    "qvalue         TEXT PRIMARY KEY," .
                    "dimension      DOUBLE," .
                    "rsquared       DOUBLE," .
                    "delta          DOUBLE," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("ResultQvalue", $lSchema); 
    }      

    private function create_relation_result_pfdata(){
        
        $lSchema =  "mammo_id   INTEGER," .                
                    "type	TEXT PRIMARY KEY," .
                    "octave	INTEGER	PRIMARY KEY," .
                    "voice      INTEGER PRIMARY KEY," .
                    "value	DOUBLE," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("PFData", $lSchema); 
    }     

    private function create_relation_partition_function(){
        
        $lSchema =  "mammo_id           INTEGER PRIMARY KEY," .                
                    "first_scale        INTEGER," .
                    "number_of_octave	INTEGER," .
                    "number_of_voices	INTEGER," .
                    "first_octave	INTEGER," .
                    "last_octave	INTEGER," .
                    "last_voice 	INTEGER," .
                    "source_size	INTEGER," .
                    "number_of_sources	INTEGER," .
                    "source_dimension	TEXT," .
                    "method             TEXT," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        
        $this->create_relation("PartitionFunction", $lSchema); 
    }       


    private function create_relation_max_gaussian(){
        
        $lSchema =  "mammo_id       INTEGER PRIMARY KEY," .         
                    "octave         INTEGER PRIMARY KEY," .
                    "voice          INTEGER PRIMARY KEY," .
                    "data           BLOB," .
                    "FOREIGN KEY (mammo_id) REFERENCES MammogramKey(mammo_id) ON DELETE CASCADE ON UPDATE CASCADE";
        
        $this->create_relation("MaxGaussian", $lSchema); 
    }     
    
    public function create_relations(){
        
        $this->create_relation_mammogram_key();
        $this->create_relation_mammogram();
        $this->create_relation_pathology_report();
        $this->create_relation_image_result();
        $this->create_relation_result();
        $this->create_relation_result_statistic();
        $this->create_relation_result_result_qvalue();
        $this->create_relation_result_pfdata();
        $this->create_relation_partition_function();
        $this->create_relation_no_scaling();
        $this->create_relation_max_gaussian();
    }


    public function create_relation($aRelationName, $aFields) {

        $lRelationName = $this->real_escape_string($aRelationName);
        $lFields = $this->real_escape_string($aFields);
        
        $lQuery = $this->query("CREATE TABLE IF NOT EXISTS "
                . $lRelationName . "(" . $lFields . ");");
        
        
        echo 'Creating Relation...';
        if ($lQuery->num_rows > 0){
            $row = $lQuery->fetch_row();
            return $row[0];
        } else {
            return null;
        }
        
    }  
}
