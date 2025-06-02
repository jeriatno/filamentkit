<?php

namespace App\Models;

use App\User;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

// <------------------------------- this one
// <---------------------- and this one

class BackpackUser extends User
{
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this

    protected $table = 'users';

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function setImageAttribute($value)
    {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = "files/users/photo/";

        if ($value == null) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
        }

        if (starts_with($value, 'data:image')) {
            $image = \Image::make($value)->encode('jpg', 90);
            $filename = md5($value . time()) . '.jpg';
            \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            $this->attributes[$attribute_name] = $destination_path . '/' . $filename;
        }
    }

    public function getFullNameAttribute() {
        return $this->sg_code.' -  '.$this->name;
    }


    public function getPhotoAttribute() {
        if($this->image){
            $path = Storage::disk('public')->path($this->image);
            if(file_exists($path)){
                return Storage::disk('public')->url($this->image);
            }else{
                return backpack_avatar_url($this);
            }
        }else{
            return backpack_avatar_url($this);
        }
    }

}
