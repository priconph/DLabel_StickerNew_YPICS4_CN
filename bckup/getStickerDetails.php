<?php

	error_reporting(E_ALL|E_STRICT);

	$poNum=$_GET["txtPoNum"];//get product number
	$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
	$dateComment=$_GET["date"];// get shipment date
	$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
	$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	$with_dot = $_GET['with_dot'];
	
	//DLabel Sticker Revision
	$dynamicQty = array();
	$dynamicQty=$_GET["txtQtyPerBox"]; //Quantity per box
	$dynamicQty=explode(",", $dynamicQty);
	$numberOfStickers2=$_GET["txtNumOfStickers"];//NumOfStickers per box
	$packageCount =$_GET["txtPackageCount"];
	$packageQty =$_GET["txtPackageQty"];
	if($_GET["txtPackageCount"] == ""){
		if($_GET["txtNumOfStickers"] == ""){
			normalPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$with_dot);
		}else{
			otherPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$dynamicQty,$numberOfStickers2);
		}
	}
	else{
		customizedPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$packageCount,$packageQty);
	}
	
function normalPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$with_dot){
	//error_reporting(E_ALL|E_STRICT);
	$total=$shipNum;
	$label="";
	/*
	$poNum=$_GET["txtPoNum"];//get product number
	$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
	$dateComment=$_GET["date"];// get shipment date
	$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
	$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	$total=$shipNum;
	$label="";
	*/
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
	
	$checkPO = str_split($poNum);
	if($checkPO[0] == 'E'){
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."'");//MSSQL Query
	}else{
		if($with_dot == 1){
			// echo "with dot";
			$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010.' OR PONum='".$poNum."00010A' OR PONum='".$poNum."00010B' OR PONum='".$poNum."00010C'");//MSSQL Query
		}else{
			// echo "without dot";
			$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query
		}
	}

	if($row=mssql_fetch_array($query)){

		$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
		$prodCode=$row["Prodcode"];//getting the product code
		$prodName=$row["Prodname"];//getting the product name

		$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
		//echo $buyerComment.":-:";
		$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated
		
		$filename = $_SERVER['REMOTE_ADDR'];
		if( !file_exists('./adjust_space/'.$filename.'.php') ){
			$filename = '192.168.40.137';
		}
		require_once('./adjust_space/'.$filename.'.php');

		for($i=0;$i<$numberOfStickers;$i++)
		{
			/*******************************************
			 **   The following are the ZPL codes     **
			 ** that the Zebra printer can understand **
			 *******************************************/
			
			/* 
				Additional Notes 
				CF = Font Size
				FO = FO position of cell in left space and top space
				GB = Box size GB Width, Height, 2, B
				Vendor, Product Name, Unit, Package Count GB Width was deducted by 19
			*/
			$label="^XA";
			//$label.="^FO ".(8+$left_space).",8";
			$label.="^FO ".(8+$left_space).",".(8+10+$top_space)."";
			$label.="^GB 456,60,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(12+15+$top_space)."";
			$label.="^FD Buyer"; 
			$label.="^FS";	  
			$label.=" ^CF 0,26"; 
			$label.=" ^FO 72,".(32+12+$top_space)."";
			$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
			$label.="^FS";	  
			$label.="^FO ".(8+$left_space).",".(67+10+$top_space)."";
			$label.="^GB 456,32,2,B"; 
			$label.="^FS";	 
			$label.="^CF 0,20"; 
			$label.="^FO ".(8+$left_space).",".(72+10+$top_space).""; 
			$label.="^FD Delivery place name"; 
			$label.="^FS";	  
			$label.="^CF 0,20";
			$label.="^FO 300,".(75+10+$top_space).""; 
			$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(130-22+$top_space)."";
			$label.="^GB 456,64,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(134-22+$top_space).""; 
			$label.="^FD Delivery Key No.";	  
			$label.="^FS^CF 0,25^FO 140,".(153-22+$top_space)."";
			$checkPO = str_split($poNum);
			if($checkPO[0] == 'E'){
				$label.="^FD".$poNum;
			}else{
				$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
			}
			$label.="^FS^FO ".(8+$left_space).",".(193-22+$top_space)."^GB 456,74,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(198-22+$top_space)."^FD Product code^FS^FO 40,".(211-22+$top_space)."^FD (buyer)^FS^CF 0,20^FO 200,".(200-22+$top_space)."";
			$label.="^FD".$drawingNumber;	  //The Drawing number
			$label.="^FS^CF 0,25^FO 44,".(234-22+$top_space)."";
			$label.="^FD".$prodCode; //The Product Code
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(266-22+$top_space)."^GB 740,58,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(268-22+$top_space)."^FD Product name^FS^CF 0,25^FO 44,".(290-22+$top_space)."^FD";	 

			$label.=$prodName;//The Product Name
			$label.="^FS^FO ".(8+$left_space).",".(323-22+$top_space)."^GB 628,56,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(330-22+$top_space)."^FD QTY/Total QTY^FS^CF 0,24^FO 390,".(350-22+$top_space)."^FD";	
			$label.="".($shipNum>=$qtyPerBox ? $qtyPerBox : $shipNum)."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			//$label.="".($dynamicQty[$i]."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="^FS^FO ".(8+$left_space).",".(378-22+$top_space)."^GB 520,64,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(384-22+$top_space)."^FD Comments for buyer^FS^CF 0,25^FO 120,".(404-22+$top_space)."^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
			
			/* Right Part */
			$label.="^FS^FO ".(463+$left_space).",".(8+10+$top_space)."^GB 285,228,2,B^FS^CF 0,20^FO ".(466+$left_space).",".(12+10+$top_space)."^FD Vendor^FS^CF 0,19^FO ".(468-5+$left_space).",".(38+10+$top_space)."^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO ".(465+$left_space).",".(105+10+$top_space)."^FD Comments for vendor";//Vendor which is PMI
			$label.="^FS^CF 0,24^FO ".(468+$left_space).",".(177-10+$top_space)."^FD";
			$label.="".$dateComment;	//Comment for vendor. or the date of shipping
			$label.="^FS^CF 0,21^FO ".(470+$left_space).",".(235-20+$top_space)."^FD";
			$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
			$label.=" ^FS^FO ".(635+$left_space).",".(323-22+$top_space)." ^GB 113,56,2,B^FS^CF 0,20^FO ".(634+$left_space).",".(330-22+$top_space)." ^FD Unit^FS^CF 0,20^FO 698,".(350-22+$top_space)."^FD PC^FS^FO ".(527+$left_space).",".(378-22+$top_space)."^GB 221,64,2,B^FS^CF 0,20^FO ".(530+$left_space).",".(390-22+$top_space)."^FD Package count^FS^CF 0,20^FO 633,".(410-22+$top_space)."^FD";	
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

function otherPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$dynamicQty,$numberOfStickers2){
	//error_reporting(E_ALL|E_STRICT);
	$total=$shipNum;
	$label="";
	/*
	$poNum=$_GET["txtPoNum"];//get product number
	$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
	$dateComment=$_GET["date"];// get shipment date
	$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
	$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	$total=$shipNum;
	$label="";
	*/
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
	
	$checkPO = str_split($poNum);
	if($checkPO[0] == 'E'){
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."'");//MSSQL Query
	}else{
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query
	}
	


	if($row=mssql_fetch_array($query)){

		$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
		$prodCode=$row["Prodcode"];//getting the product code
		$prodName=$row["Prodname"];//getting the product name

		$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
		//echo $buyerComment.":-:";
		$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated
		// $left_space = '18';
		// $top_space = '20';
		require_once('./adjust_space/space.php');
		for($i=0;$i<$numberOfStickers2;$i++)
		{
			/*******************************************
			 **   The following are the ZPL codes     **
			 ** that the Zebra printer can understand **
			 *******************************************/
			$label="^XA";
			//$label.="^FO ".(8+$left_space).",8";
			$label.="^FO ".(8+$left_space).",".(8+$top_space)."";
			$label.="^GB 456,60,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(12+$top_space)."";
			$label.="^FD Buyer"; 
			$label.="^FS";	  
			$label.=" ^CF 0,26"; 
			$label.=" ^FO 72,".(32+$top_space)."";
			$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
			$label.="^FS";	  
			$label.="^FO ".(8+$left_space).",".(67+$top_space)."";
			$label.="^GB 456,64,2,B"; 
			$label.="^FS";	 
			$label.="^CF 0,20"; 
			$label.="^FO ".(8+$left_space).",".(72+$top_space).""; 
			$label.="^FD Delivery place name"; 
			$label.="^FS";	  
			$label.="^CF 0,20";
			$label.="^FO 300,".(75+$top_space).""; 
			$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(130+$top_space)."";
			$label.="^GB 456,64,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(134+$top_space).""; 
			$label.="^FD Delivery Key No.";	  
			$label.="^FS^CF 0,25^FO 140,".(153+$top_space)."";
			$checkPO = str_split($poNum);
			if($checkPO[0] == 'E'){
				$label.="^FD".$poNum;
			}else{
				$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
			}
			$label.="^FS^FO ".(8+$left_space).",".(193+$top_space)."^GB 456,74,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(198+$top_space)."^FD Product code^FS^FO 40,".(211+$top_space)."^FD (buyer)^FS^CF 0,20^FO 200,".(200+$top_space)."";
			$label.="^FD".$drawingNumber;	  //The Drawing number
			$label.="^FS^CF 0,25^FO 44,".(234+$top_space)."";
			$label.="^FD".$prodCode; //The Product Code
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(266+$top_space)."^GB 759,58,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(268+$top_space)."^FD Product name^FS^CF 0,25^FO 44,".(290+$top_space)."^FD";	 

			$label.=$prodName;//The Product Name
			$label.="^FS^FO ".(8+$left_space).",".(323+$top_space)."^GB 628,56,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(330+$top_space)."^FD QTY/Total QTY^FS^CF 0,24^FO 390,".(350+$top_space)."^FD";	
			//$label.="".($shipNum>=$qtyPerBox ? $qtyPerBox : $shipNum)."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="".$dynamicQty[$i]."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="^FS^FO ".(8+$left_space).",".(378+$top_space)."^GB 520,64,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(384+$top_space)."^FD Comments for buyer^FS^CF 0,25^FO 120,".(404+$top_space)."^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
			$label.="^FS^FO ".(463+$left_space).",".(8+$top_space)."^GB 304,259,2,B^FS^CF 0,20^FO ".(466+$left_space).",".(12+$top_space)."^FD Vendor^FS^CF 0,21^FO ".(468+$left_space).",".(38+$top_space)."^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO ".(465+$left_space).",".(105+$top_space)."^FD Comments for vendor";//Vendor which is PMI
			$label.="^FS^CF 0,24^FO ".(468+$left_space).",".(177+$top_space)."^FD";
			$label.="".$dateComment;	//Comment for vendor. or the date of shipping
			$label.="^FS^CF 0,21^FO ".(470+$left_space).",".(235+$top_space)."^FD";
			$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
			$label.=" ^FS^FO ".(635+$left_space).",".(323+$top_space)." ^GB 132,56,2,B^FS^CF 0,20^FO ".(634+$left_space).",".(330+$top_space)." ^FD Unit^FS^CF 0,20^FO 698,".(350+$top_space)."^FD PC^FS^FO ".(527+$left_space).",".(378+$top_space)."^GB 240,64,2,B^FS^CF 0,20^FO ".(530+$left_space).",".(390+$top_space)."^FD Package count^FS^CF 0,20^FO 633,".(410+$top_space)."^FD";	
			$stickerCount = $i+1;
			if($department==1){
				$label.= $stickerCount."/".$numberOfStickers2; // dynamic package count to the number specified number of stickers
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

function customizedPrinting($poNum,$qtyPerBox,$dateComment,$deliveryPlaceName,$shipNum,$department,$packageCount,$packageQty){
	//error_reporting(E_ALL|E_STRICT);
	$total=$shipNum;
	$label="";
	/*
	$poNum=$_GET["txtPoNum"];//get product number
	$qtyPerBox=$_GET["txtMaxQty"];//get qty per box based on input
	$dateComment=$_GET["date"];// get shipment date
	$deliveryPlaceName=$_GET["selDeliveryPlaceName"]; // get the delivery place name
	$shipNum=$_GET["txtShipQty"]; //get the number of items to be shipped
	$department=$_GET["department"];//Department 1=TS 2=CN 3=YF
	$total=$shipNum;
	$label="";
	*/
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
	
	$checkPO = str_split($poNum);
	if($checkPO[0] == 'E'){
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."'");//MSSQL Query
	}else{
		$query=mssql_query("select * from ".$viewName." where PONum='".$poNum."00010'");//MSSQL Query
	}
	


	if($row=mssql_fetch_array($query)){
		
		$drawingNumber=$row["DRAWING_NUM"];//getting the drawing num from the data base
		$prodCode=$row["Prodcode"];//getting the product code
		$prodName=$row["Prodname"];//getting the product name

		$buyerComment=$row["BUYERS_CODE"];//"./ 0001334571-000010    /03";//  $row["(put the field name of the comment for buyers from the database)"]  ::the comment for buyer.(The data here was not given. Change it if it was already given)
		//echo $buyerComment.":-:";
		
		$filename = $_SERVER['REMOTE_ADDR'];
		if( !file_exists('./adjust_space/'.$filename.'.php') ){
			$filename = '192.168.40.137';
		}
		require_once('./adjust_space/'.$filename.'.php');
		
		$numberOfStickers=ceil($shipNum/$qtyPerBox);//Computing the number of stickers to be generated
			/*******************************************
			 **   The following are the ZPL codes     **
			 ** that the Zebra printer can understand **
			 *******************************************/
			
			/* 
				Additional Notes 
				CF = Font Size
				FO = FO position of cell in left space and top space
				GB = Box size GB Width, Height, 2, B
				Vendor, Product Name, Unit, Package Count GB Width was deducted by 19
			*/
			$label="^XA";
			//$label.="^FO ".(8+$left_space).",8";
			$label.="^FO ".(8+$left_space).",".(8+10+$top_space)."";
			$label.="^GB 456,60,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(12+15+$top_space)."";
			$label.="^FD Buyer"; 
			$label.="^FS";	  
			$label.=" ^CF 0,26"; 
			$label.=" ^FO 72,".(32+12+$top_space)."";
			$label.="^FD YAMAICHI ELECTRONICS CO., LTD";//The Buyer
			$label.="^FS";	  
			$label.="^FO ".(8+$left_space).",".(67+10+$top_space)."";
			$label.="^GB 456,32,2,B"; 
			$label.="^FS";	 
			$label.="^CF 0,20"; 
			$label.="^FO ".(8+$left_space).",".(72+10+$top_space).""; 
			$label.="^FD Delivery place name"; 
			$label.="^FS";	  
			$label.="^CF 0,20";
			$label.="^FO 300,".(75+10+$top_space).""; 
			$label.="^FD".$deliveryPlaceName;//The Delivery Place Name
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(130-22+$top_space)."";
			$label.="^GB 456,64,2,B";
			$label.="^FS";
			$label.="^CF 0,20";
			$label.="^FO ".(8+$left_space).",".(134-22+$top_space).""; 
			$label.="^FD Delivery Key No.";	  
			$label.="^FS^CF 0,25^FO 140,".(153-22+$top_space)."";
			$checkPO = str_split($poNum);
			if($checkPO[0] == 'E'){
				$label.="^FD".$poNum;
			}else{
				$label.="^FD".$poNum."-003+001";  //The Delivery Key Num or the Product number.(the "00010" at the end is removed and replaced with -003+001)
			}
			$label.="^FS^FO ".(8+$left_space).",".(193-22+$top_space)."^GB 456,74,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(198-22+$top_space)."^FD Product code^FS^FO 40,".(211-22+$top_space)."^FD (buyer)^FS^CF 0,20^FO 200,".(200-22+$top_space)."";
			$label.="^FD".$drawingNumber;	  //The Drawing number
			$label.="^FS^CF 0,25^FO 44,".(234-22+$top_space)."";
			$label.="^FD".$prodCode; //The Product Code
			$label.="^FS";	   
			$label.="^FO ".(8+$left_space).",".(266-22+$top_space)."^GB 740,58,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(268-22+$top_space)."^FD Product name^FS^CF 0,25^FO 44,".(290-22+$top_space)."^FD";	 

			$label.=$prodName;//The Product Name
			$label.="^FS^FO ".(8+$left_space).",".(323-22+$top_space)."^GB 628,56,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(330-22+$top_space)."^FD QTY/Total QTY^FS^CF 0,24^FO 390,".(350-22+$top_space)."^FD";	
			$label.="".$packageQty."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			//$label.="".($dynamicQty[$i]."/".$total; //The number of items in a box over the total number of items.The number of items in a box depends on what the user input
			$label.="^FS^FO ".(8+$left_space).",".(378-22+$top_space)."^GB 520,64,2,B^FS^CF 0,20^FO ".(8+$left_space).",".(384-22+$top_space)."^FD Comments for buyer^FS^CF 0,25^FO 120,".(404-22+$top_space)."^FD ".$buyerComment;//The Comment for buyer.(The data here was not given. Change it if it was already given)
			
			/* Right Part */
			$label.="^FS^FO ".(463+$left_space).",".(8+10+$top_space)."^GB 285,228,2,B^FS^CF 0,20^FO ".(466+$left_space).",".(12+10+$top_space)."^FD Vendor^FS^CF 0,19^FO ".(468-5+$left_space).",".(38+10+$top_space)."^FD PRICON MICROELECTRONICS INC.^FS^CF 0,20^FO ".(465+$left_space).",".(105+10+$top_space)."^FD Comments for vendor";//Vendor which is PMI
			$label.="^FS^CF 0,24^FO ".(468+$left_space).",".(177-10+$top_space)."^FD";
			$label.="".$dateComment;	//Comment for vendor. or the date of shipping
			$label.="^FS^CF 0,21^FO ".(470+$left_space).",".(235-20+$top_space)."^FD";
			$label.="Made in the Philippines";	//Comment for vendor. or the date of shipping
			$label.=" ^FS^FO ".(635+$left_space).",".(323-22+$top_space)." ^GB 113,56,2,B^FS^CF 0,20^FO ".(634+$left_space).",".(330-22+$top_space)." ^FD Unit^FS^CF 0,20^FO 698,".(350-22+$top_space)."^FD PC^FS^FO ".(527+$left_space).",".(378-22+$top_space)."^GB 221,64,2,B^FS^CF 0,20^FO ".(530+$left_space).",".(390-22+$top_space)."^FD Package count^FS^CF 0,20^FO 633,".(410-22+$top_space)."^FD";	
			
			if($department==1){
				$label.=$packageCount;//package count over the number of stickers to be produced
			}
			else
			{
				$label.=$packageCount;//package count over the number of stickers to be produced
			}
			$label.="^FS^XZ";
			
			echo $label.(($i+1==$numberOfStickers)? "":":-:");//adds a delimiter ":-:" if it is not the final sticker
			$shipNum-=$qtyPerBox;
	}
}

?>
















































































<?php
						/***********************************************************************************************************
						 ** 				By the time that you are reading this dear developer,            					  **
						 ** 		I would already have graduated in the degree that I have chosen.(I hope)					  **
						 ** I know that the reason you are reading this is because they are asking you to modify the code.(Maybe) **
						 ** 		If so.....I'm sorry for not doing a great job of keeping this code clean.                     **
						 ** 	I trust that you can do a better job. So do as you please. Have fun with your stay at PRICON	  **
						 ***********************************************************************************************************/
?>