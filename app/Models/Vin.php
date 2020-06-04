<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'txid',
        'coinbase',
        'tx_height',
        'tx_index',
        'scriptSig_asm',
        'scriptSig_hex',
        'vout',
        'rbf',
        'transaction_id',
    ];
}
