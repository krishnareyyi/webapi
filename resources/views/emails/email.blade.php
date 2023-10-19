<html>
    <head>
        
<style>
    table{
        border-collapse: collapse;
        width: 100%;

    }
    th, td{
        padding:6px;
        border-color:#c7c7c7;
        font-size:14px;
    }
    .lines{
        border-bottom: 1px dashed;
    
    padding: 0px 5px;
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    line-height: 25px;
    }
    </style>
</head>
    <body>
    <div style="width:800px;     font-family: sans-serif;">
<table   style="margin-bottom:10px">
    <tr>
        <td><img src="https://www.aakritihousing.com/lp/images/logo.png" width="80px"/></td>
        <td align="center" width="30%" style="font-size:20px">Payment Recept</td>
        <td align="right" width="40%">{{$businessdetails->business_name}},<br/>
    2-25, moin road, korlam, </br/>
somepte, visakhapatnam, ap-530016</td>
</tr>
</table>
<div style="margin-bottom:20px; line-height:50px">
Received from <span class="lines"  style="min-width: 50%;"> {{$personalinfo[0]->cname}}</span> Rs. <span class="lines" style="min-width: 26%; ">{{$request->amount}}</span>  Particular <span class="lines"  style="min-width: 25%; ">{{$request->type}}</span>
</div>
<table border="1" style="margin-bottom:10px">
    <tr>
        <td>Project Name</td>
        <td>Block</td>
        <td>Line</td>
        <td>Unit No</td>
        <td>Area</td>
        <td>Per/sq.ft</td>
</tr>
<tr>
        <td>{{$personalinfo[0]->project_name}}</td>
        <td>{{$personalinfo[0]->block}}</td>
        <td>{{$personalinfo[0]->line}}</td>
        <td>{{$personalinfo[0]->unit_no}}</td>
        <td>250</td>
        <td>3000.00</td>
</tr>
</table>

</div>


</body>
    </html>
