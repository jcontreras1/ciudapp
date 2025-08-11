<?php

namespace App\Http\Controllers\IA\Strategies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

interface ResponseStrategy
{
    public function handle(array $parts, string $originalPrompt): array;
}
