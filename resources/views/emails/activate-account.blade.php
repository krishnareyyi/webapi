<!DOCTYPE html>
<html>

<head>
    <title>Page Title</title>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;800&amp;display=swap"
        rel="stylesheet">
    <style>
    body {
        font-family: 'Mulish', sans-serif;
        /* font-size: 16px; */
    }

    .emailtemplate {
        max-width: 400px;
        padding: 20px;
        border: 2px dashed;
    }
    .emailtemplate a {
        background: #09186a;
        padding: 8px 30px;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        display: inline-flex;
    }
    .emailtemplate  h4{
        margin-top:0px;
    }
    </style>
</head>

<body>
    <div class="emailtemplate">
        <div>
            <h4>Dear {{ $emaildata['name'] }}</h4>
        </div>
        <div>
            <p>Thank you for registering with us. in order to activate your account please click the button below
        </div>
        <div>
            <a href="{{$emaildata['veriication_link']}}">Activate Account</a>
        </div>
    </div>

</body>

</html>