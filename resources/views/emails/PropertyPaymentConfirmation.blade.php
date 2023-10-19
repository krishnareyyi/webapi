<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap 5 Example</title>
    <meta charset="utf-8">

    <style>
        body{
            font-family: sans-serif;
        }
        .success-icon {
            background-color: green;
            mask: url(images/tick-round.svg) no-repeat center / contain;
            -webkit-mask: url(images/tick-round.svg) no-repeat center / contain;
            height: 80px;
            width: 80px;
            margin: 0px auto;
        }
        .email-mail{
                max-width: 400px;
    font-size: 14px;
    background: #f9f9f9;
    border: 1px solid #a3a1a1;
    padding: 15px;
    border-radius: 10px;
        }
        .text-center{
            text-align: center;
        }
        table{
            width: 100%;
            border-collapse: collapse;
        }
        table td,  table th{
            padding: 5px;
        }
        .bg-success{
            background: rgb(25,135,84);
        }
        .text-white{
            color: #fff;
        }
    </style>
</head>

<body>

<div class="email-mail">
                    <div class="row">
                        <div class="bg-light p-3  border">
                            <div class="col-md-12 text-center">
                                <div class="success-icon"></div>
                                <h4 class="text-center mt-3 text-success mt-3">Payment Received</h4>
                                <p>
                                    Hello, {{$personalinfo[0]->cname}}
                                </p>
                                <p>Thank you for your payment of INR {{$request->amount}} on 24 mar 2023 useing {{$request->type}}</p>
                            </div>
                            <div class="col-md-12">
                                <table class="table border table-sm">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" class="bg-success fw-bold text-center text-white">Payment Details</td>

                                        </tr>
                                        <tr>
                                            <td>Payment Amount</td>
                                            <td class="text-end">INR {{$request->amount}}</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Date</td>
                                            <td class="text-end">Mar 22, 2021</td>
                                        </tr>
                                        <tr>
                                            <td>Payment Method</td>
                                            <td class="text-end">{{$request->type}}</td>
                                        </tr>
                                        <tr>
                                            <td>Recepit No</td>
                                            <td class="text-end"><a href="">#256385</a></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="table border table-sm">
                                    <tbody>
                                        <tr>
                                            <td colspan="2" class="bg-success fw-bold text-center text-white">Property Details</td>

                                        </tr>
                                        <tr>
                                            <td>Vencher name</td>
                                            <td class="text-end">{{$personalinfo[0]->project_name}}</td>
                                        </tr>
                                        <tr>
                                            <td>Address</td>
                                            <td class="text-end">HYD</td>
                                        </tr>
                                         <tr>
                                            <td>Area</td>
                                            <td class="text-end">250SQ.YD</td>
                                        </tr>
                                        <tr>
                                            <td>Block</td>
                                            <td class="text-end">{{$personalinfo[0]->block}}</td>
                                        </tr>
                                        <tr>
                                            <td>Line</td>
                                            <td class="text-end">{{$personalinfo[0]->line}}</td>
                                        </tr>
                                        <tr>
                                            <td>Unit</td>
                                            <td class="text-end">{{$personalinfo[0]->unit_no}}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>



</body>

</html>
