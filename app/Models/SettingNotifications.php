<?php

namespace App\Models;

use App\User;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class SettingNotifications extends Model
{
    use CrudTrait, SoftDeletes;

    protected $table = 'setting_notifications';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public static $repo = [
        'your_module'        => YourRepository::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Uuid::uuid4();
        });
    }

    public function notifiable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notifiable_id', 'id');
    }

    public function notifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'notifier_id', 'id');
    }
}
