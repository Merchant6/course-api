<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Traits\Cart;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteCartData
{
    use Cart;
    

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        $this->deleteCartData();
    }
}
