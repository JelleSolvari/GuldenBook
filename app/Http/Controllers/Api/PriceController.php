<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PriceCollection;
use App\Models\Price;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function index(string $timeframe)
    {
        //todo: refactor to enums in php 8.1
        $since = match($timeframe) {
            '1d' => now()->subDay(),
            '7d' => now()->subDays(7),
            '1m' => now()->subMonth(),
            '3m' => now()->subMonths(3),
            '1y' => now()->subYear(),
            'ytd' => now()->startOfYear(),
            'all' => null,
        };

        $groupBy = match($timeframe) {
            '1d' => 60,
            '7d' => 300,
            '1m' => 3600,
            '3m' => 43200,
            '1y', 'all' => 86400,
            'ytd' => now()->startOfYear()->diffInSeconds(now()) / 1440,
        };

        $prices = Price::query()
            ->select([
                DB::raw("TIMESTAMP 'epoch' + INTERVAL '1 second' * ROUND(EXTRACT('epoch' FROM timestamp) / $groupBy) * $groupBy as time"),
                DB::raw("AVG(price) AS price"),
            ])
            ->groupBy('time')
            ->orderBy('time');

        if($since !== null) {
            $prices->whereDate('timestamp', '>=', $since);
        }

        return PriceCollection::make($prices->get());
    }
}
