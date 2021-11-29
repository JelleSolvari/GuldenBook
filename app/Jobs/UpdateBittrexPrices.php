<?php

namespace App\Jobs;

use App\Models\Price;
use App\Repositories\PriceRepository;
use App\Services\BittrexService;
use App\Transformers\PriceTransformer;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UpdateBittrexPrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Fetch the current NLG-BTC and BTC-EUR prices, and calculate the NLG-EUR price, by comparing it to the current BTC-EUR price.
     * @param BittrexService $bittrexService
     */
    public function handle(BittrexService $bittrexService)
    {
        $lastDate = Price::query()
                ->where('source', '=', Price::SOURCE_BITTREX)
                ->max('timestamp') ?? Carbon::create(2020, 3, 31);

        foreach (CarbonPeriod::create($lastDate, now())->floorDays() as $date) {
            \Log::channel('stderr')->info($date);

            $guldenPrices = $bittrexService->getPrices($date, 'NLG-BTC');
            $bitcoinPrices = $bittrexService->getPrices($date, 'BTC-EUR');

            foreach (range(0, min($guldenPrices->count() - 1, $bitcoinPrices->count() - 1)) as $index) {
                $avgGuldenPrice = $this->calculateAverage($guldenPrices[$index]);
                $avgBitcoinPrice = $this->calculateAverage($bitcoinPrices[$index]);

                Price::updateOrCreate([
                    'timestamp' => $guldenPrices[$index]['startsAt'],
                ], [
                    'price' => $avgBitcoinPrice * $avgGuldenPrice,
                    'source' => Price::SOURCE_BITTREX,
                ]);
            }
        }
    }

    private function calculateAverage(Collection $prices): float
    {
        return array_sum([
            $prices['open'],
            $prices['high'],
            $prices['low'],
            $prices['close'],
        ]) / 4;
    }
}
