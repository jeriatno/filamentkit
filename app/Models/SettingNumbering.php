<?php

namespace App\Models;

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

    const module = [];

    const for    = [];

    public static $forModel = [];

    public $guarded = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];
}
