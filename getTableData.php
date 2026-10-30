<?php
	error_reporting(E_ALL);
	//Department 1=TS 2=CN 3=YF
	$department=$_GET['department'];
	//Mode either "Date","Num", or "None"(None means that the data taken will be of the date today)
	$mode=$_GET['mode'];
	
	$dateFrom;//FROM date
	$dateTo;//TO date
	$PONum;//Product number
	$dbName="db_dlabel";//MYSQL database name
	$user="root";//MYSQL username
	$pass="r@p1d0nl1n3";//MYSQL password
	$host="localhost";//MYSQL host
	$tblName="tblDLabel";//MYSQL table/view

	$conn=mysql_connect($host,$user,$pass) or die("There was an error in the connection");//MYSQL connection
	$db_select=mysql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of DB
	$query;
	
	function check_in_range($start_date, $date_from_user)
	{
	  // Convert to timestamp
	  
	  $user_ts = strtotime($date_from_user);
	  $start_ts = strtotime($start_date);
	  $end_ts = strtotime("+365 day",$start_ts);

	  // Check that user date is between start & end
	  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}
	
	
	if($mode=='None'){//if mode is None
		$query=mysql_query("Select * from ".$tblName." where department='".$department."' and CAST(transDate AS DATE)=curdate() order by transNum desc") or die("query error");
	}
	else if($mode=='Date')
	{//if mode is "Date" gets the values from the database based on the date range
		$dateFrom=$_GET['from'];
		$dateTo=$_GET['to'];
		$query=mysql_query("Select * from ".$tblName." where department='".$department."' and transDate between '".date("Y-m-d", strtotime($dateFrom))."' and '".date("Y-m-d", strtotime($dateTo."+1 day"))."' order by transNum desc" ) or die("query error");
		//$query=mysql_query("Select * from ".$tblName." where transDate between '".date("Y-m-d", strtotime($dateFrom))."' and '".date("Y-m-d", strtotime($dateTo."+1 day"))."' order by transNum desc" ) or die("query error");
		
	}
	else//gets the values from the database based on Product number
	{
		$PONum=$_GET['PONum'];
		//echo $department;
		$query=mysql_query("Select * from ".$tblName." where department='".$department."' and PONum like CONCAT('".$PONum."','%') order by transNum desc") or die("query error");
		//$query=mysql_query("Select * from ".$tblName." where PONum like CONCAT('".$PONum."','%') order by transNum desc") or die("query error");
	}
	while($row=mysql_fetch_array($query))//sends the table values
	{
		/*$clickable="class='clickableRow' href='index.php'";
		$date=$row['transDate'];
		$datefromDateTime=date( 'Y-m-d', strtotime($date) );
		$shipDate=date_format(date_create($row['shipmentDate']),"M-d-Y");
		$transNum=$row['transNum'];
		
		$PONum=$row['PONum'];
		$SQty=$row['ShippedQty'];
		$MQty=$row['MaxQty'];
		$DPN=$row['DeliveryPlaceName'];
		$isEditable=$row['Editable'];
		$url='"input.php?Mode=Edit&department='.$department.'&PONum='.$PONum.'&Trans='.$transNum.'&SQty='.$SQty.'&ShipDate='.$shipDate.'&MQty='.$MQty.'&DPN='.$DPN.'"';
		//$url='"input.php?Mode=Edit&PONum='.$PONum.'&Trans='.$transNum.'&SQty='.$SQty.'&ShipDate='.$shipDate.'&MQty='.$MQty.'&DPN='.$DPN.'"';
											$datetdy=date("Y-m-d");
											$datediff=(floor((strtotime($datetdy)-strtotime($datefromDateTime))/3600/24));
											echo "<tr ".(($datediff==0&&$isEditable!=0)?$clickable:"").">";
												echo "<td width='150px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo date_format(date_create($date),"M-d-Y");echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $PONum;echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='120px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $row['OrigQty'];echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $SQty;echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='90px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $row['QtyOnHand'];echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($datediff==0&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $shipDate;echo (($datediff==0&&$isEditable!=0)?"</a>":"")."</td>";
											echo "</tr>";*/
		$clickable="class='clickableRow' href='index.php'";
											$date=$row['transDate'];
											$datefromDateTime=date( 'Y-m-d', strtotime($date) );
											$shipDate=date_format(date_create($row['shipmentDate']),"M-d-Y");
											$transNum=$row['transNum'];
											$PONum=$row['PONum'];
											$SQty=$row['ShippedQty'];
											$MQty=$row['MaxQty'];
											$DPN=$row['DeliveryPlaceName'];
											$isEditable=$row['Editable'];
											$url='"input.php?Mode=Edit&department='.$department.'&PONum='.$PONum.'&Trans='.$transNum.'&SQty='.$SQty.'&ShipDate='.$shipDate.'&MQty='.$MQty.'&DPN='.$DPN.'"';
											//$url='"input.php?Mode=Edit&PONum='.$PONum.'&Trans='.$transNum.'&SQty='.$SQty.'&ShipDate='.$shipDate.'&MQty='.$MQty.'&DPN='.$DPN.'"';
											$datetdy=date("Y-m-d");
											$datediff=(floor((strtotime($datetdy)-strtotime($datefromDateTime))/3600/24));
											$underWeek=check_in_range($datefromDateTime, $datetdy);
											//echo $datefromDateTime." ".$datetdy."     ".$datediff;
											echo "<tr ".(($underWeek&&$isEditable!=0)?$clickable:"").">";
												echo "<td width='150px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo date_format(date_create($date),"M-d-Y");echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $PONum;echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='120px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $row['OrigQty'];echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $SQty;echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='90px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $row['QtyOnHand'];echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
												echo "<td width='100px'>".(($underWeek&&$isEditable!=0)?"<a href=".$url.">":"")."";echo $shipDate;echo (($underWeek&&$isEditable!=0)?"</a>":"")."</td>";
											echo "</tr>";
											
	}
?>