<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wishlist;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class WishlistSavingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_wishlist_allocation_reduces_balance_and_creates_expense_transaction()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Liburan ke Bali',
            'target_harga' => 5000000,
            'status' => 'aktif',
            'terkumpul' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.alokasi', $wishlist->id), [
            'jumlah' => 1000000,
        ]);

        $response->assertRedirect();
        
        $wishlist->refresh();
        $this->assertEquals(1000000, $wishlist->terkumpul);

        // Check transaction created
        $transaction = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(11, $transaction->category_id); // WISHLIST category
        $this->assertEquals(2, $transaction->transactionType_id); // Expense type
        $this->assertEquals(1000000, $transaction->total_amount);
        $this->assertEquals('Saving Wishlist: Liburan ke Bali', $transaction->description);
    }

    public function test_wishlist_allocation_cannot_exceed_remaining_target()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Sepeda Lipat',
            'target_harga' => 2000000,
            'status' => 'aktif',
            'terkumpul' => 1500000,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.alokasi', $wishlist->id), [
            'jumlah' => 600000, // Exceeds by 100k
        ]);

        $response->assertSessionHasErrors(['jumlah']);
        $wishlist->refresh();
        $this->assertEquals(1500000, $wishlist->terkumpul);
    }

    public function test_wishlist_cancellation_adds_income_transaction_and_resets_terkumpul()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Smart TV',
            'target_harga' => 3000000,
            'status' => 'aktif',
            'terkumpul' => 1000000,
        ]);

        $response = $this->actingAs($user)->delete(route('wishlist.destroy', $wishlist->id));

        $response->assertRedirect();
        
        $wishlist->refresh();
        $this->assertEquals(0, $wishlist->terkumpul);
        $this->assertEquals('dibatalkan', $wishlist->status);

        // Check income transaction created
        $transaction = Transaction::where('user_id', $user->id)
            ->where('category_id', 10) // INCOME category
            ->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(1, $transaction->transactionType_id); // Income type
        $this->assertEquals(1000000, $transaction->total_amount);
        $this->assertEquals('Pembatalan Wishlist: Smart TV', $transaction->description);
    }

    public function test_wishlist_confirmation_updates_status_and_saving_transaction_description()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Smartphone',
            'target_harga' => 4000000,
            'status' => 'tercapai',
            'terkumpul' => 4000000,
        ]);

        // Mock the original saving transaction
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => 11,
            'transactionType_id' => 2, // Expense
            'total_amount' => 4000000,
            'transaction_date' => Carbon::today()->toDateString(),
            'description' => 'Saving Wishlist: Smartphone',
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.konfirmasi', $wishlist->id));

        $response->assertRedirect();
        
        $wishlist->refresh();
        $this->assertEquals('dibeli', $wishlist->status);

        // Check transaction description changed
        $transaction = Transaction::where('user_id', $user->id)->first();
        $this->assertEquals('Pembelian Wishlist: Smartphone', $transaction->description);
    }
}
