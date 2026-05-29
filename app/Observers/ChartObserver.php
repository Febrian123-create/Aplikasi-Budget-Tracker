<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Services\ChartService;


class ChartObserver implements ObserverInterface
{
    private bool $needsRefresh = false;

    public function __construct(
        private ChartService $chartService
    ) {}

    
    public function onUpdate(string $event, mixed $data): void
    {
        $this->needsRefresh = true;

        
        
    }

    
    public function needsRefresh(): bool
    {
        return $this->needsRefresh;
    }

    
    public function resetRefresh(): void
    {
        $this->needsRefresh = false;
    }

    
    public function subscribe(): void
    {
        TransactionSubject::getInstance()->subscribe($this);
    }

    
    public function unsubscribe(): void
    {
        TransactionSubject::getInstance()->unsubscribe($this);
    }
}
