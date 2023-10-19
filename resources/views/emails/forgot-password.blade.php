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
    border-top: 10px solid #1A237E;
    background: #fff;
    margin: 0px auto;
    border-bottom: 10px solid #1A237E;
    border-left: 2px solid #1A237E;
    border-right: 2px solid #1A237E;
    }
    .page-icon {
        text-align: center;
    }
    .page-title {
        text-align: center;
    }
    .page-title h1{
        color: #1A237E;
    }
    .page-button a {
    background: #1A237E;
    padding: 12px 41px;
    display: inline-block;
    color: #fff;
    border-radius: 20px;
    text-decoration: none;
}
.page-button {
    text-align: center;
}
    </style>
</head>

<body>
    <div class="emailtemplate">
        <div class="page-icon">
            <img src="{{env('APP_BASE_URL')}}images/forgot-password.png"/>
        </div>
        <div class="page-title">
           <h1>FORGOT<br/> Your Password</h1>
           <p>Not to worry, we got you! Let's get you a new password</p>
        </div>
        <div class="page-button">
           <a href="{{$emaildata['veriication_link']}}">Rest Password</a>
        </div>
    </div>

</body>

</html>
