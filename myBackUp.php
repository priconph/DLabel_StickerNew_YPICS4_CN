<?php

if($_POST["txtNumOfStickers"] == ""){ //if the user did not specify a specific number of stickers do the recent printing in DLabel
	normalPrinting(); //calls the function for normal printing
}else{ //if the user specify a specific number of ticket print
	otherPrinting(); //call the function to print the desired amount of qty per sticker
}

//function dlabel normal
function nomalPrinting(){
error_reporting(E_ALL|E_STRICT);

$poNum=$_GET["txtPoNum"];//get product number
$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
$dateComment=$_GET["date"];// get shipment date
$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
$total=$shipNum;
$label="";


$dbName="CN_YPICS1";//name of the database
$user="builder";//username
$pass="";//password
$host="192.168.3.251\MSSQLSERVER";//host
$viewName;//="vwDLabel";//table/view name to be used
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

$conn=mssql_connect($host,$user,$pass);//connection to the Microsoft SQL
$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of Database
$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query


if($row=mssql_fetch_array($query)){

	$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
	$prodCode=$row["Prodcode"];//getting the product code
	$prodName=$row["Prodname"];//getting the product name

	$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
	//echo $buyerComment.":-:";
	$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated

	for($i=0;$i<$numberOfStickers;$i++)
	{
		/*******************************************
		 **   The following are the ZPL codes     **
		 ** that the Zebra printer can understand **
		 *******************************************/
		$label="^XA";
		$label.="^FO 8,8";
		$label.="^GB 456,60,2,B";
		$label.="^FS";	  
		$label.="^CF 0,20"; 
		$label.="^FO 8,12";
		$label.="^FD Buyer"; 
		$label.="^FS";	  
		$label.=" ^CF 0,26"; 
		$label.=" ^FO 72,32";
		$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
		$label.="^FS";	  
		$label.="^FO 8,67";
		$label.="^GB 456,64,2,B"; 
		$label.="^FS";	 
		$label.="^CF 0,20"; 
		$label.="^FO 8,72"; 
		$label.="^FD Delivery place name"; 
		$label.="^FS";	  
		$label.="^CF 0,20";
		$label.="^FO 300,75"; 
		$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
		$label.="^FS";	   
		$label.="^FO 8,130";
		$label.="^GB 456,64,2,B";
		$label.="^FS";
		$label.="^CF 0,20";
		$label.="^FO 8,134"; 
		$label.="^FD Delivery Key No.";	  
		$label.="^FS^CF 0,25^FO 140,153";
		$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
		$label.="^FS^FO 8,193^GB 456,74,2,B^FS^CF 0,20^FO 8,198^FD Product code^FS^FO 40,211^FD (buyer)^FS^CF 0,20^FO 200,200";
		$label.="^FD".$drawingNumber;	  //The Drawing number
		$label.="^FS^CF 0,25^FO 44,234"; 
		$label.="^FD".$prodCode; //The Product Code
		$label.="^FS";	   
		$label.="^FO 8,266^GB 759,58,2,B^FS^CF 0,20^FO 8,268^FD Product name^FS^CF 0,25^FO 44,290^FD";	 

		$label.=$prodName;//The Product Name
		$label.="^FS^FO 8,323^GB 628,56,2,B^FS^CF 0,20^FO 8,330^FD QTY/Total QTY^FS^CF 0,24^FO 390,350^FD";	
		$label.="".($shipNum>=$qtyPerBox ? $qtyPerBox : $shipNum)."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
		$label.="^FS^FO 8,378^GB 520,64,2,B^FS^CF 0,20^FO 8,384^FD Comments for buyer^FS^CF 0,25^FO 120,404^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
		$label.="^FS^FO 463,8^GB 304,259,2,B^FS^CF 0,20^FO 466,12^FD Vendor^FS^CF 0,21^FO 468,38^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO 465,105^FD Comments for vendor";//Vendor which is PMI
		$label.="^FS^CF 0,24^FO 468,177^FD";	
		$label.="".$dateComment;	//Comment for vendor. or the date of shipping
		//$label.="^FS^CF 0,21^FO 470,207^FD";
		//$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
		$label.=" ^FS^FO 635,323 ^GB 132,56,2,B^FS^CF 0,20^FO 634,330 ^FD Unit^FS^CF 0,20^FO 698,350^FD PC^FS^FO 527,378^GB 240,64,2,B^FS^CF 0,20^FO 530,390^FD Package count^FS^CF 0,20^FO 633,410^FD";	
		if($department==1){
			$label.=($numberOfStickers-$i)."/".$numberOfStickers;//package count over the number of stickers to be produced
		}
		else
		{
		$label.=($i+1)."/".$numberOfStickers;//package count over the number of stickers to be produced
		}
		$label.="^FS^XZ";
	    
		echo $label.(($i+1==$numberOfStickers)? "":":-:");//adds a delimiter ":-:" if it is not the final sticker
		$shipNum-=$qtyPerBox;
	}
	
}
}

//function for dlabel other mode of printing
function otherPrinting(){
	error_reporting(E_ALL|E_STRICT);
	$poNum=$_GET["txtPoNum"];//get product number
	$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
	$dateComment=$_GET["date"];// get shipment date
	$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
	$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	$total=$shipNum;
	$label="";

	//DLabel Sticker Revision
	$dynamicQty = array();
	$dynamicQty=$_GET["txtQtyPerBox"]; //Quantity per box
	$dynamicQty=explode(",", $dynamicQty); 
	$numOfStickers2=$_GET["txtNumOfStickers"];//NumOfStickers per box

	$dbName="CN_YPICS1";//name of the database
	$user="builder";//username
	$pass="";//password
	$host="192.168.3.251\MSSQLSERVER";//host
	$viewName;//="vwDLabel";//table/view name to be used
	//select a department 
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

	$conn=mssql_connect($host,$user,$pass);//connection to the Microsoft SQL
	$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of Database
	$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query


	if($row=mssql_fetch_array($query)){

		$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
		$prodCode=$row["Prodcode"];//getting the product code
		$prodName=$row["Prodname"];//getting the product name

		$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
		//echo $buyerComment.":-:";
		$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated

		for($i=0;$i<$numberOfStickers2;$i++)
		{
			/*******************************************
			 **   The following are the ZPL codes     **
			 ** that the Zebra printer can understand **
			 *******************************************/
			$label="^XA";
			$label.="^FO 8,8";
			$label.="^GB 456,60,2,B";
			$label.="^FS";	  
			$label.="^CF 0,20"; 
			$label.="^FO 8,12";
			$label.="^FD Buyer"; //Buyer Label
			$label.="^FS";	  
			$label.=" ^CF 0,26"; 
			$label.=" ^FO 72,32";
			$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
			$label.="^FS";	  
			$label.="^FO 8,67";
			$label.="^GB 456,64,2,B"; 
			$label.="^FS";	 
			$label.="^CF 0,20"; 
			$label.="^FO 8,72"; 
			$label.="^FD Delivery place name"; //Delevery Place Label
			$label.="^FS";	  
			$label.="^CF 0,20";
			$label.="^FO 300,75"; 
			$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
			$label.="^FS";	   
			$label.="^FO 8,130";
			$label.="^GB 456,64,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO 8,134"; 
			$label.="^FD Delivery Key No.";	  //Delivery key number label
			$label.="^FS^CF 0,25^FO 140,153";
			$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
			$label.="^FS^FO 8,193^GB 456,74,2,B^FS^CF 0,20^FO 8,198^FD Product code^FS^FO 40,211^FD (buyer)^FS^CF 0,20^FO 200,200";
			$label.="^FD".$drawingNumber;	  //The Drawing number
			$label.="^FS^CF 0,25^FO 44,234"; 
			$label.="^FD".$prodCode; //The Product Code
			$label.="^FS";	   
			$label.="^FO 8,266^GB 759,58,2,B^FS^CF 0,20^FO 8,268^FD Product name^FS^CF 0,25^FO 44,290^FD"; //product name label	 

			$label.=$prodName;//The Product Name
			$label.="^FS^FO 8,323^GB 628,56,2,B^FS^CF 0,20^FO 8,330^FD QTY/Total QTY^FS^CF 0,24^FO 390,350^FD";	
			//$label.="".($shipNum>=$qtyPerBox ? $qtyPerBox : $shipNum)."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="".($dynamicQty[$i]."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="^FS^FO 8,378^GB 520,64,2,B^FS^CF 0,20^FO 8,384^FD Comments for buyer^FS^CF 0,25^FO 120,404^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
			$label.="^FS^FO 463,8^GB 304,259,2,B^FS^CF 0,20^FO 466,12^FD Vendor^FS^CF 0,21^FO 468,38^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO 465,105^FD Comments for vendor";//Vendor which is PMI
			$label.="^FS^CF 0,24^FO 468,177^FD";	
			$label.="".$dateComment;	//Comment for vendor. or the date of shipping
			//$label.="^FS^CF 0,21^FO 470,207^FD";
			//$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
			$label.=" ^FS^FO 635,323 ^GB 132,56,2,B^FS^CF 0,20^FO 634,330 ^FD Unit^FS^CF 0,20^FO 698,350^FD PC^FS^FO 527,378^GB 240,64,2,B^FS^CF 0,20^FO 530,390^FD Package count^FS^CF 0,20^FO 633,410^FD";	
			if($department==1){
				$label.= $i."/".$numberOfStickers2; // dynamic package count to the number specified number of stickers
			}
			else
			{
			$label.=($i+1)."/".$numberOfStickers;//package count over the number of stickers to be produced
			}
			$label.="^FS^XZ";
			
			echo $label.(($i+1==$numberOfStickers)? "":":-:");//adds a delimiter ":-:" if it is not the final sticker
			$shipNum-=$qtyPerBox;
		}
		
	}
}
?>



function otherPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$dynamicQty,$numberOfStickers2){
	//error_reporting(E_ALL|E_STRICT);

	$total=$shipNum;
	$label="";

	

	$dbName="CN_YPICS1";//name of the database
	$user="builder";//username
	$pass="";//password
	$host="192.168.3.251\MSSQLSERVER";//host
	$viewName;//="vwDLabel";//table/view name to be used
	//select a department 
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

	$conn=mssql_connect($host,$user,$pass);//connection to the Microsoft SQL
	$db_select=mssql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of Database
	$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query


	if($row=mssql_fetch_array($query)){

		$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
		$prodCode=$row["Prodcode"];//getting the product code
		$prodName=$row["Prodname"];//getting the product name

		$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
		//echo $buyerComment.":-:";
		$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated

		for($i=0;$i<$numberOfStickers2;$i++)
		{
			/*******************************************
			 **   The following are the ZPL codes     **
			 ** that the Zebra printer can understand **
			 *******************************************/
			$label="^XA";
			$label.="^FO 8,8";
			$label.="^GB 456,60,2,B";
			$label.="^FS";	  
			$label.="^CF 0,20"; 
			$label.="^FO 8,12";
			$label.="^FD Buyer"; //Buyer Label
			$label.="^FS";	  
			$label.=" ^CF 0,26"; 
			$label.=" ^FO 72,32";
			$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
			$label.="^FS";	  
			$label.="^FO 8,67";
			$label.="^GB 456,64,2,B"; 
			$label.="^FS";	 
			$label.="^CF 0,20"; 
			$label.="^FO 8,72"; 
			$label.="^FD Delivery place name"; //Delevery Place Label
			$label.="^FS";	  
			$label.="^CF 0,20";
			$label.="^FO 300,75"; 
			$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
			$label.="^FS";	   
			$label.="^FO 8,130";
			$label.="^GB 456,64,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO 8,134"; 
			$label.="^FD Delivery Key No.";	  //Delivery key number label
			$label.="^FS^CF 0,25^FO 140,153";
			$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
			$label.="^FS^FO 8,193^GB 456,74,2,B^FS^CF 0,20^FO 8,198^FD Product code^FS^FO 40,211^FD (buyer)^FS^CF 0,20^FO 200,200";
			$label.="^FD".$drawingNumber;	  //The Drawing number
			$label.="^FS^CF 0,25^FO 44,234"; 
			$label.="^FD".$prodCode; //The Product Code
			$label.="^FS";	   
			$label.="^FO 8,266^GB 759,58,2,B^FS^CF 0,20^FO 8,268^FD Product name^FS^CF 0,25^FO 44,290^FD"; //product name label	 

			$label.=$prodName;//The Product Name
			$label.="^FS^FO 8,323^GB 628,56,2,B^FS^CF 0,20^FO 8,330^FD QTY/Total QTY^FS^CF 0,24^FO 390,350^FD";	
			//$label.="".($shipNum>=$qtyPerBox ? $qtyPerBox : $shipNum)."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="".($dynamicQty[$i]."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="^FS^FO 8,378^GB 520,64,2,B^FS^CF 0,20^FO 8,384^FD Comments for buyer^FS^CF 0,25^FO 120,404^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
			$label.="^FS^FO 463,8^GB 304,259,2,B^FS^CF 0,20^FO 466,12^FD Vendor^FS^CF 0,21^FO 468,38^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO 465,105^FD Comments for vendor";//Vendor which is PMI
			$label.="^FS^CF 0,24^FO 468,177^FD";	
			$label.="".$dateComment;	//Comment for vendor. or the date of shipping
			//$label.="^FS^CF 0,21^FO 470,207^FD";
			//$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
			$label.=" ^FS^FO 635,323 ^GB 132,56,2,B^FS^CF 0,20^FO 634,330 ^FD Unit^FS^CF 0,20^FO 698,350^FD PC^FS^FO 527,378^GB 240,64,2,B^FS^CF 0,20^FO 530,390^FD Package count^FS^CF 0,20^FO 633,410^FD";	
			if($department==1){
				$label.= $i."/".$numberOfStickers2; // dynamic package count to the number specified number of stickers
			}
			else
			{
			$label.=($i+1)."/".$numberOfStickers;//package count over the number of stickers to be produced
			}
			$label.="^FS^XZ";
			
			echo $label.(($i+1==$numberOfStickers)? "":":-:");//adds a delimiter ":-:" if it is not the final sticker
			$shipNum-=$qtyPerBox;
		}
		
	}
}
















































































<?php
						/***********************************************************************************************************
						 ** 				By the time that you are reading this dear developer,            					  **
						 ** 		I would already have graduated in the degree that I have chosen.(I hope)					  **
						 ** I know that the reason you are reading this is because they are asking you to modify the code.(Maybe) **
						 ** 		If so.....I'm sorry for not doing a great job of keeping this code clean.                     **
						 ** 	I trust that you can do a better job. So do as you please. Have fun with your stay at PRICON	  **
						 ***********************************************************************************************************/
?>