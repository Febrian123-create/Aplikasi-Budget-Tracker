<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pengingat Transaksi Rutin BUNREK</title>
    <style>
        body { font-family: 'Inter', Helvetica, Arial, sans-serif; background-color: #f8f9fc; margin: 0; padding: 0; }
        .container { max-width: 500px; margin: 40px auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; }
        .logo { font-size: 24px; font-weight: 800; color: #6366f1; letter-spacing: 0.5px; text-align: center; margin-bottom: 24px; }
        .badge { display: inline-block; background: #ede9fe; color: #6d28d9; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px; }
        .title { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
        .text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 20px; }
        .info-box { background: #f1f5f9; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #6366f1; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .info-label { color: #64748b; font-weight: 500; }
        .info-value { color: #0f172a; font-weight: 700; }
        .amount-value { color: #ef4444; }
        .amount-income { color: #10b981; }
        .message-box { background: #fefce8; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 14px; color: #92400e; line-height: 1.5; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">BUNREK</div>

        @php
            $label = $daysBefore === 0 ? 'Hari Ini' : "H-{$daysBefore}";
            $isIncome = $recurring->amount_type === 'pemasukan';
            $amountFmt = 'Rp ' . number_format($recurring->amount, 0, ',', '.');
            $dateStr = $recurring->next_run_date->format('d M Y');
        @endphp

        <div>
            <span class="badge">Pengingat {{ $label }}</span>
        </div>
        <h2 class="title">Transaksi rutin akan segera dieksekusi</h2>
        <p class="text">
            Halo! Berikut adalah pengingat transaksi rutin kamu di BUNREK.
        </p>

        @if($customMessage)
            <div class="message-box">
                💬 {{ $customMessage }}
            </div>
        @endif

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Transaksi</span>
                <span class="info-value">{{ $recurring->description }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nominal</span>
                <span class="info-value {{ $isIncome ? 'amount-income' : 'amount-value' }}">{{ $amountFmt }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipe</span>
                <span class="info-value">{{ $isIncome ? 'Pemasukan' : 'Pengeluaran' }}</span>
            </div>
            <div class="info-row" style="margin-bottom: 0;">
                <span class="info-label">Tanggal Eksekusi</span>
                <span class="info-value">{{ $dateStr }}</span>
            </div>
        </div>

        <p class="text" style="font-size: 13px;">
            Transaksi ini akan dicatat otomatis oleh sistem BUNREK. Kamu tidak perlu melakukan apapun.
        </p>

        <div class="footer">
            &copy; 2026 BUNREK. All rights reserved.<br>
            Email ini dikirim otomatis, harap jangan membalas.
        </div>
    </div>
</body>
</html>
