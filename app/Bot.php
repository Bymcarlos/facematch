<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $table = 'fs_bot';
	public $timestamps = false;

    protected $fillable = [
        'token','authenticationCode'
    ];
}
