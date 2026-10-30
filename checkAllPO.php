
<?php
error_reporting(E_ALL|E_STRICT);
$dbName="CN_YPICS1";//MSSQL database name
$user="builder";//MSSQL username
$pass="";//MSSQL password
$host="192.168.3.251\MSSQLSERVER";//MSSQL host

$conn=mssql_connect($host,$user,$pass);//MSSQL connection
$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of database
$query=mssql_query("select * from vwDLabel_TS");//MSSQL Query
//echo "asdasdasdas";
/*while($row=mssql_fetch_array($query))
{
	echo $row["PONum"];
	echo "<br>";
}

echo "<br>";
echo "<br>";
echo "break";
echo "<br>";
echo "<br>";*/
$query=mssql_query("select * from vwDLabel_CN");//MSSQL Query
while($row=mssql_fetch_array($query))
{
	echo $row["PONum"];
	echo "<br>";
}

echo "<br>";
echo "<br>";
echo "break";
echo "<br>";
echo "<br>";
/*$query=mssql_query("select * from vwDLabel_YF");//MSSQL Query
while($row=mssql_fetch_array($query))
{
	echo $row["PONum"];
	echo "<br>";
}*/


?>