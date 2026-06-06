<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveEase</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1E3A5F; padding: 30px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 26px; letter-spacing: 1px; }
        .header p { color: #A9C4E4; font-size: 13px; margin-top: 4px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; margin-bottom: 16px; }
        .message { font-size: 15px; line-height: 1.7; color: #444; margin-bottom: 24px; }
        .card { background: #F0F6FC; border-left: 4px solid #2E75B6; border-radius: 6px; padding: 20px 24px; margin-bottom: 24px; }
        .card-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #dde8f3; font-size: 14px; }
        .card-row:last-child { border-bottom: none; }
        .card-row .label { color: #666; }
        .card-row .value { font-weight: bold; color: #1E3A5F; }
        .btn { display: inline-block; background: #2E75B6; color: #fff !important; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-size: 15px; font-weight: bold; margin-bottom: 24px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #E2F0D9; color: #1E7145; }
        .badge-warning { background: #FCE4D6; color: #C55A11; }
        .badge-info    { background: #D5E8F0; color: #2E75B6; }
        .footer { background: #F5F5F5; padding: 20px 40px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
        .footer a { color: #2E75B6; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🚗 DriveEase</h1>
        <p>Sistem Rental Mobil Terpercaya</p>
    </div>
    <div class="body">
        @yield('content')
    </div>
    <div class="footer">
        <p>Email ini dikirim otomatis oleh sistem DriveEase.</p>
        <p style="margin-top:6px;">Jangan balas email ini. Hubungi kami via <a href="{{ config('app.url') }}/chat">Live Chat</a>.</p>
        <p style="margin-top:10px;">© {{ date('Y') }} DriveEase. All rights reserved.</p>
    </div>
</div>
</body>
</html>