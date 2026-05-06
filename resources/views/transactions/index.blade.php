<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Tracker - Manajemen Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }
        .glass {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .income-card {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        .expense-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.05) 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body class="min-h-screen py-10 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-bold mb-2 gradient-text">Budget Tracker</h1>
            <p class="text-slate-400">Atur keuangan harian Anda dengan mudah</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="glass p-6 rounded-3xl shadow-xl">
                <p class="text-sm text-slate-400 mb-1">Total Saldo</p>
                <h3 class="text-2xl font-bold {{ $balance >= 0 ? 'text-blue-400' : 'text-red-400' }}">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </h3>
            </div>
            <div class="income-card p-6 rounded-3xl shadow-xl">
                <p class="text-sm text-green-400 mb-1">Pemasukan</p>
                <h3 class="text-2xl font-bold text-green-400">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </h3>
            </div>
            <div class="expense-card p-6 rounded-3xl shadow-xl">
                <p class="text-sm text-red-400 mb-1">Pengeluaran</p>
                <h3 class="text-2xl font-bold text-red-400">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="glass p-8 rounded-3xl sticky top-10">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Transaksi
                    </h2>
                    
                    @if(session('success'))
                        <div class="bg-green-500/20 text-green-400 p-4 rounded-2xl mb-6 text-sm border border-green-500/30">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm border border-red-500/30">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Tipe</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="income" class="hidden peer" checked>
                                    <div class="text-center p-3 rounded-2xl border border-slate-700 peer-checked:bg-green-500/20 peer-checked:border-green-500/50 peer-checked:text-green-400 transition-all hover:bg-slate-800">
                                        Pemasukan
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="expense" class="hidden peer">
                                    <div class="text-center p-3 rounded-2xl border border-slate-700 peer-checked:bg-red-500/20 peer-checked:border-red-500/50 peer-checked:text-red-400 transition-all hover:bg-slate-800">
                                        Pengeluaran
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Jumlah (Rp)</label>
                            <input type="number" name="amount" required placeholder="0"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm text-slate-400 mb-2">Deskripsi</label>
                            <textarea name="description" required placeholder="Contoh: Gaji Bulanan, Makan Siang..."
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-2xl p-4 focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all h-24"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-500/25 transition-all transform hover:-translate-y-1">
                            Simpan Transaksi
                        </button>
                    </form>
                </div>
            </div>

            <!-- List Section -->
            <div class="lg:col-span-3">
                <div class="glass rounded-3xl overflow-hidden">
                    <div class="p-8 border-b border-slate-700">
                        <h2 class="text-xl font-bold">Riwayat Transaksi</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-slate-400 text-sm uppercase tracking-wider">
                                    <th class="px-8 py-4 font-medium">Tanggal</th>
                                    <th class="px-8 py-4 font-medium">Deskripsi</th>
                                    <th class="px-8 py-4 font-medium text-right">Jumlah</th>
                                    <th class="px-8 py-4 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @forelse($transactions as $t)
                                <tr class="hover:bg-slate-800/30 transition-colors group">
                                    <td class="px-8 py-5 text-sm text-slate-400">
                                        {{ \Carbon\Carbon::parse($t->date)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="font-medium text-slate-200">{{ $t->description }}</p>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full {{ $t->type == 'income' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                                            {{ $t->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right font-bold {{ $t->type == 'income' ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $t->type == 'income' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <form action="{{ route('transactions.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-500 hover:text-red-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center text-slate-500">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Belum ada transaksi
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
