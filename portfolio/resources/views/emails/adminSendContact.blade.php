<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Response to Your Inquiry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #444;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center; /* Căn giữa nội dung */
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
        }
        h2 {
            font-size: 22px;
            margin-top: 20px;
        }
        p {
            font-size: 16px;
            text-align: left; /* Căn trái nội dung thư */
        }
        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 style="text-transform: uppercase;"><strong>MyPortfolio</strong></h1>
    <h2 style="text-align: center;">Response to Your Inquiry</h2>

    <p>Dear Customer,</p>

    <p>Thank you for reaching out to us. We appreciate your inquiry and would like to provide you with the following information:</p>

    <p><strong>{{ $data['messageContent'] }}</strong></p>

    <p>If you have any further questions or need assistance, please do not hesitate to contact us.</p>

    <p>Thank you for choosing us!</p>
</div>
</body>
</html>
