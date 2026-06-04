<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Transaction;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WishlistController extends Controller
{
    /**
     * index: Tampilkan semua wishlist milik authenticated user
     * Urutkan by status (aktif dulu) lalu by created_at desc
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->orderByRaw("FIELD(status, 'aktif', 'tercapai', 'dibatalkan')")
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung summary data untuk active wishlists
        $activeWishlists = $wishlists->where('status', 'aktif');
        $totalActiveWishlists = $activeWishlists->count();
        $totalRemainingNeeded = $activeWishlists->sum(function ($wishlist) {
            return max(0, $wishlist->target_harga - $wishlist->terkumpul);
        });

        return view('wishlist.index', compact('wishlists', 'totalActiveWishlists', 'totalRemainingNeeded'));
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
     * alokasi: Tambah dana ke wishlist
     * Jika terkumpul >= target_harga, otomatis ubah status menjadi tercapai
     * Buat transaksi Pengeluaran baru
     */
    public function alokasi(Request $request, Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1000',
        ], [
            'jumlah.min' => 'Jumlah alokasi minimum Rp 1.000',
        ]);

        // Tambahkan ke terkumpul
        $wishlist->terkumpul += $validated['jumlah'];

        // Jika terkumpul >= target_harga, ubah status menjadi tercapai
        if ($wishlist->terkumpul >= $wishlist->target_harga) {
            $wishlist->status = 'tercapai';
        }

        $wishlist->save();

        // Buat transaksi Pengeluaran baru
        $expenseTypeId = TransactionType::where('name', 'expense')->value('transactionType_id');

        Transaction::create([
            'user_id' => Auth::id(),
            'category_id' => 11,
            'transactionType_id' => $expenseTypeId,
            'total_amount' => $validated['jumlah'],
            'transaction_date' => Carbon::today()->toDateTimeString(),
            'description' => "Alokasi Wishlist: {$wishlist->nama}",
        ]);

        return redirect()->route('wishlist.index')->with('success', 'Dana berhasil dialokasikan!');
    }

    /**
     * destroy: Ubah status menjadi dibatalkan (soft delete)
     * Jangan hapus permanent
     * Pastikan ownership check
     */
    public function destroy(Wishlist $wishlist)
    {
        // Ownership check
        if ($wishlist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $wishlist->update(['status' => 'dibatalkan']);

        return redirect()->route('wishlist.index')->with('success', 'Wishlist berhasil dibatalkan!');
    }
}
