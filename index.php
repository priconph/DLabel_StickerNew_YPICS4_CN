<?php
	
	$dbName="db_dlabel";//MYSQL database name
	$user="root";//MYSQL username
	$pass="r@p1d0nl1n3";//MYSQL password
	$host="localhost";//MYSQL host
	$tblName="tblDLabel";//MYSQL table/view

	$conn=mysql_connect($host,$user,$pass) or die("There was an error in the connection");//MYSQL Connection
	$db_select=mysql_select_db($dbName,$conn) or die("Could not connect to DB");//Selection of database
	//$query=mysql_query("Select * from ".$tblName." where department='1' and CAST(transDate AS DATE)=curdate() order by transNum desc") or die("query error");//MYSQL query
	$query=mysql_query("Select * from ".$tblName." where department='2' and CAST(transDate AS DATE)=curdate() order by transNum desc") or die("query error");//MYSQL query
	$row;
	
	function check_in_range($start_date, $date_from_user)
	{
	  // Convert to timestamp
	  
	  $user_ts = strtotime($date_from_user); //datetdy
	  $start_ts = strtotime($start_date);	//datefromDateTime
	  $end_ts = strtotime("+365 day",$start_ts);

	  // Check that user date is between start & end
	  return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
	}
?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="myCSS.css">
		<link rel="stylesheet" type="text/css" href="PageSlider/jquery.pageslide.css">
		<link rel="stylesheet" href="DatePicker/jquery-ui.css">
		<script src="DatePicker/jquery-1.10.2.js"></script>
		<script src="DatePicker/jquery-ui.js"></script>
		<script>
		
			var product=2;
			
			$(function() {//sets date picker
				$("#datepicker_from").datepicker({
				dateFormat: 'MM dd,yy',
				showButtonPanel: true,
				//minDate: 0,
				maxDate: '+30Y',
				onSelect:function(selectedDate){//sets the min date of the TO date to what is entered in the FROM date
					$('#datepicker_to').datepicker('option', { minDate: new Date(selectedDate)});
					getResultFromDate();
					},
					onClose : function() {
						$(this).trigger( 'change' );
					  }
				}).datepicker("setDate", "0").click(function(e){getResultFromDate();});
			});
			
			function getResultFromDate()//sets the table values based on date
			{
				$('#tableInner').load("getTableData.php?mode=Date&department="+product+"&from="+$("#datepicker_from").val().replace(/\s/g,"%20")+"&to="+$("#datepicker_to").val().replace(/\s/g,"%20"),function(){
						$(".scrollit tr.clickableRow a").pageslide({direction:'left',
				 onClose:function(){close();
						}
			});
					});
			}
			
			$(document).ready(function(){
			console.log(product)
				$("#ponumsearch").keyup(function(){//sets the table values for each key click
					if($("#ponumsearch").val()!=""){
						$('#tableInner').load("getTableData.php?mode=Num&department="+product+"&PONum="+$(this).val(),function(){
							$(".scrollit tr.clickableRow a").pageslide({direction:'left',
				 onClose:function(){close();
						}
			});
						});
					}
					
				})
			});
			
			$(function() {//sets the datepicker TO
				$("#datepicker_to").datepicker({
				dateFormat: 'MM dd,yy',
				showButtonPanel: true,
				minDate: 0,
				maxDate: '+30Y',
				onSelect:function(selectedDate){
					getResultFromDate();
					//('#tableInner').load("getTableData.php?mode=Num&PONum=12345");
					}
				}).datepicker("setDate", "0");
			});
			
			function isNumberKey(evt){
				var charCode = (evt.which) ? evt.which : event.keyCode;
				if (charCode > 31 && (charCode < 48 || charCode > 57))
					return false;
				
				return true;
			}
			
			function prodClick(prod)
			{
				product=prod;
				if(prod==1)
				{
					TS();
				}
				else if(prod==2)
				{
					CN();
				}
				else if(prod==3)
				{
					YF();
				}
			}
			
			function TS()
			{
				$(".new_Sticker").attr("href", "input.php?Mode=New&department=1");
				$('#tableInner').load("getTableData.php?mode=None&department=1",function(){
							$(".scrollit tr.clickableRow a").pageslide({direction:'left',
				 onClose:function(){close();
						}
			});
						});
				$('body').css({"background-color":"#1E824C"});
				$('#prodIden').css({"background":"#65C6BB"});
				$('th').css({"background-color":"#16A085"});
				$('th').css({"color":"#FFFFFF"});
				$('.scrollit tr:hover td').css({"background-color":"#356600"});
				$('div#inputs').css({"background-color":"#03A678"});
				$('#tableBox').css({"background-color":"#16A085"});
				
				$('#prodIden span').html("TS");
				
			}
			
			function CN()
			{
				$(".new_Sticker").attr("href", "input.php?Mode=New&department=2");
				$('#tableInner').load("getTableData.php?mode=None&department=2",function(){
							$(".scrollit tr.clickableRow a").pageslide({direction:'left',
				 onClose:function(){close();
						}
			});
						});
				$('body').css({"background-color":"#336E7B"});
				$('#prodIden').css({"background":"#52B3D9"});
				$('th').css({"background-color":"#2574A9"});
				$('th').css({"color":"#FFFFFF"});
				$('.scrollit tr:hover td').css({"background-color":"#004266"});
				$('div#inputs').css({"background-color":"#1E8BC3"});
				$('#tableBox').css({"background-color":"#2574A9"});
				
				$('#prodIden span').html("CN");
			}
			
			function YF()
			{
				$(".new_Sticker").attr("href", "input.php?Mode=New&department=3");
				$('#tableInner').load("getTableData.php?mode=None&department=3",function(){
							$(".scrollit tr.clickableRow a").pageslide({direction:'left',
				 onClose:function(){close();
						}
			});
						});
				$('body').css({"background-color":"#E67E22"});
				$('#prodIden').css({"background":"#F9BF3B"});
				$('th').css({"background-color":"#F9BF3B"});
				$('th').css({"color":"#FFF"});
				$('.scrollit tr:hover td').css({"background-color":"#636600"});
				$('div#inputs').css({"background-color":"#F4B350"});
				$('#tableBox').css({"background-color":"#F9BF3B"});
				
				$('#prodIden span').html("YF");
			}
		
		
		</script>
	</head>
	
	
	<body>
		<div id="departments">
				<ul id="products">
					<li class="item" id="listHeader"><p>PRODUCTS</p></li>
					<!-- <li class="item" id="TS"><a href="javascript:prodClick(1);" class="TS"><div><span>TS</span></div></a></li> -->
					<li class="item" id="CN"><a href="javascript:prodClick(2);" class="CN"><div><span>CN</span></div></a></li>
					<!-- <li class="item" id="YF"><a href="javascript:prodClick(3);" class="YF"><div><span>YF</span></div></a></li> -->
				</ul>
		</div>
		<div id="wrapper">
			
			
			<div id="prodIden"><span>TS</span></div>
			<div id="inputs">
				<!--<div id="search">PO#:<input type="text" id="ponumsearch" name="search" onkeypress="return isNumberKey(event);" maxlength="10"></div>-->
				<div id="search">PO#:<input type="text" id="ponumsearch" name="search" onkeypress="" maxlength="10"></div>
				<div id="date_today">Date: <?php echo date('F d,Y'); ?></div>
				<div id="tofromDate">From: <input  type="text" name="date" id="datepicker_from" readonly='true'>
									   To: <input  type="text" name="date" id="datepicker_to" readonly='true'></div>
				<div id="newSticker"><a href="input.php?Mode=New&department=1" class="new_Sticker">New Sticker</a></div>
			</div>
			<div id="tableBox">
				<table>
					<tr>
						<th width='150px'>Transaction Date</th>
						<th width='100px'>PO#</th>
						<th width='120px'>Original Qty</th>
						<th width='100px' >Shipped Qty</th>
						<th width='90px'>On Hand</th>
						<th width='100px'>Shipment Date</th>
					</tr>
					<tr>
						<td colspan="6">
							<div class="scrollit">
								<table id="tableInner">
									<?php
										while($row=mysql_fetch_array($query))//sets the value of the table at load
										{
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
											$url='"input.php?Mode=Edit&department=1&PONum='.$PONum.'&Trans='.$transNum.'&SQty='.$SQty.'&ShipDate='.$shipDate.'&MQty='.$MQty.'&DPN='.$DPN.'"';
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
								</table>
							</div>
						</td>
					</tr>
					
				</table>
			</div>
		</div>
		
		<div id="asd"></div>
		<!--<script type="text/javascript" src="PageSlider/lib/jquery-1.7.1.min.js"></script>-->
		<script type="text/javascript" src="PageSlider/jquery.pageslide.js"></script>
		<script>
			$(".scrollit tr.clickableRow a").pageslide(//sets the scrollable slide
			{direction:'left',
				 onClose:function(){close();
						}
			}
			);
			
			function close()//what happens when the pageslider closes
			{
				$("#datepicker_from").datepicker("setDate", "0");
				$("#datepicker_to").datepicker("setDate", "0");
				$('#tableInner').load("getTableData.php?mode=None&department="+product,function(){
						
								$(".scrollit tr.clickableRow a").pageslide({direction:'left',onClose:function(){close();}});
											
							});
			}
			
			$(".new_Sticker").pageslide({direction:'left',
										 onClose:function(){
										 close();
										 }});//when the new sticker is clicked
			
			CN();
		</script>
	</body>
</html>