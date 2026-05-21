<?php

namespace App\Factories;

use App\Models\Transaction;
use App\Models\TransactionType;
use App\Models\RecurringTransaction;
use App\Helpers\RecurringHelper;
use Carbon\Carbon;


class TransactionFactory
{
    
    public static function createRegularTransaction(array $data): Transaction
    {
        $transactionTypeId = TransactionType::where('name', $data['type'])->value('transactionType_id');

        return Transaction::create([
            'user_id'            => $data['user_id'],
            'category_id'        => $data['category_id'],
            'transactionType_id' => $transactionTypeId,
            'total_amount'       => $data['amount'],
            'transaction_date'   => $data['date'],
            'description'        => $data['description'],
        ]);
    }

    
    public static function createRecurringTransaction(array $data): RecurringTransaction
    {
        $startDate = Carbon::parse($data['start_date']);

        return RecurringTransaction::create([
            'user_id'       => $data['user_id'],
            'category_id'   => $data['category_id'],
            'amount'        => $data['amount'],
            'frequency'     => $data['frequency'],
            'start_date'    => $startDate->toDateString(),
            'end_date'      => isset($data['end_date']) ? Carbon::parse($data['end_date'])->toDateString() : null,
            'next_run_date' => $startDate->toDateString(), 
            'description'   => $data['description'],
            'amount_type'   => $data['amount_type'],
            'status'        => 'aktif',
            'reminder_id'   => null, 
        ]);
    }

    
    public static function createTransactionFromRecurring(RecurringTransaction $recurring): Transaction
    {
        
        $typeName = $recurring->amount_type === 'pemasukan' ? 'income' : 'expense';
        $transactionTypeId = TransactionType::where('name', $typeName)->value('transactionType_id');

        return Transaction::create([
            'user_id'            => $recurring->user_id,
            'category_id'        => $recurring->category_id,
            'transactionType_id' => $transactionTypeId,
            'total_amount'       => $recurring->amount,
            'transaction_date'   => $recurring->next_run_date->toDateString(),
            'description'        => $recurring->description . ' (Recurring)',
        ]);
    }
}
