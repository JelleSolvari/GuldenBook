<?php

namespace App\DataObjects;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class BlockData extends DataTransferObject
{
    public int $height;

    public string $hash;

    public Carbon $timestamp;

    public float $value;

    public int $transactions;

    public int $version;

    public string $merkleRoot;
}
