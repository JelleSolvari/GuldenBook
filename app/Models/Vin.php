<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Vin
 *
 * @package App\Models
 */
class Vin extends Model
{
    protected $table = 'vins';

    protected $fillable = [
        'prevout_type',
        'coinbase',
        'tx_height',
        'tx_index',
        'scriptSig_asm',
        'scriptSig_hex',
        'vout',
        'rbf',
        'transaction_id',
        'vout_id',
    ];

    public const PREVOUT_TYPE_INDEX = 'index';
    public const PREVOUT_TYPE_HASH = 'hash';

    public const PREVOUT_TYPES = [
        self::PREVOUT_TYPE_INDEX,
        self::PREVOUT_TYPE_HASH,
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function vout(): BelongsTo
    {
        return $this->belongsTo(Vout::class);
    }
}
