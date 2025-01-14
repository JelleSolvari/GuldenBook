<?php

namespace App\Services;

use App\DataObjects\BlockSubsidyData;
use App\Repositories\BlockRepository;
use Carbon\Carbon;

class BlockService
{
    public function __construct(
        private readonly BlockRepository $blockRepository
    ) {
    }

    public function getBlockSubsidy(int $height): BlockSubsidyData
    {
        if ($height === 1) {
            return new BlockSubsidyData(170_000_000, 0, 0); // First block (premine)
        } elseif ($height < config('gulden.fixed_reward_reduction_height')) {
            return new BlockSubsidyData(1000, 0, 0); // 1000 Gulden per block for first 250k blocks
        } elseif ($height < config('gulden.dev_block_subsidy_activation_height')) {
            return new BlockSubsidyData(100, 0, 0); // 100 Gulden per block (fixed reward/no halving)
        } elseif ($height < config('gulden.pow2_phase_4_first_block_height') + 1) {
            return new BlockSubsidyData(50, 20, 40); // 110 Gulden per block (fixed reward/no halving) - 50 mining, 40 development, 20 witness.
        } elseif ($height <= 1_226_651) {
            return new BlockSubsidyData(50, 30, 40); // 120 Gulden per block (fixed reward/no halving) - 50 mining, 40 development, 30 witness.
        } elseif ($height <= 1_228_003) {
            return new BlockSubsidyData(90, 30, 80); // 200 Gulden per block (fixed reward/no halving) - 90 mining, 80 development, 30 witness.
        } elseif ($height <= config('gulden.halving_introduction_height')) {
            return new BlockSubsidyData(50, 30, 80); // 160 Gulden per block (fixed reward/no halving) - 50 mining, 80 development, 30 witness.
        } else {
            // From this point on reward is as follows:
            // 90 Gulden per block; 10 mining, 15 witness, 65 development
            // Halving every 842500 blocks (roughly 4 years)
            // Rewards truncated to a maximum of 2 decimal places if large enough to have a number on the left of the decimal place
            // Otherwise truncated to 3 decimal places (if first place is occupied with a non zero number or otherwise a maximum of 4 decimal places
            // This is done to keep numbers a bit cleaner and more manageable
            // Halvings as follows:
            // 5 mining, 7.5 witness, 32.5 development
            // 2.5 mining, 3.75 witness, 16.25 development
            // 1.25 mining, 1.87 witness, 8.12 development
            // 0.625 mining, 0.937 witness, 4.06 development
            // 0.312 mining, 0.468 witness, 2.03 development
            // 0.156 mining, 0.234 witness, 1.01 development
            // 0.0781 mining, 0.117 witness, 0.507 development
            // 0.0390 mining, 0.0585 witness, 0.253 development
            // 0.0195 mining, 0.0292 witness, 0.126 development
            // 0.0976 mining, 0.0146 witness, 0.634 development
            // 0.0488 mining, 0.0732 witness, 0.317 development
            // 0.0244 mining, 0.0366 witness, 0.158 development
            // 0.0122 mining, 0.0183 witness, 0.0793 development
            // 0.0061 mining, 0.0091 witness, 0.0396 development
            // 0.0030 mining, 0.0045 witness, 0.0198 development
            // 0.0015 mining, 0.0022 witness, 0.0099 development
            // 0.0007 mining, 0.0011 witness, 0.0049 development
            // 0.0003 mining, 0.0005 witness, 0.0024 development
            // 0.0001 mining, 0.0002 witness, 0.0012 development
            // NB! We could use some bit shifts and other tricks here to do the halving calculations (the specific truncation rounding we are using makes it a bit difficult)
            // However we opt instead for this simple human readable "table" layout so that it is easier for humans to inspect/verify this.
            $halvings = (int) floor(($height - 1 - config('gulden.halving_introduction_height')) / 842500);

            return match ($halvings) {
                0 => new BlockSubsidyData(10, 15, 65),
                1 => new BlockSubsidyData(5, 7.5, 32.5),
                2 => new BlockSubsidyData(2.5, 3.75, 16.25),
                3 => new BlockSubsidyData(1.25, 1.87, 8.12),
                4 => new BlockSubsidyData(0.625, 0.937, 4.06),
                5 => new BlockSubsidyData(0.312, 0.468, 2.03),
                6 => new BlockSubsidyData(0.156, 0.234, 1.01),
                7 => new BlockSubsidyData(0.0781, 0.117, 0.507),
                8 => new BlockSubsidyData(0.0390, 0.0585, 0.253),
                9 => new BlockSubsidyData(0.0195, 0.0292, 0.126),
                10 => new BlockSubsidyData(0.0976, 0.0146, 0.634),
                11 => new BlockSubsidyData(0.0488, 0.0732, 0.317),
                12 => new BlockSubsidyData(0.0244, 0.0366, 0.158),
                13 => new BlockSubsidyData(0.0122, 0.0183, 0.0793),
                14 => new BlockSubsidyData(0.0061, 0.0091, 0.0396),
                15 => new BlockSubsidyData(0.0030, 0.0045, 0.0198),
                16 => new BlockSubsidyData(0.0015, 0.0022, 0.0099),
                17 => new BlockSubsidyData(0.0007, 0.0011, 0.0049),
                18 => new BlockSubsidyData(0.0003, 0.0005, 0.0024),
                default => ($height <= config('gulden.final_subsidy_block')) ?
                    new BlockSubsidyData(0.0001, 0.0002, 0.0012) :
                    new BlockSubsidyData(0, 0, 0)
            };
        }
    }

    /**
     * Calculates the approximate date the block will be mined.
     */
    public function calculateMinedAtDate(int $height): Carbon
    {
        $seconds = ($height - $this->blockRepository->currentHeight()) * config('gulden.blocktime');

        return now()->addSeconds($seconds);
    }
}
