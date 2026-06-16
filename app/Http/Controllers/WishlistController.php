<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WishlistController extends Controller
{
    /**
     * index: Tampilkan semua wishlist milik authenticated user
     * Urutkan by deadline terdekat (asc), yang deadline null ditaruh paling bawah
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->orderByRaw('CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC')
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung summary data untuk active wishlists
        $activeWishlists = $wishlists->where('status', 'aktif');
        $totalActiveWishlists = $activeWishlists->count();
        $totalRemainingNeeded = $activeWishlists->sum(function ($wishlist) {
            return max(0, $wishlist->target_harga - $wishlist->allocated_amount);
        });

        $totalAllocated = $wishlists->whereIn('status', ['aktif', 'tercapai'])->sum('allocated_amount');
        $currentBalance = $this->getCurrentBalance(Auth::id());
        $availableBalance = max(0, $currentBalance - $totalAllocated);

        return view('wishlist.index', compact(
            'wishlists',
            'totalActiveWishlists',
            'totalRemainingNeeded',
            'totalAllocated',
            'availableBalance'
        ));
    }

    private function getCurrentBalance(int $userId): float
    {
        $incomeTypeId = TransactionType::where('name', 'Income')->value('transactionType_id');
        $expenseTypeId = TransactionType::where('name', 'Expense')->value('transactionType_id');

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('transactionType_id', $incomeTypeId)
            ->sum('total_amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('transactionType_id', $expenseTypeId)
            ->sum('total_amount');

        return $totalIncome - $totalExpense;
    }

    /**
     * store: Validasi dan simpan wishlist baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'target_harga' => 'required|integer|min:1000',
            'deadline' => 'nullable|date|after_or_equal:today',
            'catatan' => 'nullable|string',
        ], [
            'target_harga.min' => 'Target harga minimum Rp 1.000',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini',
        ]);

        Wishlist::create([
            'user_id' => Auth::id(),
            'nama' => $validated['nama'],
            'target_harga' => $validated['target_harga'],
            'allocated_amount' => 0,
            'deadline' => $validated['deadline'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'aktif',
            'terkumpul' => 0,
        ]);

        return redirect()->route('wishlist.index')->with('success', 'Wishlist berhasil dibuat!');
    }

    /**
     * update: Validasi dan update wishlist
     * Pastikan ownership check
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'target_harga' => 'required|integer|min:1000',
            'deadline' => 'nullable|date|after_or_equal:today',
            'catatan' => 'nullable|string',
        ], [
            'target_harga.min' => 'Target harga minimum Rp 1.000',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini',
        ]);

        $wishlist->update([
            'nama' => $validated['nama'],
            'target_harga' => $validated['target_harga'],
            'deadline' => $validated['deadline'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('wishlist.index')->with('success', 'Wishlist berhasil diperbarui!');
    }

    /**
     * alokasi: Tambah dana ke wishlist sebagai alokasi reservasi
     * Jika allocated_amount >= target_harga, otomatis ubah status menjadi tercapai
     * Tidak membuat transaksi pengeluaran saat alokasi.
     */
    public function alokasi(Request $request, Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $maxAlokasi = (int) max(0, floor($wishlist->target_harga - $wishlist->allocated_amount));

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|integer|min:1000',
        ], [
            'jumlah.required' => 'Nominal alokasi wajib diisi.',
            'jumlah.integer' => 'Nominal alokasi harus berupa angka bulat tanpa koma.',
            'jumlah.min' => 'Jumlah alokasi minimum Rp 1.000',
        ]);

        if ($validator->fails()) {
            return redirect()->route('wishlist.index')
                ->withErrors($validator)
                ->withInput()
                ->with('allocationError', $validator->errors()->first('jumlah'));
        }

        $validated = $validator->validated();
        $jumlah = $validated['jumlah'];

        if ($jumlah > $maxAlokasi) {
            return redirect()->route('wishlist.index')
                ->withErrors(['jumlah' => 'Nominal melebihi sisa target. Maksimal yang bisa kamu alokasikan adalah Rp ' . number_format($maxAlokasi, 0, ',', '.') . '.'])
                ->withInput()
                ->with('allocationError', 'Nominal alokasi melebihi sisa target.');
        }

        $wishlist->allocated_amount += $jumlah;
        $wishlist->terkumpul = $wishlist->allocated_amount;

        if ($wishlist->allocated_amount >= $wishlist->target_harga) {
            $wishlist->status = 'tercapai';
        }

        $wishlist->save();

        return redirect()->route('wishlist.index')->with('success', 'Dana berhasil dialokasikan!');
    }

    /**
     * destroy: Ubah status menjadi dibatalkan
     * Batalkan alokasi dana dan kembalikan alokasi tersebut ke saldo utama.
     */
    public function destroy(Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $wishlist->update([
            'allocated_amount' => 0,
            'terkumpul' => 0,
            'status' => 'dibatalkan',
        ]);

        return redirect()->route('wishlist.index')->with('success', 'Wishlist berhasil dibatalkan!');
    }

    /**
     * konfirmasiPembelian: Ubah status wishlist menjadi dibeli dan catat transaksi pembelian sesungguhnya.
     */
    public function konfirmasiPembelian(Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($wishlist->status !== 'tercapai') {
            return redirect()->route('wishlist.index')->with('error', 'Hanya wishlist yang sudah tercapai yang dapat dikonfirmasi pembeliannya.');
        }

        $expenseTypeId = TransactionType::where('name', 'Expense')->value('transactionType_id');

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => 11,
            'transactionType_id' => $expenseTypeId,
            'total_amount' => $wishlist->target_harga,
            'transaction_date' => Carbon::today()->toDateString(),
            'description' => "Pembelian Wishlist: {$wishlist->nama}",
        ]);

        $wishlist->update(['status' => 'dibeli']);

        return redirect()->route('wishlist.index')->with('success', 'Pembelian wishlist berhasil dikonfirmasi!');
    }
}
