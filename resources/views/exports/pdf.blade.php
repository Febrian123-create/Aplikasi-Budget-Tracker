<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Transaksi' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #fff;
        }

        .header {
            text-align: center;
            padding: 20px 0 15px;
            border-bottom: 3px solid #1b2838;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1b2838;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .header .subtitle {
            font-size: 11px;
            color: #6c757d;
        }

        .filters-info {
            background: #f0f4f8;
            border: 1px solid #d1dce6;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .filters-info strong {
            color: #1b2838;
        }

        .filters-info span {
            color: #495057;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr {
            background: #1b2838;
        }

        thead th {
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #e9ecef;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody td {
            padding: 8px;
            font-size: 10px;
            color: #343a40;
        }

        .badge-income {
            background: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .badge-expense {
            background: #f8d7da;
            color: #721c24;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }

        .amount {
            text-align: right;
            font-weight: 600;
            white-space: nowrap;
        }

        .summary {
            margin-top: 10px;
            border-top: 3px solid #1b2838;
            padding-top: 15px;
        }

        .summary-table {
            width: 350px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 6px 12px;
            font-size: 11px;
        }

        .summary-table .label {
            font-weight: 600;
            color: #495057;
            text-align: left;
        }

        .summary-table .value {
            text-align: right;
            font-weight: 700;
        }

        .summary-table .income-value {
            color: #28a745;
        }

        .summary-table .expense-value {
            color: #dc3545;
        }

        .summary-table .balance-row {
            border-top: 2px solid #1b2838;
        }

        .summary-table .balance-value {
            color: #1b2838;
            font-size: 13px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #adb5bd;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'Laporan Transaksi' }}</h1>
        <div class="subtitle">
            Tanggal Export: {{ $exportDate }} | Aplikasi Budget Tracker
        </div>
    </div>

    @if(!empty($filters))
        <div class="filters-info">
            <strong>Filter Aktif:</strong>
            <span>{{ implode(' | ', $filters) }}</span>
        </div>
    @endif

    @if($transactions->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 18%;">Kategori</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 30%;">Deskripsi</th>
                    <th style="width: 20%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
                        <td>{{ $transaction->category->category_name ?? '-' }}</td>
                        <td>
                            @if($transaction->transactionType_id == 1)
                                <span class="badge-income">Pemasukan</span>
                            @else
                                <span class="badge-expense">Pengeluaran</span>
                            @endif
                        </td>
                        <td>{{ $transaction->description }}</td>
                        <td class="amount">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td class="label">Total Pemasukan</td>
                    <td class="value income-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Pengeluaran</td>
                    <td class="value expense-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
                <tr class="balance-row">
                    <td class="label">Saldo</td>
                    <td class="value balance-value">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            Tidak ada data transaksi yang ditemukan.
        </div>
    @endif

    <div class="footer">
        Dokumen ini di-generate secara otomatis oleh Aplikasi Budget Tracker &mdash; {{ $exportDate }}
    </div>
</body>
</html>
