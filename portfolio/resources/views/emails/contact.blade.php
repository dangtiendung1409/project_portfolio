<!DOCTYPE html>
<html>
<head>
    <title>New Contact Message</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }
        .email-header {
            text-align: center;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            font-size: 22px;
            font-weight: bold;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .email-content {
            padding: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .info-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
        }
        .info-table td.label {
            font-weight: bold;
            color: #007bff;
            width: 30%;
        }
        .email-message {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            color: #444;
            margin-top: 15px;
        }
        .email-footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            margin-top: 15px;
        }
        .footer-logo {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
    </style>
</head>
<body>

<div class="email-container">
    <!-- Tiêu đề -->
    <div class="email-header">
        📩 New Contact Message
    </div>

    <!-- Nội dung chính -->
    <div class="email-content">
        <table class="info-table">
            <tr>
                <td class="label">👤 Name:</td>
                <td>{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td class="label">📧 Email:</td>
                <td>{{ $data['email'] }}</td>
            </tr>
            <tr>
                <td class="label">📌 Subject:</td>
                <td>{{ $data['subject'] }}</td>
            </tr>
        </table>

        <!-- Tin nhắn -->
        <div class="email-message">
            <strong>📝 Message:</strong><br>
            {{ $data['message'] }}
        </div>
    </div>

    <!-- Chân trang -->
    <div class="email-footer">
        📬 This is an automated message. Please do not reply.<br>
        <span class="footer-logo">🌟 YourCompany</span>
    </div>
</div>

</body>
</html>
