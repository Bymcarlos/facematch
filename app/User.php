<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'fs_user';
    //To avoid the field 'remember_token' on table users:
    protected $rememberTokenName = false;
    //This doesn't works, it's necessary to edit file "/var/www/html/.../vendor/laravel/framework/src/Illuminate/Auth/SessionGuard.php", and comment the code inside cycleRememberToken(..) function (line 534).
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username','creation_datetime','level'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role)
    {
        //if ($this->roles()->where('name', $role)->first()) {
        if (strtolower($this->level)==strtolower("$role")) {
            return true;
        }
        return false;
    }
}
