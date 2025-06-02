<?php

namespace App\Models\Rent;

use App\Enums\BillStatus;
use App\Enums\RentStatus;
use App\Models\Master\Partner;
use App\Models\Master\Warehouse;
use App\Traits\AutoDocNumber;
use Illuminate\Database\Eloquent\Model;

class RentBill extends Model
{
    use AutoDocNumber;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'rent_bill';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];
    protected $docNo = 'code';

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public static function boot() {
        parent::boot();

        // generate doc number
        static::generateNumber('rent_bill');

        static::creating(function ($model) {
            $model->status = BillStatus::UNPAID;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function rentIn()
    {
        return $this->belongsTo(RentIn::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
