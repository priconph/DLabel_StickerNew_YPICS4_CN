<?php
$mysqlDBName="db_dlabel";//MYSQL database name
$mysqlUser="root";//MYSQL username
$mysqlPass="r@p1d0nl1n3";//MYSQL password
$mysqlHost="localhost";//MYSQL host
$mysqlTable="tblDLabel";//MYSQL table
$mysqlConn=mysql_connect($mysqlHost,$mysqlUser,$mysqlPass);//MYSQL connection
$mysqlDB=mysql_select_db($mysqlDBName,$mysqlConn);//Selection of database
//$mysqlQuery;

//echo "test";
if(is_ajax()) {
	if(isset($_POST["action"]) && !empty($_POST["action"])) {
		$action = $_POST["action"];
		switch($action) {
			case "deleteSticker" : deleteSticker(); break;
		}
	}
}

function is_ajax() {
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

//deleteSticker(); //for testing do not delete
function deleteSticker(){	//function for delete and update
$return = $_POST;
$poNum=$return["PoNum"];//Product Number
//$poNum="4500557017";//Product Number
$result = "";
//$result = mysql_query("select * from ".$mysqlTable." where PONum='".$poNum."' order by transNum desc LIMIT 1");
$result = mysql_query("select * from tblDLabel where PONum = '$poNum' order by transNum desc LIMIT 1"); 

while($row=mysql_fetch_array($result)){
	$return['transNum'] = $row['transNum'];
	$transnum = $row['transNum'];
}

$result = mysql_query("delete from tblDLabel where transNum = $transnum");
$result = mysql_query("select * from tblDLabel where PONum = '$poNum' order by transNum desc LIMIT 1"); 

while($row=mysql_fetch_array($result)){
	$return['transNum'] = $row['transNum'];
	$transnum = $row['transNum'];
}

$result = mysql_query("update tblDLabel set Editable = 1 where transNum = $transnum"); 

$return['PoNum'] = $poNum;
$return["result"] = json_encode($result);
echo json_encode($return);
}

?>