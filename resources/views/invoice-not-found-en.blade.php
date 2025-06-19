<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Invoice Not Found</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            direction: ltr;
            font-size: 14px;
            max-width: 650px;
            margin: auto;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        .error-box {
            background: #fff;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header img.logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .error-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 24px;
            color: #dc3545;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .error-details {
            color: #666;
            margin-bottom: 25px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <div class="header">
            <img src="https://i.ibb.co/zH8KXpy6/Group-3.png" alt="App Logo" class="logo">
        </div>

        <div class="error-icon">❌</div>
        <div class="error-message">Invoice Not Found</div>
        <div class="error-details">
            We couldn't find the requested invoice.<br>
            Please check the invoice number and try again.
        </div>

        <a href="/" class="back-button">Return to Homepage</a>

        <div class="footer">
            <p>If you believe this is an error, please contact our support team</p>
            <p>support@glintup.ae | +971 4 000 0000</p>
        </div>
    </div>
</body>
</html> 