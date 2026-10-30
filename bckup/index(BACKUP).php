<!-- http://www.linuxjournal.com/article/6636?page=0,0 -->
<html>
	<head>
		<title>DLabel Sticker</title>
		<style type="text/css">
			body{
				/*background-color:#ED3939;*/
				background-color:#008714;
				color:#ffffff;
				font-family:"Franklin Gothic Demi";
			}
			#wrapper{
				margin: 0 auto;
				width: 800px;
				border-color:#000000;
			}
			table{
				align:center;
				margin:0 auto;
				margin-bottom: 10px;
				border-color:#000000;
				border-collapse: collapse;
				border-spacing: 0;
				font-size:20px;
			}
			input[type="text"],select{
				width:200px;
				border:1px solid #ffffff;
				font-size:20px;
			}
			input[type="submit"]{
				font-family:"Franklin Gothic Demi";
				background-color:#ffffff;
				border:1px solid #ffffff;
				font-size:20px;
			}
		</style>
		
		<link rel="stylesheet" href="DatePicker/jquery-ui.css">
		<script src="DatePicker/jquery-1.10.2.js"></script>
		<script src="DatePicker/jquery-ui.js"></script>
		<script>
		$(function() {
			$( "#datepicker" ).datepicker({
			dateFormat: 'ddmmyy',
			showButtonPanel: true,
			minDate: 0,
			maxDate: '+30Y'}).datepicker("setDate", "0");
		});
		
		function isNumberKey(evt)
		{
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57))
				return false;

			return true;
		}
		
		function validateInput()
		{
			var txtPoNum=document.getElementById("txtPoNum").value;
			var txtMaxQty=document.getElementById("txtMaxQty").value;
			 if(txtPoNum==""||txtMaxQty==""){
				alert("Please fill in all fields correctly.");
				return false;
			 }
			
			return true;
		}
		
		
		
		</script>
	</head>


	<body>
		<div id="wrapper">
			<form action="PDFViewer.php" method="get" target="_blank">
				<table>
					<tr>
						<td align="right">
							Product Number:
						</td>
						<td>
							<input type="text"  name="txtPoNum" id="txtPoNum" onkeypress="return isNumberKey(event);" maxlength="10">
						</td>
					</tr>
					<tr>
						<td align="right">
							Maximum Qty:
						</td>
						<td>
							<input type="text"  name="txtMaxQty" id="txtMaxQty"  onkeypress="return isNumberKey(event);">
						</td>
					</tr>
					<tr>
						<td align="right">Delivery place name:</td>
						<td><select name="selDeliveryPlaceName"><option value="W021">W021</option><option value="W010">W010</option></select></td>
					</tr>
					<tr>
						<td align="right">
							Date:
						</td>
						<td>
							<input type="text" name="date" id="datepicker" readonly='true'>
						</td>
					</tr>
					<tr>
						<td></td>
						<td align="left">
							<input type="submit" value="Search" name="btnSearch" onclick="return validateInput();">
						</td>
						
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>