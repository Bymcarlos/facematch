<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Emailrecepient extends Model
{
    protected $table = 'fs_emailrecepients';
	public $timestamps = false;

    protected $fillable = [
        'description','email_address'
    ];
}
