<?php

namespace App\Http\Controllers\IA\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralResponseStrategy implements ResponseStrategy
{
    public function handle(array $parts, string $originalPrompt): array
    {
        return [
            "response" => $parts[1] ?? '',
            "query" => ""
        ];
    }
}
