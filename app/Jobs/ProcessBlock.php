<?php

namespace App\Jobs;

use App\Repositories\BlockRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\VinRepository;
use App\Repositories\VoutRepository;
use App\Services\GuldenService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessBlock implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int
     */
    private int $height;

    /**
     * Create a new job instance.
     *
     * @param int $height
     */
    public function __construct(int $height)
    {
        $this->height = $height;
    }

    /**
     * Execute the job.
     *
     * @param GuldenService $guldenService
     * @return void
     * @throws Throwable
     */
    public function handle(GuldenService $guldenService)
    {
        $blockData = $guldenService->getBlock($guldenService->getBlockHash($this->height), 1);

        DB::beginTransaction();

        $block = BlockRepository::syncBlock($blockData);

        if($block->hashps === null) {
            dispatch(new SetHashrate($block->height));
        }

        foreach ($blockData->get('tx') as $txid) {
            $tx = $guldenService->getTransaction($txid, true);

            $transaction = TransactionRepository::syncTransaction($tx, $block);

            VoutRepository::syncVouts($tx->get('vout'), $transaction);

            VinRepository::syncVins($tx->get('vin'), $transaction);
        }

        DB::commit();

        if($block->isWitness() && $block->transactions()->count() < 2) {
            dispatch((new ProcessBlock($this->height)))->delay(now()->addSeconds(config('gulden.sync_delay')));
        }
    }
}
