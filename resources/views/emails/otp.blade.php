<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi BUNREK</title>
    <style>
        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            background-color: #f8f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #6366f1;
            letter-spacing: 0.5px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-box {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: 6px;
            margin-bottom: 24px;
            border: 1px dashed #cbd5e1;
        }
        .footer {
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            margin-top: 32px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">BUNREK</div>
        </div>
        <h2 class="title">Halo, {{ $userName }}!</h2>
        <p class="text">
            Terima kasih telah mendaftar di BUNREK. Gunakan kode verifikasi di bawah ini untuk mengaktifkan akun Anda:
        </p>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <p class="text" style="font-size: 13px; color: #64748b;">
            Kode ini berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapapun demi keamanan akun Anda.
        </p>
        <p class="text">
            Jika Anda tidak merasa mendaftar di aplikasi kami, abaikan email ini.
        </p>
        <div class="footer">
            &copy; 2026 BUNREK. All rights reserved.
        </div>
    </div>
</body>
</html>
