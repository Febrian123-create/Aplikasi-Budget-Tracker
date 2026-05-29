<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Peringatan Budget BUNREK</title>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f8f9fc; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .logo { font-size: 24px; font-weight: 800; color: #6366f1; letter-spacing: 0.5px; text-align: center; margin-bottom: 24px; }
        .badge { display: inline-block; background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 16px; text-transform: uppercase; }
        .title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
        .text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; }
        .info-box { background: #fff7ed; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #f59e0b; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .info-label { color: #64748b; font-weight: 500; }
        .info-value { color: #0f172a; font-weight: 700; }
        .progress-bar-bg { background: #e2e8f0; border-radius: 999px; height: 10px; margin: 12px 0; }
        .progress-bar-fill { height: 10px; border-radius: 999px; background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">BUNREK</div>

        @php
            $spentFmt = 'Rp ' . number_format($spent, 0, ',', '.');
            $limitFmt = 'Rp ' . number_format($limit, 0, ',', '.');
            $pct = min(100, round($usagePercent));
        @endphp

        <div>
            <span class="badge">⚠️ Peringatan Budget</span>
        </div>
        <h2 class="title">Budget {{ $categoryName }} hampir habis!</h2>
        <p class="text">
            Halo, {{ $user->name }}! Pengeluaran kamu untuk kategori <strong>{{ $categoryName }}</strong>
            sudah mencapai <strong>{{ $usagePercent }}%</strong> dari batas budget yang kamu tetapkan.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Kategori</span>
                <span class="info-value">{{ $categoryName }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sudah Dipakai</span>
                <span class="info-value" style="color: #ef4444;">{{ $spentFmt }}</span>
            </div>
            <div class="info-row" style="margin-bottom: 4px;">
                <span class="info-label">Batas Budget</span>
                <span class="info-value">{{ $limitFmt }}</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: {{ $pct }}%;"></div>
            </div>
            <div style="text-align: right; font-size: 12px; color: #ef4444; font-weight: 700;">{{ $pct }}% terpakai</div>
        </div>

        <p class="text" style="font-size: 13px;">
            Perhatikan pengeluaranmu agar tidak melebihi batas. Kamu bisa cek histori transaksi di aplikasi BUNREK.
        </p>

        <div class="footer">
            &copy; 2026 BUNREK. All rights reserved.<br>
            Email ini dikirim otomatis, harap jangan membalas.
        </div>
    </div>
</body>
</html>
