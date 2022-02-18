<?php

namespace App\DataObjects\Address;

use App\Enums\WitnessAddressPartStatusEnum;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class WitnessAddressData extends DataTransferObject
{
    public string $address;

    public ?float $totalAmountLocked;

    public ?float $totalWeight;

    public ?int $lockedFromBlock;

    public ?Carbon $lockedFromTimestamp;

    public ?int $lockedUntilBlock;

    public ?Carbon $lockedUntilTimestamp;

    public ?Collection $parts;

    public int $totalRewards;

    public float $totalRewardsValue;
}
