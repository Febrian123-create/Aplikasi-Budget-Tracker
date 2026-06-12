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

    public function test_wishlist_allocation_reserves_amount_without_creating_transaction()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Liburan ke Bali',
            'target_harga' => 5000000,
            'status' => 'aktif',
            'allocated_amount' => 0,
            'terkumpul' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.alokasi', $wishlist->id), [
            'jumlah' => 1000000,
        ]);

        $response->assertRedirect();

        $wishlist->refresh();
        $this->assertEquals(1000000, $wishlist->allocated_amount);
        $this->assertCount(0, Transaction::where('user_id', $user->id)->get());
    }

    public function test_wishlist_allocation_cannot_exceed_remaining_target()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Sepeda Lipat',
            'target_harga' => 2000000,
            'status' => 'aktif',
            'allocated_amount' => 1500000,
            'terkumpul' => 1500000,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.alokasi', $wishlist->id), [
            'jumlah' => 600000, // Exceeds by 100k
        ]);

        $response->assertSessionHasErrors(['jumlah']);
        $wishlist->refresh();
        $this->assertEquals(1500000, $wishlist->allocated_amount);
        $this->assertCount(0, Transaction::where('user_id', $user->id)->get());
    }

    public function test_wishlist_cancellation_resets_allocation_without_creating_transaction()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Smart TV',
            'target_harga' => 3000000,
            'status' => 'aktif',
            'allocated_amount' => 1000000,
            'terkumpul' => 1000000,
        ]);

        $response = $this->actingAs($user)->delete(route('wishlist.destroy', $wishlist->id));

        $response->assertRedirect();

        $wishlist->refresh();
        $this->assertEquals(0, $wishlist->allocated_amount);
        $this->assertEquals('dibatalkan', $wishlist->status);
        $this->assertCount(0, Transaction::where('user_id', $user->id)->get());
    }

    public function test_wishlist_confirmation_creates_purchase_transaction()
    {
        $user = User::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Smartphone',
            'target_harga' => 4000000,
            'status' => 'tercapai',
            'allocated_amount' => 4000000,
            'terkumpul' => 4000000,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.konfirmasi', $wishlist->id));

        $response->assertRedirect();

        $wishlist->refresh();
        $this->assertEquals('dibeli', $wishlist->status);

        $transaction = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(11, $transaction->category_id);
        $this->assertEquals(2, $transaction->transactionType_id);
        $this->assertEquals(4000000, $transaction->total_amount);
        $this->assertEquals('Pembelian Wishlist: Smartphone', $transaction->description);
    }
}
