<?php

namespace App\Contracts;


interface SubjectInterface
{
    
    public function subscribe(ObserverInterface $observer): void;

    
    public function unsubscribe(ObserverInterface $observer): void;

    
    public function notifyObservers(string $event, mixed $data): void;
}
