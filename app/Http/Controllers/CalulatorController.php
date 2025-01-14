<?php

namespace App\Http\Controllers;

use App\Repositories\BlockRepository;
use App\Repositories\PriceRepository;
use App\Services\BlockService;
use App\Services\GuldenService;

class CalulatorController extends Controller
{
    public function __construct(
        private readonly GuldenService $guldenService,
        private readonly PriceRepository $priceRepository,
        private readonly BlockRepository $blockRepository,
        private readonly BlockService $blockService,
    ) {
    }

    public function witnessYieldCalculator()
    {
        $witnessInfo = $this->guldenService->getWitnessInfo();
        $currentSubsidy = $this->blockService->getBlockSubsidy($this->blockRepository->currentHeight());

        return view('pages.calculators.witness', [
            'networkWeight' => $witnessInfo->get('total_witness_weight_raw'),
            'networkWeightAdjusted' => $witnessInfo->get('total_witness_weight_eligible_adjusted'),
            'witnessReward' => $currentSubsidy->witness,
        ]);
    }

    public function miningYieldCalculator()
    {
        $price = $this->priceRepository->getCurrentPrice();
        $hashrate = $this->guldenService->getNetworkHashrate();
        $difficulty = (int) $this->blockRepository->getAverageDifficulty();

        return view('pages.calculators.mining', [
            'price' => $price,
            'hashrate' => $hashrate,
            'difficulty' => $difficulty,
        ]);
    }
}
