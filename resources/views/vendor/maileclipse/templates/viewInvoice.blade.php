<!doctype html>
<html>
   <head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
	<title>Invoice of {{ $invoice->invoice_number }}</title>
	
	<style type="text/css">
		body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Arial"; font-size:x-small }
		td {
			padding: 1px 5px;
		}
		.bl {
			border-left: 1px solid #000;
		}
		.br {
			border-right: 1px solid #000;
		}
		.bt {
			border-top: 1px solid #000;
		}
		.bb {
			border-bottom: 1px solid #000;
		}
		.bg {
			background: #808080
		}
		.vt {
			vertical-align: top;
		}
		.vm {
			vertical-align: middle;
		}
		.vb {
			vertical-align: bottom;
		}
	</style>
	
</head>

<body>
<table cellspacing="0" border="0" align="center" style="max-width: 600px" cellspacing="0" cellpadding="0">
	<tr>
		<td class="bl br bb bt vm" style="padding:1px 6px" colspan=3 rowspan=3 height="89" align="left"><span style="font-weight: bold; font-size: 20px">LUXURY UNLIMITED</span></font></td>
		<td class="br bb bt vm" colspan=3 rowspan=3 height="89" align="left"><span style="float: right; font-size: 12px; margin-top: 8px">105,5 EA, DAFZA DUBAI, UAE</span></font></td>
		</tr>
	<tr>
		</tr>
	<tr>
		</tr>
	<tr>
		<td class="bl br bb bt bg vm" colspan=3 height="28" align="left"><font color="#FFFFFF">COMMERCIAL INVOICE</font></td>
		<td class="br bb bt bg vm" colspan=3 height="28" align="left"></td>
		</tr>
	<tr>
		<td class="bl br bg bb vm" colspan=3 height="30" align="left"><font color="#FFFFFF">Shipper/Exporter of Record</font></td>
		<td class="br bb bg vm" colspan=3 align="left"><font color="#FFFFFF">SHIPMENTORDER</font></td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left">Luxury Unlimited </td>
		<td class="br vb" colspan="3" align="left">INVOICE#: {{ $invoice->invoice_number }}</td>
	</tr>
	<tr>
		<td class="bl br" height="17" colspan="3" align="left">Address: 105,5 EA, DAFZA DUBAI, UAE</td>
		<td class="br" colspan="3" align="left">{{ $invoice->invoice_date }}</td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left">Order IDs: @foreach($orders as $order) <p>{{$order->order_id}}</p> @endforeach </td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left"><br></td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left">Numbers of parcels: 0</td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"> </td>
		<td class="br vb" colspan="3" align="left">Total actual weight 0.41 kg</td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left"><br></td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"> </td>
		<td class="br vb" colspan="3" align="left"><br></td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left">Currency of sale: {{$orders[0]->currency}} </td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left">Incoterms : DDP</td>
	</tr>
	<tr>
		<td class="bl br vb" height="17" colspan="3" align="left"></td>
		<td class="br vb" colspan="3" align="left"><br></td>
	</tr>
	<tr>
		<td class="bl bg vm" height="30" colspan="3" align="left"><font color="#FFFFFF">SHIP TOIGONSIGNEE</font></td>
		<td class="bg vm bl br bb bt" align="left" colspan="3"><font color="#FFFFFF">SOLD TO PARTY</font></td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left">@if($customer) {{ $customer->name }} @endif</td>
		<td class="bl br bt vb" colspan="3" align="left">@if($customer) {{ $customer->name }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left">@if($customer) {{ $customer->address }} @endif</td>
		<td class="bl br vb" colspan="3" align="left">@if($customer) {{ $customer->address }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left">@if($customer) {{ $customer->city }} @endif</td>
		<td class="bl br vb" colspan="3" align="left">@if($customer) {{ $customer->city }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left">@if($customer) {{ $customer->country }} @endif</td>
		<td class="bl br vb" colspan="3" align="left">@if($customer) {{ $customer->country }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left">@if($customer) {{ $customer->pincode }} @endif </td>
		<td class="bl br vb" colspan="3" align="left">@if($customer) {{ $customer->pincode }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left" sdval="97142943242" sdnum="1033;">@if($customer) {{ $customer->phone }} @endif</td>
		<td class="bl br vb" colspan="3" align="left">@if($customer) {{ $customer->phone }} @endif</td>
	</tr>
	<tr>
		<td class="bl vb" height="17" colspan="3" align="left"><u><font color="#0000FF"><a href="mailto:YOGESHMORDANI@ICLOUD.COM">@if($customer) {{ $customer->email }} @endif</a></font></u></td>
		<td class="bl br vb" colspan="3" align="left"><u><font color="#0000FF"><a href="mailto:YOGESHMORDANI@ICLOUD.COM">@if($customer) {{ $customer->email }} @endif</a></font></u></td>
	</tr>
	<tr>
		<td class="bl vb" colspan="3" height="17" align="left"><br></td>
		<td class="bl br bb vb" colspan="3" align="left"><br></td>
	</tr>
	<tr>
		<td class="bl bb bt vm" height="31" align="left" bgcolor="#808080"><font color="#FFFFFF">DESCRIPTION </font></td>
		<td class="bl bb bt bg vm" align="left"><font color="#FFFFFF">HS Code</font></td>
		<td class="bl bb bt bg vm" align="left"><font color="#FFFFFF">Country of orgin</font></td>
		<td class="bl bb bg vm" align="left"><font color="#FFFFFF">Units</font></td>
		<td class="bl bb bg vm" align="left"><font color="#FFFFFF">UNIT VALUE</font></td>
		<td class="bl br bb bg vm" align="left"><font color="#FFFFFF">TOTOAL VALUE</font></td>
	</tr>
	{!! $orderItems !!}
	<!-- <tr>
		<td class="bl br vm" height="34" align="left">000000060108527008-<br>Unisex cotton <br>rich Jumper</td>
		<td class="vm" align="left" sdval="611020" sdnum="1033;">611020</td>
		<td class="bl vm" align="left">BANGLADESH</td>
		<td class="bl vm" align="left" sdval="1" sdnum="1033;">1</td>
		<td class="bl vm" align="left">AED 66.04</td>
		<td class="bl br vm" align="left">AED 66.04</td>
	</tr> -->
	<tr>
		<td class="bl br bt" colspan=6 rowspan=2 height="34" align="center"><br></td>
		</tr>
	<tr>
		</tr>
	<tr>
		<td class="bl br" colspan=6 height="17" align="left">DO NOT DIFFERENT COMMODITIES - UNDER A SINGAL HS CODE . USE HS COMMODITY CODES AS PROVIDED.</td>
		</tr>
	<tr>
		<td class="bl bg vm br" colspan="3" height="29" align="left"><font color="#FFFFFF"><b>Notes on import duty &amp; taxes due</b></font></td>
		<td class="bg vm" colspan="3" align="left"><font color="#fff"><b>Totals</b></font></td>
	</tr>
	<tr>
		<td class="bl br vb bb" height="17" colspan="3" align="left">VALUE FOR CUSTOMERS</td>
		<td class="br vb bb" colspan="3" align="left">
			<table cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>Total cost foods( FOB) Shipping</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>& handing insurance charges</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>Discount 50%</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>Final Price</td>
					<td align="right">{{ $orderTotal }}</td>
				</tr>
				<tr>
					<td>Total (CIF)</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>Import Duty & taxes due</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>Import Paid for the order</td>
					<td align="right">-</td>
				</tr>
				<tr>
					<td>CIF</td>
					<td align="right">-</td>
				</tr>
			</table>
		</td>
	</tr>
	
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
</body>

</html>
