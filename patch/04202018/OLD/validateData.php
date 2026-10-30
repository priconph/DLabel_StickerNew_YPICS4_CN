<?php
//Gets the mode of the transaction. Either "Edit" or "New"
$mode=$_GET["mode"];
$edit_mode=($mode=="Edit");
$department=$_GET["department"];//Department 1=TS 2=CN 3=YF

$poNum=$_GET["PONum"];//Product Number
$transNum=$_GET["Trans"];//Transaction Number
$qtyPerBox=$_GET["MaxQty"];//Quantity per box
$NumOfStickers=$_GET["NumOfStickers"];//NumOfStickers per box
$dynamicQty=$_GET["QtyPerBox"];//Quantity per box 2
$dateComment=$_GET["date"];//Date of shipment
$deliveryPlaceName=$_GET["dpn"];//Delivery place name
$shipNum = $_GET["ShipQty"];//Shipment quantity
$with_dot = $_GET['with_dot'];


$mysqlDBName="db_dlabel";//MYSQL database name
$mysqlUser="root";//MYSQL username
$mysqlPass="r@p1d0nl1n3";//MYSQL password
$mysqlHost="localhost";//MYSQL host
$mysqlTable="tblDLabel";//MYSQL table
$mysqlConn=mysql_connect($mysqlHost,$mysqlUser,$mysqlPass);//MYSQL connection
$mysqlDB=mysql_select_db($mysqlDBName,$mysqlConn);//Selection of database
$mysqlQuery;
if($edit_mode)//if mode is "Edit"
{
	//The query for "Edit"
	$mysqlQuery=mysql_query("select * from ".$mysqlTable." where transNum='".$transNum."'");
}
else//I AM BATMAN
{
	//The query for "New"
	$mysqlQuery=mysql_query("select * from ".$mysqlTable." where PONum='".$poNum."' order by transNum desc LIMIT 1");
}

/* $dbName="CN_YPICS1";//MSSQL database name
$user="builder";//MSSQL username
$pass="";//MSSQL password
$host="192.168.3.251\MSSQLSERVER";//MSSQL host
$viewName="vwDLabel";//MSSQL table/view name
if($department==1)
{
	$viewName="vwDLabel_TS";
}
else if($department==2)
{
	$viewName="vwDLabel_CN";
}
else
{
	$viewName="vwDLabel_YF";
}

$conn=mssql_connect($host,$user,$pass);//MSSQL connection
$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of database 

$checkPO = str_split($poNum);
if($checkPO[0] == 'E'){ //if PO starts on E
	$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."'");//MSSQL Query
}else if (strpos($poNum, '4501927162') !== false || strpos($poNum, '4501885018') !== false) {
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00011'");//MSSQL Query
}else{
	if($with_dot == 1){
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010.' OR PONum='".$poNum."00010A' OR PONum='".$poNum."00010B' OR PONum='".$poNum."00010C' OR PONum='".$poNum."00011'");//MSSQL Query
	}else{
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query
	}
	// $query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query
}

*/

$checkPO = str_split($poNum);

if($department == 1) {
	$username		= "sa";							//MSSQL username
	$password		= "Pr1c0N";						//MSSQL password
	$server			= "192.168.3.247";				//MSSQL host
	$db_name		= "bu2_v4";						//MSSQL database name


	$query			= 'SELECT XRECE.SORDER AS PONum, XRECE.CODE AS Prodcode, XHEAD.NAME AS Prodname, 
						  XRECE.KVOL AS OrderQty, XITEM.DRAWING_NUM, XRECE.BUYERS_CODE
						  FROM XHEAD INNER JOIN XRECE ON XHEAD.CODE = XRECE.CODE INNER JOIN
						  XITEM ON XHEAD.CODE = XITEM.CODE'; 
	
	if($checkPO[0] == 'E'){ //if PO starts on E
		$query = $query . " where XRECE.SORDER = '".$poNum."'";//MSSQL Query
	} else if (strpos($poNum, '4501927162') !== false || strpos($poNum, '4501885018') !== false) {
		$query = $query . " where XRECE.SORDER = '".$poNum."00011'";//MSSQL Query
	} else{
		if($with_dot == 1){
			$query .= " where XRECE.SORDER='".$poNum."00010.' OR XRECE.SORDER='".$poNum."00010A' OR XRECE.SORDER='".$poNum."00010B' OR XRECE.SORDER='".$poNum."00010C' OR XRECE.SORDER='".$poNum."00011'";//MSSQL Query
		}else{
			$query = $query . " where XRECE.SORDER='".$poNum."00010'";//MSSQL Query
		}
	}				
} else {
	if($department == 2) {
		$username		= "builder";					//MSSQL username
		$password		= "";							//MSSQL password
		$server			= "192.168.3.251\MSSQLSERVER";  //MSSQL host
		$db_name		= "CN_YPICS1";					//MSSQL database name
		
		$query			= 'select * from vwDLabel_CN';
	 } else if($department == 3) {
		$username		= "builder";					//MSSQL username
		$password		= "";							//MSSQL password
		$db_name		= "CN_YPICS1";					//MSSQL database name
		$server			= "192.168.3.251\MSSQLSERVER";  //MSSQL host
		
		$query			= 'select * from vwDLabel_YF';
	 }
	 
	if($checkPO[0] == 'E'){ //if PO starts on E
		$query = $query . " where PONum='".$poNum."'";//MSSQL Query
	}else if (strpos($poNum, '4501927162') !== false || strpos($poNum, '4501885018') !== false) {
		$query = $query . " where PONum='".$poNum."00011'";//MSSQL Query
	}else{
		if($with_dot == 1){
			$query = $query . " where PONum='".$poNum."00010.' OR PONum='".$poNum."00010A' OR PONum='".$poNum."00010B' OR PONum='".$poNum."00010C' OR PONum='".$poNum."00011'";//MSSQL Query
		}else{
			$query = $query . " where PONum='".$poNum."00010'";//MSSQL Query
		}
	}
} 
 
	$conn			= mssql_connect($server,$username,$password);							//MSSQL Connection
	$db_select		= mssql_select_db($db_name,$conn) or die("Could not connect to DB2");	//Selection of database
	$query			= mssql_query($query);													//MSSQL Query 
	
if($row=mssql_fetch_array($query))//If the MSSQL query has a value
{
	$drawingNumber=$row["DRAWING_NUM"];//Drawing number
	$prodCode=$row["Prodcode"];//Product Code
	$prodName=$row["Prodname"];//Product Name
	$orderQty=$row["OrderQty"];//Order quantity(Or the number of products for the Product)
	
		$d=DateTime::createFromFormat('dmY', $dateComment);//Create date from format 'dmY' (sample-140414)
		if($row2=mysql_fetch_array($mysqlQuery))//If the MYSQL query has a value
		{
			$transNum=$row2["transNum"];//transaction number
			$oldShipNum=$row2["ShippedQty"];//Old shipment quantity
			$oldQtyOnHand=$row2["QtyOnHand"];//Old Quantity on hand
			$QtyOnHand=$row2["QtyOnHand"];//Quantity on hand
			$ORGQTY=$row2["OrigQty"];//Original quantity
			$editable=$row2["Editable"];//If the Transaction is still editable
			
			if(!$edit_mode){//if the mode is "New"
				if($oldQtyOnHand<$shipNum)//If the Quantity on hand is less than the shipment quantity
				{
					echo "There is only a number of ".$oldQtyOnHand." products left. Shipping Qty should be lower than/equal to ".$oldQtyOnHand;
				}
				else//If the values are valid
				{
					mysql_query("update tblDLabel set Editable='0' where PONum='".$poNum."'");//sets the editable value of the transactions made with the Product number to '0'(zero) or false
					mysql_query("insert into tblDLabel values('0',NOW(),'".$poNum."','".$ORGQTY."','".$shipNum."','".($QtyOnHand-$shipNum)."','".$d->format('Y-m-d')."','".$qtyPerBox."','".$deliveryPlaceName."','".$department."','1')");//inserts a new transaction with the new values
					
					echo "Done.";
				}
			}
			else//if the mode is "Edit"
			{
				
				$difference=$shipNum-$oldShipNum;//gets the difference of the SHIPMENT QTY and the OLD SHIPMENT QTY (or how much Shipment QTY was changed)
				$newShipNum=$oldShipNum+$difference;//Sets the new Shipment QTY based on how much was changed
				$newQtyOnHand=$oldQtyOnHand-$difference;//sets the new QTY ON HAND based on how much was changed
				$first=(($oldShipNum+$oldQtyOnHand)==$ORGQTY);//Checks if the transaction being edited is the first transaction for the Product Number
				if($first)//if it is the first transaction
				{
					if($shipNum>$ORGQTY){//if the Shippment QTY is greater than Original QTY
						echo "There is only a number of ".$ORGQTY." products left. Shipping Qty should be lower than/equal to ".$ORGQTY;
					}
					else if($editable==0)//if the transaction is not editable.(The table row shouldn't be clickable if it is now editable. this is just an extra precaution)
					{
						echo "This is not editable.";
					}
					else//I AM BATMAN
					{
						mysql_query("update tblDLabel set shippedQty='".$newShipNum."',QtyOnHand='".$newQtyOnHand."',MaxQty='".$qtyPerBox."',DeliveryPlaceName='".$deliveryPlaceName."',shipmentDate='".$d->format('Y-m-d')."' where transNum='".$transNum."'");//updates the transaction with the new values
						echo "Updated.";
					}
				}
				else{//I AM BATMAN
					if($shipNum>($oldShipNum+$oldQtyOnHand)){//if the Shippment QTY is greater than the sum of (OLD SHIPMENT QTY and OLD QTY ON HAND)
						echo "There is only a number of ".($oldShipNum+$oldQtyOnHand)." products left. Shipping Qty should be lower than/equal to ".($oldShipNum+$oldQtyOnHand);
					}
					else{//I AM BATMAN
						mysql_query("update tblDLabel set shippedQty='".$newShipNum."',QtyOnHand='".$newQtyOnHand."',MaxQty='".$qtyPerBox."',DeliveryPlaceName='".$deliveryPlaceName."',shipmentDate='".$d->format('Y-m-d')."' where transNum='".$transNum."'");//updates the transaction with the new values
						echo "Updated.";
					}
				}
			}
		}
		else//if the product number does not exist in the MYSQL database(Which means this will be the first transaction for the Product number that will be inserted to the MYSQL database)
		{
			if($shipNum>$orderQty){//If the Shipment quantity is greater than the order quantity
				echo "There is only a number of ".$orderQty." products left. Shipping Qty should be lower than/equal to ".$orderQty;
			}
			else//I AM BATMAN
			{
				$ret=mysql_query("insert into tblDLabel values('0',NOW(),'".$poNum."','".$orderQty."','".$shipNum."','".($orderQty-$shipNum)."','".$d->format('Y-m-d')."','".$qtyPerBox."','".$deliveryPlaceName."','".$department."','1')");//Inserts the new value to the database
				if(! $ret)//If inserting fails (if you know what I mean)
				{
					//echo $d->format('Y-m-d');
					die('Could not enter data: ' . mysql_error());
				}
				echo "Done.";
			}
		}
		
	
}
else//if the Product number does not exist.
{	
	if($with_dot == 1){
		echo "The PO# ".$poNum."00010. does not exist.";
	}else{
		echo "The PO# ".$poNum."00010 does not exist.";
	}
	
}


?>