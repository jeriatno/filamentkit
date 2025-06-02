<?php

    namespace App\Models;

    use App\User;
    use Backpack\CRUD\CrudTrait;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class SettingEmailLog extends Model
    {
        use CrudTrait;
        use SoftDeletes;

        /*
        |--------------------------------------------------------------------------
        | GLOBAL VARIABLES
        |--------------------------------------------------------------------------
        */

        public const app = 'email-spool';
        protected $table = 'setting_email_logs';
        // protected $primaryKey = 'id';
        // public $timestamps = false;
        protected $guarded = ['id'];
        // protected $fillable = [];
        // protected $hidden = [];
        // protected $dates = [];

        /*
        |--------------------------------------------------------------------------
        | CONST VARIABLES
        |--------------------------------------------------------------------------
        */

        public const PENDING = 0;
        public const FAILED  = -1;
        public const SENT    = 1;

        public static $docType = [
            'your_module' => '',
        ];

        public static $emailConf = [
            'your_module' => '',
        ];

        public static $emailLog = [
            'your_module' => '',
        ];

        public const module = [
            'your_module' => '',
        ];

        public const views = [
            'your_module' => '',
        ];

        public const url = [
            'your_module' => '',
        ];

        public static $docNumber = [
            'your_module' => '',
        ];

        /*
        |--------------------------------------------------------------------------
        | FUNCTIONS
        |--------------------------------------------------------------------------
        */
        public static function getByModule($module)
        {
            return self::where('module_name', $module)
                ->get();
        }

        public static function findByModule($module, $docId)
        {
            return self::where('module_name', $module)
                ->where('doc_id', $docId)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | RELATIONS
        |--------------------------------------------------------------------------
        */
        public function actionBy()
        {
            return $this->belongsTo(User::class, 'sender', 'email');
        }

        /*
        |--------------------------------------------------------------------------
        | SCOPES
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | ACCESORS
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | MUTATORS
        |--------------------------------------------------------------------------
        */
    }
