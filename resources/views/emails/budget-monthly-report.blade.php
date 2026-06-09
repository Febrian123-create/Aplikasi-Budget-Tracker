<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Helvetica, Arial, sans-serif; background: #f8f9fc; margin: 0; padding: 0; }
.wrap { max-width: 520px; margin: 40px auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,.05); border: 1px solid #e2e8f0; }
.logo { font-size: 24px; font-weight: 800; color: #6366f1; text-align: center; margin-bottom: 24px; }
.badge { display: inline-block; background: #e0e7ff; color: #4338ca; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 999px; margin-bottom: 16px; }
h2 { font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
p { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 16px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
th { text-align: left; font-size: 11px; text-transform: uppercase; color: #94a3b8; padding: 8px 10px; border-bottom: 2px solid #e2e8f0; }
td { font-size: 13px; padding: 10px; border-bottom: 1px solid #f1f5f9; color: #0f172a; }
.over  { display: inline-block; background: #fee2e2; color: #b91c1c; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 999px; }
.under { display: inline-block; background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 999px; }
.footer { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 16px; }
</style>
</head>
<body>
<div class="wrap">
    <div class="logo">BUNREK</div>
    <div><span class="badge">&#128202; Laporan Budget Bulanan</span></div>
    <h2>Laporan Budget {{ $report['monthLabel'] }}</h2>
    <p>Halo, <strong>{{ $user->name }}</strong>! Berikut ringkasan status budget kamu untuk <strong>{{ $report['monthLabel'] }}</strong>. Laporan lengkap dengan riwayat per minggu tersedia di lampiran PDF.</p>

    @if(!$report['isEmpty'])
    <table>
        <thead><tr><th>Kategori</th><th>Terpakai / Budget</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($report['categories'] as $cat)
            <tr>
                <td>{{ $cat['category_name'] }}</td>
                <td>{{ $cat['monthly_spent_formatted'] }} / {{ $cat['monthly_amount_formatted'] }} ({{ $cat['monthly_pct'] }}%)</td>
                <td>
                    @if($cat['isOverBudget'])
                        <span class="over">Overbudget</span>
                    @else
                        <span class="under">Underbudget</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <p style="font-size:13px;">Lihat lampiran PDF untuk rincian alokasi dan realisasi tiap minggu (Senin–Minggu).</p>
    <div class="footer">&copy; 2026 BUNREK. Email ini dikirim otomatis, harap jangan membalas.</div>
</div>
</body>
</html>
