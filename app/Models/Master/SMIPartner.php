<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class SMIPartner extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $connection = 'smip';
    protected $table = 'm_db_partner';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
     protected $guarded = ['id'];
//    protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    public function scopeSearchDisplay(Builder $query, string $term): Builder
    {
        return $query->select('id', DB::raw("CONCAT(code, ' - ', name) AS display_name"))
            ->whereRaw("CONCAT(code, ' ', name) LIKE ?", ["%{$term}%"])
            ->limit(50);
    }

    public function scopeWithDisplayName(Builder $query): Builder
    {
        return $query->select('id', DB::raw("CONCAT(code, ' - ', name) AS display_name"));
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
