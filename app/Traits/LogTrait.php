<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LogTrait
{
    public function logException(\Exception $exception, string $context): void
    {
        Log::error('Exception' . " in $context" . ': ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
