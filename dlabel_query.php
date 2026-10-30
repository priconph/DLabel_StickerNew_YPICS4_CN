<?php
class YPICS {
	//MALACHIXD CONNECTION INFO
	private $serverM = "192.168.3.251\MSSQLSERVER"; 
	private $userM ="builder";
	private $passM = "";
	private $databaseM = "CN_YPICS1";
	private $db_handleM ="";
	
	//FUNCTION TO CONNECT TO MALACHI SERVER	
    private function connect_DBM(){
		$this->db_handleM = mssql_connect($this->serverM,$this->userM,$this->passM);
			return mssql_select_db($this->databaseM,$this->db_handleM);
    }
   
    //FUNCTION TO CLOSE MALACHI SERVER
    private function close_DBM(){
        if(isset($this->db_handleM)){
            mssql_close($this->db_handleM);
        }
    }
	
	/////////////////////////////////PUBLIC FUNCTIONS///////////////////////////////////////////////	
	//FUNCTION TO LOAD PO NUM FROM MALACHI DATABASE
	public function fetch_po_data($viewName,$PONum){
		$db_found = $this->connect_DBM();
		if($db_found){
			$checkPO = str_split($PONum);
			if($checkPO[0] == 'E'){
				$SQL = "select * from ".$viewName." WHERE PONum = '".$PONum."';";
			}else{
				$SQL = "select * from ".$viewName." WHERE PONum = '".$PONum."00010';";
			}				
			$result = mssql_query($SQL);
			return $result;
			mssql_free_result($result);
		}
		else{
			$errorMsg = "Database not found, Please contact your administrator";
		}
		$this->close_DBM();
	}
}

class DLABEL {
	//MALACHIXD CONNECTION INFO
	private $serverM = "localhost"; 
	private $userM ="root";
	private $passM = "r@p1d0nl1n3";
	private $databaseM = "db_dlabel";
	private $db_handleM ="";
	
	//FUNCTION TO CONNECT TO MALACHI SERVER	
    private function connect_DBM(){
		$this->db_handleM = mysql_connect($this->serverM,$this->userM,$this->passM);
			return mysql_select_db($this->databaseM,$this->db_handleM);
    }
   
    //FUNCTION TO CLOSE MALACHI SERVER
    private function close_DBM(){
        if(isset($this->db_handleM)){
            mysql_close($this->db_handleM);
        }
    }
	
	/////////////////////////////////PUBLIC FUNCTIONS///////////////////////////////////////////////	
	//FUNCTION TO LOAD PO NUM FROM MALACHI DATABASE
	public function fetch_po_data($PONum){
		$db_found = $this->connect_DBM();
		if($db_found){
			$SQL = "select SUM(ShippedQty) AS item_shipped from tblDLabel WHERE PONum = '".$PONum."';";
			$result = mysql_query($SQL);
			return $result;
			mysql_free_result($result);
		}
		else{
			$errorMsg = "Database not found, Please contact your administrator";
		}
		$this->close_DBM();
	}
}


// error_reporting(E_ALL);
// ini_set('display_errors', 1);
/* test items */
//450175236700010
//105548201
// $ypics = new YPICS();
// $result = $ypics->fetch_po_data('vwDLabel_CN','4501752367','105548201');
// while($row = mssql_fetch_array($result)){
	// echo $row['Prodcode']." ".$row['OrderQty']."<br>";
// }

// $dlabel = new DLABEL();
// $result = $dlabel->fetch_po_data('4501630764','');
// while($row = mysql_fetch_array($result)){
	// echo "data from dlabel: ".$row['ShippedQty'];
// }