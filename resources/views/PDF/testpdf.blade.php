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
    font-weight: bold;
    line-height: 25px;
    }
    /* @page { margin:0px; } */
    </style>
</head>
    <body>
    <div style="width:1050px;   font-family: sans-serif;   border: 3px double;
    padding: 19px; background:url({{env('APP_BASE_URL')}}images/invoice-bg.png); background-size:cover">
<table   style="margin-bottom:10px">
    <tr>
        <!-- <td><img src="{{env('APP_BASE_URL')}}images/infra-logo.png" style="width:100%"/></td> -->
        <td width="33%"><img src="{{env('APP_BASE_URL')}}api/storage/logos/{{$data[0]->logo}}" alt=""   style="max-width: 100%;max-height: 81px;"/></td>
        <td  width="33%"></td>
        <td  width="33%" align="right">
            <h1 style="font-size:20px; margin-bottom:0px"><?php echo @$data[0]->business_name?></h1>
            <p  style="font-size:16px; margin-top:0px">
            <?php echo @$data[0]->address ?>, <?php echo @$data[0]->district_name ?>, <?php echo @$data[0]->StateName ?>, pincode - <?php echo @$data[0]->pincode ?>
            </p>
            <p  style="font-size:14px; margin-top:0px; margin-bottom:10px">
              GST No: FGTYUHJKKJ
            </p>
            <p  style="font-size:14px; margin-top:0px; margin-bottom:10px">
             Recipet: <?php echo @$data[0]->receiptno ?>
            </p>
            <p  style="font-size:14px; margin-top:0px; margin-bottom:10px">
             Date  : <?php echo @$data[0]->paymentdate?>
            </p>
        </td>
         
</tr>
</table>
<div style="margin-bottom:20px; line-height:50px">
  Plot No <span class="lines"  style="min-width:150px;"> 20</span>  Plot Size <span class="lines"  style="min-width:150px;"> 350SQ.YD</span>  
Project name <span class="lines"  style="min-width:450px;"><?php echo @$data[0]->project_name?></span> </span> 
Received wiht thanks from Mr/Mrs. <span class="lines"  style="min-width:450px;"> <?php echo @$data[0]->name?></span> As Sum of Rs. <span class="lines" style="min-width: 210px; "><?php echo @$data[0]->amount?></span> In Words<span class="lines"  style="min-width:960px; "><?php echo @$data[0]->inwores?> rupes only </span> By<span class="lines"  style="min-width: 252px; "><?php echo @$data[0]->payment_type?></span> Towards<span class="lines"  style="min-width: 378px; "><?php echo @$data[0]->type?></span>
</div>
 
<h3 style="    margin: 0;
    text-align: right;
    margin-bottom: 14px;"><?php echo @$data[0]->business_name?></h3>
    <div style="display:block; margin-top:45px">
    <div style="width: 33%; float:left; text-align:center">Collected By</div>
    <div style="width: 33%; float:left; text-align:center">Collected By</div>
    <div style="width: 33%; float:left; text-align:center">Collected By</div>
</div>
</div>


</body>
    </html>
