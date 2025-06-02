<?php

namespace App\Models\Rent;

use App\Enums\RentStatus;
use App\Models\Master\City;
use App\Models\Master\Partner;
use App\Models\Master\Warehouse;
use App\Traits\AutoDocNumber;
use Illuminate\Database\Eloquent\Model;

class RentIn extends Model
{
    use AutoDocNumber;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'rent_in';
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
        static::generateNumber('rent_in');

        static::creating(function ($model) {
            $model->status = RentStatus::NEW;
        });
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function details()
    {
        return $this->hasMany(RentInDetail::class);
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
