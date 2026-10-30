<?php
if(isset($_POST['action'])){
	switch($_POST['action']){
		case "get_quantity"	: get_quantity(); break; 
	}
}

function get_quantity(){
	require_once('dlabel_query.php');
	$return = array();
	$post = $_POST;
	$OrderQty 		= 0;
	$item_shipped 	= 0;
	$available_qty 	= 0;
	$ypics = new YPICS();
	$result = $ypics->fetch_po_data($post['viewName'],$post['PONum']);
	while($row = mssql_fetch_array($result)){
		$OrderQty = $row['OrderQty'];
		$LotQty = $row['LotQty'];
	}
	
	$dlabel = new DLABEL();
	$result = $dlabel->fetch_po_data($post['PONum']);
	while($row = mysql_fetch_array($result)){
		$item_shipped = $row['item_shipped'];
	}
	$return['LotQty'] 			= $LotQty;
	$return['OrderQty'] 		= $OrderQty;
	$return['available_qty'] 	= $OrderQty - $item_shipped;
	$return['result'] 			= json_encode($return);
	echo json_encode($return);
}