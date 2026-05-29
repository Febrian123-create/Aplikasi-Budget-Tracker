<?php

namespace App\Contracts;


interface ObserverInterface
{
    
    public function onUpdate(string $event, mixed $data): void;
}
