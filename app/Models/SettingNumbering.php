<?php

namespace App\Models;

use App\Models\Master\Rate;
use App\Models\Master\Warehouse;
use App\Models\Rent\RentIn;
use App\Models\Rent\RentOut;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingNumbering extends Model
{
    use SoftDeletes;

    protected $table = 'setting_numberings';

    public const DEFAULT  = 'default';
    public const FORMAT   = 'format';
    public const ALPHABET = 'alphabet';
    public const RESET    = 'reset';
    public const ROMAN    = 'roman';

    const module = [
        'warehouse' => 'warehouse',
        'rate'      => 'rate',
        'rent_in'   => 'rent_in',
        'rent_out'  => 'rent_out',
    ];

    const for    = [
        'warehouse' => 'code',
        'rate'      => 'code',
        'rent_in'   => 'doc_no',
        'rent_out'  => 'doc_no',
    ];

    public static $forModel = [
        'warehouse' => Warehouse::class,
        'rate'      => Rate::class,
        'rent_in'   => RentIn::class,
        'rent_out'  => RentOut::class,
    ];

    public $guarded = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
