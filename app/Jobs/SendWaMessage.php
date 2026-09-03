<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWaMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    protected string $phoneNumber;
    protected string $message;
    protected bool $birthday;

    public function __construct(string $phoneNumber = '', string $message = '', bool $birthday = false)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
        $this->birthday = $birthday;
    }

    public function handle(): void
    {
        // WA Gateway disabled / pruned for MVP scope
        return;
    }
}
