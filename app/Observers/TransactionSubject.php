<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Contracts\SubjectInterface;


class TransactionSubject implements SubjectInterface
{
    private static ?TransactionSubject $instance = null;

    
    private array $observers = [];

    private function __construct()
    {
        
    }

    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    
    public function subscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        $this->observers[$key] = $observer;
    }

    
    public function unsubscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        unset($this->observers[$key]);
    }

    
    public function notifyObservers(string $event, mixed $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->onUpdate($event, $data);
        }
    }

    
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
