<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Api extends Model
{
    protected $table = 'fs_api';
	public $timestamps = false;

    protected $fillable = [
        'alert_type','port','enabled','password','system_name','username','virtual_input','ip_address'
    ];
}
