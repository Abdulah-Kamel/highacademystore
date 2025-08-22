<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class ProductObserver
{
    /**
     * Handle the Product "updating" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updating(Product $product)
    {
        if ($product->isDirty('state')) {
            $oldState = $product->getOriginal('state');
            $newState = $product->state;
            // Get a limited backtrace to find the source of the change
            $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7))->map(function ($item) {
                return ($item['file'] ?? 'unknown') . ':' . ($item['line'] ?? 'unknown');
            });

            Log::info("Product State Change - Product ID: {$product->id}, Old State: {$oldState}, New State: {$newState}", [
                'trace' => $trace
            ]);
        }
    }
}
