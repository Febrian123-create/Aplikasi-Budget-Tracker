<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TransactionLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed standard tables
        $this->seed();
    }

    public function test_categories_10_and_11_are_excluded_from_dropdown_on_transactions_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertStatus(200);
        $response->assertDontSee('value="10"');
        $response->assertDontSee('value="11"');
        $response->assertSee('FOOD'); // FOOD
    }

    public function test_income_transaction_without_category_automatically_sets_to_income_category_10()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'date' => Carbon::today()->toDateString(),
            'type' => 'income',
            'amount' => 150000,
            'description' => 'Gaji Bulanan',
            'category' => '', // Empty
        ]);

        $response->assertRedirect();
        
        $transaction = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(10, $transaction->category_id);
    }

    public function test_wishlist_allocation_sets_category_id_to_11()
    {
        $user = User::factory()->create();
        
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'nama' => 'Beli Sepatu Baru',
            'target_harga' => 1000000,
            'deadline' => Carbon::tomorrow()->toDateString(),
            'catatan' => 'Sepatu lari',
            'status' => 'aktif',
            'terkumpul' => 0,
        ]);

        $response = $this->actingAs($user)->post(route('wishlist.alokasi', $wishlist->id), [
            'jumlah' => 150000,
        ]);

        $response->assertRedirect();

        $transaction = Transaction::where('user_id', $user->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(11, $transaction->category_id);
        $this->assertEquals(150000, $transaction->total_amount);
        $this->assertEquals('Alokasi Wishlist: Beli Sepatu Baru', $transaction->description);
    }

    public function test_transactions_with_other_dates_do_not_appear_on_today_transactions()
    {
        $user = User::factory()->create();

        // Create transaction for yesterday
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => 1,
            'transactionType_id' => 2, // Expense
            'total_amount' => 50000,
            'transaction_date' => Carbon::yesterday()->toDateString(),
            'description' => 'Beli makan kemarin',
        ]);

        // Create transaction for today
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => 1,
            'transactionType_id' => 2, // Expense
            'total_amount' => 30000,
            'transaction_date' => Carbon::today()->toDateString(),
            'description' => 'Beli makan hari ini',
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertStatus(200);
        $response->assertSee('Beli makan hari ini');
        $response->assertDontSee('Beli makan kemarin');
    }

    public function test_today_transactions_are_paginated_max_10_per_page()
    {
        $user = User::factory()->create();

        $names = [
            1 => 'Transaksi SATU',
            2 => 'Transaksi DUA',
            3 => 'Transaksi TIGA',
            4 => 'Transaksi EMPAT',
            5 => 'Transaksi LIMA',
            6 => 'Transaksi ENAM',
            7 => 'Transaksi TUJUH',
            8 => 'Transaksi DELAPAN',
            9 => 'Transaksi SEMBILAN',
            10 => 'Transaksi SEPULUH',
            11 => 'Transaksi SEBELAS',
            12 => 'Transaksi DUABELAS',
        ];

        // Create 12 transactions for today
        for ($i = 1; $i <= 12; $i++) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => 1,
                'transactionType_id' => 2, // Expense
                'total_amount' => 10000,
                'transaction_date' => Carbon::today()->addSeconds($i)->toDateTimeString(),
                'description' => $names[$i],
            ]);
        }

        // View first page
        $response = $this->actingAs($user)->get(route('transactions.index'));
        $response->assertStatus(200);
        $response->assertSee('Transaksi DUABELAS');
        $response->assertDontSee('Transaksi SATU'); // Order by desc, so 11 and 12 are first. Transactions 1 and 2 are on page 2.
        
        // Check that pagination links exist
        $response->assertSee('Pagination Navigation');

        // View second page
        $response2 = $this->actingAs($user)->get(route('transactions.index', ['page' => 2]));
        $response2->assertStatus(200);
        $response2->assertSee('Transaksi SATU');
        $response2->assertDontSee('Transaksi DUABELAS');
    }

    public function test_today_totals_include_all_transactions_for_today_even_when_paginated()
    {
        $user = User::factory()->create();

        // Create 12 transactions for today, 10000 each (total 120000)
        for ($i = 1; $i <= 12; $i++) {
            Transaction::create([
                'user_id' => $user->id,
                'category_id' => 1,
                'transactionType_id' => 1, // Income
                'total_amount' => 10000,
                'transaction_date' => Carbon::today()->addSeconds($i)->toDateTimeString(),
                'description' => "Tx-{$i}",
            ]);
        }

        $response = $this->actingAs($user)->get(route('transactions.index'));
        $response->assertStatus(200);
        // Total should be 120.000, not 100.000 (which is page 1 total)
        $response->assertSee('Rp 120.000');
    }
}
