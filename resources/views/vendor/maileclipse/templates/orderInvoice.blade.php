<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  font-size: 12px;
}

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 5px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers th {
  padding-top: 5px;
  padding-bottom: 5px;
  text-align: left;
  background-color: #808080;
  color: white;
}
</style>
</head>
<body>
<table id="customers">
<tbody>
<tr>
<th colspan="3">COMMERCIAL INVOICE</th>
</tr>
<tr>
<th>Shipper/Exporter of Record</th>
<th>&nbsp;</th>
<th>SHIPMENT ORDER</th>
</tr>
<tr>
<td>LUXURY UNLIMITED</td>
<td style="width: 30%;" rowspan="8">&nbsp;</td>
<td>INVOICE#: 00000000000004200065</td>
</tr>
<tr>
<td>Address:105,5EA,DAFZA DUBAI,UAE</td>
<td>Date: 2020-09-30</td>
</tr>
<tr>
<td>UNITED ARAB EMIRATES</td>
<td>Order ID: GE457784290GB</td>
</tr>
<tr>
<td>Email: info@theluxuryunlimited.com</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>Numbers of parcels: 1</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>Total actual weight 0.41 kg</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>The currency of sale: USD</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>Incoterms: DDP</td>
</tr>
<tr>
<th>SHIP TOIGONSIGNEE</th>
<th>&nbsp;</th>
<th>SOLD TO PARTY</th>
</tr>
<tr>
<td>Client name</td>
<td style="width: 30%;" rowspan="8">&nbsp;</td>
<td>Importer type Private Importer</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Client name</td>
<td>@if($buyerDetails) {{ $buyerDetails->name }} @endif</td>
</tr>
<tr>
<td>name</td>
<td>BREACH CANDY APARTMENTS</td>
</tr>
<tr>
<td>name</td>
<td>OPP PREMSONS WARDEN ROAD</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>MUMBAI</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>INDIA</td>
</tr>
</tbody>
</table>
<table id="customers">
<tbody>
<tr>
<th>DESCRIPTION</th>
<th>Country of origin</th>
<th>Units</th>
<th>UNIT V</th>
<th>TOTAL VALUE</th>
</tr>
<tr>
<td>BAG</td>
<td>ITALY</td>
<td>1</td>
<td>USD</td>
<td>USD 150.00</td>
</tr>
<tr>
<td colspan="5">DO NOT CLEAR DIFFERENT COMMODITIES - UNDER A SINGAL HS CODE. USE HS COMMODITY CODES AS PROVIDED.</td>
</tr>
<tr>
<th colspan="2">DESCRIPTION</th>
<th colspan="2">TOTALS</th>
<th>&nbsp;</th>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="2">The total cost of foods (FOB) Shipping &amp; handling Insurance charges</td>
<td style="text-align: right;">
<p>USD 150.00</p>
<p>USD 0.00</p>
<p>USD 0.00</p>
</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="2">Total (CIF)</td>
<td style="text-align: right;">
<p>USD 150.00</p>
<p>USD 0.00</p>
</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="2">Import Duty &amp; taxes due</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="2">Total Paid for the order</td>
<td style="text-align: right;">USD 150.00</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
<td colspan="2">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan="2">VALUE FOR CUSTOMERS:</td>
<td colspan="2">CIF</td>
<td style="text-align: right;">USD 150.00</td>
</tr>
</tbody>
</table>
</body>
</html>